
<?php
require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {

    $id = intval($_POST['id']);

    if ($id > 0 && $db_obj->deleteStudent($id)) {
        $message = "Student deleted successfully.";
    } else {
        $message = "Unable to delete student.";
    }
}

$sql = $db_obj->getStudents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">
     <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Admin Dashboard</h2>

<p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></p>

<p class="p">
    <a href="adminDashboard.php">Students</a> |
    <a href="adminReservation.php">Reservation Requests</a> |
    <a href="adminSession.php">Manage Sessions</a> |
     <a href="logout.php">Logout</a>
</p>

<hr>

<h3>Students Management</h3>

<?php if ($message != "") { ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php } ?>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>

            <td>
                <?php echo htmlspecialchars($row['name']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['email']); ?>
            </td>

            <td>
                <form method="POST" action="adminDashboard.php">
                    <input type="hidden" name="id"
                           value="<?php echo $row['id']; ?>">

                    <button type="submit" name="delete" class="del">
                <i class="fa-regular fa-trash-can"></i>
            </button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html> 
