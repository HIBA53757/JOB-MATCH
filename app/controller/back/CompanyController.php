<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\Company;

class CompanyController extends baseController
{
    protected Company $company;

    public function __construct()
    {
        parent::__construct();
        $this->company = new Company();
    }

    public function renderCompanies(){
        $csrfToken = $this->security->generateCsrfToken();
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $companies = $this->company->display();
        $this->render("back/Companies", ["csrf_token" => $csrfToken, "companies" => $companies]);
    }

    public function renderCompanyForm(){
    $csrfToken = $this->security->generateCsrfToken();
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/companyForm", ["csrf_token" => $csrfToken]);
    }

    public function renderCompanyFormEdit(){
    $csrfToken = $this->security->generateCsrfToken();
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("back/Companies", ["title" => "welcome to register page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
                die();
            }

            $data = [
                "id" => $_POST['id'],
            ];

            $rules = [
                "id" => "numeric"
            ];

            if(!$this->validator->validate($data , $rules)){
                $errors = $this->validator->errors();
                $this->render("back/Companies", ["csrf_token" => $csrfToken, "errors" => $errors]);
                die();
            }

            $id = $_POST['id'];

            $companyData = $this->company->find($id);
        }
        $this->render("back/companyForm", ["csrf_token" => $csrfToken , "company" => $companyData , "action" => "/admin/company/saveEdit"]);
    }

    public function createCompany(){
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
        }

        $data = [
            "name" => $_POST['name'],
            "secteur" => $_POST['secteur'],
            "location" => $_POST['location'],
            "email" => $_POST['email'],
            "telephone" => $_POST['telephone']
        ];

        $rules = [
            "name" => "required",
            "secteur" => "required",
            "location" => "required",
            "email" => "required|email|unique:company,email",
            "telephone" => "required"      
        ];

        if(!$this->validator->validate($data , $rules)){
            $errors = $this->validator->errors();
            $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data]);
            die();
        }

        $logoName = 'default_logo.png'; 

        // مصفوفة الامتدادات المسموح بها
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            
            $name = basename($_FILES['logo']['name']);
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            // 1. تحقق أمني: هل الامتداد مسموح به؟
            if (!in_array($extension, $allowedExtensions)) {
                $errors = ["logo" => ["Type de fichier non pris en charge ! Veuillez télécharger un fichier (jpg, jpeg, png, webp)"]];
                $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data]);
                die();
            }

            // 2. التحقق من الحجم (اختياري، مثلاً 2 ميجا)
            if ($_FILES['logo']['size'] > 2000000) {
                $errors = ["logo" => ["L'image fait plus de 2 Mo!"]];
                $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data]);
                die();
            }

            // 3. الرفع الآمن
            $tmpName = $_FILES['logo']['tmp_name'];
            $newName = uniqid() . '.' . $extension;
            $uploadDir = __DIR__ . '/../../../public/uploads/logos/';
            
            // تأكد من وجود المجلد كما اتفقنا
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            if(move_uploaded_file($tmpName, $uploadDir . $newName)) {
                $logoName = $newName;
            } else {
                // في حالة فشل النقل لسبب ما في السيرفر
                $errors = ["logo" => ["Erreur lors de l'envoi de l'image, veuillez réessayer."]];
                $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data]);
                die();
            }

        } elseif ($_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            // الأخطاء التقنية الأخرى
            $errors = ["logo" => ["Erreur lors de l'envoi de l'image (Error Code: " . $_FILES['logo']['error'] . ")"]];
            $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data]);
            die();
        }

        $sendData = [
            "name" => $_POST['name'],
            "secteur" => $_POST['secteur'],
            "location" => $_POST['location'],
            "email" => $_POST['email'],
            "telephone" => $_POST['telephone'],
            "logo" => $logoName
        ];

        $this->company->create($sendData);

        $this->renderCompanies();

    }

    public function editCompany(){
        $csrfToken = $this->security->generateCsrfToken();

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("auth/register", ["title" => "welcome to register page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "action" => "/admin/company/saveEdit"]);
                die();
            }
        }
        
        $id = $_POST['id'];

        $currentCompany = $this->company->find($id); 

        if (!$currentCompany) {
            $this->view->redirect('/admin/companies');
        }

        $emailRule = "required|email";

        
        if ($_POST['email'] !== $currentCompany['email']) {
            $emailRule .= "|unique:company,email";
        }

        $data = [
            "name" => $_POST['name'],
            "secteur" => $_POST['secteur'],
            "location" => $_POST['location'],
            "email" => $_POST['email'],
            "telephone" => $_POST['telephone']
        ];

        $rules = [
            "name" => "required",
            "secteur" => "required",
            "location" => "required",
            "email" => $emailRule,
            "telephone" => "required"      
        ];

        if(!$this->validator->validate($data , $rules)){
            $errors = $this->validator->errors();
            $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/company/saveEdit"]);
            die();
        }

        $logoName = $currentCompany['logo'];

        // مصفوفة الامتدادات المسموح بها
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            
            $name = basename($_FILES['logo']['name']);
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            // 1. تحقق أمني: هل الامتداد مسموح به؟
            if (!in_array($extension, $allowedExtensions)) {
                $errors = ["logo" => ["Type de fichier non pris en charge ! Veuillez télécharger un fichier (jpg, jpeg, png, webp)"]];
                $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/company/saveEdit"]);
                die();
            }

            // 2. التحقق من الحجم (اختياري، مثلاً 2 ميجا)
            if ($_FILES['logo']['size'] > 2000000) {
                $errors = ["logo" => ["L'image fait plus de 2 Mo!"]];
                $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/company/saveEdit"]);
                die();
            }

            // 3. الرفع الآمن
            $tmpName = $_FILES['logo']['tmp_name'];
            $newName = uniqid() . '.' . $extension;
            $uploadDir = __DIR__ . '/../../../public/uploads/logos/';
            
            // تأكد من وجود المجلد كما اتفقنا
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            if(move_uploaded_file($tmpName, $uploadDir . $newName)) {
                $logoName = $newName;
            } else {
                // في حالة فشل النقل لسبب ما في السيرفر
                $errors = ["logo" => ["Erreur lors de l'envoi de l'image, veuillez réessayer."]];
                $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/company/saveEdit"]);
                die();
            }

        } elseif ($_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            // الأخطاء التقنية الأخرى
            $errors = ["logo" => ["Erreur lors de l'envoi de l'image (Error Code: " . $_FILES['logo']['error'] . ")"]];
            $this->render("back/companyForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/company/saveEdit"]);
            die();
        }

        $sendData = [
            "name" => $_POST['name'],
            "secteur" => $_POST['secteur'],
            "location" => $_POST['location'],
            "email" => $_POST['email'],
            "telephone" => $_POST['telephone'],
            "logo" => $logoName
        ];

        $id = $_POST['id'];

        $this->company->update($id , $sendData);

        $this->renderCompanies();
    }

    public function deleteCompany(){
        $csrfToken = $this->security->generateCsrfToken();

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("back/Companies", ["title" => "welcome to register page" ,
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors]);
                die();
            }

            $data = [
                "id" => $_POST['id'],
            ];

            $rules = [
                "id" => "numeric"
            ];

            if(!$this->validator->validate($data , $rules)){
                $errors = $this->validator->errors();
                $this->render("back/Companies", ["csrf_token" => $csrfToken, "errors" => $errors]);
                die();
            }

            $id = $_POST['id'];

            $this->company->delete($id);

            $this->renderCompanies();
        }
    }
}