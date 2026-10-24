<?php

require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book'])) {
    $session_id = intval($_POST['session_id']);

    $result = $db_obj->bookSession($_SESSION['id'], $session_id);

if ($result == "success") {
    $_SESSION['booking_message'] = "Your booking request has been submitted successfully.";
} elseif ($result == "already_booked") {
    $_SESSION['booking_message'] = "You have already booked this session.";
} elseif ($result == "full") {
    $_SESSION['booking_message'] = "This session is full. Please choose another session.";
} elseif ($result == "not_found") {
    $_SESSION['booking_message'] = "Session not found.";
} else {
    $_SESSION['booking_message'] = "An error occurred while booking. Please try again.";
}
    header("Location: my-sessions.php");
    exit;
}

$sql = $db_obj->getAvailableSessions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Sessions</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Available Sessions</h2>

<p>
    Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>
</p>

<p>
    <a href="my-sessions.php">My Sessions</a> |
    <a href="profile.php">My Profile</a> |
</p>

<table border="1" cellpadding="10">
    <tr>
        <th>Lab</th>
        <th>Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Total Seats</th>
        <th>Booked Seats</th>
        <th>Remaining Seats</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
            <td><?php echo htmlspecialchars($row['session_date']); ?></td>
            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
            <td><?php echo htmlspecialchars($row['end_time']); ?></td>
            <td><?php echo $row['capacity']; ?></td>
            <td><?php echo $row['booked_seats']; ?></td>
            <td><?php echo $row['remaining_seats']; ?></td>
            <td>
                <?php if ($row['remaining_seats'] > 0) { ?>
                    <form method="POST" action="studentDashboard.php">
                        <input type="hidden" name="session_id"
                               value="<?php echo $row['id']; ?>">
                        <button type="submit" name="book">Book Session</button>
                    </form>
                <?php } else { ?>
                    Session Full
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>