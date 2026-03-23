# Axel Club - Système de Gestion de Club de Patinage

Un système ERP complet pour gérer un club de patinage artistique avec Flask et SQLAlchemy.

## 🎯 Fonctionnalités

### Authentification & Rôles
- ✅ Inscription et connexion des utilisateurs
- ✅ Gestion multi-rôles: Patineur, Coach, Comité, Parents, Admin
- ✅ Profils utilisateurs personnalisables

### Gestion des Membres
- ✅ Création/Modification/Suppression de membres
- ✅ Suivi des profils (nom, email, téléphone, groupe, statut)
- ✅ Statuts: Actif, Inactif, Suspendu
- ✅ Informations d'urgence et numéro de licence

### Gestion des Groupes
- ✅ Création de groupes par niveau (Débutant, Intermédiaire, Avancé, Compétition)
- ✅ Gestion des coachs
- ✅ Définition des horaires et tarifs
- ✅ Suivi de la capacité des groupes

### Suivi des Entraînements
- ✅ Enregistrement des séances d'entraînement
- ✅ Gestion des présences (Présent, Absent, Retard, Justifié)
- ✅ Notes des coachs sur les séances
- ✅ Taux de présence automatique

### Gestion Financière
- ✅ Enregistrement des paiements
- ✅ Suivi du statut des paiements (Payé, En attente, En retard)
- ✅ Différents types de paiement (Mensuel, Annuel, Leçons, etc.)
- ✅ Rapports financiers

### Rapports & Analytics
- ✅ Rapport de présences (taux d'assiduité par membre)
- ✅ Rapport financier (revenu, montants en attente)
- ✅ Rapport de membres par groupe
- ✅ Statistiques générales du club

### Interfaces
- ✅ Pages publiques (Accueil, Groupes, À propos)
- ✅ Tableau de bord personnel pour chaque membre
- ✅ Panneau d'administration complet pour les admins
- ✅ Responsive design pour mobile/tablet

## 📁 Structure du Projet

```
patinage_club/
├── app/
│   ├── __init__.py           # Initialisation Flask & Blueprint
│   ├── models.py             # Modèles SQLAlchemy (User, Member, Group, etc.)
│   ├── services.py           # Logique métier
│   ├── routes.py             # Routes principales
│   ├── auth.py               # Routes authentification
│   ├── admin.py              # Routes administration
│   └── public.py             # Routes publiques
├── templates/
│   ├── auth/                 # Templates authentification
│   ├── admin/                # Templates administration
│   ├── public/               # Templates publics
│   └── *.html                # Templates utilisateur
├── static/
│   ├── css/style.css         # Styles globaux
│   └── js/admin.js           # Scripts JavaScript
├── config.py                 # Configuration (dev, prod, test)
├── run.py                    # Point d'entrée de l'application
├── requirements.txt          # Dépendances Python
├── .env                      # Variables d'environnement
└── README.md                 # Ce fichier
```

## 🚀 Installation & Démarrage

### Prérequis
- Python 3.8+
- pip (gestionnaire de paquets Python)

### Étapes d'installation

1. **Installer les dépendances**
```bash
cd patinage_club
pip install -r requirements.txt
```

2. **Configurer les variables d'environnement**
```bash
# Éditer .env avec vos paramètres
nano .env
```

3. **Lancer le serveur**
```bash
python run.py
```

Le serveur démarre sur `http://127.0.0.1:5000/`

## 📊 Modèles de Données

### User
```python
- id (PK)
- username (unique)
- email (unique)
- password_hash
- role: admin, coach, parent, patineur
- is_active
- created_at
```

### Member
```python
- id (PK)
- user_id (FK)
- first_name, last_name
- date_of_birth
- phone
- emergency_contact
- license_number (unique)
- group_id (FK)
- status: active, inactive, suspended
- joined_date
```

### Group
```python
- id (PK)
- name (unique)
- level: Débutant, Intermédiaire, Avancé, Compétition
- coach_id (FK)
- max_members
- schedule
- price_per_month
- description
```

### Attendance
```python
- id (PK)
- member_id (FK)
- session_id (FK)
- session_date
- status: present, absent, late, excused
- notes
- recorded_by_id (FK)
```

### PaymentRecord
```python
- id (PK)
- member_id (FK)
- amount
- payment_type: monthly, annual, lessons
- payment_method: cash, card, transfer, check
- payment_date
- status: paid, pending, overdue, cancelled
```

## 🔑 Comptes de Démonstration

Après la première exécution, créez des comptes via l'interface d'inscription.

## 📱 Pages Disponibles

### Publiques
- `/` - Accueil
- `/about` - À propos
- `/groups` - Voir les groupes

### Authentification
- `/auth/login` - Connexion
- `/auth/register` - Inscription
- `/auth/profile` - Mon profil
- `/auth/logout` - Déconnexion

### Tableau de Bord Personnel
- `/dashboard` - Tableau de bord
- `/my-attendance` - Mes présences

### Administration (Admin uniquement)
- `/admin/` - Dashboard admin
- `/admin/members` - Gestion des membres
- `/admin/members/create` - Ajouter un membre
- `/admin/members/<id>/edit` - Modifier un membre
- `/admin/groups` - Gestion des groupes
- `/admin/groups/create` - Créer un groupe
- `/admin/attendance` - Gestion des présences
- `/admin/payments` - Gestion des paiements
- `/admin/reports` - Rapports et statistics

## 🔐 Sécurité

- ✅ Hashage des mots de passe avec Werkzeug
- ✅ Sessions Flask-Login pour gérer les utilisateurs connectés
- ✅ Décorateurs pour contrôler l'accès par rôle
- ✅ CSRF protection avec Flask-WTF

## 🎨 Design

- Responsive design (mobile, tablet, desktop)
- Palette de couleurs professionnelle
- Interface intuitive et facile à utiliser
- Dark/Light themes compatibles

## 📈 Déploiement

### En Local
```bash
python3 run.py
```

### En Production (avec Gunicorn)
```bash
pip install gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 run:app
```

### Avec Docker
```dockerfile
FROM python:3.9
WORKDIR /app
COPY . .
RUN pip install -r requirements.txt
CMD ["gunicorn", "-w", "4", "-b", "0.0.0.0:5000", "run:app"]
```

## 🔧 Configuration

Fichier `config.py`:
```python
class DevelopmentConfig:
    SQLALCHEMY_DATABASE_URI = 'sqlite:///patinage_club.db'
    DEBUG = True
    
class ProductionConfig:
    SQLALCHEMY_DATABASE_URI = 'mysql://user:pass@localhost/patinage'
    DEBUG = False
```

## 📚 Dépendances

- Flask 3.0.0
- Flask-SQLAlchemy 3.1.1
- Flask-Login 0.6.3
- Flask-WTF 1.2.1
- Werkzeug 3.0.1
- python-dotenv 1.0.0

## 🤝 Contribution

N'hésitez pas à améliorer ce projet!

## 📝 Licence

MIT License

## 👥 Auteur

Créé pour le Axel Club de Patinage Artistique
# AxelClubTournai
