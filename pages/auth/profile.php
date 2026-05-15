<?php
login_required();
$user = auth_user();
$year = (int)date('Y');
$payment = null;
$children_payments = [];
if (user_has_role($user, 'patineur')) $payment = season_payment_get($user['id'], $year);
if (user_has_role($user, 'parent')) {
    foreach (user_get_children($user['id']) as $child) {
        $children_payments[] = ['child' => $child, 'payment' => season_payment_get($child['id'], $year)];
    }
}
$__title = 'Mon Profil - Axel Club';
$__extra_css = ['admin.css', 'auth.css', 'dashboard.css'];
$__active = 'profile';
$__extra_head = '<style>
.payment-view-table{width:100%;border-collapse:collapse;margin-top:var(--space-md);}
.payment-view-table th,.payment-view-table td{padding:10px 14px;text-align:center;border-bottom:1px solid var(--silver-light);font-size:.9rem;}
.payment-view-table th{background:var(--ice-light);color:var(--ice-deep);font-weight:600;font-size:.8rem;text-transform:uppercase;}
.payment-view-table th:first-child,.payment-view-table td:first-child{text-align:left;}
.payment-view-table .total-row{background:var(--ice-light);font-weight:700;}
.payment-view-table .total-row td{border-top:2px solid var(--ice-deep);}
.reste-positif{color:var(--danger);font-weight:600;}
.reste-zero{color:var(--success);font-weight:600;}
.child-section{margin-bottom:var(--space-lg);}
.child-section h4{color:var(--ice-deep);margin-bottom:var(--space-sm);padding-bottom:var(--space-xs);border-bottom:2px solid var(--ice-light);}
</style>';
require __DIR__ . '/../../includes/header.php';
?>
<div class="profile-container">
    <h2 style="color:var(--ice-deep);margin-bottom:var(--space-lg);">Mon Profil</h2>
    <div class="profile-header">
        <div class="profile-avatar"><?= e(mb_substr($user['first_name'],0,1)) ?><?= e(mb_substr($user['last_name'],0,1)) ?></div>
        <div class="profile-info">
            <h1><?= e($user['first_name']) ?> <?= e($user['last_name']) ?></h1>
            <span class="badge badge-success"><?= e(ucfirst($user['status'] ?? 'actif')) ?></span>
            <span class="badge badge-info"><?= e(get_role_display($user['roles'])) ?></span>
            <p>Membre depuis <?= format_date($user['joined_date'], 'F Y') ?></p>
        </div>
    </div>

    <?php if (user_has_role($user, 'patineur')): ?>
    <div class="profile-card">
        <h3>Statistiques</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:var(--space-md);margin-top:var(--space-md);">
            <div style="text-align:center;padding:var(--space-md);background:var(--ice-light);border-radius:var(--radius-sm);">
                <div style="font-size:2rem;font-weight:700;color:var(--ice-deep);"><?= user_attendance_rate($user['id']) ?>%</div>
                <div style="font-size:.85rem;color:var(--text-light);">Taux de présence</div>
            </div>
            <div style="text-align:center;padding:var(--space-md);background:var(--ice-light);border-radius:var(--radius-sm);">
                <div style="font-size:2rem;font-weight:700;color:var(--ice-deep);"><?= count(user_attendances($user['id'])) ?></div>
                <div style="font-size:.85rem;color:var(--text-light);">Séances</div>
            </div>
            <?php $grp = user_group($user['id']); ?>
            <div style="text-align:center;padding:var(--space-md);background:var(--ice-light);border-radius:var(--radius-sm);">
                <div style="font-size:1.2rem;font-weight:600;color:var(--ice-deep);"><?= e($grp ? $grp['name'] : 'Aucun') ?></div>
                <div style="font-size:.85rem;color:var(--text-light);">Groupe</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="profile-card">
        <h3>Modifier mes informations</h3>
        <form method="POST" action="/auth/profile/edit" class="form" style="margin-top:var(--space-md);">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <div class="form-row">
                <div class="form-group"><label>Prénom</label><input type="text" name="first_name" value="<?= e($user['first_name']) ?>" required></div>
                <div class="form-group"><label>Nom</label><input type="text" name="last_name" value="<?= e($user['last_name']) ?>" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Téléphone</label><input type="tel" name="phone" value="<?= e($user['phone'] ?? '') ?>" placeholder="+32 XXX XX XX XX"></div>
                <div class="form-group"><label>Date de naissance</label><input type="date" name="date_of_birth" value="<?= e($user['date_of_birth'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>" required></div>
            </div>
            <div class="form-group">
                <label style="font-weight:bold;display:block;margin-bottom:var(--space-sm);">Numéros d'urgence</label>
                <div id="emergency-contacts" style="display:flex;flex-direction:column;gap:var(--space-sm);">
                    <?php foreach ($user['emergency_contacts'] as $c): ?>
                    <div style="display:flex;gap:var(--space-sm);align-items:center;">
                        <input type="tel" name="emergency_contacts[]" value="<?= e($c) ?>" style="flex:1;">
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.remove()">×</button>
                    </div>
                    <?php endforeach; ?>
                    <div style="display:flex;gap:var(--space-sm);align-items:center;">
                        <input type="tel" name="emergency_contacts[]" placeholder="+32 XXX XX XX XX" style="flex:1;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="addContact()">+</button>
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-md);">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/dashboard" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
        <script>function addContact(){var d=document.createElement('div');d.style.cssText='display:flex;gap:var(--space-sm);align-items:center;';d.innerHTML='<input type="tel" name="emergency_contacts[]" placeholder="+32 XXX XX XX XX" style="flex:1;"><button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.remove()">×</button>';document.getElementById('emergency-contacts').appendChild(d);}</script>
    </div>

    <div class="profile-card">
        <h3>Changer mon mot de passe</h3>
        <form method="POST" action="/auth/change-password" class="form" style="margin-top:var(--space-md);">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <div class="form-group"><label>Mot de passe actuel</label><input type="password" name="current_password" required autocomplete="current-password"></div>
            <div class="form-row">
                <div class="form-group"><label>Nouveau mot de passe</label><input type="password" name="new_password" required minlength="8" autocomplete="new-password"></div>
                <div class="form-group"><label>Confirmer</label><input type="password" name="confirm_new_password" required minlength="8" autocomplete="new-password"></div>
            </div>
            <button type="submit" class="btn btn-primary">Modifier le mot de passe</button>
        </form>
    </div>

    <?php if (user_has_role($user, 'patineur') && $payment): ?>
    <div class="profile-card">
        <h3>Mes Paiements — Saison <?= $year ?></h3>
        <?php
        $cats = [
            ['Licence et assurance', $payment['licence_due'], $payment['licence_paid']],
            ['Saison Tournai', $payment['saison_tournai_due'], $payment['saison_tournai_paid']],
            ['Wasquehal P1', $payment['wasquehal_p1_due'], $payment['wasquehal_p1_paid']],
            ['Wasquehal P2', $payment['wasquehal_p2_due'], $payment['wasquehal_p2_paid']],
            ['Compétitions', $payment['competitions_due'], $payment['competitions_paid']],
        ];
        ?>
        <div style="overflow-x:auto;"><table class="payment-view-table"><thead><tr><th>Catégorie</th><th>Dû</th><th>Payé</th><th>Reste</th><th>Statut</th></tr></thead><tbody>
        <?php foreach ($cats as [$name,$due,$paid]): $d=(float)$due;$p=(float)$paid;$r=$d-$p; ?>
        <tr><td><strong><?= e($name) ?></strong></td><td><?= number_format($d,2) ?> €</td><td><?= number_format($p,2) ?> €</td>
            <td class="<?= $r>0?'reste-positif':'reste-zero' ?>"><?= number_format($r,2) ?> €</td>
            <td><span class="badge <?= $r<=0?'badge-success':($p>0?'badge-warning':'badge-danger') ?>"><?= $r<=0?'Payé':($p>0?'Partiel':'Non payé') ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php $td=sp_total_due($payment);$tp=sp_total_paid($payment);$bal=sp_balance($payment); ?>
        <tr class="total-row"><td><strong>TOTAL</strong></td><td><strong><?= number_format($td,2) ?> €</strong></td><td><strong><?= number_format($tp,2) ?> €</strong></td>
            <td class="<?= $bal>0?'reste-positif':'reste-zero' ?>"><strong><?= number_format($bal,2) ?> €</strong></td>
            <td><span class="badge <?= sp_is_paid($payment)?'badge-success':($tp>0?'badge-warning':'badge-danger') ?>"><?= sp_is_paid($payment)?'Tout payé':($tp>0?'En cours':'Non payé') ?></span></td>
        </tr>
        </tbody></table></div>
    </div>
    <?php endif; ?>

    <?php if ($children_payments): ?>
    <div class="profile-card">
        <h3>Paiements de mes enfants — Saison <?= $year ?></h3>
        <?php foreach ($children_payments as $item): $cp=$item['payment']; ?>
        <div class="child-section">
            <h4><?= e($item['child']['first_name']) ?> <?= e($item['child']['last_name']) ?>
                <?php $cgrp=user_group($item['child']['id']); if($cgrp): ?><span style="font-weight:400;font-size:.85rem;color:var(--text-light);"> — <?= e($cgrp['name']) ?></span><?php endif; ?>
            </h4>
            <?php if ($cp): ?>
            <?php $ctd=sp_total_due($cp);$ctp=sp_total_paid($cp);$cbal=sp_balance($cp); ?>
            <div style="overflow-x:auto;"><table class="payment-view-table"><thead><tr><th>Catégorie</th><th>Dû</th><th>Payé</th><th>Reste</th><th>Statut</th></tr></thead><tbody>
            <?php $ccats=[['Licence',($cp['licence_due']??0),($cp['licence_paid']??0)],['Saison Tournai',($cp['saison_tournai_due']??0),($cp['saison_tournai_paid']??0)],['Wasquehal P1',($cp['wasquehal_p1_due']??0),($cp['wasquehal_p1_paid']??0)],['Wasquehal P2',($cp['wasquehal_p2_due']??0),($cp['wasquehal_p2_paid']??0)],['Compétitions',($cp['competitions_due']??0),($cp['competitions_paid']??0)]];
            foreach($ccats as [$cn,$cd,$cpd]):$cr=(float)$cd-(float)$cpd; ?>
            <tr><td><?= e($cn) ?></td><td><?= number_format((float)$cd,2) ?> €</td><td><?= number_format((float)$cpd,2) ?> €</td>
                <td class="<?= $cr>0?'reste-positif':'reste-zero' ?>"><?= number_format($cr,2) ?> €</td>
                <td><span class="badge <?= $cr<=0?'badge-success':($cpd>0?'badge-warning':'badge-danger') ?>"><?= $cr<=0?'Payé':($cpd>0?'Partiel':'Non payé') ?></span></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row"><td><strong>TOTAL</strong></td><td><strong><?= number_format($ctd,2) ?> €</strong></td><td><strong><?= number_format($ctp,2) ?> €</strong></td>
                <td class="<?= $cbal>0?'reste-positif':'reste-zero' ?>"><strong><?= number_format($cbal,2) ?> €</strong></td>
                <td><span class="badge <?= sp_is_paid($cp)?'badge-success':($ctp>0?'badge-warning':'badge-danger') ?>"><?= sp_is_paid($cp)?'Tout payé':($ctp>0?'En cours':'Non payé') ?></span></td>
            </tr>
            </tbody></table></div>
            <?php else: ?><p style="color:var(--text-light);">Aucun paiement enregistré.</p><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
