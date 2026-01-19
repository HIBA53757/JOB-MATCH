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

    //all test

    public function allTest()
    {
        $user = new user();
        var_dump($user);

        echo "<pre>";
        // print_r($users);
        echo "</pre>";
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
    //-------------------------------------------------

   

    public function testTwifg()
    {
        $this->render("login", ["title" => "welcome to login page"]);
    }
}
