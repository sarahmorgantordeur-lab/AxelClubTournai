<?php
require_once __DIR__ . '/config.php';
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/utils.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/models.php';

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Routes statiques
$routes = [
    ''                      => 'pages/home.php',
    'a-propos'              => 'pages/about.php',
    'groupes'               => 'pages/groups_public.php',
    'contact'               => 'pages/contact.php',
    'mentions-legales'      => 'pages/legal.php',
    'robots.txt'            => 'pages/robots.php',
    'sitemap.xml'           => 'pages/sitemap.php',
    'auth/login'            => 'pages/auth/login.php',
    'auth/register'         => 'pages/auth/register.php',
    'auth/logout'           => 'pages/auth/logout.php',
    'auth/profile'          => 'pages/auth/profile.php',
    'auth/profile/edit'     => 'pages/auth/profile_edit.php',
    'auth/change-password'  => 'pages/auth/change_password.php',
    'dashboard'             => 'pages/main/dashboard.php',
    'my-attendance'         => 'pages/main/attendance.php',
    'admin'                 => 'pages/admin/dashboard.php',
    'admin/users'           => 'pages/admin/users.php',
    'admin/members'         => 'pages/admin/users.php',
    'admin/users/create'    => 'pages/admin/users_create.php',
    'admin/groups'          => 'pages/admin/groups.php',
    'admin/groups/create'   => 'pages/admin/groups_create.php',
    'admin/attendance'      => 'pages/admin/attendance.php',
    'admin/attendance/record' => 'pages/admin/attendance_record.php',
    'admin/payments'        => 'pages/admin/payments.php',
    'admin/payments/add'    => 'pages/admin/payments_add.php',
    'admin/registrations'   => 'pages/admin/registrations.php',
    'admin/reports'         => 'pages/admin/reports.php',
    'admin/send-email'      => 'pages/admin/send_email.php',
    'admin/profile'         => 'pages/admin/profile.php',
    'admin/database'        => 'pages/admin/database.php',
];

if (isset($routes[$uri])) {
    $file = __DIR__ . '/' . $routes[$uri];
    if (file_exists($file)) { require $file; exit; }
}

// Routes dynamiques avec paramètres
$patterns = [
    '#^admin/users/(\d+)/edit$#'               => ['pages/admin/users_edit.php', 'user_id'],
    '#^admin/users/(\d+)/delete$#'             => ['pages/admin/users_delete.php', 'user_id'],
    '#^admin/users/(\d+)/payments$#'           => ['pages/admin/user_payments.php', 'user_id'],
    '#^admin/users/(\d+)/payments/update$#'    => ['pages/admin/user_payments_update.php', 'user_id'],
    '#^admin/groups/(\d+)/edit$#'              => ['pages/admin/groups_edit.php', 'group_id'],
    '#^admin/groups/(\d+)/delete$#'            => ['pages/admin/groups_delete.php', 'group_id'],
    '#^admin/registrations/register/(\d+)$#'   => ['pages/admin/reg_register.php', 'user_id'],
    '#^admin/registrations/unregister/(\d+)$#' => ['pages/admin/reg_unregister.php', 'user_id'],
    '#^admin/database/(\w+)$#'                 => ['pages/admin/database_table.php', 'table_name'],
];

foreach ($patterns as $pattern => [$page, $param]) {
    if (preg_match($pattern, $uri, $m)) {
        $GLOBALS[$param] = $m[1];
        $file = __DIR__ . '/' . $page;
        if (file_exists($file)) { require $file; exit; }
    }
}

// 404
http_response_code(404);
$__title = 'Page non trouvée';
$__extra_css = ['home.css'];
require __DIR__ . '/includes/header.php';
echo '<div class="container" style="text-align:center;padding:80px 20px;"><h1>404</h1><p>Page non trouvée.</p><a href="/" class="btn btn-primary">Retour à l\'accueil</a></div>';
require __DIR__ . '/includes/footer.php';
