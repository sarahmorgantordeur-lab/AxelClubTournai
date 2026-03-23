# Gestion de la Base de Données

## La base de données est-elle protégée ?

✅ **OUI**. La base de données n'est **plus supprimée automatiquement** au démarrage de l'application.

Voici ce qui a changé :
- `db.create_all()` crée les tables seulement si elles n'existent pas
- L'application relancée n'écrase jamais vos données existantes
- `init_db.py` refuse de créer une DB si elle existe déjà

## Utiliser la base de données

### 1. Initialiser une nouvelle base de données (première utilisation)
```bash
python3 init_db.py
```
✅ Crée une nouvelle base de données vierge

### 2. Vérifier le statut de la base de données
```bash
python3 db_manage.py status
```
Affiche la taille, le chemin et la dernière modification de la DB

### 3. Sauvegarder la base de données (RECOMMANDÉ régulièrement)
```bash
python3 db_manage.py backup
```
Crée une sauvegarde horodatée dans le dossier `backups/`

### 4. Réinitialiser complètement la base de données (⚠️ PERTE DE DONNÉES)
```bash
python3 db_manage.py reset
```
- Demande une confirmation (tapez `oui`)
- Crée une sauvegarde avant suppression
- Supprime la DB et en crée une nouvelle (vierge)

## Workflow recommandé

1. **Démarrer l'app**: Les données existantes sont préservées
2. **Avant modifications importantes**: 
   ```bash
   python3 db_manage.py backup
   ```
3. **Si erreur**: Restaurez la sauvegarde manuellement
4. **Réinitialiser en développement**: Utilisez `reset` avec confirmation

## Emplacement de la base de données

📁 `instance/patinage_club.db`

Les sauvegardes sont stockées dans : `backups/patinage_club_backup_YYYYMMDD_HHMMSS.db`

## En résumé

- ✅ Vos données sont **en sécurité** au redémarrage
- ✅ Vous pouvez faire des **sauvegardes facilement**
- ✅ Les **suppressions** demandent une confirmation
- ✅ Les **sauvegardes horodatées** permettent de restaurer facilement
