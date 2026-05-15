<?php
admin_required();
$pdo = get_db();
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$table_info = [];
foreach ($tables as $t) {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM \"$t\"")->fetchColumn();
    $table_info[] = ['name' => $t, 'count' => $count];
}
$__title = 'Base de données - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_database';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Explorateur de base de données</h2></div>
    <section class="admin-section"><div class="admin-section-body">
        <div class="table-container"><table class="table">
            <thead><tr><th>Table</th><th>Enregistrements</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($table_info as $t): ?>
            <tr>
                <td><strong><?= e($t['name']) ?></strong></td>
                <td><span class="badge badge-info"><?= $t['count'] ?></span></td>
                <td><a href="/admin/database/<?= e($t['name']) ?>" class="btn btn-outline btn-sm">Voir</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
