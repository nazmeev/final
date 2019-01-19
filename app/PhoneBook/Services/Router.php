<?php

namespace PhoneBook\Services;

class Router
{

    /**
     * @var array
     */
    private $routes = [];

    /**
     * Router constructor
     * @param array $routes
     */
    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    /**
     *
     * Routing entry point
     * @throws \Exception
     */
    public function dispatch()
    {
        $klein = new \Klein\Klein();
        foreach ($this->routes as $route) {
            $klein->respond(
                $route['method'],
                $route['path'],
                function ($request, $response) use ($route) {
                    /** @var ControllerInterface $controller */
                    $controller = DiContainer::getInstance()->get($route['className']);
                    if ($controller instanceof ControllerInterface) {
                        return $controller->execute($request, $response);
                    } else {
                        throw new SystemException('Controller Class not found');
                    }
                });
        }

        $klein->dispatch();
    }

    /**
     * @return \DI\Container
     * @throws \Exception
     */
    private function getDiContainer()
    {
        return DiContainer::getInstance();
    }

}
