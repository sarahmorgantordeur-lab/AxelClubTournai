from flask import Blueprint, render_template, request, make_response, flash, redirect, url_for
from flask_mail import Message
from app import mail

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


@bp.route('/contact', methods=['GET', 'POST'])
def contact():
    if request.method == 'POST':
        # Honeypot anti-spam
        if request.form.get('website'):
            return redirect(url_for('public.contact'))

        name = request.form.get('name', '').strip()
        email = request.form.get('email', '').strip()
        phone = request.form.get('phone', '').strip()
        subject = request.form.get('subject', '').strip()
        message = request.form.get('message', '').strip()

        if not all([name, email, subject, message]):
            flash('Veuillez remplir tous les champs obligatoires.', 'error')
            return redirect(url_for('public.contact'))

        try:
            msg = Message(
                subject=f'[Axel Club] {subject}',
                recipients=['axelclubtournai@federe.com'],
                reply_to=email,
                body=f'Nom : {name}\nEmail : {email}\nTéléphone : {phone or "Non renseigné"}\n\nMessage :\n{message}'
            )
            mail.send(msg)
            flash('Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.', 'success')
        except Exception:
            flash('Une erreur est survenue lors de l\'envoi. Contactez-nous directement par email.', 'error')

        return redirect(url_for('public.contact'))

    return render_template('public/contact.html')


@bp.route('/mentions-legales')
def legal():
    return render_template('public/legal.html')


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
