<?php
(strict_types=1);
session_start();
define("db-host","localhost");
define("db-root","root");
define("db-name","computer-lap-reservation");
define("db-pass","");

class DB-con{

    public $db;
     public function 
    __construct(){
        try{
             $con = new PDO(
                "mysql:host=" . db-host . ";dbname=" . db_name,
                db_root,
                pass
            );

             $con->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->db = $con;
          }
         catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
           }

}

?>