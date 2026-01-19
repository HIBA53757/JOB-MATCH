<?php



namespace app\core;

class Router
{
    // Singleton instance
    private static ?Router $instance = null;

    // Routes storage
    private static array $routes = [
        "GET" => [],
        "POST" => []
    ];

    // Prevent external instantiation
    private function __construct() {}  // we use empty construnc so we can't create new instance

    // Get the singleton instance
    public static function getRouter(): Router
    {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    // Register GET route
    public function get(string $path, $callback)
    {
        self::$routes["GET"][$path] = $callback;
    }

    public function post(string $path, $callback)
    {
        self::$routes["POST"][$path] = $callback;
    }


    // Dispatch the request
    public function dispatch()   
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach (self::$routes[$method] as $path => $callback) {

            // Convert route params {id} or {id:\d+} into regex
            $routeRegex = preg_replace_callback('/{\w+(:([^}]+))?}/', function ($matches) {
                return isset($matches[2]) ? '(' . $matches[2] . ')' : '([a-zA-Z0-9_-]+)';
            }, $path);

            $routeRegex = '@^' . $routeRegex . '$@';

            if (preg_match($routeRegex, $uri, $matches)) {
                array_shift($matches); 

                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                    return;
                }

               
                if (is_array($callback) && count($callback) === 2) {
                    [$controller, $methodName] = $callback;
                    $instanceController = new $controller();
                    call_user_func_array([$instanceController, $methodName], $matches);
                    return;
                }
            }
        }

   
        if (isset(self::$routes["GET"]["/404"])) {
            call_user_func(self::$routes["GET"]["/404"]);
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
