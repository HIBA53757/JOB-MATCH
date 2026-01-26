<?php
session_start();

use app\core\Router;
use app\controller\AuthController;
use app\controller\back\CompanyController;
use app\controller\back\UserController;
use app\controller\back\AnnonceController;
use app\controller\back\DashboardController;
use app\controller\front\HomeController;
use app\controller\front\ApplicationController;
use app\models\Application;

include __DIR__ . '/../vendor/autoload.php';

$router = Router::getRouter();

$isLogin = $_SESSION['user_id'] ?? null;

if($_SERVER["REQUEST_URI"] === '/' && $isLogin === null){
    $_SERVER["REQUEST_URI"] = "/login";
    
}

$router->get("/register" , [AuthController::class , "renderRegister"]);
$router->post("/register" , [AuthController::class , "createUser"]);

$router->get("/login" , [AuthController::class , "renderlogin"]);
$router->post("/login" , [AuthController::class , "loginCheck"]);

$router->get("/logout" , [AuthController::class , "logout"]);

$router->get("/admin/dashboard" , [DashboardController::class , "renderDashboard"]);

$router->get("/admin/users" , [UserController::class , "renderUsers"]);

$router->get("/admin/companies" , [CompanyController::class , "renderCompanies"]);
$router->get("/admin/company/create" , [CompanyController::class , "renderCompanyForm"]);
$router->post("/admin/company/save" , [CompanyController::class , "createCompany"]);
$router->post("/admin/company/edit" , [CompanyController::class , "renderCompanyFormEdit"]);
$router->post("/admin/company/saveEdit" , [CompanyController::class , "editCompany"]);
$router->post("/admin/company/delete" , [CompanyController::class , "deleteCompany"]);


$router->get("/admin/posts" , [AnnonceController::class , "renderPosts"]);
$router->get("/admin/posts/archived" , [AnnonceController::class , "renderPostsArchived"]);
$router->get("/admin/post/create" , [AnnonceController::class , "renderPostForm"]);
$router->post("/admin/post/save" , [AnnonceController::class , "createPost"]);
$router->post("/admin/post/edit" , [AnnonceController::class , "renderPostFormEdit"]);
$router->post("/admin/post/saveEdit" , [AnnonceController::class , "editPost"]);
$router->post("/admin/post/archive" , [AnnonceController::class , "archivePost"]);

$router->post("/user/post/motivation" , [ApplicationController::class , "saveMotivation"]);
$router->post("/admin/application/update" , [DashboardController::class , "updateAction"]);

$router->get("/user/home" , [HomeController::class , "renderHome"]);
$router->post("/user/postDetails" , [HomeController::class , "renderPostDetails"]);
$router->post("/user/companyDetails" , [HomeController::class , "renderCompanyDetails"]);
$router->get("/user/MyDemand" , [DashboardController::class , "updateAction"]);
$router->get("/user/MyDemandes" , [HomeController::class , "renderDemand"]);


// $router->get("/user/{id}", [AuthController::class, "find"]);
$router->get("/test", [AuthController::class, "allTest"]);
 

$router->get("/404", function(){
    echo "404";
});

$router->get("/user/filter-annonces", [HomeController::class, "filter"]);


$router->get('/test-all', callback: [AuthController::class, 'findByIdTest']);

$router->post("/add", function(){
    print_r($_POST);
});


$router->dispatch();
