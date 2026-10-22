
<?php
require_once "config.php";

$db = new DB_con();

if (isset($_POST["register"])) {
    $db->register(
        $_POST["name"] ?? "",
        $_POST["email"] ?? "",
        $_POST["password"] ?? ""
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
</head>
<body>

    <h2>Student Registration</h2>

    <form method="POST" action="">

        <label>Name:</label>
        <input type="text" name="name" required>
        <br><br>

        <label>Email:</label>
        <input type="email" name="email" required>
        <br><br>

        <label>Password:</label>
        <input type="password" name="password" required>
        <br><br>

        <button type="submit" name="register">Register</button>

    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>

</body>
</html>
