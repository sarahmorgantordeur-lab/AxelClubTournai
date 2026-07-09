<?php
login_required();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/auth/profile'); }
csrf_check();
$user = auth_user();
$email = trim($_POST['email'] ?? $user['email']);
if ($email !== $user['email'] && user_find_by_email($email)) {
    flash('Cet email est déjà utilisé.', 'error');
    redirect('/auth/profile');
}
$contacts = array_filter(array_map('trim', $_POST['emergency_contacts'] ?? []), fn($c) => $c !== '');
user_update($user['id'], [
    'first_name' => trim($_POST['first_name'] ?? $user['first_name']),
    'last_name'  => trim($_POST['last_name'] ?? $user['last_name']),
    'phone'      => trim($_POST['phone'] ?? '') ?: null,
    'address'    => trim($_POST['address'] ?? '') ?: null,
    'email'      => $email,
    'roles'      => $user['roles'],
    'group_id'   => $user['group_id'],
    'license_number' => $user['license_number'],
    'status'     => $user['status'],
    'emergency_contacts' => array_values($contacts),
    'date_of_birth' => $_POST['date_of_birth'] ?? $user['date_of_birth'],
]);
flash('Profil mis à jour avec succès.', 'success');
redirect('/auth/profile');
