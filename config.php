<?php
declare(strict_types=1);
session_start();
define("db_host","localhost");
define("db_root","root");
define("db_name","computer-lab-reservation");
define("db_pass","");

class DB_con{

    public $db;
     public function 
    __construct(){
        try{
             $con = new PDO(
                "mysql:host=" . db_host . ";dbname=" . db_name,
                db_root,
                db_pass
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

public function register ($name,$email,$password){
   

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $name = trim($_POST["name"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $password = $_POST["password"] ?? "";

            if ($name == "" || $email == "" || $password == "") {
                echo "All fields are required";
                return false;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email";
                return false;
            }

            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                echo "User already registered";
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try{
      $sql = "INSERT INTO users (name, email, password, role)
                        VALUES (?, ?, ?, 'student')";

                $stmt1 = $this->db->prepare($sql);
                $stmt1->execute([
                    $name,
                    $email,
                    $hashedPassword
                ]);
                  echo "Registration successful";
            return true;

}
catch (PDOException $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                echo "Registration failed. Please try again.";
                return false;
         }

}

}
/////////////////////////////////////////////

public function Login($email, $password)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";

        if ($email == "" || $password == "") {
            echo "All fields are required";
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email";
            return false;
        }

        $sql = "SELECT id, password FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            echo "Invalid email or password";
            return false;
        } else {
            header("Location: dashboard.php");
            exit;
        }
    }
}





}
?>