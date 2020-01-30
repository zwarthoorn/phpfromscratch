<?php

require_once '../vendor/autoload.php';
require_once '../routing/routing.php';

use routing\routing;
$routing = new routing();

$controllerResponse = $routing->routing();

if ($controllerResponse instanceof \Symfony\Component\HttpFoundation\RedirectResponse) {
    $controllerResponse->send();
} else {
    $response = new \Symfony\Component\HttpFoundation\Response();


    $response->setContent($controllerResponse);

    $response->send();
}

