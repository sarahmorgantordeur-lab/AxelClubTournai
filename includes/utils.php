<?php
function flash(string $message, string $type = 'info'): void {
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function get_flash_messages(): array {
    $msgs = $_SESSION['flash'] ?? [];
    $_SESSION['flash'] = [];
    return $msgs;
}

function redirect(string $url): void {
    header('Location: ' . BASE_URL . $url);
    exit;
}

function e($val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('CSRF token invalide.');
        }
    }
}

function paginate(string $sql, array $params, int $page, int $per_page): array {
    $pdo = get_db();
    $countSql = preg_replace('/SELECT .+? FROM/si', 'SELECT COUNT(*) FROM', $sql);
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();
    $total_pages = (int)ceil($total / $per_page);
    $page = max(1, min($page, max(1, $total_pages)));
    $offset = ($page - 1) * $per_page;
    $stmt = $pdo->prepare($sql . " LIMIT $per_page OFFSET $offset");
    $stmt->execute($params);
    return [
        'items' => $stmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => $total_pages,
    ];
}

function format_date(?string $dt, string $fmt = 'd/m/Y'): string {
    if (!$dt) return '-';
    try { return (new DateTime($dt))->format($fmt); } catch (Exception $e) { return '-'; }
}

function current_url(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function base_url(string $path = ''): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
}
