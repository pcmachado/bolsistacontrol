<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check if the application is under maintenance...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Captura a requisição
$request = Request::capture();

// Ajuste para funcionar em subpasta /ProBolsas
if (strpos($request->getRequestUri(), '/ProBolsas') === 0) {
    $newUrl = substr($request->getRequestUri(), 10);

    if (empty($newUrl)) {
        $newUrl = '/';
    }

    $request->server->set('REQUEST_URI', $newUrl);
}

// Processa a requisição
$app->handleRequest($request);
