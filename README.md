# Axel Club Tournai — Application de Gestion

Application web de gestion pour le club de patinage artistique **Axel Tournai Fédéré**, développée en PHP et déployée sur OVH mutualisé.

**Site en production :** https://axelclub.be

---

## Fonctionnalités

**Pages publiques**
- Accueil avec présentation du club
- Page Groupes avec tarifs et horaires
- Page À propos (histoire, mission, coach)
- Formulaire de contact avec protection honeypot
- SEO : sitemap.xml, robots.txt, mentions légales

**Espace membre**
- Inscription et connexion avec toggle mot de passe
- Tableau de bord personnel (groupe, taux de présence, paiements)
- Profil modifiable (coordonnées, numéros d'urgence, date de naissance)
- Changement de mot de passe
- Historique des présences
- Dashboard parent avec suivi des paiements des enfants

**Panneau d'administration**
- Gestion des membres (création, modification, suppression, rôles, statuts)
- Gestion des groupes (horaires, tarifs, description)
- Inscription/désinscription aux groupes
- Enregistrement des présences par séance
- Gestion financière par saison (licence, saison Tournai, Wasquehal P1/P2, compétitions)
- Historique des paiements
- Rapports : présences, finances, répartition par groupe
- Envoi d'emails groupés (tous, patineurs, parents, groupe spécifique)
- Explorateur de base de données SQLite

---

## Stack technique

| Composant | Technologie |
|-----------|-------------|
| Backend | PHP 8.2 |
| Base de données | SQLite (PDO) |
| Auth | Sessions PHP natives |
| Hébergement | OVH mutualisé Perso |
| Déploiement | FTP (lftp) |

---

## Structure du projet

```
patinage_club/
├── index.php               # Front controller — routage de toutes les requêtes
├── config.php              # Constantes (DB_PATH, SECRET_KEY, SITE_EMAIL…)
├── install.php             # Initialisation BDD + compte admin (à supprimer après usage)
├── .htaccess               # Réécriture URL, protection des fichiers sensibles
├── includes/
│   ├── db.php              # Connexion PDO SQLite (singleton)
│   ├── models.php          # Toutes les fonctions d'accès aux données
│   ├── auth.php            # auth_user(), login_required(), admin_required()
│   ├── utils.php           # flash(), redirect(), e(), csrf_token(), paginate()
│   ├── header.php          # <head>, navbar, messages flash
│   └── footer.php          # Footer HTML + chargement nav.js
├── pages/
│   ├── home.php / about.php / contact.php / legal.php / groups_public.php
│   ├── robots.php / sitemap.php
│   ├── auth/               # login, register, logout, profile, change_password
│   ├── main/               # dashboard, attendance (espace membre)
│   └── admin/              # dashboard, users, groups, attendance, payments,
│                           # registrations, reports, send_email, database
└── static/
    ├── css/                # base.css, style.css, home.css, admin.css, auth.css…
    ├── js/                 # nav.js (menu burger + toggle mot de passe)
    └── images/             # logo.png, favicon.ico
```

---

## Déploiement sur OVH

### Prérequis
- Hébergement OVH mutualisé avec PHP 8.0+
- Accès FTP
- Extension PDO SQLite activée (activée par défaut sur OVH)

### Étapes

```bash
# 1. Uploader les fichiers via FTP
lftp -u USERNAME,PASSWORD ftp.cluster100.hosting.ovh.net <<'EOF'
set ftp:passive-mode true
put index.php -o /www/index.php
put config.php -o /www/config.php
put .htaccess -o /www/.htaccess
put install.php -o /www/install.php
mirror --reverse includes /www/includes
mirror --reverse pages /www/pages
mirror --reverse static /www/static
quit
EOF

# 2. Initialiser la base de données
# Ouvrir https://axelclub.be/install.php dans le navigateur

# 3. Supprimer install.php du serveur
lftp -u USERNAME,PASSWORD ftp.cluster100.hosting.ovh.net \
  -e "set ftp:passive-mode true; rm /www/install.php; quit"
```

### Variables de configuration

Modifier `config.php` avant le déploiement :

| Constante | Description |
|-----------|-------------|
| `DB_PATH` | Chemin vers la base SQLite (par défaut : un niveau au-dessus de `www/`) |
| `SECRET_KEY` | Clé secrète pour les tokens CSRF |
| `SITE_EMAIL` | Email expéditeur pour les envois groupés |
| `SITE_NAME` | Nom du club affiché dans les emails |

---

## Rôles utilisateurs

| Rôle | Accès |
|------|-------|
| `patineur` | Tableau de bord, profil, présences, paiements personnels |
| `parent` | Tableau de bord, profil, paiements de ses enfants |
| `admin` | Tout ce qui précède + panneau d'administration complet |

---

## Routes principales

| Route | Description |
|-------|-------------|
| `/` | Accueil public |
| `/groupes` | Groupes disponibles |
| `/a-propos` | À propos du club |
| `/contact` | Formulaire de contact |
| `/auth/login` | Connexion |
| `/auth/register` | Inscription |
| `/auth/profile` | Mon profil |
| `/dashboard` | Tableau de bord membre |
| `/my-attendance` | Mes présences |
| `/admin` | Dashboard administrateur |
| `/admin/users` | Gestion des membres |
| `/admin/groups` | Gestion des groupes |
| `/admin/registrations` | Inscriptions aux groupes |
| `/admin/attendance` | Suivi des présences |
| `/admin/payments` | Gestion financière |
| `/admin/reports` | Rapports |
| `/admin/send-email` | Envoi d'emails groupés |
| `/admin/database` | Explorateur SQLite |

---

## Auteur

Développé par **Sarah Tordeur** pour l'Axel Club Tournai.
