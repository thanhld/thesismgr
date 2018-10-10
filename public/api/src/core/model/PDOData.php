<?php
namespace core\model;

use \PDO;
use PDOException;

class PDOData
{
    private $conn;

    public function __construct() {}

    /**
     * Construct connection function
     */
    public function connect()
    {
        // Connect to database
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=thesismgr;charset=utf8', 'root', 'root');
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
        return $this->conn;
    }

    /**
     * Disconnect function
     */
    public function disconnect()
    {
        // Disconnect to database
        try {
            $this->conn = null;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function __destruct() {
        $this->disconnect();
    }
}
