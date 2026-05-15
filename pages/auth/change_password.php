<?php
login_required();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/auth/profile'); }
csrf_check();
$user = auth_user();
$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_new_password'] ?? '';
if (!password_verify($current, $user['password_hash'])) {
    flash('Mot de passe actuel incorrect.', 'error');
} elseif ($new !== $confirm) {
    flash('Les nouveaux mots de passe ne correspondent pas.', 'error');
} elseif (strlen($new) < 8) {
    flash('Le nouveau mot de passe doit contenir au moins 8 caractères.', 'error');
} else {
    user_change_password($user['id'], $new);
    flash('Mot de passe modifié avec succès.', 'success');
}
redirect('/auth/profile');
