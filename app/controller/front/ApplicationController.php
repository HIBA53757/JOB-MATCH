<?php

namespace app\controller\front;

use app\core\baseController;
use app\models\Annonce;
use app\models\Application;
use app\models\Company;

class ApplicationController extends baseController
{       
    protected Application $application;
    protected Annonce $annonce;
    protected Company $company;


    public function __construct()
    {
        parent::__construct();
        $this->application = new Application();
        $this->annonce = new Annonce();
        $this->company = new Company();
    }

    public function saveMotivation(){
        $csrfToken = $this->security->generateCsrfToken();
        $user_id = $this->session->get("user_id");
        $annonce_id = $_POST['announcement_id'];
        $annonce = $this->annonce->find($annonce_id);
        $skills = $annonce['skills'];
        $skillsSparator = explode("," , $skills);
        $company = $this->company->find($_POST['company_id']);
        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("front/postDetails", ["annonce" => $annonce , "company" => $company , "skills" => $skillsSparator , "user_id" => $user_id , "csrf_token" => $csrfToken]);
                die();
            }

            $data = [
                "announcement_id" => $_POST['announcement_id'] ?? '',
                "user_id" => $_POST['user_id'] ?? '',
                "motivation" => $_POST['motivation'] ?? '',
                "cv" => $_POST['cv'] ?? ''
            ];

            $data = [
                "announcement_id" => $_POST['announcement_id'] ?? '',
                "user_id" => $_POST['user_id'] ?? '',
                "motivation" => $_POST['motivation'] ?? '',
                "cv_path" => $_POST['cv'] ?? ''
            ];

            $rules = [
                "announcement_id" => "required|numeric",
                "user_id" => "required|numeric",
                "motivation" => "required"
            ];

            if(!$this->validator->validate($data , $rules)){
                $errors = $this->validator->errors();
                $this->render("front/postDetails", ["annonce" => $annonce , "company" => $company , "skills" => $skillsSparator , "user_id" => $user_id , "csrf_token" => $csrfToken]);
                die();
            }

            $this->application->create($data);
            $this->render("front/postDetails", ["annonce" => $annonce , "company" => $company , "skills" => $skillsSparator , "user_id" => $user_id , "csrf_token" => $csrfToken]);
        }
    }
}