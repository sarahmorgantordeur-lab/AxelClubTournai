from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager
from flask_mail import Mail
from config import Config
import os

db = SQLAlchemy()
login_manager = LoginManager()
mail = Mail()

def create_app(config_class=Config):
    # Obtenir le chemin du répertoire racine du projet
    base_dir = os.path.abspath(os.path.dirname(os.path.dirname(__file__)))
    template_dir = os.path.join(base_dir, 'templates')
    static_dir = os.path.join(base_dir, 'static')
    app = Flask(__name__, template_folder=template_dir, static_folder=static_dir)
    app.config.from_object(config_class)
    
    db.init_app(app)
    login_manager.init_app(app)
    mail.init_app(app)
    login_manager.login_view = 'auth.login'
    
    from app import models  # noqa: F401 — doit être importé avant create_all()

    with app.app_context():
        db.create_all()
        from app.models import Site
        from sqlalchemy import text
        # Ajoute site_id sur attendances si elle n'existe pas (migration sans Flask-Migrate)
        try:
            db.session.execute(text(
                'ALTER TABLE attendances ADD COLUMN IF NOT EXISTS site_id INTEGER REFERENCES sites(id)'
            ))
            db.session.commit()
        except Exception:
            db.session.rollback()
        if not Site.query.first():
            db.session.add_all([Site(name='Tournai'), Site(name='Wasquehal')])
            db.session.commit()

    from app.models import User
    
    @login_manager.user_loader
    def load_user(user_id):
        return User.query.get(int(user_id))
    
    # Enregistrer les blueprints
    from app.routes import bp as main_bp
    from app.auth import bp as auth_bp
    from app.admin import bp as admin_bp
    from app.public import bp as public_bp
    
    app.register_blueprint(auth_bp)
    app.register_blueprint(main_bp)
    app.register_blueprint(admin_bp)
    app.register_blueprint(public_bp)
    
    return app
