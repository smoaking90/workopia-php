<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router{
    protected array $routes = [];

    /**
     * Add a new route
     *
     * @param string $method
     * @param string $uri
     * @param string $action
     * @param array $middleware
     * @return void
     */
    public function registerRoute($method, $uri, $action, $middleware = []){

        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleware
        ];
    }


    /**
     * Add GET route
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function get($uri, $controller, $middleware = []){
        $this->registerRoute('GET', $uri, $controller, $middleware);
    }

     /**
     * Add POST route
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function post($uri, $controller, $middleware = []){
        $this->registerRoute('POST', $uri, $controller, $middleware);

    }

     /**
     * Add PUT route
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function put($uri, $controller, $middleware = []){
        $this->registerRoute('PUT', $uri, $controller, $middleware);

    }

     /**
     * Add DELETE route
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function delete($uri, $controller, $middleware = []){
        $this->registerRoute('DELETE', $uri, $controller, $middleware);

    }

    

    /**
     * Route the request
     * @param string $uri
     * @param string $method
     * @return void
     */

     public function route($uri){
        //inspect($uri);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // check for _method input
        if($requestMethod === "POST" && isset($_POST['_method'])){
            // override the request method with the value of _method
            $requestMethod = strtoupper($_POST['_method']);
        }

        foreach($this->routes as $route){

            // Split the current URI into segments
            $uriSegments = explode('/', trim($uri, '/'));
            //inspect($uriSegments);

            // Split the route URI into segments
            $routeSegments = explode('/', trim($route['uri'], '/'));
            //inspect($routeSegments);

            $match = true;

            // Check if the number of segments matches
            if(count($uriSegments) === count($routeSegments) 
            && strtoupper($route['method']) === $requestMethod){
                $params = [];
                $match = true;

                for($i = 0; $i < count($uriSegments); $i++){
                    // if uri's do not match and there is no param
                    if($routeSegments[$i] !== $uriSegments[$i] &&
                    !preg_match('/\{(.+?)\}/', $routeSegments[$i])){
                        $match = false;
                        break;
                    }

                    // check for the param and add to params array
                    if(preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)){
                       $params[$matches[1]] = $uriSegments[$i];
                    //    inspect($matches);
                    //    inspectAndDie($params);
                    }
                }

                if($match){
                    foreach($route['middleware'] as $role){
                        (new Authorize()->handle($role));
                    }

                    // Extract controller and controller method
                    $controller = "App\\Controllers\\" . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];

                    // Instantiate the controller and call the method
                    $controllerInstance = new $controller();
                    $controllerInstance->$controllerMethod($params);
                    return;
                }
            }
        }
        ErrorController::notFound();
     }
}


