<?php
admin_required();
$uid = (int)($GLOBALS['user_id'] ?? 0);
$user = user_find($uid) or (flash('Utilisateur introuvable.','error') && redirect('/admin/users'));
$groups = group_all(); $sites = site_all(); $patineurs = users_by_role('patineur');
$user_sites = array_column(user_get_sites($uid), 'id');
$user_children = array_column(user_get_children($uid), 'id');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $roles = $_POST['roles'] ?? ['patineur'];
    if (!is_array($roles)) $roles = [$roles];
    $lic = trim($_POST['license_number'] ?? '') ?: null;
    if ($lic) { $ex = get_db()->prepare("SELECT id FROM users WHERE license_number=? AND id!=?")->execute([$lic,$uid]); }
    user_update($uid, [
        'first_name'=>trim($_POST['first_name']??''), 'last_name'=>trim($_POST['last_name']??''),
        'phone'=>trim($_POST['phone']??'')?:null, 'email'=>trim($_POST['email']??$user['email']),
        'roles'=>$roles, 'group_id'=>in_array('patineur',$roles)?($_POST['group_id']??null):null,
        'license_number'=>$lic, 'status'=>$_POST['status']??'active',
        'emergency_contacts'=>$user['emergency_contacts'], 'date_of_birth'=>$user['date_of_birth'],
    ]);
    user_set_sites($uid, $_POST['site_ids'] ?? []);
    user_set_children($uid, in_array('parent',$roles) ? ($_POST['children'] ?? []) : []);
    flash('Utilisateur mis à jour.', 'success');
    redirect('/admin/users');
}
$__title = 'Modifier ' . $user['first_name'] . ' - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_users';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Modifier <?= e($user['first_name']) ?> <?= e($user['last_name']) ?></h2><a href="/admin/users" class="btn btn-secondary">Retour</a></div>
    <section class="admin-section"><div class="admin-section-body">
    <form method="POST" class="form">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
            <div class="form-group"><label>Prénom *</label><input type="text" name="first_name" value="<?= e($user['first_name']) ?>" required></div>
            <div class="form-group"><label>Nom *</label><input type="text" name="last_name" value="<?= e($user['last_name']) ?>" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>"></div>
            <div class="form-group"><label>Téléphone</label><input type="tel" name="phone" value="<?= e($user['phone']??'') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Numéro de licence</label><input type="text" name="license_number" value="<?= e($user['license_number']??'') ?>"></div>
            <div class="form-group"><label>Statut</label>
                <select name="status" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <?php foreach (['active'=>'Actif','inactive'=>'Inactif','suspended'=>'Suspendu'] as $v=>$l): ?>
                    <option value="<?= e($v) ?>" <?= $user['status']===$v?'selected':'' ?>><?= e($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group"><label>Rôles</label>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:.5rem;">
                <?php foreach (['patineur'=>'Patineur','parent'=>'Parent','admin'=>'Administrateur'] as $r=>$label): ?>
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;">
                    <input type="checkbox" name="roles[]" value="<?= e($r) ?>" <?= in_array($r,$user['roles'])?'checked':'' ?>> <?= e($label) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Groupe</label>
                <select name="group_id" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="">-- Aucun groupe --</option>
                    <?php foreach ($groups as $g): ?><option value="<?= $g['id'] ?>" <?= $user['group_id']==$g['id']?'selected':'' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Sites</label>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem;">
                    <?php foreach ($sites as $s): ?>
                    <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;">
                        <input type="checkbox" name="site_ids[]" value="<?= $s['id'] ?>" <?= in_array($s['id'],$user_sites)?'checked':'' ?>> <?= e($s['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php if ($patineurs): ?>
        <div class="form-group"><label>Enfants (si rôle Parent)</label>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem;">
                <?php foreach ($patineurs as $p): if ($p['id']===$uid) continue; ?>
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;">
                    <input type="checkbox" name="children[]" value="<?= $p['id'] ?>" <?= in_array($p['id'],$user_children)?'checked':'' ?>> <?= e($p['first_name']) ?> <?= e($p['last_name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/users" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
