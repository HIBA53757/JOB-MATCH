<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\Company;
use app\models\Annonce;
use app\models\user;

class DashboardController extends baseController
{
    protected $company;
    protected $annonce;
    protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->company = new Company();
        $this->annonce = new Annonce();
        $this->user = new user();
    }

    public function renderDashboard(){
        $annonces = $this->annonce->findWithJoin("*" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 0' , "INNER");
        $annonces_active = $this->annonce->countWhere("deleted" , "0");
        $annonces_archive = $this->annonce->countWhere("deleted" , "1");
        $users_num = $this->user->countWhere("role" , "APPRENANT");
        $company_num = $this->company->count();
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/dashboard", ["users_num" => $users_num,
                                         "company_num" => $company_num,
                                        "annonces_active" => $annonces_active,
                                        "annonces" => $annonces,
                                        "annonces_archive" => $annonces_archive
                                        ]);
    }
}