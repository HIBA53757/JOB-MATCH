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

    public function find($id)
    {
        // echo "test. ID = $id";

        $data = [
            ["name" => "hiba"],
            ["name" => "yassmin"],
            ["name" => "souad"],
            ["name" => "rim"],
            ["name" => "nada"],
        ];

        $this->view("user", ["users" => $data, "id" => $id]);
    }

    //create test
    public function createTest()
    {
        $user = new user();

        $user->create([
            'name' => 'Test User',
            'email' => 'test@mail.com',
            'password' => '123456'
        ]);

        echo "User success!";
    }

    public function checkRole(){
        if($this->session->get('user_role') === "ADMIN"){
            $this->view->redirect('/dashboard');
        }else{
            $this->view->redirect('/home');
        }
    }



    public function renderlogin()
    {
        $csrfToken = $this->security->generateCsrfToken();
        $this->render("auth/login", ["title" => "welcome to login page",
                                "csrf_token" => $csrfToken]);
    }

    public function renderRegister()
    {
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
                $this->render("auth/register", ["title" => "welcome to register page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
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
                $this->render("auth/register", ["title" => "welcome to register page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
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
                $this->render("auth/login", ["title" => "welcome to register page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
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
                $this->render("auth/login", ["title" => "welcome to login page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
                die();
            }

            $results = $this->user->where('email' , $_POST['email']);
            if(count($results) === 0){
                $errors =  ["email" => ["Adresse e-mail introuvable!"]];
                $this->render("auth/login", ["title" => "welcome to login page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
                die();
            }

            $password = $_POST['password'];
            $dbPassword = $results[0]['password_hash'];

            
            if(!$this->security->verifyPassword($password , $dbPassword)){
                $errors =  ["password" => ["Mot de passe incorrect!"]];
                $this->render("auth/login", ["title" => "welcome to login page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
                die();
            }



            
            $this->session->set("user_id" , $results[0]["id"]);
            $this->session->set("user_full_name" , $results[0]["full_name"]);
            $this->session->set("user_email" , $results[0]["email"]);
            $this->session->set("user_role" , $results[0]["role"]);
            
            $this->checkRole();
        }
   
    }

    public function renderDashboard(){
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/dashboard", ["title" => "welcome to admin office"]);
    }

    public function renderHome(){
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $this->render("front/home", ["title" => "welcome to user office"]);
    }

}
