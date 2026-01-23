<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\Company;
use app\models\Annonce;
use app\models\user;
use app\models\Application;

class DashboardController extends baseController
{
    protected $company;
    protected $annonce;
    protected $user;
    protected $application;

    public function __construct()
    {
        parent::__construct();
        $this->company = new Company();
        $this->annonce = new Annonce();
        $this->user = new user();
        $this->application = new Application();
    }

    public function renderDashboard(){
        $csrfToken = $this->security->generateCsrfToken();
        $annonces = $this->annonce->findWithJoin("*" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 0 ORDER BY annonce.created_at DESC LIMIT 10' , "INNER");
        $annonces_active = $this->annonce->countWhere("deleted" , "0");
        $annonces_archive = $this->annonce->countWhere("deleted" , "1");
        $users_num = $this->user->countWhere("role" , "APPRENANT");
        $company_num = $this->company->count();
        $applications = $this->application->findWithJoinStatique("SELECT 
                                                                applications.*, 
                                                                annonce.title, 
                                                                company.name AS company_name, 
                                                                company.logo AS company_logo,
                                                                users.full_name AS user_name,
                                                                annonce.location AS annonce_location
                                                                FROM applications 
                                                                INNER JOIN annonce ON applications.announcement_id = annonce.id 
                                                                INNER JOIN company ON annonce.company_id = company.id
                                                                INNER JOIN users ON applications.user_id = users.id
                                                                ORDER BY applications.created_at DESC
                                                                LIMIT 20;");
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/dashboard", ["users_num" => $users_num,
                                         "company_num" => $company_num,
                                        "annonces_active" => $annonces_active,
                                        "annonces" => $annonces,
                                        "annonces_archive" => $annonces_archive,
                                        "applications" => $applications,
                                        "csrf_token" => $csrfToken
                                        ]);
    }

    public function updateAction(){
        $csrfToken = $this->security->generateCsrfToken();
        $tokenFromPost = $_POST['csrf_token'] ?? '';

        if (!$this->security->verifyCSRFToken($tokenFromPost)) {
            $errors =  ["csrf" => ["csrf token non valid!"]];
            $this->renderDashboard();
            die();
        }

        $id = $_POST["id"];
        $status = $_POST["status"];

        $this->application->update($id , ["status" => $status]);
        $this->renderDashboard();
    }
}