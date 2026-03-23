#!/usr/bin/env python3
"""
Utilitaire de gestion de la base de données.
Usage:
    python3 db_manage.py init        # Initialiser une nouvelle DB
    python3 db_manage.py reset       # Supprimer et réinitialiser la DB (ATTENTION: perte de données)
    python3 db_manage.py status      # Voir le statut de la DB
    python3 db_manage.py backup      # Sauvegarder la DB actuelle
"""

import os
import sys
import shutil
from datetime import datetime
from app import create_app, db

DB_PATH = os.path.join(os.path.dirname(__file__), 'instance', 'patinage_club.db')
BACKUP_DIR = os.path.join(os.path.dirname(__file__), 'backups')

def ensure_backup_dir():
    """Créer le répertoire de sauvegarde s'il n'existe pas"""
    if not os.path.exists(BACKUP_DIR):
        os.makedirs(BACKUP_DIR)
        print(f"📁 Répertoire de sauvegarde créé: {BACKUP_DIR}")

def init():
    """Initialiser une nouvelle base de données"""
    if os.path.exists(DB_PATH):
        print(f"❌ Erreur: La base de données existe déjà à {DB_PATH}")
        print("   Utilisez 'reset' si vous voulez réinitialiser la DB (ATTENTION: perte de données)")
        return False
    
    app = create_app()
    with app.app_context():
        db.create_all()
    print(f"✅ Base de données initialisée avec succès à: {DB_PATH}")
    return True

def reset():
    """Supprimer et réinitialiser la base de données"""
    if not os.path.exists(DB_PATH):
        print(f"⚠️  La base de données n'existe pas à {DB_PATH}")
        print("   Utilisation de 'init' pour créer une nouvelle DB...")
        return init()
    
    # Demander confirmation
    response = input(f"⚠️  ATTENTION: Cela supprimera toutes les données de {DB_PATH}\n"
                    "Êtes-vous sûr? (tapez 'oui' pour confirmer): ")
    if response.lower() != 'oui':
        print("❌ Opération annulée")
        return False
    
    # Faire une sauvegarde avant de supprimer
    try:
        backup()
        print("✅ Sauvegarde créée avant suppression")
    except Exception as e:
        print(f"⚠️  Erreur lors de la sauvegarde: {e}")
    
    # Supprimer la DB
    os.remove(DB_PATH)
    print(f"🗑  Base de données supprimée: {DB_PATH}")
    
    # Réinitialiser
    app = create_app()
    with app.app_context():
        db.create_all()
    print(f"✅ Nouvelle base de données créée: {DB_PATH}")
    return True

def status():
    """Afficher le statut de la base de données"""
    if os.path.exists(DB_PATH):
        size = os.path.getsize(DB_PATH)
        modified = os.path.getmtime(DB_PATH)
        modified_date = datetime.fromtimestamp(modified).strftime('%Y-%m-%d %H:%M:%S')
        print(f"✅ Base de données trouvée")
        print(f"   Chemin: {DB_PATH}")
        print(f"   Taille: {size} bytes ({size/1024:.2f} KB)")
        print(f"   Dernière modification: {modified_date}")
        return True
    else:
        print(f"❌ Base de données non trouvée à {DB_PATH}")
        return False

def backup():
    """Sauvegarder la base de données actuelle"""
    if not os.path.exists(DB_PATH):
        print(f"❌ Erreur: La base de données n'existe pas à {DB_PATH}")
        return False
    
    ensure_backup_dir()
    
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    backup_path = os.path.join(BACKUP_DIR, f'patinage_club_backup_{timestamp}.db')
    
    try:
        shutil.copy2(DB_PATH, backup_path)
        print(f"✅ Sauvegarde créée: {backup_path}")
        return True
    except Exception as e:
        print(f"❌ Erreur lors de la sauvegarde: {e}")
        return False

def main():
    if len(sys.argv) < 2:
        print(__doc__)
        sys.exit(1)
    
    command = sys.argv[1].lower()
    
    if command == 'init':
        init()
    elif command == 'reset':
        reset()
    elif command == 'status':
        status()
    elif command == 'backup':
        backup()
    else:
        print(f"❌ Commande inconnue: {command}")
        print(__doc__)
        sys.exit(1)

if __name__ == '__main__':
    main()
