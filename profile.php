<?php

require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $result = $db_obj->updateProfile(
        $_SESSION['id'],
        $name,
        $email
    );

    if ($result == "success") {
        $_SESSION['name'] = $name;
        $message = "Profile updated successfully.";
    } elseif ($result == "exists") {
        $message = "Email or Student Number already exists.";
    } else {
        $message = "Something went wrong.";
    }
}

$user = $db_obj->getProfile($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>My Profile</h2>
<p>
    <a href="studentDashboard.php">Available Sessions</a> |
    <a href="my-sessions.php">My Sessions</a> |
    <a href="logout.php">Logout</a>
</p>

</p>

<?php if ($message != "") { ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php } ?>

<?php if ($user) { ?>

    <form method="POST" action="profile.php">

        <label>Name:</label>
        <input type="text" name="name"
               value="<?php echo htmlspecialchars($user['name']); ?>"
               required>
        <br><br>

        <label>Email:</label>
        <input type="email" name="email"
               value="<?php echo htmlspecialchars($user['email']); ?>"
               required>
        <br><br>

        <button type="submit" name="update">Update Profile</button>

    </form>

<?php } else { ?>

    <p>Student profile not found.</p>

<?php } ?>

</body>
</html>