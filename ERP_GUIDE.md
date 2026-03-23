# GUIDE ERP - Axel Club de Patinage

## 🎯 Vue d'Ensemble du Système ERP

Le système Axel Club est un **Enterprise Resource Planning (ERP)** complet pour la gestion d'un club de patinage artistique. Il intègre :

- Gestion des ressources humaines (membres, coachs)
- Gestion des programmes de formation (groupes, séances)
- Suivi des présences et assiduité
- Gestion financière et paiements
- Reporting et analytics
- Multi-niveaux d'accès par rôle

## 👥 Gestion des Rôles & Permissions

### Admin
- Accès complet au système
- Gestion des utilisateurs et membres
- Gestion des groupes et coachs
- Enregistrement des présences
- Gestion des paiements
- Génération de rapports

### Coach
- Gestion des séances de son groupe
- Enregistrement des présences
- Voir les statistiques de son groupe
- Notes sur les séances

### Patineur
- Voir son profil
- Consulter ses présences et taux d'assiduité
- Voir son groupe et les horaires
- Informations personnelles

### Parent
- Voir les profils de ses enfants
- Suivre l'assiduité
- Informations de facturation

### Comité
- Gestion des groupes
- Gestion financière
- Rapports et statistiques

## 📋 Processus Métier

### 1️⃣ Inscription d'un Nouveau Membre

```
1. Inscription → 2. Créer Profil → 3. Affecter à Groupe → 4. Première Séance
```

**Étapes:**
- Nouvel utilisateur crée un compte (auto-enregistrement)
- Admin crée le profil Member associé
- Admin affecte le membre à un groupe
- Coach enregistre la première séance

**Formulaire Membre:**
- Nom/Prénom
- Email
- Téléphone
- Date de naissance
- Groupe
- Numéro de licence
- Contact d'urgence

### 2️⃣ Gestion des Groupes & Horaires

**Créer un Groupe:**
```
Accueil → Admin → Groupes → + Créer
```

**Champs:**
- Nom: "Débutants Lundi"
- Niveau: Débutant/Intermédiaire/Avancé/Compétition
- Coach: Sélectionner un coach
- Horaires: "Lundi 18h-19h, Mercredi 19h-20h"
- Tarif: 50€/mois
- Capacité: 15 membres max
- Description: Details optionnels

### 3️⃣ Suivi des Présences

**Processus Quotidien:**
```
1. Coach sélectionne son groupe
2. Coach sélectionne la date/heure de la séance
3. Coach enregistre les présences pour chaque membre
4. Statut: Présent, Absent, Retard, Justifié
5. Notes optionnelles par membre
```

**Dashboard de Présences:**
- Vue globale des séances
- Historique des présences par membre
- Taux de présence automatique = (Présences / Total Séances) × 100%

### 4️⃣ Gestion Financière

**Paiements Mensuels:**
```
Type: Monthly
Montant: 50€ (prix du groupe × durée)
Date: 1er du mois
Statut: Paid/Pending/Overdue
```

**Types de Paiements:**
- **Monthly**: Cotisation mensuelle
- **Annual**: Cotisation annuelle
- **Lessons**: Leçons particulières
- **Competition**: Frais de compétition

**Méthodes:**
- Cash (liquide)
- Card (carte bancaire)
- Transfer (virement)
- Check (chèque)

**Statuts:**
- **Paid**: Paiement reçu ✅
- **Pending**: En attente de paiement ⏳
- **Overdue**: Retard de paiement ⚠️
- **Cancelled**: Annulé ❌

### 5️⃣ Rapports & Analytics

#### Rapport de Présences
- Taux d'assiduité par membre
- Total de séances par membre
- Tendances sur période donnée
- Détail des absences justifiées/non justifiées

#### Rapport Financier
- Revenus totaux
- Montants en attente
- Paiements en retard
- Revenus par groupe
- Analyse par type de paiement

#### Rapport de Membres
- Membres par groupe
- Remplissage des groupes (% capacité)
- Répartition par niveau
- Taux de rétention

## 🎯 Cas d'Usage Détaillés

### Cas 1: Accueillir un Nouveau Patineur

**Scénario:**
Marie s'inscrit sur le site, elle souhaite rejoindre le groupe "Débutants Lundi"

**Processus:**

1. **Marie s'inscrit:**
   - Va sur `/auth/register`
   - Remplie: Prénom, Nom, Email, Username, Mot de passe
   - Valide → Un User est créé avec rôle "patineur"

2. **Admin crée son profil:**
   - Admin va sur `/admin/members/create`
   - Remplie les infos complémentaires
   - Sélectionne le groupe "Débutants Lundi"
   - Valide → Un Member est créé et lié au User

3. **Première séance:**
   - Coach enregistre Marie comme "present"
   - Données: session_date = Lundi 18h, status = "present"

4. **Suivi:**
   - Marie peut voir son taux de présence sur son dashboard
   - Admin peut voir les stats dans les rapports

### Cas 2: Gestion des Paiements

**Scénario:**
Marc est dans le groupe "Débutants" à 50€/mois. Son paiement de janvier est en attente.

**Processus:**

1. **Créer un paiement:**
   - Admin va sur `/admin/payments/add`
   - Sélectionne Marc
   - Montant: 50€
   - Type: monthly
   - Méthode: transfer
   - Date: 2026-01-05
   - Statut: pending (par défaut)

2. **Marquer comme payé:**
   - Si Marc paie en janvier: statut → "paid"
   - Si pas de paiement en février: statut → "overdue"

3. **Rapport financier:**
   - Revenu jan 2026: somme des paiements "paid"
   - En attente: somme des paiements "pending"
   - En retard: somme des paiements "overdue"

### Cas 3: Générateur de Rapports

**Scénario:**
Besoin d'un rapport d'assiduité pour janvier 2026

**Processus:**

1. Admin va sur `/admin/reports`
2. Sélectionne l'onglet "Présences"
3. Le système calcule:
   - Pour chaque membre: (présences présentes / total séances) × 100
   - Liste triée par taux décroissant
4. Export possible en CSV/PDF

## 🔄 Workflows Automatisés

### Workflow 1: Calcul Taux Assiduité
```python
taux_assiduité = (nombre_présences / nombre_séances_totales) × 100
```
- Recalculé automatiquement à chaque enregistrement
- Visible sur le dashboard personnel

### Workflow 2: Détection Paiements Expirés
```
Si date_paiement + 30 jours < date_actuelle:
    statut = "overdue"
```

### Workflow 3: Notification Groupe Plein
```
Si nombre_membres >= max_membres:
    afficher: "Groupe complet - Liste d'attente disponible"
```

## 🛠️ Fonctionnalités Avancées à Développer

### Phase 2:
- ✅ Factures électroniques
- ✅ Relances de paiement automatiques
- ✅ Email notifications
- ✅ SMS alertes
- ✅ Export CSV/PDF
- ✅ API REST pour intégrations

### Phase 3:
- ✅ Compétitions management
- ✅ Grille d'évaluation
- ✅ Certificats automatiques
- ✅ Tableau de bord statistiques
- ✅ Plan de carrière patineur
- ✅ Intégration calendrier (Google, Outlook)

## 📊 Tableaux de Bord

### Dashboard Admin
- Statistiques globales (membres, groupes, revenus)
- Derniers paiements
- Alertes (retards de paiement, etc.)
- Raccourcis vers gestion

### Dashboard Coach
- Ses groupes assignés
- Ses séances programmées
- Ses membres et présences
- Statistiques du groupe

### Dashboard Patineur
- Ses informations
- Son groupe et horaires
- Son taux d'assiduité
- Historique présences

## 🔐 Sécurité & Données

### Données Sensibles
- Mots de passe: Hashés avec Werkzeug
- Emails: Stockés de manière sécurisée
- Numéro licence: Chiffré
- Infos d'urgence: Champ sensible

### Audits
- Logs des modifications
- Historique des paiements
- Traçabilité des présences

## 📈 Intégrations Possibles

- **Paiement:** Stripe, PayPal, Wise
- **Email:** SendGrid, Mailgun
- **SMS:** Twilio, Orange SMS
- **Calendrier:** Google Calendar API
- **Comptabilité:** Sage, QuickBooks
- **CRM:** Salesforce (optionnel)

---

**Dernière mise à jour:** 22 janvier 2026
