<?php
admin_required();
$groups = group_all();
$sites = site_all();
$patineurs = users_by_role('patineur');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $roles = $_POST['roles'] ?? ['patineur'];
    if (!is_array($roles)) $roles = [$roles];
    $email = trim($_POST['email'] ?? '');
    $fn = trim($_POST['first_name'] ?? '');
    $ln = trim($_POST['last_name'] ?? '');
    $uname = trim($_POST['username'] ?? '') ?: strtolower("$fn.$ln");
    if (user_find_by_email($email)) { flash('Cet email existe déjà.', 'error'); redirect('/admin/users/create'); }
    if (user_find_by_username($uname)) { flash("Ce nom d'utilisateur existe déjà.", 'error'); redirect('/admin/users/create'); }
    $lic = trim($_POST['license_number'] ?? '') ?: null;
    $gid = in_array('patineur',$roles) ? ($_POST['group_id'] ?? null) : null;
    $uid = user_create(['username'=>$uname,'email'=>$email,'password'=>$_POST['password']??'default123','first_name'=>$fn,'last_name'=>$ln,'phone'=>$_POST['phone']??null,'roles'=>$roles,'group_id'=>$gid,'license_number'=>$lic]);
    user_set_sites($uid, $_POST['site_ids'] ?? []);
    if (in_array('parent',$roles)) user_set_children($uid, $_POST['children'] ?? []);
    flash('Utilisateur créé avec succès.', 'success');
    redirect('/admin/users');
}
$__title = 'Créer un utilisateur - Admin';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_users';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container admin-container">
    <div class="page-header"><h2>Créer un utilisateur</h2><a href="/admin/users" class="btn btn-secondary">Retour</a></div>
    <section class="admin-section"><div class="admin-section-body">
    <form method="POST" class="form">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-row">
            <div class="form-group"><label>Prénom *</label><input type="text" name="first_name" required></div>
            <div class="form-group"><label>Nom *</label><input type="text" name="last_name" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Nom d'utilisateur</label><input type="text" name="username" placeholder="Auto-généré si vide"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Téléphone</label><input type="tel" name="phone"></div>
            <div class="form-group"><label>Numéro de licence</label><input type="text" name="license_number"></div>
        </div>
        <div class="form-group"><label>Mot de passe *</label><input type="password" name="password" value="default123" required></div>
        <div class="form-group"><label>Rôles</label>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:.5rem;">
                <?php foreach (['patineur'=>'Patineur','parent'=>'Parent','admin'=>'Administrateur'] as $r=>$label): ?>
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;">
                    <input type="checkbox" name="roles[]" value="<?= e($r) ?>" <?= $r==='patineur'?'checked':'' ?>> <?= e($label) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Groupe</label>
                <select name="group_id" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="">-- Aucun groupe --</option>
                    <?php foreach ($groups as $g): ?><option value="<?= $g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Sites</label>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem;">
                    <?php foreach ($sites as $s): ?>
                    <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;">
                        <input type="checkbox" name="site_ids[]" value="<?= $s['id'] ?>"> <?= e($s['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php if ($patineurs): ?>
        <div class="form-group"><label>Enfants (si rôle Parent)</label>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem;">
                <?php foreach ($patineurs as $p): ?>
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;">
                    <input type="checkbox" name="children[]" value="<?= $p['id'] ?>"> <?= e($p['first_name']) ?> <?= e($p['last_name']) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
            <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
            <a href="/admin/users" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
    </div></section>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
