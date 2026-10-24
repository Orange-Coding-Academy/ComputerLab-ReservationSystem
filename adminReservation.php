
<?php
require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $status = $_POST['status'];

    if ($reservation_id > 0 &&
        $db_obj->updateReservationStatus($reservation_id, $status)) {

        $message = "Reservation status updated successfully.";
    } else {
        $message = "Unable to update reservation status.";
    }
}

$sql = $db_obj->getAllReservations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Requests</title>
     <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>

<h2>Reservation Requests</h2>

<p class="p">
    <a href="adminDashboard.php">Students</a> |
    <a href="adminReservation.php">Reservation Requests</a> |
    <a href="adminSession.php">Manage Sessions</a>|
     <a href="logout.php">Logout</a>
</p>

<?php if ($message != "") { ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php } ?>

<table border="1" cellpadding="8">
    <tr>
        <th>Student</th>
        <th>Email</th>
        <th>Lab</th>
        <th>Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
            <td><?php echo htmlspecialchars($row['session_date']); ?></td>
            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
            <td><?php echo htmlspecialchars($row['end_time']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>

          
<td>
    <?php if ($row['status'] == 'pending') { ?>
<div style="display: flex; align-items: center; gap: 10px;">
        <form method="POST" action="adminReservation.php">
            <input type="hidden" name="reservation_id"
                   value="<?php echo $row['reservation_id']; ?>">
            <input type="hidden" name="status" value="confirmed">
            <button type="submit" name="update" id ="green"><i class="fa-solid fa-check"></i></button>
        </form>

        <form method="POST" action="adminReservation.php">
            <input type="hidden" name="reservation_id"
                   value="<?php echo $row['reservation_id']; ?>">
            <input type="hidden" name="status" value="rejected">
            <button type="submit" name="update" class="del"><i class="fa-solid fa-xmark"></i></button>
        </form>

    <?php } else { ?>
        No Action
    <?php } ?>
</div>
</td>

        </tr>
    <?php } ?>
</table>

</body>
</html>
