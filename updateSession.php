
<?php
require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();
$message = "";

$id = 0;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} elseif (isset($_POST['id'])) {
    $id = intval($_POST['id']);
}

if ($id <= 0) {
    header("Location: adminSession.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $lab_id = intval($_POST['lab_id']);
    $date = $_POST['session_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    if ($lab_id > 0 && $date != "" && $start != "" && $end != "") {
        if ($db_obj->updateSession($id, $lab_id, $date, $start, $end)) {
            header("Location: adminsession.php");
            exit;
        } else {
            $message = "Cannot update session. Check the time or overlapping sessions.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}

$session = $db_obj->getOneSession($id);

if (!$session) {
    header("Location: adminSession.php");
    exit;
}

$labs = $db_obj->getLabs();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Session</title>
     <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Update Session</h2>

<p>
    <a href="adminSession.php">Back to Sessions</a>
</p>

<?php if ($message != "") { ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php } ?>

<form method="POST" action="updateSession.php">

    <input type="hidden" name="id"
           value="<?php echo $id; ?>">

    <label>Choose Lab:</label>

    <select name="lab_id" required>
        <?php while ($lab = $labs->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?php echo $lab['id']; ?>"
                <?php if ($lab['id'] == $session['lab_id']) {
                    echo "selected";
                } ?>>
                <?php echo htmlspecialchars($lab['name']); ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Date:</label>
    <input type="date" name="session_date"
           value="<?php echo htmlspecialchars($session['session_date']); ?>"
           required>

    <br><br>

    <label>Start Time:</label>
    <input type="time" name="start_time"
           value="<?php echo htmlspecialchars(substr($session['start_time'], 0, 5)); ?>"
           required>

    <br><br>

    <label>End Time:</label>
    <input type="time" name="end_time"
           value="<?php echo htmlspecialchars(substr($session['end_time'], 0, 5)); ?>"
           required>

    <br><br>

    <button type="submit" name="update">Update Session</button>

</form>

</body>
</html>
