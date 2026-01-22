<?php

namespace app\controller\front;

use app\core\baseController;
use app\models\home;

class homeControllerController extends baseController
{
    public function index()
    {
        // Fake data pour test
        $annonces = [
            [
                'id' => 1,
                'title' => 'Développeur PHP',
                'description' => 'Développement de sites web',
                'contract_type' => 'CDI',
                'location' => 'Casablanca',
                'skills' => 'PHP, MySQL',
                'company_name' => 'YouCode',
                'image' => 'default.png'
            ],
            [
                'id' => 2,
                'title' => 'Designer UI/UX',
                'description' => 'Création d’interfaces modernes',
                'contract_type' => 'Stage',
                'location' => 'Rabat',
                'skills' => 'Figma, Photoshop',
                'company_name' => 'TechCorp',
                'image' => 'default.png'
            ]
        ];

        // Passer les annonces à Twig
        $this->render('front/jobs/index', ['annonces' => $annonces]);
    }
}
