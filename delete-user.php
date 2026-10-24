
<?php
require_once "config.php";

$db = new DB_con();

if (isset($_POST["delete"]) && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    if ($db->deleteUser($id)) {
        $_SESSION["message"] = "User deleted successfully.";
    } else {
        $_SESSION["message"] = "Could not delete user.";
    }
}

header("Location: admin-dashboard.php");
exit;
?>
