<?php
// ---- Users ----

function user_find(int $id): ?array {
    $stmt = get_db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return decode_user($stmt->fetch() ?: null);
}

function user_find_by_email(string $email): ?array {
    $stmt = get_db()->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?)');
    $stmt->execute([$email]);
    return decode_user($stmt->fetch() ?: null);
}

function user_find_by_username(string $username): ?array {
    $stmt = get_db()->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    return decode_user($stmt->fetch() ?: null);
}

function decode_user(?array $row): ?array {
    if (!$row) return null;
    $row['roles'] = json_decode($row['roles'] ?? '[]', true) ?: ['patineur'];
    $row['emergency_contacts'] = json_decode($row['emergency_contacts'] ?? '[]', true) ?: [];
    return $row;
}

function users_by_role(string $role): array {
    $stmt = get_db()->prepare("SELECT * FROM users WHERE roles LIKE ? ORDER BY last_name, first_name");
    $stmt->execute(['%"' . $role . '"%']);
    return array_map('decode_user', $stmt->fetchAll());
}

function user_count_by_role(string $role): int {
    $stmt = get_db()->prepare("SELECT COUNT(*) FROM users WHERE roles LIKE ?");
    $stmt->execute(['%"' . $role . '"%']);
    return (int)$stmt->fetchColumn();
}

function user_create(array $data): int {
    $pdo = get_db();
    $pdo->prepare("INSERT INTO users (username,email,password_hash,first_name,last_name,phone,roles,group_id,license_number,registration_year,status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)")->execute([
        $data['username'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT),
        $data['first_name'], $data['last_name'], $data['phone'] ?? null,
        json_encode($data['roles'] ?? ['patineur']),
        ($data['group_id'] ?? null) ?: null, ($data['license_number'] ?? null) ?: null,
        date('Y'), 'active'
    ]);
    return (int)$pdo->lastInsertId();
}

function user_update(int $id, array $data): void {
    $pdo = get_db();
    $pdo->prepare("UPDATE users SET first_name=?,last_name=?,phone=?,address=?,email=?,roles=?,group_id=?,license_number=?,status=?,emergency_contacts=?,date_of_birth=?,updated_at=datetime('now') WHERE id=?")
        ->execute([
            $data['first_name'], $data['last_name'], $data['phone'] ?? null, $data['address'] ?? null, $data['email'],
            json_encode($data['roles'] ?? ['patineur']),
            ($data['group_id'] ?? null) ?: null, ($data['license_number'] ?? null) ?: null, $data['status'] ?? 'active',
            json_encode($data['emergency_contacts'] ?? []),
            $data['date_of_birth'] ?? null, $id
        ]);
}

function user_change_password(int $id, string $password): void {
    get_db()->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([password_hash($password, PASSWORD_BCRYPT), $id]);
}

function user_delete(int $id): void {
    get_db()->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
}

function user_attendance_rate(int $user_id, ?int $site_id = null): float {
    [$start, $end] = current_season();
    $sql = "SELECT status FROM attendances WHERE user_id=? AND session_date >= ? AND session_date <= ?";
    $params = [$user_id, $start, $end];
    if ($site_id !== null) { $sql .= " AND site_id=?"; $params[] = $site_id; }
    $stmt = get_db()->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    $total = count($rows);
    if ($total === 0) return 0.0;
    $present = count(array_filter($rows, fn($r) => $r['status'] === 'present'));
    return round($present / $total * 100, 1);
}

function user_attendances(int $user_id): array {
    $stmt = get_db()->prepare("SELECT a.*, s.name as site_name FROM attendances a LEFT JOIN sites s ON a.site_id=s.id WHERE a.user_id=? ORDER BY a.session_date DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function user_get_children(int $user_id): array {
    $stmt = get_db()->prepare("SELECT u.* FROM users u JOIN parent_child pc ON u.id=pc.child_id WHERE pc.parent_id=?");
    $stmt->execute([$user_id]);
    return array_map('decode_user', $stmt->fetchAll());
}

function user_get_parents(int $user_id): array {
    $stmt = get_db()->prepare("SELECT u.* FROM users u JOIN parent_child pc ON u.id=pc.parent_id WHERE pc.child_id=?");
    $stmt->execute([$user_id]);
    return array_map('decode_user', $stmt->fetchAll());
}

function user_set_children(int $user_id, array $child_ids): void {
    $pdo = get_db();
    $pdo->prepare("DELETE FROM parent_child WHERE parent_id=?")->execute([$user_id]);
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO parent_child (parent_id, child_id) VALUES (?,?)");
    foreach ($child_ids as $cid) $stmt->execute([$user_id, (int)$cid]);
}

function user_get_sites(int $user_id): array {
    $stmt = get_db()->prepare("SELECT s.* FROM sites s JOIN user_sites us ON s.id=us.site_id WHERE us.user_id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function user_set_sites(int $user_id, array $site_ids): void {
    $pdo = get_db();
    $pdo->prepare("DELETE FROM user_sites WHERE user_id=?")->execute([$user_id]);
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO user_sites (user_id, site_id) VALUES (?,?)");
    foreach ($site_ids as $sid) $stmt->execute([$user_id, (int)$sid]);
}

function user_group(int $user_id): ?array {
    $stmt = get_db()->prepare("SELECT g.* FROM groups g JOIN users u ON u.group_id=g.id WHERE u.id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetch() ?: null;
}

// ---- Groups ----

function group_all(): array {
    return get_db()->query("SELECT * FROM groups ORDER BY name")->fetchAll();
}

function group_find(int $id): ?array {
    $stmt = get_db()->prepare("SELECT * FROM groups WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function group_member_count(int $group_id): int {
    $stmt = get_db()->prepare("SELECT COUNT(*) FROM users WHERE group_id=?");
    $stmt->execute([$group_id]);
    return (int)$stmt->fetchColumn();
}

function group_create(array $data): void {
    get_db()->prepare("INSERT INTO groups (name,schedule,price_per_season,description) VALUES (?,?,?,?)")
        ->execute([$data['name'], $data['schedule'] ?? null, $data['price_per_season'] ?? null, $data['description'] ?? null]);
}

function group_update(int $id, array $data): void {
    get_db()->prepare("UPDATE groups SET name=?,schedule=?,price_per_season=?,description=?,updated_at=datetime('now') WHERE id=?")
        ->execute([$data['name'], $data['schedule'] ?? null, $data['price_per_season'] ?? null, $data['description'] ?? null, $id]);
}

function group_delete(int $id): void {
    get_db()->prepare("DELETE FROM groups WHERE id=?")->execute([$id]);
}

// ---- Attendance ----

function attendance_recent(int $limit = 50): array {
    $stmt = get_db()->prepare("SELECT a.*, u.first_name, u.last_name, s.name as site_name FROM attendances a JOIN users u ON a.user_id=u.id LEFT JOIN sites s ON a.site_id=s.id ORDER BY a.session_date DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function attendance_create(array $data): void {
    get_db()->prepare("INSERT INTO attendances (user_id,session_date,status,notes,site_id,recorded_by_id) VALUES (?,?,?,?,?,?)")
        ->execute([$data['user_id'], $data['session_date'], $data['status'] ?? 'present', $data['notes'] ?? null, $data['site_id'] ?? null, $data['recorded_by_id'] ?? null]);
}

// ---- Sites ----

function site_all(): array {
    return get_db()->query("SELECT * FROM sites ORDER BY name")->fetchAll();
}

// ---- Payments ----

function payment_records_paginated(int $page, int $per_page = 10): array {
    return paginate(
        "SELECT pr.*, u.first_name, u.last_name FROM payment_records pr JOIN users u ON pr.user_id=u.id ORDER BY pr.payment_date DESC",
        [], $page, $per_page
    );
}

function payment_record_create(array $data): void {
    get_db()->prepare("INSERT INTO payment_records (user_id,amount,payment_type,payment_method,payment_date,status,notes) VALUES (?,?,?,?,?,?,?)")
        ->execute([$data['user_id'], $data['amount'], $data['payment_type'] ?? null, $data['payment_method'] ?? null, $data['payment_date'], $data['status'] ?? 'paid', $data['notes'] ?? null]);
}

function total_revenue(): float {
    return (float)(get_db()->query("SELECT COALESCE(SUM(amount),0) FROM payment_records WHERE status='paid'")->fetchColumn());
}

// ---- Season Payments ----

function season_payment_get(int $user_id, int $year): array {
    $stmt = get_db()->prepare("SELECT * FROM season_payments WHERE user_id=? AND season_year=?");
    $stmt->execute([$user_id, $year]);
    $row = $stmt->fetch();
    if (!$row) {
        get_db()->prepare("INSERT INTO season_payments (user_id,season_year) VALUES (?,?)")->execute([$user_id, $year]);
        return season_payment_get($user_id, $year);
    }
    return $row;
}

function season_payment_update(int $user_id, int $year, array $data): void {
    $sp = season_payment_get($user_id, $year);
    get_db()->prepare("UPDATE season_payments SET licence_due=?,saison_tournai_due=?,wasquehal_p1_due=?,wasquehal_p2_due=?,competitions_due=?,licence_paid=?,saison_tournai_paid=?,wasquehal_p1_paid=?,wasquehal_p2_paid=?,competitions_paid=?,notes=?,updated_at=datetime('now') WHERE id=?")
        ->execute([
            (float)($data['licence_due'] ?? 0), (float)($data['saison_tournai_due'] ?? 0),
            (float)($data['wasquehal_p1_due'] ?? 0), (float)($data['wasquehal_p2_due'] ?? 0),
            (float)($data['competitions_due'] ?? 0), (float)($data['licence_paid'] ?? 0),
            (float)($data['saison_tournai_paid'] ?? 0), (float)($data['wasquehal_p1_paid'] ?? 0),
            (float)($data['wasquehal_p2_paid'] ?? 0), (float)($data['competitions_paid'] ?? 0),
            $data['notes'] ?? null, $sp['id']
        ]);
}

function sp_total_due(array $sp): float {
    return ($sp['licence_due']??0)+($sp['saison_tournai_due']??0)+($sp['wasquehal_p1_due']??0)+($sp['wasquehal_p2_due']??0)+($sp['competitions_due']??0);
}
function sp_total_paid(array $sp): float {
    return ($sp['licence_paid']??0)+($sp['saison_tournai_paid']??0)+($sp['wasquehal_p1_paid']??0)+($sp['wasquehal_p2_paid']??0)+($sp['competitions_paid']??0);
}
function sp_balance(array $sp): float { return sp_total_due($sp) - sp_total_paid($sp); }
function sp_is_paid(array $sp): bool { return sp_balance($sp) <= 0; }

// ---- Helpers ----

function current_season(): array {
    $y = (int)date('Y');
    $m = (int)date('m');
    if ($m >= 9) return [$y . '-09-01', ($y+1) . '-06-30'];
    return [($y-1) . '-09-01', $y . '-06-30'];
}
