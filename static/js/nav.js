document.addEventListener('DOMContentLoaded', function () {
    // Menu burger
    const toggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (toggle && navLinks) {
        toggle.addEventListener('click', function () {
            toggle.classList.toggle('active');
            navLinks.classList.toggle('open');
        });

        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                toggle.classList.remove('active');
                navLinks.classList.remove('open');
            });
        });
    }

    // Afficher/masquer mot de passe
    document.querySelectorAll('.password-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.closest('.password-wrapper').querySelector('input');
            var eyeIcon = btn.querySelector('.eye-icon');
            var eyeOffIcon = btn.querySelector('.eye-off-icon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = '';
                btn.setAttribute('aria-label', 'Masquer le mot de passe');
            } else {
                input.type = 'password';
                eyeIcon.style.display = '';
                eyeOffIcon.style.display = 'none';
                btn.setAttribute('aria-label', 'Afficher le mot de passe');
            }
        });
    });
});
