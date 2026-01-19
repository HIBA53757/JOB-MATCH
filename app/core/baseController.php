<?php
// what a basecontroller coud have:
// Rendering views
// Redirecting
// Shared helpers
// Middleware hooks
// Auth checks (generic) 


namespace app\core;

abstract class baseController
{

    protected $view;
    protected $security;
    protected $session;
    protected $validator;

    public function __construct()
    {
        $this->view = new View();
        $this->security = new Security();
        $this->session = Session::getInstance();
        $this->validator = new Validator();
    }

    public function view($viewName, $data = [])
    {
        $viewFile = __DIR__ . "/../views/" . $viewName . ".php";



        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        } else {
            echo "Error: view file not found";
        }
    }

     protected function json(array $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url, int $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

     protected function verifyCsrf()
    {
        if (!$this->security->verifyCsrfToken($_POST['_token'] ?? '')) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
            http_response_code(403);
        }
    }
}
