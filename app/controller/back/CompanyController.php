<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\Company;

class CompanyController extends baseController
{
    protected Company $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new Company();
    }

    public function createCompany(){
        echo "create company";
    }
}