<?php
admin_required();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/payments'); }
csrf_check();
$uid = (int)($GLOBALS['user_id'] ?? 0);
$year = (int)($_POST['year'] ?? date('Y'));
season_payment_update($uid, $year, $_POST);
flash('Paiements mis à jour.', 'success');
redirect("/admin/users/$uid/payments?year=$year");
