<?php

namespace app\controller\front;

use app\core\baseController;
use app\models\Annonce;
use app\models\Company;
use app\models\Application;

class HomeController extends baseController
{       
    protected Annonce $annonce;

    protected Company $company;

    protected Application $application;


    public function __construct()
    {
        parent::__construct();
        $this->annonce = new Annonce();
        $this->company = new Company();
        $this->application = new Application();
    }
        public function renderHome(){
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $annonces = $this->annonce->findWithJoin("annonce.*, company.name AS company_name, company.id AS company_real_id" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 0' , "INNER");
        $this->render("front/home", ['annonces' => $annonces]);
    }

        public function renderPostDetails(){
        $csrfToken = $this->security->generateCsrfToken();
        $user_id = $this->session->get("user_id");
        $result = $this->application->countWhere("user_id" , $user_id);
        $annonce_id = $_POST['annonce_id'];
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $annonce = $this->annonce->find($annonce_id);

        $skills = $annonce['skills'];
        $skillsSparator = explode("," , $skills);
        
        $company = $this->company->find($annonce['company_id']);
        $this->render("front/postDetails", ["annonce" => $annonce , "company" => $company , "skills" => $skillsSparator , "user_id" => $user_id , "csrf_token" => $csrfToken , "result" => $result]);
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

    public function renderDemand(){
        $user_id = $this->session->get("user_id");
        $application = $this->application->findWithJoinStatique("SELECT 
                                                    applications.*, annonce.title, annonce.contract_type, 
                                                    company.name AS company_name, company.logo AS company_logo 
	                                                FROM applications 
                                                    INNER JOIN annonce ON applications.announcement_id = annonce.id 
                                                    INNER JOIN company ON annonce.company_id = company.id
                                                    WHERE applications.user_id = " . $user_id ." ;");
        if($this->session->get('user_role') !== "APPRENANT"){
            $this->view->redirect('/login');
        }
        $this->render("front/demand", ["myApplications" => $application]);
    }

    public function filter()
{
    if ($this->session->get('user_role') !== "APPRENANT") {
        http_response_code(403);
        exit;
    }

    $company  = $_GET['company'] ?? null;
    $contract = $_GET['contract'] ?? null;

    $conditions = "annonce.company_id = company.id AND annonce.deleted = 0";

    if (!empty($company)) {
        $conditions .= " AND company.name = '$company'";
    }

    if (!empty($contract)) {
        $conditions .= " AND annonce.contract_type = '$contract'";
    }

    $annonces = $this->annonce->findWithJoin(
        "annonce.*, company.name AS company_name, company.id AS company_real_id",
        "company",
        $conditions,
        "INNER"
    );

   
    $this->render("front/partials/jobs", [
        "annonces" => $annonces
    ]);
}
   public function search()
{
    if ($this->session->get('user_role') !== "APPRENANT") {
        http_response_code(403);
        exit;
    }

    $keyword = $_GET['keyword'] ?? '';

    $conditions = "annonce.company_id = company.id AND annonce.deleted = 0";

    if (!empty($keyword)) {
        // basic search in title, skills, or description
        $keywordEscaped = addslashes($keyword);
        $conditions .= " AND (annonce.title LIKE '%$keywordEscaped%' 
                             OR annonce.skills LIKE '%$keywordEscaped%' 
                             OR annonce.description LIKE '%$keywordEscaped%')";
    }

    $annonces = $this->annonce->findWithJoin(
        "annonce.*, company.name AS company_name, company.id AS company_real_id",
        "company",
        $conditions,
        "INNER"
    );

    // render the same partial as filters
    $this->render("front/partials/jobs", [
        "annonces" => $annonces
    ]);
}

}
