import os

class Config:
    """Configuration de base"""
    SQLALCHEMY_DATABASE_URI = 'sqlite:///patinage_club.db'
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
