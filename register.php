
<?php
require_once("config.php");

$db_obj = new DB_con();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $result = $db_obj->register(
        $name,
        $email,
        $password
    );

    if ($result) {
        $message = "Registration Successful";
    } else {
        $message = "Email already exists";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="style.css">
    <title>Student Registration</title>
</head>
<body>

    <h2>Student Registration</h2>

    <p><?php echo $message; ?></p>

    <form action="register.php" method="POST">

        <label>Name</label>
        <input type="text" name="name" required>
        <br><br>
        <label>Email</label>
        <input type="email" name="email" required>
        <br><br>

        <label>Password</label>
        <input type="password" name="password" required minlength="8">
        <br><br>

        <button type="submit">Register</button>
    </form>
      <p>
       have an account?
        <a href="login.php">Login</a>
    </p>


</body>
</html>
