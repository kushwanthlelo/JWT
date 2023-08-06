<?php

class Database{
    //specify the database credentials
    private $host = 'localhost';
    private $db_name = 'apidb';
    private $username = 'root';
    private $password = 'Chandu@1299';
    public $conn ;

    // get db connection
    public function getConnection() {
        $this->conn = NULL;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name ,
            $this->username, $this->password);
            $this->conn->exec("set names utf8");
            // $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXECPTION);
        }catch( PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
            die();
        }
        return $this->conn;
    }
}