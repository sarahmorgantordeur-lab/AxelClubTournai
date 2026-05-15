<?php
function auth_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    static $user = null;
    if ($user === null) {
        $stmt = get_db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if (!$row) { unset($_SESSION['user_id']); return null; }
        $row['roles'] = json_decode($row['roles'] ?? '[]', true) ?: ['patineur'];
        $row['emergency_contacts'] = json_decode($row['emergency_contacts'] ?? '[]', true) ?: [];
        $user = $row;
    }
    return $user;
}

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function user_has_role(array $user, string $role): bool {
    return in_array($role, $user['roles'] ?? []);
}

function current_user_has_role(string $role): bool {
    $user = auth_user();
    return $user ? user_has_role($user, $role) : false;
}

function login_required(): void {
    if (!is_logged_in()) {
        flash('Veuillez vous connecter.', 'error');
        redirect('/auth/login');
    }
}

function admin_required(): void {
    login_required();
    if (!current_user_has_role('admin')) {
        flash('Accès refusé.', 'error');
        redirect('/dashboard');
    }
}

function get_role_display(array $roles): string {
    $map = ['admin' => 'Administrateur', 'patineur' => 'Patineur', 'parent' => 'Parent'];
    return implode(', ', array_map(fn($r) => $map[$r] ?? ucfirst($r), $roles));
}
