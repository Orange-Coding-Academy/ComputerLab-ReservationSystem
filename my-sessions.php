<?php
require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();

$message = "";

if (isset($_SESSION['booking_message'])) {
    $message = $_SESSION['booking_message'];
    unset($_SESSION['booking_message']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel'])) {
    $reservation_id = intval($_POST['reservation_id']);

    $result = $db_obj->cancelReservation(
        $_SESSION['id'],
        $reservation_id
    );

    if ($result) {
        $message = "The reservation was canceled.";
    } else {
        $message = "Error .";
    }
}

$sql = $db_obj->getMySessions($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Sessions</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>My Sessions</h2>

<p class="p">
    <a href="studentDashboard.php">Available Sessions</a> |
     <a href="my-sessions.php">My Sessions</a> |
    <a href="profile.php">My Profile</a> 
</p>

<?php if ($message != "") { ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php } ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Lab</th>
        <th>Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
            <td><?php echo htmlspecialchars($row['session_date']); ?></td>
            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
            <td><?php echo htmlspecialchars($row['end_time']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <?php
                if ($row['status'] == 'pending' ||
                    $row['status'] == 'confirmed') {
                ?>
                    <form method="POST" action="my-sessions.php">
                        <input type="hidden" name="reservation_id"
                               value="<?php echo $row['reservation_id']; ?>">
                        <button type="submit" name="cancel" class="del">
                            Cancel Reservation
                        </button>
                    </form>
                <?php } elseif ($row['status'] == 'cancelled') { ?>
                    Cancelled
                <?php } else { ?>
                    No Action
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>