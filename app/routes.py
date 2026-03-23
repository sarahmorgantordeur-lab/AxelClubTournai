from flask import Blueprint, render_template, request, jsonify
from flask_login import login_required, current_user
from app.models import User, Group, Attendance, SeasonPayment
from datetime import datetime

bp = Blueprint('main', __name__)

@bp.route('/dashboard')
@login_required
def dashboard():
    """Tableau de bord personnel de l'utilisateur"""
    user = current_user
    current_year = datetime.now().year
    payment = SeasonPayment.query.filter_by(user_id=user.id, season_year=current_year).first()
    stats = {
        'group': user.group.name if user.group else 'Aucun groupe',
        'attendance_rate': user.attendance_rate(),
        'total_sessions': len(user.attendances),
        'next_training': 'À venir'
    }
    return render_template('main/dashboard.html', stats=stats, user=user, payment=payment)

@bp.route('/my-attendance')
@login_required
def my_attendance():
    """Voir ses propres présences"""
    attendances = Attendance.query.filter_by(user_id=current_user.id).all()
    return render_template('main/my_attendance.html', attendances=attendances)

@bp.route('/api/users', methods=['GET'])
@login_required
def get_users():
    """API pour récupérer la liste des patineurs"""
    users = User.query_by_role('patineur').all()
    return jsonify([{
        'id': u.id,
        'first_name': u.first_name,
        'last_name': u.last_name,
        'group': u.group.name if u.group else None
    } for u in users])

@bp.route('/api/groups', methods=['GET'])
@login_required
def get_groups():
    """API pour récupérer la liste des groupes"""
    groups = Group.query.all()
    return jsonify([{
        'id': g.id,
        'name': g.name,
        'schedule': g.schedule,
        'members_count': len(g.members)
    } for g in groups])

@bp.route('/api/groups/<int:group_id>/members', methods=['GET'])
@login_required
def get_group_members(group_id):
    """API pour récupérer les membres d'un groupe"""
    group = Group.query.get_or_404(group_id)
    return jsonify([{
        'id': u.id,
        'first_name': u.first_name,
        'last_name': u.last_name
    } for u in group.members])
