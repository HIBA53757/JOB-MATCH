<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\User;

class UserController extends baseController
{
    protected User $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
    }

    public function renderUsers(){
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $users = $this->user->where("role" , "APPRENANT");

        $this->render("back/Users", ["users" => $users]);
    }
}