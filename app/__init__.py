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
        try:
            db.create_all()
        except Exception as e:
            app.logger.error(f'db.create_all() failed: {e}')

        from app.models import Site
        from sqlalchemy import text

        for ddl in [
            'ALTER TABLE attendances ADD COLUMN site_id INTEGER REFERENCES sites(id)',
            'ALTER TABLE users ADD COLUMN registration_year INTEGER',
            'ALTER TABLE users ADD COLUMN emergency_contacts JSON',
            'ALTER TABLE users ADD COLUMN license_number VARCHAR(50)',
            'ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT \'active\'',
            'ALTER TABLE users ADD COLUMN phone VARCHAR(20)',
            'ALTER TABLE users ADD COLUMN date_of_birth DATE',
            'CREATE TABLE IF NOT EXISTS user_sites '
            '(user_id INTEGER REFERENCES users(id), '
            'site_id INTEGER REFERENCES sites(id), '
            'PRIMARY KEY (user_id, site_id))',
        ]:
            try:
                with db.engine.connect() as conn:
                    conn.execute(text(ddl))
                    conn.commit()
            except Exception:
                pass

        try:
            for site_name in ['Tournai', 'Wasquehal']:
                if not Site.query.filter_by(name=site_name).first():
                    db.session.add(Site(name=site_name))
            db.session.commit()
        except Exception as e:
            app.logger.error(f'Site seeding failed: {e}')
            db.session.rollback()

    from app.models import User
    
    @login_manager.user_loader
    def load_user(user_id):
        return db.session.get(User, int(user_id))
    
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
