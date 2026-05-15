<?php
admin_required();
$role_filter = $_GET['role'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$where = $role_filter ? "WHERE roles LIKE '%\"$role_filter\"%'" : '';
$total = (int)get_db()->query("SELECT COUNT(*) FROM users $where")->fetchColumn();
$total_pages = max(1, (int)ceil($total / $per_page));
$page = min($page, $total_pages);
$offset = ($page-1)*$per_page;
$users = get_db()->query("SELECT * FROM users $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset")->fetchAll();
$users = array_map('decode_user', $users);
$__title = 'Gestion des Membres - Axel Club';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_users';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header">
        <h2>Gestion des Membres</h2>
        <a href="/admin/users/create" class="btn btn-primary">+ Ajouter</a>
    </div>
    <div style="margin-bottom:var(--space-md);display:flex;gap:.5rem;flex-wrap:wrap;">
        <a href="/admin/users" class="btn btn-sm <?= !$role_filter?'btn-primary':'btn-outline' ?>">Tous</a>
        <a href="/admin/users?role=patineur" class="btn btn-sm <?= $role_filter==='patineur'?'btn-primary':'btn-outline' ?>">Patineurs</a>
        <a href="/admin/users?role=parent" class="btn btn-sm <?= $role_filter==='parent'?'btn-primary':'btn-outline' ?>">Parents</a>
        <a href="/admin/users?role=admin" class="btn btn-sm <?= $role_filter==='admin'?'btn-primary':'btn-outline' ?>">Admins</a>
    </div>
    <section class="admin-section"><div class="admin-section-body">
        <?php if ($users): ?>
        <div class="table-container"><table class="table">
            <thead><tr><th>Nom</th><th>Email</th><th>Rôles</th><th>Groupe</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): $ug=user_group($u['id']); ?>
            <tr>
                <td><strong><?= e($u['first_name']) ?> <?= e($u['last_name']) ?></strong></td>
                <td><?= e($u['email']) ?></td>
                <td><?php foreach($u['roles'] as $r): ?><span class="badge badge-info" style="margin-right:2px;"><?= e($r) ?></span><?php endforeach; ?></td>
                <td><?= e($ug ? $ug['name'] : '-') ?></td>
                <td><span class="badge <?= $u['status']==='active'?'badge-success':'badge-warning' ?>"><?= e($u['status']) ?></span></td>
                <td class="table-actions">
                    <a href="/admin/users/<?= $u['id'] ?>/edit" class="btn btn-secondary btn-sm">Modifier</a>
                    <a href="/admin/users/<?= $u['id'] ?>/payments" class="btn btn-outline btn-sm">Paiements</a>
                    <form method="POST" action="/admin/users/<?= $u['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Supprimer <?= e($u['first_name']) ?> ?');">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php if ($total_pages > 1): ?>
        <div style="display:flex;gap:.5rem;margin-top:var(--space-md);justify-content:center;flex-wrap:wrap;">
            <?php for($p=1;$p<=$total_pages;$p++): ?>
            <a href="?<?= $role_filter?'role='.$role_filter.'&':'' ?>page=<?= $p ?>" class="btn btn-sm <?= $p===$page?'btn-primary':'btn-outline' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state"><div class="empty-state-icon">👥</div><h3>Aucun utilisateur</h3><a href="/admin/users/create" class="btn btn-primary">Ajouter</a></div>
        <?php endif; ?>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
