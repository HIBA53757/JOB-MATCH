<?php

namespace app\controller\front;

use app\core\baseController;
use app\models\Annonce;

class HomeController extends baseController
{       
    protected Annonce $annonce;

    public function __construct()
    {
        parent::__construct();
        $this->annonce = new Annonce();
    }
        public function renderHome(){
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $annonces = $this->annonce->findWithJoin("*" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 0' , "INNER");
        $this->render("front/home", ['annonces' => $annonces]);
    }
}
