
<?php
require_once("adminSession.php");
require_once("config.php");

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
    header("Location: adminSessions.php");
    $sql = "SELECT * FROM labs";
$labs = $db->query($sql);
}
// Fetch labs

?>

<form method="POST" action="addSession.php">

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
