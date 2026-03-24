from flask import Blueprint, render_template, request, make_response

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


@bp.route('/robots.txt')
def robots():
    sitemap_url = request.host_url.rstrip('/') + '/sitemap.xml'
    response = make_response(render_template('public/robots.txt', sitemap_url=sitemap_url))
    response.headers['Content-Type'] = 'text/plain'
    return response


@bp.route('/sitemap.xml')
def sitemap():
    base_url = request.host_url
    response = make_response(render_template('public/sitemap.xml', base_url=base_url))
    response.headers['Content-Type'] = 'application/xml'
    return response
