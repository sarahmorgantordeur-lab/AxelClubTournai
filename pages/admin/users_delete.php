<?php
admin_required();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('/admin/users'); }
csrf_check();
$uid = (int)($GLOBALS['user_id'] ?? 0);
$me = auth_user();
if ($uid === (int)$me['id']) { flash('Vous ne pouvez pas supprimer votre propre compte.', 'error'); redirect('/admin/users'); }
user_delete($uid);
flash('Utilisateur supprimé.', 'success');
redirect('/admin/users');
