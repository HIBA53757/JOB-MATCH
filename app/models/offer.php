<?php
namespace App\Models;

use App\Core\Database;

class Offer
{
    private $db;

    public function __construct()
    {
        $this->db = Database::get_instance();
    }

    public function searchActiveOffers(string $keyword, string $contract): array
    {
        $sql = "
            SELECT o.*, c.name AS company_name
            FROM offers o
            JOIN companies c ON c.id = o.company_id
            WHERE o.is_active = 1
        ";

        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (
                o.title LIKE :keyword
                OR o.description LIKE :keyword
                OR c.name LIKE :keyword
            )";
            $params['keyword'] = "%$keyword%";
        }

        if (!empty($contract)) {
            $sql .= " AND o.contract_type = :contract";
            $params['contract'] = $contract;
        }

        $sql .= " ORDER BY o.created_at DESC";

        return $this->db->query($sql, $params);
    }
}
