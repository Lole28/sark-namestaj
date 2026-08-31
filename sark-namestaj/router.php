<?php
/**
 * Ruter samo za PHP-ov ugrađeni server (brzi test bez Apache-a):
 *
 *     php -S localhost:8000 router.php
 *
 * Na pravom serveru (XAMPP / Apache) ovaj fajl se NE koristi —
 * tamo rutiranje radi .htaccess. Ovde samo propuštamo postojeće
 * statičke fajlove, a sve ostalo šaljemo na index.php.
 */
$putanja = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$fajl = __DIR__ . $putanja;

if ($putanja !== '/' && is_file($fajl)) {
    return false; // Apache-ekvivalent: posluži fajl kakav jeste
}

require __DIR__ . '/index.php';
