# Axel Club Tournai — Application de Gestion

Application web de gestion pour le club de patinage artistique **Axel Tournai Fédéré**, développée avec Flask et déployée sur Render.

**Site en production :** https://axelclubtournai.onrender.com

---

## Fonctionnalités

**Pages publiques**
- Accueil avec présentation du club
- Page Groupes avec tarifs et horaires
- Page À propos (histoire, mission, coach)
- SEO : meta descriptions, Open Graph, JSON-LD SportsClub, sitemap.xml, robots.txt

**Espace membre**
- Inscription et connexion
- Tableau de bord personnel (groupe, taux de présence, paiements)
- Profil modifiable (coordonnées, numéros d'urgence, date de naissance)
- Consultation des paiements de la saison

**Panneau d'administration (admin uniquement)**
- Gestion des membres (création, modification, suppression, statuts)
- Gestion des groupes (niveau, horaires, tarifs, capacité)
- Suivi des présences par séance
- Gestion financière par saison (licence, saison Tournai, Wasquehal P1/P2, compétitions)
- Rapports : présences, finances, membres par groupe

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Backend | Python 3.11 / Flask 3.0 |
| ORM | SQLAlchemy 2.0 + Flask-SQLAlchemy |
| Auth | Flask-Login |
| Base de données | PostgreSQL (prod) / SQLite (dev) |
| Serveur WSGI | Gunicorn |
| Déploiement | Docker + Render |

---

## Structure du projet

```
patinage_club/
├── app/
│   ├── __init__.py       # Application factory, enregistrement des blueprints
│   ├── models.py         # Modèles SQLAlchemy (User, Group, Attendance, SeasonPayment)
│   ├── services.py       # Logique métier
│   ├── routes.py         # Blueprint `main` (dashboard, présences, API)
│   ├── auth.py           # Blueprint `auth` (inscription, connexion, profil)
│   ├── admin.py          # Blueprint `admin` (gestion complète)
│   ├── public.py         # Blueprint `public` (pages publiques, sitemap, robots)
│   └── schema.sql        # Schéma SQL de référence
├── templates/
│   ├── auth/             # Connexion, inscription, profil
│   ├── admin/            # Dashboard et pages d'administration
│   ├── main/             # Dashboard membre, présences
│   └── public/           # Accueil, groupes, à propos, sitemap, robots
├── static/
│   ├── css/              # Feuilles de style (base, home, admin, auth…)
│   ├── js/               # nav.js (menu burger)
│   └── images/           # logo.png (fond transparent)
├── scripts/              # Utilitaires CLI (init_db, add_group, check_groups)
├── config.py             # Configurations (Dev, Prod, Test)
├── run.py                # Point d'entrée
├── Dockerfile
└── requirements.txt
```

---

## Installation locale

### Prérequis

- Python 3.11+
- pip

### Démarrage

```bash
# 1. Créer et activer l'environnement virtuel
python3 -m venv .venv
source .venv/bin/activate      # macOS/Linux
.venv\Scripts\activate         # Windows

# 2. Installer les dépendances
pip install -r requirements.txt

# 3. Configurer les variables d'environnement
cp .env.example .env           # puis éditer .env

# 4. Lancer le serveur
python run.py
```

L'application est accessible sur `http://127.0.0.1:5000`.

### Variables d'environnement

| Variable | Description | Exemple |
|----------|-------------|---------|
| `SECRET_KEY` | Clé secrète Flask (obligatoire en prod) | `une-clé-aléatoire-longue` |
| `DATABASE_URL` | URL de connexion PostgreSQL (optionnel en dev) | `postgresql://user:pass@host/db` |

En développement, sans `DATABASE_URL`, l'application utilise SQLite (`instance/patinage_club.db`).

---

## Déploiement sur Render

Le projet se déploie via Docker sur Render.

1. Créer un **Web Service** sur Render en pointant sur ce dépôt (Runtime : Docker)
2. Créer une **base de données PostgreSQL** sur Render
3. Ajouter les variables d'environnement dans Render :
   - `SECRET_KEY` — générer une valeur aléatoire sécurisée
   - `DATABASE_URL` — copier l'**Internal Database URL** fournie par Render PostgreSQL
4. Déployer — les tables sont créées automatiquement au démarrage (`db.create_all()`)

Pour créer le premier compte administrateur via le shell Render :

```python
from app import create_app, db
from app.models import User
app = create_app()
with app.app_context():
    u = User.query.filter_by(username='votre_username').first()
    u.roles = ['admin']
    db.session.commit()
```

---

## Rôles utilisateurs

| Rôle | Accès |
|------|-------|
| `patineur` | Tableau de bord, profil, présences, paiements personnels |
| `parent` | Tableau de bord, profil, paiements de ses enfants |
| `admin` | Tout ce qui précède + panneau d'administration complet |

L'assignation à un groupe est réservée aux administrateurs.

---

## Routes principales

| Route | Description |
|-------|-------------|
| `/` | Accueil public |
| `/groups` | Groupes disponibles |
| `/about` | À propos du club |
| `/robots.txt` | Fichier robots |
| `/sitemap.xml` | Sitemap XML |
| `/auth/login` | Connexion |
| `/auth/register` | Inscription |
| `/auth/profile` | Mon profil |
| `/dashboard` | Tableau de bord membre |
| `/my-attendance` | Mes présences |
| `/admin/` | Dashboard administrateur |
| `/admin/members` | Gestion des membres |
| `/admin/groups` | Gestion des groupes |
| `/admin/attendance` | Suivi des présences |
| `/admin/payments` | Gestion financière |
| `/admin/reports` | Rapports |

---

## Auteur

Développé par **Sarah Tordeur** pour l'Axel Club Tournai.
