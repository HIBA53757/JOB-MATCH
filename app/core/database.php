<?php
namespace app\core;
use PDO;
use PDOException;

class Database
{
    private $db;
    private static $instance = null;

    private function __construct()
    {
        try {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=router",
                "root",
                "" 
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function connection()
    {
        return $this->db;
    }
}