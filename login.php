
<?php
require_once "config.php";

$db = new DB_con();

if (isset($_POST["login"])) {
    $db->Login(
        $_POST["email"] ?? "",
        $_POST["password"] ?? ""
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student login </title>
</head>
<body>

    <h2>Login :</h2>

    <form method="POST" action="">

        <label>Email:</label>
        <input type="email" name="email" required>
        <br><br>

        <label>Password:</label>
        <input type="password" name="password" required>
        <br><br>

        <button type="submit" name="login">Login</button>

    </form>
</body>
</html>
