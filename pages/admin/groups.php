<?php
admin_required();
$groups = group_all();
$__title = 'Groupes - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_groups';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header">
        <h2>Gestion des Groupes</h2>
        <a href="/admin/groups/create" class="btn btn-primary">+ Ajouter</a>
    </div>
    <section class="admin-section"><div class="admin-section-body">
        <?php if ($groups): ?>
        <div class="table-container"><table class="table">
            <thead><tr><th>Nom</th><th>Horaire</th><th>Prix/saison</th><th>Membres</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($groups as $g): $cnt = group_member_count($g['id']); ?>
            <tr>
                <td><strong><?= e($g['name']) ?></strong><?php if ($g['description']): ?><br><small style="color:var(--text-light)"><?= e($g['description']) ?></small><?php endif; ?></td>
                <td><?= e($g['schedule'] ?? '-') ?></td>
                <td><?= $g['price_per_season'] ? number_format((float)$g['price_per_season'],2).' €' : '-' ?></td>
                <td><span class="badge badge-info"><?= $cnt ?></span></td>
                <td class="table-actions">
                    <a href="/admin/groups/<?= $g['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
                    <form method="POST" action="/admin/groups/<?= $g['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Supprimer <?= e($g['name']) ?> ?');">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php else: ?>
        <div class="empty-state"><div class="empty-state-icon">🏒</div><h3>Aucun groupe</h3><a href="/admin/groups/create" class="btn btn-primary">Créer un groupe</a></div>
        <?php endif; ?>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
