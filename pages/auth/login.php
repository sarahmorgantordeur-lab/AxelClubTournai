<?php
if (is_logged_in()) { redirect('/dashboard'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = user_find_by_email($email);
    if ($user && $user['is_active'] && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        redirect('/dashboard');
    }
    flash('Identifiants incorrects ou compte désactivé.', 'error');
    redirect('/auth/login');
}
$__title = 'Connexion - Axel Club';
$__extra_css = ['auth.css'];
require __DIR__ . '/../../includes/header.php';
?>
<div class="auth-container">
    <div class="auth-box">
        <div class="auth-icon"><img src="/static/images/logo.png" alt="Axel Club" style="width:50px;height:50px;object-fit:contain;"></div>
        <h1>Axel Club</h1>
        <h2>Connexion</h2>
        <form method="POST" class="form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <div class="form-group">
                <label>Adresse email</label>
                <input type="email" name="email" placeholder="votre@email.com" required autofocus>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="pwd" required>
                    <button type="button" class="password-toggle" aria-label="Afficher le mot de passe">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <p class="auth-footer">Pas encore inscrit ? <a href="/auth/register">Créer un compte</a></p>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
