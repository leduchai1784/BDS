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

if (isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/api/locations/test-routes') !== false || strpos($_SERVER['REQUEST_URI'], '/api/locations/provinces') !== false || strpos($_SERVER['REQUEST_URI'], '/api/locations/wards') !== false)) {
    header('Content-Type: text/plain');
    echo "Index.php debug probe active. URI: " . $_SERVER['REQUEST_URI'] . "\n";
    try {
        $cachePath = __DIR__ . '/../bootstrap/cache/routes-v7.php';
        echo "Route cache file exists: " . (file_exists($cachePath) ? 'YES' : 'NO') . "\n";
        
        $routesFile = __DIR__ . '/../routes/web.php';
        echo "routes/web.php exists: " . (file_exists($routesFile) ? 'YES' : 'NO') . "\n";
        
        echo "Bootstrapping Laravel...\n";
        /** @var Application $app */
        $app = require_once __DIR__.'/../bootstrap/app.php';
        
        // Boot kernel
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        
        echo "Checking LocationController class existence...\n";
        if (class_exists(\App\Http\Controllers\LocationController::class)) {
            echo "LocationController class exists!\n";
            $controller = $app->make(\App\Http\Controllers\LocationController::class);
            echo "LocationController resolved successfully!\n";
            if (strpos($_SERVER['REQUEST_URI'], '/api/locations/provinces') !== false) {
                $response = $controller->getProvinces();
                echo "Response content: " . $response->getContent() . "\n";
            } elseif (strpos($_SERVER['REQUEST_URI'], '/api/locations/wards') !== false) {
                $response = $controller->getWards();
                echo "Response content length: " . strlen($response->getContent()) . " bytes\n";
            }
        } else {
            echo "LocationController class does NOT exist via autoloading!\n";
        }
    } catch (\Throwable $e) {
        echo "EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
        echo "Trace:\n" . $e->getTraceAsString() . "\n";
    }
    exit;
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
