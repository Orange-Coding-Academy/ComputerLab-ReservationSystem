<?php
declare(strict_types=1);
session_start();
define("db_host","localhost");
define("db_root","root");
define("db_name","computer-lab-reservation");
define("db_pass","");

class DB_con{

    public $db;
     public function 
    __construct(){
        try{
             $con = new PDO(
                "mysql:host=" . db_host . ";dbname=" . db_name,
                db_root,
                db_pass
            );

             $con->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->db = $con;
          }
         catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
           }




public function register($name, $email, $password)
{
    $check = "SELECT id FROM users WHERE email = ? ";

    $stmt = $this->db->prepare($check);
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        return false;
    }

    $sql = "INSERT INTO users
            (name, email, password)
            VALUES (?,?, ?)";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        $name,
        $email,
        $password
    ]);
}
/////////////////////////////////////////////


public function Login($email, $password)
{
    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$email]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        return $row;
    } else {
        return false;
    }
}

public function getAvailableSessions()
{
    $sql = "SELECT sessions.*, labs.name AS lab_name,
            labs.capacity,
            COUNT(reservations.id) AS booked_seats,
            labs.capacity - COUNT(reservations.id) AS remaining_seats
            FROM sessions
            INNER JOIN labs ON sessions.lab_id = labs.id
            LEFT JOIN reservations
            ON sessions.id = reservations.session_id
            AND reservations.status IN ('pending', 'confirmed')
            GROUP BY sessions.id, labs.id
            ORDER BY sessions.session_date, sessions.start_time";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt;
}
public function bookSession($user_id, $session_id)
{
   
    $sql = "SELECT labs.capacity
            FROM sessions
            INNER JOIN labs ON sessions.lab_id = labs.id
            WHERE sessions.id = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$session_id]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        return "not_found";
    }

   
    $sql = "SELECT id, status FROM reservations
            WHERE user_id = ? AND session_id = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$user_id, $session_id]);
    $oldReservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($oldReservation &&
        in_array($oldReservation['status'], ['pending', 'confirmed'])) {
        return "already_booked";
    }

   
    $sql = "SELECT COUNT(*) FROM reservations
            WHERE session_id = ?
            AND status IN ('pending', 'confirmed')";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$session_id]);
    $booked = $stmt->fetchColumn();

 
    if ($booked >= $session['capacity']) {
        return "full";
    }

   
    if ($oldReservation) {
        $sql = "UPDATE reservations
                SET status = 'pending'
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$oldReservation['id']]);

    } else {
        $sql = "INSERT INTO reservations
                (user_id, session_id, status)
                VALUES (?, ?, 'pending')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $session_id]);
    }

    return "success";
}

public function cancelReservation($user_id, $reservation_id)
{
    $sql = "UPDATE reservations
            SET status = 'cancelled'
            WHERE id = ? AND user_id = ?
            AND status IN ('pending', 'confirmed')";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        $reservation_id,
        $user_id
    ]);
}

public function getMySessions($user_id)
{
    $sql = "SELECT reservations.id AS reservation_id,
            reservations.status,
            sessions.session_date,
            sessions.start_time,
            sessions.end_time,
            labs.name AS lab_name
            FROM reservations
            INNER JOIN sessions
            ON reservations.session_id = sessions.id
            INNER JOIN labs ON sessions.lab_id = labs.id
            WHERE reservations.user_id = ?
            ORDER BY reservations.created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$user_id]);

    return $stmt;
}

public function getProfile($id)
{
    $sql = "SELECT id, name, email
            FROM users
            WHERE id = ? AND role = 'student'";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updateProfile($id, $name, $email)
{
    $sql = "SELECT id FROM users
            WHERE (email = ?)
            AND id != ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$email, $id]);

    if ($stmt->fetch()) {
        return "exists";
    }

    $sql = "UPDATE users
            SET name = ?,  email = ?
            WHERE id = ? AND role = 'student'";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        $name,
        $email,
        $id
    ]);

    return "success";
}


public function getStudents()
{
    $sql = "SELECT id, name, email
            FROM users
            WHERE role = 'student'";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt;
}

public function deleteStudent($id)
{
    $sql = "DELETE FROM reservations
            WHERE user_id = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    $sql = "DELETE FROM users
            WHERE id = ? AND role = 'student'";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() == 0) {
        return false;
    }

    return true;
}
public function getAllReservations()
{
    $sql = "SELECT reservations.id AS reservation_id,
                   reservations.status,
                   users.name AS student_name,
                   users.email,
                   labs.name AS lab_name,
                   sessions.session_date,
                   sessions.start_time,
                   sessions.end_time,
                   labs.capacity
            FROM reservations
            INNER JOIN users
                ON reservations.user_id = users.id
            INNER JOIN sessions
                ON reservations.session_id = sessions.id
            INNER JOIN labs
                ON sessions.lab_id = labs.id
            ORDER BY reservations.created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt;
}

public function updateReservationStatus($reservation_id, $status)
{
    if ($status != 'confirmed' && $status != 'rejected') {
        return false;
    }

    if ($status == 'confirmed') {
        $sql = "SELECT sessions.id, labs.capacity
                FROM reservations
                INNER JOIN sessions
                    ON reservations.session_id = sessions.id
                INNER JOIN labs
                    ON sessions.lab_id = labs.id
                WHERE reservations.id = ?
                AND reservations.status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reservation_id]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation) {
            return false;
        }

        $sql = "SELECT COUNT(*)
                FROM reservations
                WHERE session_id = ?
                AND status IN ('pending', 'confirmed')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reservation['id']]);
        $booked = $stmt->fetchColumn();

        if ($booked > $reservation['capacity']) {
            return false;
        }
    }

    $sql = "UPDATE reservations
            SET status = ?
            WHERE id = ? AND status = 'pending'";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$status, $reservation_id]);

    return $stmt->rowCount() > 0;
}


public function getLabs()
{
    $sql = "SELECT * FROM labs";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt;
}

public function getAllSessions()
{
    $sql = "SELECT sessions.*, labs.name AS lab_name
            FROM sessions
            INNER JOIN labs ON sessions.lab_id = labs.id
            ORDER BY sessions.session_date, sessions.start_time";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt;
}

public function checkSessionTime($lab_id, $date, $start, $end, $id = 0)
{
    if ($start >= $end) {
        return false;
    }

    $sql = "SELECT id FROM sessions
            WHERE lab_id = ?
            AND session_date = ?
            AND start_time < ?
            AND end_time > ?
            AND id != ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        $lab_id,
        $date,
        $end,
        $start,
        $id
    ]);

    return $stmt->rowCount() == 0;
}

public function addSession($lab_id, $date, $start, $end)
{
    if (!$this->checkSessionTime($lab_id, $date, $start, $end)) {
        return false;
    }

    $sql = "INSERT INTO sessions
            (lab_id, session_date, start_time, end_time)
            VALUES (?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        $lab_id,
        $date,
        $start,
        $end
    ]);
}

public function getOneSession($id)
{
    $sql = "SELECT * FROM sessions WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updateSession($id, $lab_id, $date, $start, $end)
{
    if (!$this->checkSessionTime($lab_id, $date, $start, $end, $id)) {
        return false;
    }

    $sql = "UPDATE sessions
            SET lab_id = ?, session_date = ?,
                start_time = ?, end_time = ?
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        $lab_id,
        $date,
        $start,
        $end,
        $id
    ]);
}

public function deleteSession($id)
{
    $sql = "SELECT id FROM reservations
            WHERE session_id = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->fetch()) {
        return false;
    }

    $sql = "DELETE FROM sessions WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->rowCount() > 0;
}

}
?>