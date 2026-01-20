<?php

use app\core\Router;
use app\controller\AuthController;

include __DIR__ . '/../vendor/autoload.php';

$router = Router::getRouter();

$router->get("/register" , [AuthController::class , "renderRegister"]);
$router->post("/register" , [AuthController::class , "createUser"]);

$router->get("/login" , [AuthController::class , "renderlogin"]);
$router->post("/login" , [AuthController::class , "loginCheck"]);

$router->get("/dashboard" , [AuthController::class , "renderDashboard"]);
$router->get("/home" , [AuthController::class , "renderHome"]);


$router->get("/user/{id}", [AuthController::class, "find"]);
$router->get("/test", [AuthController::class, "allTest"]);
 

$router->get("/404", function(){
    echo "404";
});


$router->get('/test-all', callback: [AuthController::class, 'findByIdTest']);

$router->post("/add", function(){
    print_r($_POST);
});


$router->dispatch();
