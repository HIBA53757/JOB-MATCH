<?php

use app\core\Router;
use app\controller\AuthController;
use app\controller\back\CompanyController;

include __DIR__ . '/../vendor/autoload.php';

$router = Router::getRouter();

$router->get("/register" , [AuthController::class , "renderRegister"]);
$router->post("/register" , [AuthController::class , "createUser"]);

$router->get("/login" , [AuthController::class , "renderlogin"]);
$router->post("/login" , [AuthController::class , "loginCheck"]);

$router->get("/logout" , [AuthController::class , "logout"]);

$router->get("/admin/dashboard" , [AuthController::class , "renderDashboard"]);
$router->get("/admin/posts" , [AuthController::class , "renderPosts"]);
$router->get("/admin/users" , [AuthController::class , "renderUsers"]);

$router->get("/admin/companies" , [CompanyController::class , "renderCompanies"]);
$router->get("/admin/company/create" , [CompanyController::class , "renderCompanyForm"]);
$router->post("/admin/company/save" , [CompanyController::class , "createCompany"]);
$router->post("/admin/company/edit" , [CompanyController::class , "renderCompanyFormEdit"]);
$router->post("/admin/company/saveEdit" , [CompanyController::class , "editCompany"]);
$router->post("/admin/company/delete" , [CompanyController::class , "deleteCompany"]);

$router->get("/user/home" , [AuthController::class , "renderHome"]);
$router->get("/user/postDetails" , [AuthController::class , "renderPostDetails"]);
$router->get("/user/companyDetails" , [AuthController::class , "renderCompanyDetails"]);


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
