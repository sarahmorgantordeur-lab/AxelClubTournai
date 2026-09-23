<?php
$__title = 'Contact - Axel Club Tournai';
$__meta_desc = t('contact.meta_desc');
$__extra_css = ['home.css', 'groups.css'];
$__active = 'contact';
$__extra_head = '<style>
.contact-section{max-width:900px;margin:0 auto;padding:var(--space-xl) var(--space-md);display:grid;grid-template-columns:1fr 1.6fr;gap:var(--space-xl);align-items:start;}
@media(max-width:700px){.contact-section{grid-template-columns:1fr;}}
.contact-info h2{color:var(--ice-deep);margin-bottom:var(--space-md);}
.contact-info p{color:var(--text-light);margin-bottom:var(--space-lg);line-height:1.7;}
.contact-detail{display:flex;align-items:center;gap:var(--space-sm);margin-bottom:var(--space-md);color:var(--text-main);}
.contact-detail-icon{width:40px;height:40px;background:var(--ice-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.contact-detail a{color:var(--ice-deep);text-decoration:none;font-weight:500;}
.contact-form-card{background:var(--crystal-white);border-radius:var(--radius-md);box-shadow:var(--shadow-card);padding:var(--space-xl);}
.contact-form-card h2{color:var(--ice-deep);margin-bottom:var(--space-lg);}
.honeypot{display:none;}
</style>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!empty($_POST['website'])) { redirect('/contact'); }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if (!$name || !$email || !$subject || !$message) {
        flash(t('contact.flash.missing'), 'error');
    } else {
        $body = "Nom : $name\nEmail : $email\nTéléphone : " . ($phone ?: 'Non renseigné') . "\n\nMessage :\n$message";
        $headers = "From: noreply@axelclub.be\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
        if (@mail(SITE_EMAIL, '[Axel Club] ' . $subject, $body, $headers)) {
            flash(t('contact.flash.success'), 'success');
        } else {
            flash(t('contact.flash.error'), 'error');
        }
    }
    redirect('/contact');
}
require __DIR__ . '/../includes/header.php';
?>
<section class="groups-hero">
    <h1><?= e(t('contact.hero.title')) ?></h1>
    <p><?= e(t('contact.hero.subtitle')) ?></p>
</section>

<div class="contact-section">
    <div class="contact-info">
        <h2><?= e(t('contact.info.title')) ?></h2>
        <p><?= e(t('contact.info.text')) ?></p>
        <div class="contact-detail"><div class="contact-detail-icon">✉</div><div><div style="font-size:.8rem;color:var(--text-light);margin-bottom:2px;"><?= e(t('contact.email.label')) ?></div><a href="mailto:axelclubtournaifedere@gmail.com">axelclubtournaifedere@gmail.com</a></div></div>
        <div class="contact-detail"><div class="contact-detail-icon">📍</div><div><div style="font-size:.8rem;color:var(--text-light);margin-bottom:2px;"><?= e(t('contact.location.label')) ?></div><span><?= e(t('contact.location.value')) ?></span></div></div>
        <div style="margin-top:var(--space-lg);padding:var(--space-md);background:var(--ice-light);border-radius:var(--radius-sm);">
            <strong style="color:var(--ice-deep);"><?= e(t('contact.trial.title')) ?></strong>
            <p style="margin:var(--space-xs) 0 0;font-size:.9rem;color:var(--text-light);"><?= e(t('contact.trial.text')) ?></p>
        </div>
    </div>
    <div class="contact-form-card">
        <h2><?= e(t('contact.form.title')) ?></h2>
        <form method="POST" class="form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <div class="honeypot"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="form-row">
                <div class="form-group"><label><?= e(t('contact.form.name')) ?> <span style="color:var(--danger);">*</span></label><input type="text" name="name" required></div>
                <div class="form-group"><label><?= e(t('contact.form.email')) ?> <span style="color:var(--danger);">*</span></label><input type="email" name="email" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label><?= e(t('contact.form.phone')) ?></label><input type="tel" name="phone" placeholder="+32 XXX XX XX XX"></div>
                <div class="form-group"><label><?= e(t('contact.form.subject')) ?> <span style="color:var(--danger);">*</span></label>
                    <select name="subject" required style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);color:var(--text-main);font-size:.95rem;">
                        <option value=""><?= e(t('contact.form.subject_placeholder')) ?></option>
                        <option><?= e(t('contact.form.subject.trial')) ?></option>
                        <option><?= e(t('contact.form.subject.groups')) ?></option>
                        <option><?= e(t('contact.form.subject.pricing')) ?></option>
                        <option><?= e(t('contact.form.subject.hours')) ?></option>
                        <option><?= e(t('contact.form.subject.other')) ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label><?= e(t('contact.form.message')) ?> <span style="color:var(--danger);">*</span></label><textarea name="message" rows="6" required style="width:100%;padding:10px 14px;border:1px solid var(--silver-light);border-radius:var(--radius-sm);background:var(--crystal-white);font-size:.95rem;resize:vertical;font-family:inherit;"></textarea></div>
            <button type="submit" class="btn btn-primary" style="width:100%;"><?= e(t('contact.form.submit')) ?></button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
