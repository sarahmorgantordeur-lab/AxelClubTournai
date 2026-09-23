<?php
define('DB_PATH', __DIR__ . '/../database.sqlite');
define('SECRET_KEY', getenv('SECRET_KEY') ?: 'c63fc39fbdf60e9897591f89e167f07d355a0a9e96acb6dafea686edfc5b33a6');
define('SITE_EMAIL', 'axelclubtournaifedereprice_per_seasongmail.com');
define('SITE_NAME', 'Axel Club Tournai');
define('BASE_URL', '');

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
