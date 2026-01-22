<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\Annonce;

class AnnonceController extends baseController
{
    protected Annonce $annonce;

    public function __construct()
    {
        parent::__construct();
        $this->annonce = new Annonce();
    }

    public function renderPostForm(){
        $csrfToken = $this->security->generateCsrfToken();
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/PostForm", ["csrf_token" => $csrfToken]);
    }

    public function renderPosts(){
        $annonces = $this->annonce->findWithJoin("*" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 0' , "INNER");
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/Posts", ["annonces" => $annonces]);
    }

    public function renderPostsArchived(){
        $annonces = $this->annonce->findWithJoin("*" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 1' , "INNER");
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/Posts", ["annonces" => $annonces]);
    }
}