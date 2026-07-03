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
    echo "Dumping Laravel routing table:\n";
    try {
        /** @var Application $app */
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $request = Request::capture();
        $app->instance('request', $request);
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $kernel->bootstrap();
        
        $routes = Route::getRoutes();
        echo "Total registered routes: " . count($routes) . "\n";
        foreach ($routes as $route) {
            echo "[" . implode('|', $route->methods()) . "] " . $route->uri() . " -> " . $route->getActionName() . "\n";
        }
    } catch (\Throwable $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
    }
    exit;
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/locations/provinces') !== false) {
    header('Content-Type: text/plain');
    echo "Inspecting request path detection:\n";
    try {
        $request = Request::capture();
        echo "requestUri: " . $request->getRequestUri() . "\n";
        echo "path: " . $request->path() . "\n";
        echo "url: " . $request->url() . "\n";
        echo "baseUrl: " . $request->baseUrl() . "\n";
        echo "decodedPath: " . $request->decodedPath() . "\n";
    } catch (\Throwable $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
    }
    exit;
}

try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    header('Content-Type: text/plain');
    echo "GLOBAL EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
    echo "Exception class: " . get_class($e) . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}
