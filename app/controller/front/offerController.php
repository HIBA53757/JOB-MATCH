<?php
namespace App\Controllers\Front;

use App\Models\Offer;
use App\Core\BaseController;

class OfferController extends BaseController
{
    private $offer;

    public function __construct()
    {
        parent::__construct();
        $this->offer = new Offer();
    }

    public function searchAjax()
    {
        $keyword  = $_GET['keyword'] ?? '';
        $contract = $_GET['contract'] ?? '';

        $offers = $this->offer->searchActiveOffers($keyword, $contract);

        // IMPORTANT: render(), not renderPartial()
        echo $this->render('front/partials/offers.twig', [
        'offers' => $offers
        ]);
    }
}

