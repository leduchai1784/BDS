<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/locations/test-routes') !== false) {
    header('Content-Type: text/plain');
    echo "Index.php is loaded! URI: " . $_SERVER['REQUEST_URI'] . "\n";
    $cachePath = __DIR__ . '/../bootstrap/cache/routes-v7.php';
    echo "Route cache file exists: " . (file_exists($cachePath) ? 'YES' : 'NO') . "\n";
    if (file_exists($cachePath)) {
        echo "Route cache content length: " . filesize($cachePath) . " bytes\n";
    }
    
    $routesFile = __DIR__ . '/../routes/web.php';
    echo "routes/web.php exists: " . (file_exists($routesFile) ? 'YES' : 'NO') . "\n";
    if (file_exists($routesFile)) {
        echo "routes/web.php last modified: " . date("Y-m-d H:i:s", filemtime($routesFile)) . "\n";
        $content = file_get_contents($routesFile);
        echo "Contains getNksProvinces in web.php: " . (strpos($content, 'getNksProvinces') !== false ? 'YES' : 'NO') . "\n";
        echo "Contains LocationController in web.php: " . (strpos($content, 'LocationController') !== false ? 'YES' : 'NO') . "\n";
    }
    exit;
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
