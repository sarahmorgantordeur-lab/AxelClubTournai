<?php
login_required();
$user = auth_user();
$attendances = user_attendances($user['id']);
$__title = 'Mes Présences - Axel Club';
$__extra_css = ['dashboard.css', 'admin.css'];
$__active = 'dashboard';
require __DIR__ . '/../../includes/header.php';
?>
<div class="dashboard-content">
    <div class="dashboard-header">
        <div>
            <h1>Mes Présences</h1>
            <p class="welcome-message">Historique de toutes vos séances</p>
        </div>
        <a href="/dashboard" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
    <div class="info-card">
        <div class="info-card-body">
            <?php if ($attendances): ?>
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>Date</th><th>Statut</th><th>Site</th><th>Notes</th></tr></thead>
                    <tbody>
                    <?php foreach ($attendances as $a): ?>
                    <tr>
                        <td><?= format_date($a['session_date'], 'd/m/Y H:i') ?></td>
                        <td><span class="badge <?= $a['status']==='present'?'badge-success':($a['status']==='absent'?'badge-danger':'badge-warning') ?>"><?= e(ucfirst($a['status'])) ?></span></td>
                        <td><?= e($a['site_name'] ?? '-') ?></td>
                        <td><?= e($a['notes'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state" style="text-align:center;padding:60px;">
                <div style="font-size:4rem;margin-bottom:20px;">📋</div>
                <h3>Aucune présence enregistrée</h3>
                <p style="color:var(--text-light);">Vos présences apparaîtront ici une fois enregistrées.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
