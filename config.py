import os

class Config:
    """Configuration de base"""
    _db_url = os.environ.get('DATABASE_URL') or 'sqlite:///patinage_club.db'
    # Render fournit des URLs "postgres://" que SQLAlchemy 2.x n'accepte pas
    SQLALCHEMY_DATABASE_URI = _db_url.replace('postgres://', 'postgresql://', 1)
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    SECRET_KEY = os.environ.get('SECRET_KEY') or 'dev-secret-key-change-in-production'

    # Configuration Flask
    DEBUG = False
    TESTING = False



class DevelopmentConfig(Config):
    """Configuration développement"""
    DEBUG = True
    TESTING = False


class TestingConfig(Config):
    """Configuration tests"""
    TESTING = True
    SQLALCHEMY_DATABASE_URI = 'sqlite:///:memory:'
    WTF_CSRF_ENABLED = False


class ProductionConfig(Config):
    """Configuration production"""
    DEBUG = False
    TESTING = False
