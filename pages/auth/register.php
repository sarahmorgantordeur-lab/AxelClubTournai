<?php
if (is_logged_in()) { redirect('/dashboard'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $role = in_array($_POST['roles'] ?? '', ['patineur','parent']) ? $_POST['roles'] : 'patineur';
    if (!$username || !$email || !$password || !$first_name || !$last_name) {
        flash('Tous les champs sont requis.', 'error');
    } elseif ($password !== $confirm) {
        flash('Les mots de passe ne correspondent pas.', 'error');
    } elseif (user_find_by_username($username)) {
        flash("Ce nom d'utilisateur existe déjà.", 'error');
    } elseif (user_find_by_email($email)) {
        flash('Cet email existe déjà.', 'error');
    } else {
        user_create(['username'=>$username,'email'=>$email,'password'=>$password,'first_name'=>$first_name,'last_name'=>$last_name,'roles'=>[$role]]);
        flash('Inscription réussie ! Veuillez vous connecter.', 'success');
        redirect('/auth/login');
    }
    redirect('/auth/register');
}
$__title = 'Inscription - Axel Club';
$__extra_css = ['auth.css'];
require __DIR__ . '/../../includes/header.php';
?>
<div class="auth-container">
    <div class="auth-box" style="max-width:480px;">
        <div class="auth-icon"><img src="/static/images/logo.png" alt="Axel Club" style="width:50px;height:50px;object-fit:contain;"></div>
        <h1>Axel Club</h1>
        <h2>Créer un compte</h2>
        <form method="POST" class="form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <div class="form-row">
                <div class="form-group"><label>Prénom</label><input type="text" name="first_name" required></div>
                <div class="form-group"><label>Nom</label><input type="text" name="last_name" required></div>
            </div>
            <div class="form-group"><label>Nom d'utilisateur</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Rôle</label>
                <select name="roles" style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;">
                    <option value="patineur">Patineur</option>
                    <option value="parent">Parent</option>
                </select>
            </div>
            <div class="form-group"><label>Mot de passe</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="pwd1" required minlength="8">
                    <button type="button" class="password-toggle" aria-label="Afficher le mot de passe">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>
            <div class="form-group"><label>Confirmer le mot de passe</label>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="pwd2" required minlength="8">
                    <button type="button" class="password-toggle" aria-label="Afficher le mot de passe">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Créer mon compte</button>
        </form>
        <p class="auth-footer">Déjà inscrit ? <a href="/auth/login">Se connecter</a></p>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
