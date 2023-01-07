<?php

namespace CountryGuesser\Models;

require_once __DIR__ . "/../../vendor/autoload.php";

/**
    Router class
    This class provides a method to define routes and redirect to the correct controller.
*/
class Router
{
    private array $routes = [];

    /**
        defineRoute method
        This method defines a route.

        @param string $route The route to define.
        @param string $controllerPath The path to the controller to redirect to.
        @return void
    */
    public function defineRoute(string $route, string $controllerPath): void
    {
        $this->routes[$route] = [
            "controller" => $controllerPath,
        ];
    }

    /**
        redirect method
        This method redirects to the correct controller.

        @param string $route The route to redirect to.
        @return void
    */
    public function redirect(string $route): void
    {
        if (array_key_exists($route, $this->routes)) {
            $controller = $this->routes[$route]["controller"];
            require_once __DIR__ . "/../Controllers/" . $controller;
        } else {
            header("location: https://pierreleocadie.notion.site/Documentation-Country-Guesser-API-51457d0229eb40198094ad8bbfde51fc");
        }
    }
}