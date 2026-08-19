<?php
/**
 * Šark nameštaj po meri — jedina ulazna tačka aplikacije (front controller).
 * Sav saobraćaj koji Apache ne pronađe kao fajl/folder preusmerava se ovamo
 * preko .htaccess pravila, a odavde ga preuzima Router.
 */
declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('APP_START', microtime(true));

/* --- Konfiguracija --------------------------------------------------------- */
$config = require BASE_PATH . '/config/config.php';
$GLOBALS['config'] = $config;

if (!empty($config['app']['debug'])) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

/* --- Autoload (Core, Controllers, Models, Services) ----------------------- */
spl_autoload_register(static function (string $class): void {
    foreach (['Core', 'Controllers', 'Models', 'Services'] as $dir) {
        $file = BASE_PATH . '/app/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

require BASE_PATH . '/app/helpers.php';

/* --- Osnovna putanja aplikacije (radi i u podfolderu htdocs-a) ----------- */
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$scriptDir = rtrim($scriptDir, '/');
define('BASE_URL', $scriptDir . '/');           // npr. /sark-namestaj/

/* --- Bezbedna sesija ----------------------------------------------------- */
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => BASE_URL,
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_name('sarksid');
session_start();

/* --- Trenutna ruta ----------------------------------------------------- */
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uri  = rawurldecode($uri);
$path = '/' . trim(substr($uri, strlen($scriptDir)), '/');

/* --- Rutiranje --------------------------------------------------------- */
$router = new Router();
require BASE_PATH . '/routes.php';

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $path);
} catch (Throwable $e) {
    http_response_code(500);
    if (!empty($config['app']['debug'])) {
        echo '<pre style="padding:1rem;font:14px/1.5 monospace">';
        echo 'Greška: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "\n\n";
        echo htmlspecialchars($e->getTraceAsString(), ENT_QUOTES);
        echo '</pre>';
    } else {
        echo 'Došlo je do greške na serveru.';
    }
}
