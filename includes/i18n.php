<?php
const SUPPORTED_LOCALES = ['fr', 'en', 'nl'];
const DEFAULT_LOCALE = 'fr';

function detect_locale(): string {
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LOCALES, true)) {
        setcookie('lang', $_GET['lang'], time() + 60 * 60 * 24 * 365, '/');
        return $_GET['lang'];
    }
    if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], SUPPORTED_LOCALES, true)) {
        return $_COOKIE['lang'];
    }
    $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    foreach (explode(',', $header) as $part) {
        $lang = strtolower(substr(trim($part), 0, 2));
        if (in_array($lang, SUPPORTED_LOCALES, true)) return $lang;
    }
    return DEFAULT_LOCALE;
}

function load_translations(string $locale): array {
    static $cache = [];
    if (isset($cache[$locale])) return $cache[$locale];
    $fr = require __DIR__ . '/lang/fr.php';
    if ($locale === DEFAULT_LOCALE) { return $cache[$locale] = $fr; }
    $file = __DIR__ . "/lang/$locale.php";
    $cache[$locale] = file_exists($file) ? array_merge($fr, require $file) : $fr;
    return $cache[$locale];
}

function t(string $key, array $params = []): string {
    global $__translations;
    $str = $__translations[$key] ?? $key;
    foreach ($params as $k => $v) $str = str_replace('{' . $k . '}', (string)$v, $str);
    return $str;
}

function lang_switch_url(string $locale): string {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return $path . '?lang=' . $locale;
}
