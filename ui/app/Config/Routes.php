<?php

namespace Config;

use Config\Services;

$routes = Services::routes();

// Home -> Empanadas
$routes->get('/', 'Empanadas::index');

// (opcional) autoroute si lo necesitas
$routes->setAutoRoute(true);