<?php

namespace app\controller\front;

use app\core\baseController;
use app\models\Annonce;
use app\models\Company;

class HomeController extends baseController
{       
    protected Annonce $annonce;

    protected Company $company;


    public function __construct()
    {
        parent::__construct();
        $this->annonce = new Annonce();
        $this->company = new Company();
    }
        public function renderHome(){
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $annonces = $this->annonce->findWithJoin("annonce.*, company.name AS company_name, company.id AS company_real_id" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 0' , "INNER");
        $this->render("front/home", ['annonces' => $annonces]);
    }

        public function renderPostDetails(){
        $annonce_id = $_POST['annonce_id'];
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $annonce = $this->annonce->find($annonce_id);

        $skills = $annonce['skills'];
        $skillsSparator = explode("," , $skills);
        
        $company = $this->company->find($annonce['company_id']);
        $this->render("front/postDetails", ["annonce" => $annonce , "company" => $company , "skills" => $skillsSparator]);
    }
    
    public function renderCompanyDetails(){
        $company_id = $_POST['company_id'];
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $company = $this->company->find($company_id);
        $annonces = $this->annonce->where("company_id" , $company_id);
        $this->render("front/companyDetails", ["company" => $company , "annonces" => $annonces]);
    }
}
