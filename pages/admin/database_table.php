<?php
admin_required();
$table_name = $GLOBALS['table_name'] ?? '';
$allowed = [];
$all_tables = get_db()->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array($table_name, $all_tables, true)) {
    flash('Table introuvable.', 'error');
    redirect('/admin/database');
}
$pdo = get_db();
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 25;
$total = (int)$pdo->query("SELECT COUNT(*) FROM \"$table_name\"")->fetchColumn();
$total_pages = max(1, (int)ceil($total / $per_page));
$page = min($page, $total_pages);
$offset = ($page-1)*$per_page;
$rows = $pdo->query("SELECT * FROM \"$table_name\" LIMIT $per_page OFFSET $offset")->fetchAll();
$cols = $rows ? array_keys($rows[0]) : [];
$__title = "Table $table_name - Admin";
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_database';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header">
        <h2>Table : <code><?= e($table_name) ?></code></h2>
        <div style="display:flex;gap:.5rem;">
            <span class="badge badge-info"><?= $total ?> enregistrements</span>
            <a href="/admin/database" class="btn btn-secondary btn-sm">Retour</a>
        </div>
    </div>
    <section class="admin-section"><div class="admin-section-body">
        <?php if ($rows): ?>
        <div class="table-container" style="overflow-x:auto;"><table class="table" style="font-size:.85rem;">
            <thead><tr><?php foreach ($cols as $c): ?><th><?= e($c) ?></th><?php endforeach; ?></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($cols as $c): ?>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($row[$c]??'') ?>"><?= e(mb_substr($row[$c]??'',0,80)) ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php if ($total_pages > 1): ?>
        <div style="display:flex;gap:.5rem;margin-top:var(--space-md);justify-content:center;flex-wrap:wrap;">
            <?php for($p=1;$p<=$total_pages;$p++): ?>
            <a href="?page=<?= $p ?>" class="btn btn-sm <?= $p===$page?'btn-primary':'btn-outline' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state"><div class="empty-state-icon">📭</div><h3>Table vide</h3></div>
        <?php endif; ?>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
