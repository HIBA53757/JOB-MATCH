<?php

namespace app\controller\back;

use app\core\baseController;
use app\models\Annonce;

class AnnonceController extends baseController
{
    protected Annonce $annonce;

    public function __construct()
    {
        parent::__construct();
        $this->annonce = new Annonce();
    }

    
}