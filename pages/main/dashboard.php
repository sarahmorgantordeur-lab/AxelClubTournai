<?php
login_required();
$user = auth_user();
$year = (int)date('Y');
$payment = null;
$children_data = [];
$grp = user_group($user['id']);
$sites = user_get_sites($user['id']);
$attendances = user_attendances($user['id']);
if (user_has_role($user, 'patineur')) $payment = season_payment_get($user['id'], $year);
if (user_has_role($user, 'parent')) {
    foreach (user_get_children($user['id']) as $child) {
        $children_data[] = ['child' => $child, 'payment' => season_payment_get($child['id'], $year)];
    }
}
$__title = 'Tableau de Bord - Axel Club';
$__extra_css = ['dashboard.css'];
$__active = 'dashboard';
$__body_class = 'dashboard-container';
require __DIR__ . '/../../includes/header.php';
?>
<div class="dashboard-content">
    <div class="dashboard-header">
        <div>
            <h1>Bonjour, <?= e($user['first_name']) ?> !</h1>
            <p class="welcome-message">Bienvenue sur votre espace personnel</p>
        </div>
        <span class="badge badge-info"><?= e(get_role_display($user['roles'])) ?></span>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <h4>Mon Groupe</h4>
            <p class="stat-number"><?= e($grp ? $grp['name'] : '-') ?></p>
        </div>
        <div class="stat-card accent">
            <div class="stat-icon">📊</div>
            <h4>Présence (saison)</h4>
            <p class="stat-number"><?= user_attendance_rate($user['id']) ?>%</p>
        </div>
        <?php foreach ($sites as $site): ?>
        <div class="stat-card">
            <div class="stat-icon">📍</div>
            <h4><?= e($site['name']) ?></h4>
            <p class="stat-number"><?= user_attendance_rate($user['id'], $site['id']) ?>%</p>
        </div>
        <?php endforeach; ?>
        <div class="stat-card success">
            <div class="stat-icon">🎯</div>
            <h4>Total Séances</h4>
            <p class="stat-number"><?= count($attendances) ?></p>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon">✨</div>
            <h4>Statut</h4>
            <p class="stat-number" style="font-size:1.5rem;"><?= e(ucfirst($user['status'] ?? 'actif')) ?></p>
        </div>
        <?php if ($payment): $bal = sp_balance($payment); ?>
        <div class="stat-card <?= $bal<=0?'success':'' ?>" <?= $bal>0?'style="border-left-color:var(--danger);"':'' ?>>
            <div class="stat-icon">💰</div>
            <h4>Solde à payer</h4>
            <p class="stat-number" style="font-size:1.5rem;<?= $bal>0?'color:var(--danger);':'' ?>"><?= number_format($bal,2) ?> €</p>
        </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-grid">
        <div class="info-card">
            <div class="info-card-header">
                <h3>Mes Informations</h3>
                <a href="/auth/profile" class="btn btn-secondary btn-sm">Modifier</a>
            </div>
            <div class="info-card-body">
                <div class="member-info-grid">
                    <div class="info-item"><label>Nom complet</label><span><?= e($user['first_name']) ?> <?= e($user['last_name']) ?></span></div>
                    <div class="info-item"><label>Email</label><span><?= e($user['email']) ?></span></div>
                    <div class="info-item"><label>Téléphone</label><span><?= e($user['phone'] ?? 'Non renseigné') ?></span></div>
                    <div class="info-item"><label>Groupe</label><span><?= e($grp ? $grp['name'] : 'Non assigné') ?></span></div>
                </div>
            </div>
        </div>
        <div class="info-card">
            <div class="info-card-header"><h3>Actions rapides</h3></div>
            <div class="info-card-body">
                <div class="quick-actions">
                    <a href="/my-attendance" class="quick-action-btn"><span>📅</span><span>Mes présences</span></a>
                    <a href="/auth/profile" class="quick-action-btn"><span>👤</span><span>Mon profil</span></a>
                    <a href="/groupes" class="quick-action-btn"><span>👥</span><span>Voir les groupes</span></a>
                    <?php if (user_has_role($user, 'admin')): ?>
                    <a href="/admin" class="quick-action-btn"><span>⚙️</span><span>Administration</span></a>
                    <?php else: ?>
                    <a href="/a-propos" class="quick-action-btn"><span>ℹ️</span><span>À propos</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($grp): ?>
    <div class="info-card" style="margin-top:var(--space-lg);">
        <div class="info-card-header"><h3>Informations du Groupe</h3></div>
        <div class="info-card-body">
            <div class="member-info-grid">
                <div class="info-item"><label>Nom du groupe</label><span><?= e($grp['name']) ?></span></div>
                <div class="info-item"><label>Horaires</label><span><?= e($grp['schedule'] ?: 'À confirmer') ?></span></div>
                <div class="info-item"><label>Tarif par saison</label><span><?= $grp['price_per_season'] ? number_format($grp['price_per_season'],2).'€' : '-' ?></span></div>
                <div class="info-item"><label>Membres</label><span><?= group_member_count($grp['id']) ?> membres</span></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($children_data): ?>
    <div class="info-card" style="margin-top:var(--space-lg);">
        <div class="info-card-header"><h3>Mes enfants</h3></div>
        <div class="info-card-body">
            <?php foreach ($children_data as $i => $item): $cp=$item['payment']; $cgrp=user_group($item['child']['id']); ?>
            <div style="<?= $i<count($children_data)-1?'border-bottom:1px solid var(--silver-light);padding-bottom:var(--space-md);margin-bottom:var(--space-md);':'' ?>">
                <h4 style="color:var(--ice-deep);margin-bottom:var(--space-sm);"><?= e($item['child']['first_name']) ?> <?= e($item['child']['last_name']) ?>
                    <?php if ($cgrp): ?><span style="font-weight:400;font-size:.85rem;color:var(--text-light);"> — <?= e($cgrp['name']) ?></span><?php endif; ?>
                </h4>
                <div class="member-info-grid">
                    <div class="info-item"><label>Taux de présence</label><span><?= user_attendance_rate($item['child']['id']) ?>%</span></div>
                    <div class="info-item"><label>Séances</label><span><?= count(user_attendances($item['child']['id'])) ?></span></div>
                    <?php if ($cp): $cbal=sp_balance($cp); $ctp=sp_total_paid($cp); ?>
                    <div class="info-item"><label>Solde à payer</label><span style="<?= $cbal>0?'color:var(--danger);font-weight:600;':'color:var(--success);font-weight:600;' ?>"><?= number_format($cbal,2) ?> €</span></div>
                    <div class="info-item"><label>Statut paiements</label><span class="badge <?= sp_is_paid($cp)?'badge-success':($ctp>0?'badge-warning':'badge-danger') ?>"><?= sp_is_paid($cp)?'Tout payé':($ctp>0?'En cours':'Non payé') ?></span></div>
                    <?php else: ?><div class="info-item"><label>Paiements</label><span style="color:var(--text-light);">Aucun enregistré</span></div><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
