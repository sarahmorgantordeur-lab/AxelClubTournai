<?php
admin_required();
$attendances = attendance_recent(100);
$sites = site_all();
$members = users_by_role('patineur');
$__title = 'Présences - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_att';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header">
        <h2>Gestion des Présences</h2>
        <a href="/admin/attendance/record" class="btn btn-primary">+ Enregistrer</a>
    </div>
    <section class="admin-section"><div class="admin-section-body">
        <?php if ($attendances): ?>
        <div class="table-container"><table class="table">
            <thead><tr><th>Date</th><th>Membre</th><th>Statut</th><th>Site</th><th>Notes</th></tr></thead>
            <tbody>
            <?php foreach ($attendances as $a): ?>
            <tr>
                <td><?= format_date($a['session_date'], 'd/m/Y H:i') ?></td>
                <td><strong><?= e($a['first_name']) ?> <?= e($a['last_name']) ?></strong></td>
                <td><span class="badge <?= $a['status']==='present'?'badge-success':($a['status']==='absent'?'badge-danger':'badge-warning') ?>"><?= e(ucfirst($a['status'])) ?></span></td>
                <td><?= e($a['site_name'] ?? '-') ?></td>
                <td><?= e($a['notes'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php else: ?>
        <div class="empty-state"><div class="empty-state-icon">📋</div><h3>Aucune présence enregistrée</h3><a href="/admin/attendance/record" class="btn btn-primary">Enregistrer une présence</a></div>
        <?php endif; ?>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
