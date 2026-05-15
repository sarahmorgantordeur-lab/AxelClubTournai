<?php
admin_required();
$gid = (int)($GLOBALS['group_id'] ?? 0);
$group = group_find($gid) or (flash('Groupe introuvable.','error') && redirect('/admin/groups'));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    if (!$name) { flash('Le nom est requis.', 'error'); redirect("/admin/groups/$gid/edit"); }
    group_update($gid, [
        'name' => $name,
        'schedule' => trim($_POST['schedule'] ?? '') ?: null,
        'price_per_season' => $_POST['price_per_season'] !== '' ? (float)$_POST['price_per_season'] : null,
        'description' => trim($_POST['description'] ?? '') ?: null,
    ]);
    flash('Groupe mis à jour.', 'success');
    redirect('/admin/groups');
}
$__title = 'Modifier ' . $group['name'] . ' - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_groups';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Modifier <?= e($group['name']) ?></h2><a href="/admin/groups" class="btn btn-secondary">Retour</a></div>
    <section class="admin-section"><div class="admin-section-body">
    <form method="POST" class="form">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
            <div class="form-group"><label>Nom *</label><input type="text" name="name" value="<?= e($group['name']) ?>" required></div>
            <div class="form-group"><label>Horaire</label><input type="text" name="schedule" value="<?= e($group['schedule']??'') ?>" placeholder="ex: Lundi 17h-18h30"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Prix par saison (€)</label><input type="number" name="price_per_season" step="0.01" min="0" value="<?= e($group['price_per_season']??'') ?>"></div>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="3" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);font-size:.95rem;resize:vertical;"><?= e($group['description']??'') ?></textarea></div>
        <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/groups" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
