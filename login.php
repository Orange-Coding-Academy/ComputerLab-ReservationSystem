
<?php
require_once("config.php");

$db_obj = new DB_con();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $result = $db_obj->Login($email, $password);

    if ($result) {

        session_regenerate_id(true);

        $_SESSION['id'] = $result['id'];
        $_SESSION['name'] = $result['name'];
        $_SESSION['role'] = $result['role'];

        if ($result['role'] == "admin") {
            header("Location: adminDashboard.php");
            exit;
        } else {
            header("Location: studentDashboard.php");
            exit;
        }

    } else {
        $message = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>Login</h2>

    <p><?php echo $message; ?></p>

    <form action="login.php" method="POST">

        <label>Email</label>
        <input type="email" name="email" required>
        <br><br>

        <label>Password</label>
        <input type="password" name="password" required>
        <br><br>

        <button type="submit">Login</button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register</a>
    </p>

</body>
</html>
