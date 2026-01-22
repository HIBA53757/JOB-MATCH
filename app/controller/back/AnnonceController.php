<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\Annonce;
use app\models\Company;

class AnnonceController extends baseController
{
    protected Annonce $annonce;
    protected Company $company;

    public function __construct()
    {
        parent::__construct();
        $this->annonce = new Annonce();
        $this->company = new Company();
    }

    public function renderPostForm(){
        $csrfToken = $this->security->generateCsrfToken();
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $companies = $this->company->display();
        $this->render("back/PostForm", ["csrf_token" => $csrfToken,
                                        'companies' => $companies,
                                        'announcement' => null,]);
    }

    public function renderPosts(){
        $csrfToken = $this->security->generateCsrfToken();
        $annonces = $this->annonce->findWithJoin("annonce.*, company.name AS company_name, company.id AS company_real_id" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 0' , "INNER");
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/posts", ["csrf_token" => $csrfToken, "annonces" => $annonces, "deleted" => "1", "active" => "inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg", "archive" => "inline-block p-4 text-gray-500 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg"]);
    }

    public function renderPostsArchived(){
        $csrfToken = $this->security->generateCsrfToken();
        $annonces = $this->annonce->findWithJoin("annonce.*, company.name AS company_name, company.id AS company_real_id" , "company" , 'annonce.company_id = company.id AND annonce.deleted = 1' , "INNER");
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        $this->render("back/posts", ["csrf_token" => $csrfToken, "annonces" => $annonces, "deleted" => "0", "active" => "inline-block p-4 text-gray-500 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg", "archive" => "inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg"]);
    }

    public function renderPostFormEdit(){
        $companies = $this->company->display();
        $csrfToken = $this->security->generateCsrfToken();
        if($this->session->get('user_role') !== "ADMIN"){
            $this->view->redirect('/login');
        }
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("back/posts", [
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
                $this->render("back/posts", ["csrf_token" => $csrfToken, "errors" => $errors]);
                die();
            }

            $id = $_POST['id'];
            echo $id;

            $PostData = $this->annonce->find($id);

        }
        $this->render("back/PostForm", ["csrf_token" => $csrfToken , "announcement" => $PostData , "action" => "/admin/post/saveEdit" , 'companies' => $companies]);
    }

    public function createPost(){
        $csrfToken = $this->security->generateCsrfToken();
        $companies = $this->company->display();
        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("back/PostForm", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   'companies' => $companies,]);
                die();
            }
        }

        $data = [
            "title" => $_POST['title'],
            "company_id" => $_POST['company_id'],
            "contract_type" => $_POST['contract_type'],
            "location" => $_POST['location'],
            "skills" => $_POST['skills'],
            "description" => $_POST['description']
        ];

        $rules = [
            "title" => "required",
            "company_id" => "required",
            "contract_type" => "required",
            "location" => "required",
            "skills" => "required",  
            "description" => "required",  
        ];

        if(!$this->validator->validate($data , $rules)){
            $errors = $this->validator->errors();
            $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, 'companies' => $companies,]);
            die();
        }

        $imageName = 'default_image.png'; 

        // مصفوفة الامتدادات المسموح بها
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            
            $name = basename($_FILES['image']['name']);
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            // 1. تحقق أمني: هل الامتداد مسموح به؟
            if (!in_array($extension, $allowedExtensions)) {
                $errors = ["image" => ["Type de fichier non pris en charge ! Veuillez télécharger un fichier (jpg, jpeg, png, webp)"]];
                $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data , 'companies' => $companies,]);
                die();
            }

            // 2. التحقق من الحجم (اختياري، مثلاً 2 ميجا)
            if ($_FILES['image']['size'] > 2000000) {
                $errors = ["image" => ["L'image fait plus de 2 Mo!"]];
                $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data , 'companies' => $companies,]);
                die();
            }

            // 3. الرفع الآمن
            $tmpName = $_FILES['image']['tmp_name'];
            $newName = uniqid() . '.' . $extension;
            $uploadDir = __DIR__ . '/../../../public/uploads/images/';
            
            // تأكد من وجود المجلد كما اتفقنا
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            if(move_uploaded_file($tmpName, $uploadDir . $newName)) {
                $imageName = $newName;
            } else {
                // في حالة فشل النقل لسبب ما في السيرفر
                $errors = ["image" => ["Erreur lors de l'envoi de l'image, veuillez réessayer."]];
                $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data , 'companies' => $companies,]);
                die();
            }

        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            // الأخطاء التقنية الأخرى
            $errors = ["image" => ["Erreur lors de l'envoi de l'image (Error Code: " . $_FILES['image']['error'] . ")"]];
            $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data , 'companies' => $companies,]);
            die();
        }

        $sendData = [
            "title" => $_POST['title'],
            "company_id" => $_POST['company_id'],
            "contract_type" => $_POST['contract_type'],
            "location" => $_POST['location'],
            "skills" => $_POST['skills'],
            "description" => $_POST['description'],
            "image" => $imageName
        ];

        $this->annonce->create($sendData);

        $this->renderPosts();
    }

    public function editPost(){
        $csrfToken = $this->security->generateCsrfToken();

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("back/PostForm", [
                                   "csrf_token" => $csrfToken,
                                   "errors" => $errors,
                                   "action" => "/admin/post/saveEdit"]);
                die();
            }
        }
        
        $id = $_POST['announce_id'];

        $currentPost = $this->annonce->find($id); 

        if (!$currentPost) {
            $this->view->redirect('/admin/posts');
        }

        $data = [
            "title" => $_POST['title'],
            "company_id" => $_POST['company_id'],
            "contract_type" => $_POST['contract_type'],
            "location" => $_POST['location'],
            "skills" => $_POST['skills'],
            "description" => $_POST['description']
        ];

        $rules = [
            "title" => "required",
            "company_id" => "required",
            "contract_type" => "required",
            "location" => "required",
            "skills" => "required",  
            "description" => "required",      
        ];

        if(!$this->validator->validate($data , $rules)){
            $errors = $this->validator->errors();
            $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/post/saveEdit"]);
            die();
        }

        $imageName = $currentPost['image'];

        // مصفوفة الامتدادات المسموح بها
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            
            $name = basename($_FILES['image']['name']);
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            // 1. تحقق أمني: هل الامتداد مسموح به؟
            if (!in_array($extension, $allowedExtensions)) {
                $errors = ["image" => ["Type de fichier non pris en charge ! Veuillez télécharger un fichier (jpg, jpeg, png, webp)"]];
                $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/post/saveEdit"]);
                die();
            }

            // 2. التحقق من الحجم (اختياري، مثلاً 2 ميجا)
            if ($_FILES['image']['size'] > 2000000) {
                $errors = ["image" => ["L'image fait plus de 2 Mo!"]];
                $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/post/saveEdit"]);
                die();
            }

            // 3. الرفع الآمن
            $tmpName = $_FILES['image']['tmp_name'];
            $newName = uniqid() . '.' . $extension;
            $uploadDir = __DIR__ . '/../../../public/uploads/images/';
            
            // تأكد من وجود المجلد كما اتفقنا
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            if(move_uploaded_file($tmpName, $uploadDir . $newName)) {
                $imageName = $newName;
            } else {
                // في حالة فشل النقل لسبب ما في السيرفر
                $errors = ["image" => ["Erreur lors de l'envoi de l'image, veuillez réessayer."]];
                $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/post/saveEdit"]);
                die();
            }

        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            // الأخطاء التقنية الأخرى
            $errors = ["image" => ["Erreur lors de l'envoi de l'image (Error Code: " . $_FILES['logo']['error'] . ")"]];
            $this->render("back/PostForm", ["csrf_token" => $csrfToken, "errors" => $errors, "old" => $data, "action" => "/admin/post/saveEdit"]);
            die();
        }

        $sendData = [
            "title" => $_POST['title'],
            "company_id" => $_POST['company_id'],
            "contract_type" => $_POST['contract_type'],
            "location" => $_POST['location'],
            "skills" => $_POST['skills'],
            "description" => $_POST['description'],
            "image" => $imageName
        ];

        $id = $_POST['announce_id'];

        $this->annonce->update($id , $sendData);

        $this->renderPosts();
    }

    public function archivePost(){
        $csrfToken = $this->security->generateCsrfToken();

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $tokenFromPost = $_POST['csrf_token'] ?? '';

            if (!$this->security->verifyCSRFToken($tokenFromPost)) {
                $errors =  ["csrf" => ["csrf token non valid!"]];
                $this->render("back/posts", ["title" => "welcome to register page" ,
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

            $sendData = [
                "deleted" => $_POST['deleted']
            ];

            if(!$this->validator->validate($data , $rules)){
                $errors = $this->validator->errors();
                $this->render("back/posts", ["csrf_token" => $csrfToken, "errors" => $errors]);
                die();
            }

            $id = $_POST["id"];
            $this->annonce->update($id , $sendData);

            if($_POST['deleted'] === '1'){
                $this->renderPosts();
            }else{
                $this->renderPostsArchived();
            }
            
        }
    }
}