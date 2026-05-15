<?php
admin_required();
$members = users_by_role('patineur');
$sites = site_all();
$me = auth_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $user_ids = $_POST['user_ids'] ?? [];
    $date = $_POST['session_date'] ?? date('Y-m-d H:i:s');
    $site_id = $_POST['site_id'] ? (int)$_POST['site_id'] : null;
    $notes = trim($_POST['notes'] ?? '') ?: null;
    foreach ($members as $m) {
        $status = $_POST['status'][$m['id']] ?? null;
        if ($status) {
            attendance_create(['user_id'=>$m['id'],'session_date'=>$date,'status'=>$status,'notes'=>$notes,'site_id'=>$site_id,'recorded_by_id'=>$me['id']]);
        }
    }
    flash('Présences enregistrées.', 'success');
    redirect('/admin/attendance');
}
$__title = 'Enregistrer des présences - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_att';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Enregistrer des présences</h2><a href="/admin/attendance" class="btn btn-secondary">Retour</a></div>
    <section class="admin-section"><div class="admin-section-body">
    <form method="POST" class="form">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Date et heure *</label>
                <input type="datetime-local" name="session_date" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
            <div class="form-group">
                <label>Site</label>
                <select name="site_id" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="">-- Tous les sites --</option>
                    <?php foreach ($sites as $s): ?><option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group"><label>Notes globales</label><input type="text" name="notes" placeholder="Remarques éventuelles..."></div>
        <?php if ($members): ?>
        <div class="table-container" style="margin-top:var(--space-md);"><table class="table">
            <thead><tr><th>Membre</th><th>Groupe</th><th>Présent</th><th>Absent</th><th>Excuse</th></tr></thead>
            <tbody>
            <?php foreach ($members as $m): $mg = user_group($m['id']); ?>
            <tr>
                <td><strong><?= e($m['first_name']) ?> <?= e($m['last_name']) ?></strong></td>
                <td><?= e($mg ? $mg['name'] : '-') ?></td>
                <td><input type="radio" name="status[<?= $m['id'] ?>]" value="present" checked></td>
                <td><input type="radio" name="status[<?= $m['id'] ?>]" value="absent"></td>
                <td><input type="radio" name="status[<?= $m['id'] ?>]" value="excused"></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php else: ?>
        <p style="color:var(--text-light)">Aucun patineur inscrit.</p>
        <?php endif; ?>
        <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/attendance" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
