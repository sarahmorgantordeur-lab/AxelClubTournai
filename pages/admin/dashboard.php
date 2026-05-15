<?php
admin_required();
$stats = [
    'total_members' => user_count_by_role('patineur'),
    'total_groups'  => (int)get_db()->query("SELECT COUNT(*) FROM groups")->fetchColumn(),
    'total_users'   => (int)get_db()->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_revenue' => total_revenue(),
];
$recent = users_by_role('patineur');
usort($recent, fn($a,$b) => strcmp($b['created_at']??'',$a['created_at']??''));
$recent = array_slice($recent, 0, 5);
$__title = 'Tableau de Bord Admin - Axel Club';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_dashboard';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <h2>Tableau de Bord</h2>
    <div class="stats">
        <a href="/admin/users" class="stat-card" style="text-decoration:none;color:inherit;"><h4>Membres actifs</h4><p class="stat-number"><?= $stats['total_members'] ?></p></a>
        <a href="/admin/groups" class="stat-card" style="text-decoration:none;color:inherit;"><h4>Groupes</h4><p class="stat-number"><?= $stats['total_groups'] ?></p></a>
        <a href="/admin/users" class="stat-card" style="text-decoration:none;color:inherit;"><h4>Utilisateurs</h4><p class="stat-number"><?= $stats['total_users'] ?></p></a>
        <a href="/admin/payments" class="stat-card" style="text-decoration:none;color:inherit;"><h4>Revenu Total</h4><p class="stat-number"><?= number_format($stats['total_revenue'],2) ?>€</p></a>
    </div>
    <section class="admin-section">
        <h3>Derniers Membres Inscrits</h3>
        <div class="admin-section-body">
            <?php if ($recent): ?>
            <div class="table-container"><table class="table">
                <thead><tr><th>Nom</th><th>Groupe</th><th>Statut</th><th>Inscription</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $m): $mg=user_group($m['id']); ?>
                <tr>
                    <td><strong><?= e($m['first_name']) ?> <?= e($m['last_name']) ?></strong></td>
                    <td><?= e($mg ? $mg['name'] : '-') ?></td>
                    <td><span class="badge badge-success"><?= e($m['status']) ?></span></td>
                    <td><?= format_date($m['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
            <?php else: ?>
            <div class="empty-state"><div class="empty-state-icon">👥</div><h3>Aucun membre</h3><a href="/admin/users/create" class="btn btn-primary">Ajouter un membre</a></div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
