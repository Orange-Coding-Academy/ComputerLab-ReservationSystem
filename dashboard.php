
<?php
require_once "config.php";


$db = new DB_con();

$sql = "SELECT s.id, l.name AS lab_name, l.capacity,
               s.session_date, s.start_time, s.end_time,
               (SELECT COUNT(*)
                FROM reservations r
                WHERE r.session_id = s.id
                AND r.status IN ('pending', 'confirmed')) AS booked_seats
        FROM sessions s
        JOIN labs l ON s.lab_id = l.id
        WHERE s.session_date >= CURDATE()
        ORDER BY s.session_date, s.start_time";

$stmt = $db->db->prepare($sql);
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>

    <h1>Available Sessions</h1>

    <a href="my-sessions.php">My Sessions</a>
    <hr>

    <?php if (count($sessions) > 0) { ?>

        <table border="1" cellpadding="10">
            <tr>
                <th>Lab</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Available Seats</th>
                <th>Action</th>
            </tr>

            <?php foreach ($sessions as $session) { ?>

                <?php
                $availableSeats =
                    $session["capacity"] - $session["booked_seats"];
                ?>

                <tr>
                    <td>
                        <?php echo htmlspecialchars($session["lab_name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($session["session_date"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($session["start_time"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($session["end_time"]); ?>
                    </td>

                    <td>
                        <?php echo max(0, $availableSeats); ?>
                    </td>

                    <td>
                        <?php if ($availableSeats > 0) { ?>

                            <form method="POST" action="book-session.php">
                                <input type="hidden" name="session_id"
                                       value="<?php echo $session["id"]; ?>">

                                <button type="submit" name="book">
                                    Book Session
                                </button>
                            </form>

                        <?php } else { ?>

                            Full

                        <?php } ?>
                    </td>
                </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <p>No sessions available.</p>

    <?php } ?>

</body>
</html>
