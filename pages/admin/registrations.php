<?php
admin_required();
$groups = group_all();
$selected_group = isset($_GET['group_id']) ? (int)$_GET['group_id'] : null;
if ($selected_group) {
    $stmt = get_db()->prepare("SELECT u.* FROM users u WHERE u.group_id=? AND u.roles LIKE '%\"patineur\"%' ORDER BY u.last_name,u.first_name");
    $stmt->execute([$selected_group]);
    $members_in = array_map('decode_user', $stmt->fetchAll());
    $stmt2 = get_db()->prepare("SELECT u.* FROM users u WHERE (u.group_id!=? OR u.group_id IS NULL) AND u.roles LIKE '%\"patineur\"%' ORDER BY u.last_name,u.first_name");
    $stmt2->execute([$selected_group]);
    $members_out = array_map('decode_user', $stmt2->fetchAll());
} else {
    $members_in = [];
    $members_out = users_by_role('patineur');
}
$__title = 'Inscriptions - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_reg';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Gestion des Inscriptions</h2></div>
    <div style="margin-bottom:var(--space-md);">
        <form method="GET" style="display:inline-flex;gap:.5rem;align-items:center;">
            <label>Groupe :</label>
            <select name="group_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($groups as $g): ?><option value="<?= $g['id'] ?>" <?= $selected_group===$g['id']?'selected':'' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
            </select>
        </form>
    </div>
    <?php if ($selected_group): $grp = group_find($selected_group); ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-md);">
        <section class="admin-section">
            <h3>Inscrits dans <?= e($grp['name']) ?> (<?= count($members_in) ?>)</h3>
            <div class="admin-section-body">
            <?php if ($members_in): ?>
            <ul style="list-style:none;padding:0;margin:0;">
            <?php foreach ($members_in as $m): ?>
            <li style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--silver-light);">
                <span><?= e($m['first_name']) ?> <?= e($m['last_name']) ?></span>
                <form method="POST" action="/admin/registrations/unregister/<?= $m['id'] ?>?group_id=<?= $selected_group ?>">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Désinscrire</button>
                </form>
            </li>
            <?php endforeach; ?>
            </ul>
            <?php else: ?><p style="color:var(--text-light)">Aucun membre dans ce groupe.</p><?php endif; ?>
            </div>
        </section>
        <section class="admin-section">
            <h3>Non inscrits (<?= count($members_out) ?>)</h3>
            <div class="admin-section-body">
            <?php if ($members_out): ?>
            <ul style="list-style:none;padding:0;margin:0;">
            <?php foreach ($members_out as $m): ?>
            <li style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--silver-light);">
                <span><?= e($m['first_name']) ?> <?= e($m['last_name']) ?></span>
                <form method="POST" action="/admin/registrations/register/<?= $m['id'] ?>?group_id=<?= $selected_group ?>">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Inscrire</button>
                </form>
            </li>
            <?php endforeach; ?>
            </ul>
            <?php else: ?><p style="color:var(--text-light)">Tous les membres sont inscrits.</p><?php endif; ?>
            </div>
        </section>
    </div>
    <?php else: ?>
    <div class="empty-state"><div class="empty-state-icon">📝</div><h3>Sélectionnez un groupe</h3><p>Choisissez un groupe pour gérer les inscriptions.</p></div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
