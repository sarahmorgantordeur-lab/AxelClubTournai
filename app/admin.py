from flask import Blueprint, render_template, request, redirect, url_for, flash, jsonify
from flask_login import login_required, current_user
from functools import wraps
from app import db
from app.models import User, Group, Attendance, PaymentRecord, TrainingSession, Report, SeasonPayment, Site
from datetime import datetime, timedelta
from sqlalchemy import cast, String
import json

bp = Blueprint('admin', __name__, url_prefix='/admin')

def admin_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not current_user.is_authenticated or not current_user.has_role('admin'):
            flash('Accès refusé', 'error')
            return redirect(url_for('main.dashboard'))
        return f(*args, **kwargs)
    return decorated_function


@bp.route('/')
@login_required
@admin_required
def dashboard():
    stats = {
        'total_members': User.query_by_role('patineur').count(),
        'total_groups': Group.query.count(),
        'total_users': User.query.count(),
        'total_revenue': sum([p.amount for p in PaymentRecord.query.filter_by(status='paid').all()]) or 0
    }
    recent_members = User.query_by_role('patineur').order_by(User.created_at.desc()).limit(5).all()
    return render_template('admin/dashboard.html', stats=stats, recent_members=recent_members)

# ===== GESTION DES UTILISATEURS =====

@bp.route('/users')
@bp.route('/members')  # Alias pour compatibilité
@login_required
@admin_required
def users():
    page = request.args.get('page', 1, type=int)
    roles_filter = request.args.get('role', '')

    query = User.query
    if roles_filter:
        query = query.filter(cast(User.roles, String).contains(f'"{roles_filter}"'))

    users = query.order_by(User.created_at.desc()).paginate(page=page, per_page=10)
    return render_template('admin/users.html', users=users, current_role=roles_filter)

@bp.route('/users/create', methods=['GET', 'POST'])
@login_required
@admin_required
def create_user():
    groups = Group.query.all()
    sites = Site.query.all()
    patineurs = User.query_by_role('patineur').all()

    if request.method == 'POST':
        username = request.form.get('username')
        email = request.form.get('email')
        first_name = request.form.get('first_name')
        last_name = request.form.get('last_name')
        phone = request.form.get('phone')

        # Gérer les rôles multiples
        roles = request.form.getlist('roles')
        if not roles:
            roles = ['patineur']

        if User.query.filter_by(email=email).first():
            flash('Cet email existe déjà', 'error')
            return redirect(url_for('admin.create_user'))

        generated_username = username or f"{first_name.lower()}.{last_name.lower()}"
        if User.query.filter_by(username=generated_username).first():
            flash('Ce nom d\'utilisateur existe déjà', 'error')
            return redirect(url_for('admin.create_user'))

        # Le groupe est autorisé seulement si le rôle patineur est présent
        group_id = None
        if 'patineur' in roles:
            group_id = request.form.get('group_id') or None

        license_number = request.form.get('license_number') or None
        if license_number and User.query.filter_by(license_number=license_number).first():
            flash('Ce numéro de licence est déjà utilisé', 'error')
            return redirect(url_for('admin.create_user'))

        user = User(
            username=generated_username,
            email=email,
            first_name=first_name,
            last_name=last_name,
            phone=phone,
            roles=roles,
            group_id=group_id,
            license_number=license_number
        )
        user.set_password(request.form.get('password', 'default123'))
        db.session.add(user)
        db.session.flush()  # obtenir l'id avant commit

        # Gérer les sites
        site_ids = request.form.getlist('site_ids')
        for sid in site_ids:
            s = Site.query.get(int(sid))
            if s:
                user.sites.append(s)

        # Gérer les relations parent-enfant
        if 'parent' in roles:
            children_ids = request.form.getlist('children')
            for child_id in children_ids:
                child = User.query.get(int(child_id))
                if child:
                    user.children.append(child)

        db.session.commit()
        flash('Utilisateur créé avec succès', 'success')
        return redirect(url_for('admin.users'))

    return render_template('admin/create_user.html', groups=groups, sites=sites, patineurs=patineurs)

@bp.route('/users/<int:user_id>/edit', methods=['GET', 'POST'])
@login_required
@admin_required
def edit_user(user_id):
    user = User.query.get_or_404(user_id)
    groups = Group.query.all()
    sites = Site.query.all()
    patineurs = User.query_by_role('patineur').all()

    if request.method == 'POST':
        user.first_name = request.form.get('first_name')
        user.last_name = request.form.get('last_name')
        user.phone = request.form.get('phone')

        # Gérer les rôles multiples
        roles = request.form.getlist('roles')
        if not roles:
            roles = ['patineur']  # Au moins un rôle par défaut
        user.roles = roles

        # Le groupe est autorisé seulement si le rôle patineur est présent
        if 'patineur' in roles:
            user.group_id = request.form.get('group_id') or None
        else:
            user.group_id = None  # Pas de groupe pour les non-patineurs

        license_number = request.form.get('license_number') or None
        if license_number:
            existing = User.query.filter_by(license_number=license_number).first()
            if existing and existing.id != user.id:
                flash('Ce numéro de licence est déjà utilisé', 'error')
                return redirect(url_for('admin.edit_user', user_id=user.id))
        user.license_number = license_number
        user.status = request.form.get('status')

        # Gérer les relations parent-enfant
        if 'parent' in roles:
            # Récupérer les enfants sélectionnés
            children_ids = request.form.getlist('children')
            # Supprimer tous les enfants actuels
            for child in user.get_children_list():
                user.children.remove(child)
            # Ajouter les nouveaux enfants
            for child_id in children_ids:
                child = User.query.get(int(child_id))
                if child:
                    user.children.append(child)
        else:
            for child in user.get_children_list():
                user.children.remove(child)

        # Gérer les sites
        site_ids = request.form.getlist('site_ids')
        user.sites.clear()
        for sid in site_ids:
            s = Site.query.get(int(sid))
            if s:
                user.sites.append(s)

        db.session.commit()
        flash('Utilisateur mis à jour', 'success')
        return redirect(url_for('admin.users'))

    return render_template('admin/edit_user.html', user=user, groups=groups, sites=sites, patineurs=patineurs)

@bp.route('/users/<int:user_id>/delete', methods=['POST'])
@login_required
@admin_required
def delete_user(user_id):
    user = User.query.get_or_404(user_id)
    if user.id == current_user.id:
        flash('Vous ne pouvez pas supprimer votre propre compte', 'error')
        return redirect(url_for('admin.users'))
    db.session.delete(user)
    db.session.commit()
    flash('Utilisateur supprimé', 'success')
    return redirect(url_for('admin.users'))

# ===== GESTION DES GROUPES =====

@bp.route('/groups')
@login_required
@admin_required
def groups():
    groups = Group.query.all()
    return render_template('admin/groups.html', groups=groups)

@bp.route('/groups/create', methods=['GET', 'POST'])
@login_required
@admin_required
def create_group():

    if request.method == 'POST':
        group = Group(
            name=request.form.get('name'),
            schedule=request.form.get('schedule'),
            price_per_season=request.form.get('price_per_season') or None,
            description=request.form.get('description')
        )
        db.session.add(group)
        db.session.commit()
        flash('Groupe créé avec succès', 'success')
        return redirect(url_for('admin.groups'))

    return render_template('admin/create_group.html')

@bp.route('/groups/<int:group_id>/edit', methods=['GET', 'POST'])
@login_required
@admin_required
def edit_group(group_id):
    group = Group.query.get_or_404(group_id)

    if request.method == 'POST':
        group.name = request.form.get('name')
        group.schedule = request.form.get('schedule')
        group.price_per_season = request.form.get('price_per_season') or None
        group.description = request.form.get('description')

        db.session.commit()
        flash('Groupe mis à jour', 'success')
        return redirect(url_for('admin.groups'))

    return render_template('admin/edit_group.html', group=group)

@bp.route('/groups/<int:group_id>/delete', methods=['POST'])
@login_required
@admin_required
def delete_group(group_id):
    group = Group.query.get_or_404(group_id)
    db.session.delete(group)
    db.session.commit()
    flash('Groupe supprimé', 'success')
    return redirect(url_for('admin.groups'))

# ===== GESTION DES PRÉSENCES =====

@bp.route('/attendance')
@login_required
@admin_required
def attendance():
    attendances = Attendance.query.order_by(Attendance.session_date.desc()).limit(50).all()
    return render_template('admin/attendance.html', attendances=attendances)

@bp.route('/attendance/record', methods=['GET', 'POST'])
@login_required
@admin_required
def record_attendance():
    groups = Group.query.all()
    sites = Site.query.all()

    if request.method == 'POST':
        group_id = request.form.get('group_id')
        session_date = datetime.fromisoformat(request.form.get('session_date'))
        site_id = request.form.get('site_id') or None
        if site_id:
            site_id = int(site_id)

        for user_id in request.form.getlist('user_ids'):
            status = request.form.get(f'status_{user_id}', 'present')
            notes = request.form.get(f'notes_{user_id}', '')

            attendance = Attendance(
                user_id=int(user_id),
                session_date=session_date,
                status=status,
                notes=notes,
                site_id=site_id,
                recorded_by_id=current_user.id
            )
            db.session.add(attendance)

        db.session.commit()
        flash('Présences enregistrées', 'success')
        return redirect(url_for('admin.attendance'))

    return render_template('admin/record_attendance.html', groups=groups, sites=sites)

# ===== GESTION DES PAIEMENTS =====

@bp.route('/payments')
@login_required
@admin_required
def payments():
    page = request.args.get('page', 1, type=int)
    payments = PaymentRecord.query.paginate(page=page, per_page=10)
    return render_template('admin/payments.html', payments=payments)

@bp.route('/payments/add', methods=['GET', 'POST'])
@login_required
@admin_required
def add_payment():
    # Afficher tous les utilisateurs pour le paiement
    users = User.query_by_role('patineur').all()

    if request.method == 'POST':
        user_id = int(request.form.get('user_id'))
        amount = float(request.form.get('amount'))
        payment_type = request.form.get('payment_type')
        status = request.form.get('status', 'paid')

        payment = PaymentRecord(
            user_id=user_id,
            amount=amount,
            payment_type=payment_type,
            payment_method=request.form.get('payment_method'),
            payment_date=datetime.fromisoformat(request.form.get('payment_date')),
            status=status,
            notes=request.form.get('notes')
        )
        db.session.add(payment)

        # Mettre à jour le SeasonPayment si le paiement est marqué comme payé
        if status == 'paid' and payment_type in ('licence', 'saison_tournai', 'wasquehal_p1', 'wasquehal_p2', 'competitions'):
            current_year = datetime.now().year
            sp = SeasonPayment.query.filter_by(user_id=user_id, season_year=current_year).first()
            if not sp:
                sp = SeasonPayment(user_id=user_id, season_year=current_year)
                db.session.add(sp)

            paid_field = f'{payment_type}_paid'
            current_paid = getattr(sp, paid_field) or 0
            setattr(sp, paid_field, current_paid + amount)

        db.session.commit()
        flash('Paiement enregistré', 'success')
        return redirect(url_for('admin.payments'))

    return render_template('admin/add_payment.html', users=users)

# ===== PAIEMENTS PAR CATÉGORIE (SAISON) =====

@bp.route('/users/<int:user_id>/payments')
@login_required
@admin_required
def user_payments(user_id):
    """Vue détaillée des paiements d'un patineur par catégorie"""
    user = User.query.get_or_404(user_id)
    current_year = datetime.now().year

    # Créer l'entrée SeasonPayment si elle n'existe pas
    sp = SeasonPayment.query.filter_by(user_id=user.id, season_year=current_year).first()
    if not sp:
        sp = SeasonPayment(user_id=user.id, season_year=current_year)
        db.session.add(sp)
        db.session.commit()

    return render_template('admin/user_payments.html', user=user, payment=sp, current_year=current_year)

@bp.route('/users/<int:user_id>/payments/update', methods=['POST'])
@login_required
@admin_required
def update_user_payments(user_id):
    """Met à jour les montants de paiement par catégorie"""
    user = User.query.get_or_404(user_id)
    current_year = datetime.now().year

    sp = SeasonPayment.query.filter_by(user_id=user.id, season_year=current_year).first()
    if not sp:
        sp = SeasonPayment(user_id=user.id, season_year=current_year)
        db.session.add(sp)

    # Montants à payer
    sp.licence_due = float(request.form.get('licence_due', 0) or 0)
    sp.saison_tournai_due = float(request.form.get('saison_tournai_due', 0) or 0)
    sp.wasquehal_p1_due = float(request.form.get('wasquehal_p1_due', 0) or 0)
    sp.wasquehal_p2_due = float(request.form.get('wasquehal_p2_due', 0) or 0)
    sp.competitions_due = float(request.form.get('competitions_due', 0) or 0)

    # Montants payés
    sp.licence_paid = float(request.form.get('licence_paid', 0) or 0)
    sp.saison_tournai_paid = float(request.form.get('saison_tournai_paid', 0) or 0)
    sp.wasquehal_p1_paid = float(request.form.get('wasquehal_p1_paid', 0) or 0)
    sp.wasquehal_p2_paid = float(request.form.get('wasquehal_p2_paid', 0) or 0)
    sp.competitions_paid = float(request.form.get('competitions_paid', 0) or 0)

    sp.notes = request.form.get('notes', '')

    db.session.commit()
    flash(f'Paiements de {user.first_name} {user.last_name} mis à jour', 'success')
    return redirect(url_for('admin.user_payments', user_id=user.id))

# ===== RÉINSCRIPTIONS ANNUELLES =====

@bp.route('/registrations')
@login_required
@admin_required
def registrations():
    """Affiche les listes des inscrits/réinscrits et non-réinscrits de l'année"""
    current_year = datetime.now().year

    # Récupérer tous les patineurs
    all_patineurs = User.query_by_role('patineur').all()

    # Séparer en deux listes
    registered_this_year = [u for u in all_patineurs if u.registration_year == current_year]
    not_registered_this_year = [u for u in all_patineurs if u.registration_year != current_year]

    return render_template('admin/registrations.html',
                          registered=registered_this_year,
                          not_registered=not_registered_this_year,
                          current_year=current_year)

@bp.route('/registrations/register/<int:user_id>', methods=['POST'])
@login_required
@admin_required
def register_user(user_id):
    """Marque un utilisateur comme inscrit/réinscrit pour l'année en cours"""
    user = User.query.get_or_404(user_id)
    current_year = datetime.now().year
    user.registration_year = current_year
    db.session.commit()
    flash(f'{user.first_name} {user.last_name} a été réinscrit(e) pour {current_year}', 'success')
    return redirect(url_for('admin.registrations'))

@bp.route('/registrations/unregister/<int:user_id>', methods=['POST'])
@login_required
@admin_required
def unregister_user(user_id):
    """Annule la réinscription d'un utilisateur (remet à l'année précédente)"""
    user = User.query.get_or_404(user_id)
    current_year = datetime.now().year
    user.registration_year = current_year - 1
    db.session.commit()
    flash(f'Réinscription de {user.first_name} {user.last_name} annulée', 'warning')
    return redirect(url_for('admin.registrations'))

# ===== PROFIL ADMIN =====

@bp.route('/profile')
@login_required
@admin_required
def profile():
    """Affiche le profil de l'administrateur connecté"""
    current_year = datetime.now().year
    all_patineurs = User.query_by_role('patineur').all()
    registered_count = len([u for u in all_patineurs if u.registration_year == current_year])
    not_registered_count = len([u for u in all_patineurs if u.registration_year != current_year])

    return render_template('admin/profile.html',
                          user=current_user,
                          total_users=User.query.count(),
                          total_groups=Group.query.count(),
                          registered_count=registered_count,
                          not_registered_count=not_registered_count,
                          current_year=current_year)

@bp.route('/profile/edit', methods=['POST'])
@login_required
@admin_required
def edit_profile():
    """Modifie le profil de l'administrateur"""
    user = current_user

    user.first_name = request.form.get('first_name', user.first_name)
    user.last_name = request.form.get('last_name', user.last_name)
    user.phone = request.form.get('phone', user.phone)

    new_email = request.form.get('email', user.email)
    if new_email != user.email:
        if User.query.filter_by(email=new_email).first():
            flash('Cet email est déjà utilisé', 'error')
            return redirect(url_for('admin.profile'))
        user.email = new_email

    date_str = request.form.get('date_of_birth')
    if date_str:
        user.date_of_birth = datetime.strptime(date_str, '%Y-%m-%d').date()

    db.session.commit()
    flash('Profil mis à jour avec succès', 'success')
    return redirect(url_for('admin.profile'))

# ===== RAPPORTS =====

@bp.route('/reports')
@login_required
@admin_required
def reports():
    report_type = request.args.get('type', 'attendance')

    if report_type == 'attendance':
        data = generate_attendance_report()
    elif report_type == 'financial':
        data = generate_financial_report()
    else:
        data = {}

    return render_template('admin/reports.html', report_type=report_type, data=data)

def generate_attendance_report():
    users = User.query_by_role('patineur').all()
    report_data = []
    for user in users:
        attendance_rate = user.attendance_rate()
        total_sessions = len(user.attendances)
        report_data.append({
            'name': f"{user.first_name} {user.last_name}",
            'attendance_rate': attendance_rate,
            'total_sessions': total_sessions
        })
    return {'user': report_data}

def generate_financial_report():
    payments = PaymentRecord.query.all()
    total_paid = sum([p.amount for p in payments if p.status == 'paid'])
    total_pending = sum([p.amount for p in payments if p.status == 'pending'])

    return {
        'total_paid': total_paid,
        'total_pending': total_pending,
        'total_revenue': total_paid
    }


# ===== ENVOI DE MAILS =====

@bp.route('/send-email', methods=['GET', 'POST'])
@login_required
@admin_required
def send_email():
    groups = Group.query.all()

    if request.method == 'POST':
        group_ids = request.form.getlist('group_ids')
        subject = request.form.get('subject', '')
        body = request.form.get('body', '')

        if not group_ids:
            flash('Veuillez sélectionner au moins un groupe', 'error')
            return render_template('admin/send_email.html', groups=groups)

        # Collecter les destinataires
        recipients = set()
        group_names = []

        for group_id in group_ids:
            group = Group.query.get(int(group_id))
            if group:
                group_names.append(group.name)
                # Ajouter les patineurs du groupe
                for member in group.members:
                    if member.email:
                        recipients.add(member.email)
                    # Ajouter les parents de chaque patineur
                    for parent in member.get_parents_list():
                        if parent.email:
                            recipients.add(parent.email)

        if not recipients:
            flash('Aucun destinataire trouvé pour les groupes sélectionnés', 'error')
            return render_template('admin/send_email.html', groups=groups)

        # Préfixe automatique du corps du mail
        groups_label = ', '.join(group_names)
        full_body = f"Concerne : Groupe {groups_label}\n\n{body}"

        # Générer le lien mailto pour ouvrir le client mail de l'utilisateur
        from urllib.parse import quote
        mailto_recipients = ','.join(recipients)
        mailto_link = f"mailto:{quote(mailto_recipients)}?subject={quote(subject)}&body={quote(full_body)}"

        return render_template('admin/send_email.html', groups=groups,
                               mailto_link=mailto_link,
                               recipients_list=sorted(recipients),
                               recipients_count=len(recipients),
                               group_names=group_names)

    return render_template('admin/send_email.html', groups=groups)

# ===== EXPLORATEUR DE BASE DE DONNÉES =====

@bp.route('/database')
@login_required
@admin_required
def database_explorer():
    """Vue d'ensemble de toutes les tables de la base de données"""
    tables = {
        'users': {
            'name': 'Utilisateurs',
            'count': User.query.count(),
            'icon': '👤'
        },
        'groups': {
            'name': 'Groupes',
            'count': Group.query.count(),
            'icon': '👥'
        },
        'attendances': {
            'name': 'Présences',
            'count': Attendance.query.count(),
            'icon': '📋'
        },
        'payments': {
            'name': 'Paiements',
            'count': PaymentRecord.query.count(),
            'icon': '💰'
        },
        'sessions': {
            'name': 'Séances',
            'count': TrainingSession.query.count(),
            'icon': '🗓'
        },
        'reports': {
            'name': 'Rapports',
            'count': Report.query.count(),
            'icon': '📊'
        }
    }
    return render_template('admin/database.html', tables=tables)

@bp.route('/database/<table_name>')
@login_required
@admin_required
def database_table(table_name):
    """Affiche le contenu d'une table spécifique"""
    page = request.args.get('page', 1, type=int)
    per_page = 20

    if table_name == 'users':
        query = User.query.order_by(User.id.desc())
        columns = ['id', 'username', 'email', 'first_name', 'last_name', 'roles', 'status', 'group_id', 'is_active', 'created_at']
        title = 'Utilisateurs'
    elif table_name == 'groups':
        query = Group.query.order_by(Group.id.desc())
        columns = ['id', 'name', 'schedule', 'price_per_season', 'description']
        title = 'Groupes'
    elif table_name == 'attendances':
        query = Attendance.query.order_by(Attendance.id.desc())
        columns = ['id', 'user_id', 'session_date', 'status', 'notes', 'recorded_by_id']
        title = 'Présences'
    elif table_name == 'payments':
        query = PaymentRecord.query.order_by(PaymentRecord.id.desc())
        columns = ['id', 'user_id', 'amount', 'payment_type', 'payment_method', 'payment_date', 'status', 'notes']
        title = 'Paiements'
    elif table_name == 'sessions':
        query = TrainingSession.query.order_by(TrainingSession.id.desc())
        columns = ['id', 'group_id', 'date', 'duration_minutes', 'coach_notes']
        title = 'Séances d\'entraînement'
    elif table_name == 'reports':
        query = Report.query.order_by(Report.id.desc())
        columns = ['id', 'report_type', 'generated_by_id', 'generated_at']
        title = 'Rapports'
    else:
        flash('Table non trouvée', 'error')
        return redirect(url_for('admin.database_explorer'))

    pagination = query.paginate(page=page, per_page=per_page)

    # Convertir les objets en dictionnaires
    rows = []
    for item in pagination.items:
        row = {}
        for col in columns:
            value = getattr(item, col, None)
            if value is not None:
                if isinstance(value, datetime):
                    value = value.strftime('%Y-%m-%d %H:%M')
                elif hasattr(value, '__class__') and value.__class__.__name__ == 'date':
                    value = value.strftime('%Y-%m-%d')
            row[col] = value
        rows.append(row)

    return render_template('admin/database_table.html',
                          table_name=table_name,
                          title=title,
                          columns=columns,
                          rows=rows,
                          pagination=pagination)
