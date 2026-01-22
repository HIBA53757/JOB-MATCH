<?php

namespace app\controller;

use app\core\baseController;
use app\models\user;

class AuthController extends baseController
{
    protected User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new user();
    }

    public function checkRole(){
        if($this->session->get('user_role') === "ADMIN"){
            $this->view->redirect('admin/dashboard');
        }else{
            $this->view->redirect('user/home');
        }
    }



    public function renderlogin()
    {
        $user_id = $this->session->get("user_id");
        if($user_id){
            $this->checkRole();
        }
        $csrfToken = $this->security->generateCsrfToken();
        $this->render("auth/login", ["title" => "welcome to login page",
                                "csrf_token" => $csrfToken]);
    }

    public function renderRegister()
    {
        $user_id = $this->session->get("user_id");
        if($user_id){
            $this->checkRole();
        }
        $csrfToken = $this->security->generateCsrfToken();
        $this->render("auth/register", ["title" => "welcome to register page" ,
                                   "csrf_token" => $csrfToken]);
    }

    public function createUser(){
        $csrfToken = $this->security->generateCsrfToken();

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("auth/register", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "old" => $_POST]);
                die();
            }

            $data = [
                "full_name" => $_POST['full_name'] ?? '',
                "email" => $_POST['email'] ?? '',
                "password" => $_POST['password'] ?? '',
                "password_confirmation" => $_POST['password_confirmation'] ?? ''
            ];

            $rules = [
                "full_name" => "required",
                "email" => "required|email|unique:users,email",
                "password" => "required|confirmed",
            ];

            if(!$this->validator->validate($data , $rules)){
                $errors = $this->validator->errors();
                $this->render("auth/register", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "old" => $data]);
                die();
            }
            
            $sendData = [
                "full_name" => $_POST['full_name'],
                "email" => $_POST['email'],
                "role" => "APPRENANT",
                "password_hash" => $this->security->hashPassword($_POST['password'])
            ];

            $this->user->create($sendData);

            $this->view->redirect('/login');
            exit();
        }
    }

    public function loginCheck(){
        $csrfToken = $this->security->generateCsrfToken();

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("auth/login", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "old" => $_POST]);
                die();
            }

            $data = [
                "email" => $_POST['email'] ?? '',
                "password" => $_POST['password'] ?? '',
            ];

            $rules = [
                "email" => "required|email",
                "password" => "required",
            ];

            if(!$this->validator->validate($data , $rules)){
                $errors = $this->validator->errors();
                $this->render("auth/login", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "old" => $data]);
                die();
            }

            $results = $this->user->where('email' , $_POST['email']);
            if(count($results) === 0){
                $errors =  ["email" => ["Adresse e-mail introuvable!"]];
                $this->render("auth/login", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "old" => $data]);
                die();
            }

            $password = $_POST['password'];
            $dbPassword = $results[0]['password_hash'];

            
            if(!$this->security->verifyPassword($password , $dbPassword)){
                $errors =  ["password" => ["Mot de passe incorrect!"]];
                $this->render("auth/login", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "old" => $data]);
                die();
            }



            
            $this->session->set("user_id" , $results[0]["id"]);
            $this->session->set("user_full_name" , $results[0]["full_name"]);
            $this->session->set("user_email" , $results[0]["email"]);
            $this->session->set("user_role" , $results[0]["role"]);
            
            $this->checkRole();
        }
   
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        $this->session->destroy();

        $this->view->redirect('/login');
        exit;
    }

}
