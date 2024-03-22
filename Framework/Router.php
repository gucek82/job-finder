<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

require '../App//controllers/ErrorController.php';

class Router {
    protected $routes = [];

    /**
     * Add a new route
     * 
     * @param string $method
     * @param string $uri
     * @param string $action
     * @return void 
     */
    public function registerRoute($method, $uri, $action, $middleweare = []) {
        list($controller, $controllerMethod) = explode('@', $action);
   
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleweare
        ];
    }
 
    /**
     * Add a GET route
     * 
     * $param string $uri
     * @param string $controller
     * @param array $middleweare
     * @return void
     */

     public function get($uri, $controller, $middleweare = []) { 
       $this->registerRoute('GET', $uri, $controller, $middleweare);
     }

       /**
     * Add a POST route
     * 
     * $param string $uri
     * @param string $controller
     * @param array $middleweare
     * @return void
     */

     public function post($uri, $controller, $middleweare = []) {
        $this->registerRoute('POST', $uri, $controller, $middleweare);
     }

       /**
     * Add a PUT route
     * 
     * $param string $uri
     * @param string $controller
     * @param array $middleweare
     * @return void
     */

     public function put($uri, $controller, $middleweare = []) {
        $this->registerRoute('PUT', $uri, $controller, $middleweare);
     }

       /**
     * Add a DELETE route
     * 
     * $param string $uri
     * @param string $controller
     * @param array $middleweare
     * @return void
     */

     public function delete($uri, $controller, $middleweare = []) {
        $this->registerRoute('DELETE', $uri, $controller, $middleweare);
     }


     /**
      * Route the request
      * @param string $uri
      * @param string $method
      * @param array $middleweare
      * @return void
      */

      public function route($uri) {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // CHeck for _method input

        if($requestMethod === 'POST' && isset($_POST['_method'])) {
          // Override the request method with the value of _method
          $requestMethod = strtoupper($_POST['_method']);
        }

        foreach($this->routes as $route) {
        // Split the current URI into segments
        $uriSegments = explode('/', trim($uri, '/'));

        // Split the route URI into segments
        $routeSegments = explode('/', trim($route['uri'], '/'));
        $match = true;

        // Check if the number of segments matches
        if(count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
          $params = [];

          $match = true;

          for($i = 0; $i < count($uriSegments); $i++) {
            // if the uri's do not match and there is no param
            if($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
              $match = false;
              break;
            }
            // Check for the params and add to $params arr
            if(preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
              $params[$matches[1]] = $uriSegments[$i];
            }
          }

          if($match) {
                foreach($route['middleware'] as $middleweare) {
                  (new Authorize())->handle($middleweare);
                }

                // Extract controller and conteroller method        
                $controller = 'App\\Controllers\\' . $route['controller'];
                $path = 'App/controllers/' . $route['controller'] . '.php';
                $controllerMethod = $route['controllerMethod'];

                //  link controller becouse was error
                require basePath($path);
              
                // Instatiate the controller and call the method
                $controllerInstance = new $controller(); 
                $controllerInstance->$controllerMethod($params);
         
                return;
          }

        }
    
        }

       ErrorController::notFound();
      }
}