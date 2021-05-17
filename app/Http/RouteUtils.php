<?php
/**
 * by stephan scheide
 */

namespace App\Http;


class RouteUtils
{

    public static function controllerName($moduleName, $entityName)
    {
        return 'Intern\\' . $moduleName . '\\' . $entityName . 'Controller';
    }

    public static function fullMethodeName($moduleName, $entityName, $methodeName)
    {
        return self::controllerName($moduleName, $entityName) . '@' . $methodeName;
    }

    public static function internDefaults(\Laravel\Lumen\Routing\Router $router, $prefix, $moduleName, $entityName)
    {
        $cn = self::controllerName($moduleName, $entityName);
        $router->get($prefix, $cn . '@all');
    }

}