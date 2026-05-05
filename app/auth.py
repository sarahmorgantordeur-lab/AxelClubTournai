from flask import Blueprint, render_template, request, redirect, url_for, flash
from flask_login import login_user, logout_user, login_required, current_user
from datetime import datetime
from app import db
from app.models import User, SeasonPayment

bp = Blueprint('auth', __name__, url_prefix='/auth')

@bp.route('/register', methods=['GET', 'POST'])
def register():
    if current_user.is_authenticated:
        return redirect(url_for('main.dashboard'))

    if request.method == 'POST':
        username = request.form.get('username')
        email = request.form.get('email')
        password = request.form.get('password')
        confirm_password = request.form.get('confirm_password')
        first_name = request.form.get('first_name')
        last_name = request.form.get('last_name')
        roles = request.form.get('roles')

        if not all([username, email, password, confirm_password, first_name, last_name]):
            flash('Tous les champs sont requis', 'error')
            return redirect(url_for('auth.register'))

        if password != confirm_password:
            flash('Les mots de passe ne correspondent pas', 'error')
            return redirect(url_for('auth.register'))

        if User.query.filter_by(username=username).first():
            flash('Ce nom d\'utilisateur existe déjà', 'error')
            return redirect(url_for('auth.register'))

        if User.query.filter_by(email=email).first():
            flash('Cet email existe déjà', 'error')
            return redirect(url_for('auth.register'))

        # Valider le rôle (seuls patineur et parent autorisés à l'inscription)
        if roles not in ('patineur', 'parent'):
            roles = 'patineur'

        user = User(
            username=username,
            email=email,
            first_name=first_name,
            last_name=last_name,
            roles=[roles]  # Convertir en array
        )
        user.set_password(password)
        db.session.add(user)
        db.session.commit()

        flash('Inscription réussie! Veuillez vous connecter.', 'success')
        return redirect(url_for('auth.login'))

    return render_template('auth/register.html')


@bp.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('main.dashboard'))

    if request.method == 'POST':
        email = request.form.get('email')
        password = request.form.get('password')

        user = User.query.filter(User.email.ilike(email)).first()

        if user and user.check_password(password) and user.is_active:
            login_user(user)
            next_page = request.args.get('next')
            return redirect(next_page) if next_page else redirect(url_for('main.dashboard'))
        else:
            flash('Identifiants incorrects ou compte désactivé', 'error')

    return render_template('auth/login.html')


@bp.route('/logout')
@login_required
def logout():
    logout_user()
    flash('Vous avez été déconnecté', 'info')
    return redirect(url_for('public.home'))


@bp.route('/profile')
@login_required
def profile():
    current_year = datetime.now().year
    payment = None
    children_payments = []

    # Si l'utilisateur est patineur, récupérer ses paiements
    if current_user.has_role('patineur'):
        payment = SeasonPayment.query.filter_by(user_id=current_user.id, season_year=current_year).first()

    # Si l'utilisateur est parent, récupérer les paiements de ses enfants
    if current_user.has_role('parent'):
        for child in current_user.get_children_list():
            child_payment = SeasonPayment.query.filter_by(user_id=child.id, season_year=current_year).first()
            children_payments.append({'child': child, 'payment': child_payment})

    return render_template('auth/profile.html', user=current_user, payment=payment,
                          children_payments=children_payments, current_year=current_year)


@bp.route('/change-password', methods=['POST'])
@login_required
def change_password():
    current_password = request.form.get('current_password')
    new_password = request.form.get('new_password')
    confirm_new = request.form.get('confirm_new_password')

    if not current_user.check_password(current_password):
        flash('Mot de passe actuel incorrect', 'error')
        return redirect(url_for('auth.profile'))

    if new_password != confirm_new:
        flash('Les nouveaux mots de passe ne correspondent pas', 'error')
        return redirect(url_for('auth.profile'))

    if len(new_password) < 8:
        flash('Le nouveau mot de passe doit contenir au moins 8 caractères', 'error')
        return redirect(url_for('auth.profile'))

    current_user.set_password(new_password)
    db.session.commit()
    flash('Mot de passe modifié avec succès', 'success')
    return redirect(url_for('auth.profile'))


@bp.route('/profile/edit', methods=['POST'])
@login_required
def edit_profile():
    user = current_user

    user.first_name = request.form.get('first_name', user.first_name)
    user.last_name = request.form.get('last_name', user.last_name)
    user.phone = request.form.get('phone', user.phone)
    
    # Gérer les numéros d'urgence
    emergency_contacts = request.form.getlist('emergency_contacts')
    # Filtrer les numéros vides
    user.emergency_contacts = [c.strip() for c in emergency_contacts if c.strip()]
    
    # Permettre la modification de l'email
    new_email = request.form.get('email', user.email)
    if new_email != user.email:
        # Vérifier que le nouvel email n'existe pas déjà
        if User.query.filter_by(email=new_email).first():
            flash('Cet email est déjà utilisé', 'error')
            return redirect(url_for('auth.profile'))
        user.email = new_email

    date_str = request.form.get('date_of_birth')
    if date_str:
        user.date_of_birth = datetime.strptime(date_str, '%Y-%m-%d').date()

    db.session.commit()
    flash('Profil mis à jour avec succès', 'success')
    return redirect(url_for('auth.profile'))
