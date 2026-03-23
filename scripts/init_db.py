import os
from app import create_app, db

app = create_app()

db_path = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'instance', 'patinage_club.db')

with app.app_context():
    # Vérifier si la base de données existe déjà
    if os.path.exists(db_path):
        print("La base de données existe déjà à:", db_path)
        print("Si vous voulez réinitialiser la base de données, supprimez d'abord le fichier:")
        print(f"   rm {db_path}")
        print("Puis relancez ce script.")
    else:
        # Créer les tables seulement si la DB n'existe pas
        db.create_all()
        print("Base de données initialisée avec succès à:", db_path)
