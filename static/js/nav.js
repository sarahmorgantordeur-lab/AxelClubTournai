document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (toggle && navLinks) {
        toggle.addEventListener('click', function () {
            toggle.classList.toggle('active');
            navLinks.classList.toggle('open');
        });

        // Fermer le menu si on clique sur un lien
        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                toggle.classList.remove('active');
                navLinks.classList.remove('open');
            });
        });
    }
});
