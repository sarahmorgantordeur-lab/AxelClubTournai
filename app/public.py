from flask import Blueprint, render_template
from flask_login import login_required, current_user

bp = Blueprint('public', __name__)

@bp.route('/')
def home():
    """Page d'accueil publique"""
    return render_template('public/home.html')

@bp.route('/about')
def about():
    """À propos du club"""
    return render_template('public/about.html')

@bp.route('/groups')
def groups():
    """Affiche les groupes disponibles (publique)"""
    from app.models import Group
    groups = Group.query.all()
    return render_template('public/groups.html', groups=groups)
