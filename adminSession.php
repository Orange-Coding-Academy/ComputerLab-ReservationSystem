
<?php
require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();
$message = "";

/* Add Session */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $lab_id = intval($_POST['lab_id']);
    $date = $_POST['session_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    if ($lab_id > 0 && $date != "" && $start != "" && $end != "") {
        if ($db_obj->addSession($lab_id, $date, $start, $end)) {
            $message = "Session added successfully.";
        } else {
            $message = "Cannot add session. Check the time or overlapping sessions.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}

/* Delete Session */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    if ($id > 0 && $db_obj->deleteSession($id)) {
        $message = "Session deleted successfully.";
    } else {
        $message = "Cannot delete this session. It may have reservations.";
    }
}

$labs = $db_obj->getLabs();
$sessions = $db_obj->getAllSessions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sessions</title>
     <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.css" integrity="sha512-x9WwyMYBnlXMNQ6kQ/Lyzu1NqIhLQKL5Oq6xByfXuRj7s9CskyCbLv/1IjqzJmXwFXWr0ov6jBV7Qbc0hh9nHg==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>

<h>Manage Sessions</h>

<p class="p">
    <a href="adminDashboard.php">Students</a> |
    <a href="adminReservation.php">Reservation Requests</a> |
    <a href="adminSession.php">Manage Sessions</a>|
     <a href="logout.php">Logout</a>
</p>

<?php if ($message != "") { ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php } ?>

<h3>All Sessions</h3>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Lab</th>
        <th>Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $sessions->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>

            <td><?php echo htmlspecialchars($row['lab_name']); ?></td>

            <td><?php echo htmlspecialchars($row['session_date']); ?></td>

            <td><?php echo htmlspecialchars($row['start_time']); ?></td>

            <td><?php echo htmlspecialchars($row['end_time']); ?></td>

           <td>
    <div style="display: flex; align-items: center; gap: 10px;">

        <a href="updateSession.php?id=<?php echo $row['id']; ?>">
            <button type="button"><i class="fa-solid fa-pen"></i></button>
        </a>

        <form method="POST" action="adminSession.php" style="margin: 0;">
            <input type="hidden" name="id"
                   value="<?php echo $row['id']; ?>">

            <button type="submit" name="delete" class="del">
                <i class="fa-regular fa-trash-can"></i>
            </button>
        </form>

    </div>
</td>
        </tr>
    <?php } ?>

</table>
<hr>
<h3>Add New Session</h3>

<form method="POST" action="adminSession.php">

    <label>Choose Lab:</label>

    <select name="lab_id" required>
        <option value="">Select Lab</option>

        <?php while ($lab = $labs->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?php echo $lab['id']; ?>">
                <?php echo htmlspecialchars($lab['name']); ?>
            </option>
        <?php } ?>

    </select>

    <br><br>

    <label>Date:</label>
    <input type="date" name="session_date" required>

    <br><br>

    <label>Start Time:</label>
    <input type="time" name="start_time" required>

    <br><br>

    <label>End Time:</label>
    <input type="time" name="end_time" required>

    <br><br>

    <button type="submit" name="add">Add Session</button>

</form>


</body>
</html>
