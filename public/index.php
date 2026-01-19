<?php

use app\core\Router;
use app\controller\AuthController;

include __DIR__ . '/../vendor/autoload.php';

$router = Router::getRouter();


$router->get("/user/{id}/{user}", function($id, $user){
    include __DIR__ . '/../views/user.php';
    
});

$router->get("/login", function(){
    include __DIR__ . '/../views/login.php';
    
});


$router->get("/user/{id}", [AuthController::class, "find"]);
$router->get("/test", [AuthController::class, "allTest"]);
 

$router->get("/404", function(){
    echo "404";
});


$router->get('/test-all', callback: [AuthController::class, 'findByIdTest']);
$router->get('/twig', callback: [AuthController::class, 'testTwifg']);

$router->post("/add", function(){
    print_r($_POST);
});


$router->dispatch();
