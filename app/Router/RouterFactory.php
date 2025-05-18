<?php

declare(strict_types=1);

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList;
        $router->addRoute('v1/product', 'Product:default');
        $router->addRoute('v1/products', 'ProductList:default');
        $router->addRoute('v1/product/history', 'ProductHistory:default');
        $router->addRoute('v1/openapi.yml', 'Default:openapiYaml');
        $router->addRoute('v1/openapi.json', 'Default:openapiJson');
        return $router;
    }
}
