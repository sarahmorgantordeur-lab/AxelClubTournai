<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/config.php';
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/utils.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/models.php';

// Simule le routage dynamique
$GLOBALS['user_id'] = '2';

echo "1. admin_required... ";
// Ne pas appeler admin_required() car redirige si pas admin - on simule juste
echo "skip (connecté: " . ($_SESSION['user_id'] ?? 'non') . ")<br>";

echo "2. user_find... ";
$uid = (int)($GLOBALS['user_id'] ?? 0);
$user = user_find($uid);
echo ($user ? "✅ " . $user['first_name'] : "❌ null") . "<br>";

echo "3. group_all / site_all / users_by_role... ";
$groups = group_all(); $sites = site_all(); $patineurs = users_by_role('patineur');
echo "✅ (" . count($groups) . " groupes, " . count($sites) . " sites, " . count($patineurs) . " patineurs)<br>";

echo "4. user_get_sites / user_get_children... ";
$user_sites = array_column(user_get_sites($uid), 'id');
$user_children = array_column(user_get_children($uid), 'id');
echo "✅<br>";

echo "5. header.php... ";
$__title = 'Test edit';
$__extra_css = ['admin.css'];
$__body_class = 'admin-layout';
$__active = 'admin_users';
ob_start();
require __DIR__ . '/includes/header.php';
ob_end_clean();
echo "✅<br>";

echo "6. users_edit.php complet... ";
ob_start();
require __DIR__ . '/pages/admin/users_edit.php';
ob_end_clean();
echo "✅<br>";

echo "<br><strong>Tout OK.</strong>";
