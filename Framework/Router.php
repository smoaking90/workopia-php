<?php

namespace Framework;

use App\Controllers\ErrorController;

class Router{
    protected array $routes = [];

    /**
     * Add a new route
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     * @return void
     */
    public function registerRoute($method, $uri, $action){

        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod
        ];
    }


    /**
     * Add GET route
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller):void{
        $this->registerRoute('GET', $uri, $controller);
    }

     /**
     * Add POST route
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller):void{
        $this->registerRoute('POST', $uri, $controller);

    }

     /**
     * Add PUT route
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller):void{
        $this->registerRoute('PUT', $uri, $controller);

    }

     /**
     * Add DELETE route
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller):void{
        $this->registerRoute('DELETE', $uri, $controller);

    }

    

    /**
     * Route the request
     * @param string $uri
     * @param string $method
     * @return void
     */

     public function route($uri, $method){
        foreach($this->routes as $route){
            if($route['uri'] === $uri && $route['method'] === $method){
                // Extract controller and controller method
                $controller = "App\\Controllers\\" . $route['controller'];
                $controllerMethod = $route['controllerMethod'];

                // Instantiate the controller and call the method
                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod();
                return;
            }
        }
        ErrorController::notFound();
     }
}


