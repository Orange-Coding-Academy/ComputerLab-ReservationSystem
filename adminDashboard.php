<?php
require_once("config.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$db_obj = new DB_con();

/* Summary numbers */
$stats = $db_obj->db->query(
    "SELECT
        (SELECT COUNT(*) FROM users WHERE role = 'student') AS students,
        (SELECT COUNT(*) FROM labs) AS labs,
        (SELECT COUNT(*) FROM sessions) AS sessions,
        (SELECT COUNT(*) FROM sessions WHERE session_date >= CURDATE()) AS upcoming"
)->fetch(PDO::FETCH_ASSOC);

/* Reservations grouped by status */
$statusCounts = [
    'pending'   => 0,
    'confirmed' => 0,
    'rejected'  => 0,
    'cancelled' => 0
];

$rows = $db_obj->db->query(
    "SELECT status, COUNT(*) AS total FROM reservations GROUP BY status"
);

while ($r = $rows->fetch(PDO::FETCH_ASSOC)) {
    if (isset($statusCounts[$r['status']])) {
        $statusCounts[$r['status']] = (int)$r['total'];
    }
}

$totalReservations = array_sum($statusCounts);

/* Latest reservations (uses the existing method) */
$recent = $db_obj->getAllReservations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* ==========================================================
   Computer Lab Reservation System - Global Stylesheet
   One file used by every page, so colors and layout stay consistent.
   ========================================================== */

:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #eff6ff;
    --success: #16a34a;
    --success-dark: #15803d;
    --danger: #dc2626;
    --danger-dark: #b91c1c;
    --warning: #f59e0b;
    --warning-dark: #d97706;

    --bg: #f3f4f6;
    --card: #ffffff;
    --text: #1f2937;
    --muted: #6b7280;
    --border: #e5e7eb;
    --radius: 10px;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Tahoma, Arial, sans-serif;
    font-size: 15px;
    line-height: 1.5;
    color: var(--text);
    background: var(--bg);
}

/* ---------- Layout ---------- */

.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 28px 30px;
    background: var(--card);
    border-radius: var(--radius);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

/* Login / Register: small centered card */
.auth-page .container {
    max-width: 420px;
    margin-top: 8vh;
}

.auth-page h2 {
    text-align: center;
}

/* ---------- Typography ---------- */

h2 {
    margin: 0 0 8px;
    font-size: 26px;
    color: var(--primary);
}

h3 {
    margin: 24px 0 14px;
    font-size: 19px;
    color: var(--text);
}

a {
    color: var(--primary);
}

a:hover {
    color: var(--primary-dark);
}

hr {
    border: 0;
    border-top: 1px solid var(--border);
    margin: 24px 0;
}

.welcome {
    margin: 0 0 4px;
    color: var(--muted);
}

/* ---------- Navigation ---------- */

.nav {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 16px 0 24px;
    padding: 8px;
    background: var(--primary-light);
    border-radius: 8px;
}

.nav a {
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    color: var(--primary);
}

.nav a:hover {
    background: var(--primary);
    color: #ffffff;
}

/* ---------- Messages ---------- */

.message {
    margin: 0 0 18px;
    padding: 12px 14px;
    background: var(--primary-light);
    border-left: 4px solid var(--primary);
    border-radius: 6px;
}

.message:empty {
    display: none;
}

/* ---------- Forms ---------- */

label {
    display: block;
    margin: 14px 0 6px;
    font-weight: 600;
}

input:not([type="hidden"]),
select {
    width: 100%;
    padding: 10px 12px;
    font-family: inherit;
    font-size: 15px;
    color: var(--text);
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
}

input:not([type="hidden"]):focus,
select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
}

.container > form {
    max-width: 480px;
}

.auth-page .container > form {
    max-width: none;
}

.container > form button {
    margin-top: 20px;
}

.auth-page .container > form button {
    width: 100%;
}

.auth-link {
    margin: 22px 0 0;
    text-align: center;
    color: var(--muted);
}

/* ---------- Buttons ---------- */

button,
.link-btn {
    display: inline-block;
    padding: 9px 16px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    color: #ffffff;
    background: var(--primary);
    border: 0;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.15s;
}

button:hover,
.link-btn:hover {
    background: var(--primary-dark);
    color: #ffffff;
}

.btn-success {
    background: var(--success);
}

.btn-success:hover {
    background: var(--success-dark);
}

.btn-danger {
    background: var(--danger);
}

.btn-danger:hover {
    background: var(--danger-dark);
}

.link-btn {
    background: var(--warning);
}

.link-btn:hover {
    background: var(--warning-dark);
}

/* ---------- Tables ---------- */

.table-wrap {
    overflow-x: auto;
    border: 1px solid var(--border);
    border-radius: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    padding: 12px 14px;
    text-align: left;
    white-space: nowrap;
    color: #ffffff;
    background: var(--primary);
}

td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

tr:last-child td {
    border-bottom: 0;
}

tr:nth-child(even) td {
    background: #f9fafb;
}

tr:hover td {
    background: var(--primary-light);
}

td form {
    display: inline-block;
    margin: 0 4px 0 0;
}

/* ---------- Small screens ---------- */

@media (max-width: 640px) {
    .container {
        margin: 12px;
        padding: 20px 16px;
    }

    .auth-page .container {
        margin: 6vh 12px;
    }

    h2 {
        font-size: 22px;
    }
}

/* ==========================================================
   Admin Dashboard
   ========================================================== */

.nav a.active {
    background: var(--primary);
    color: #ffffff;
}

/* Summary cards */
.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 16px;
    margin-bottom: 8px;
}

.stat-card {
    display: flex;
    flex-direction: column;
    padding: 18px 20px;
    background: var(--primary-light);
    border: 1px solid #dbeafe;
    border-left: 5px solid var(--primary);
    border-radius: 8px;
}

.stat-card.highlight {
    background: #fffbeb;
    border-color: #fde68a;
    border-left-color: var(--warning);
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    line-height: 1.1;
    color: var(--text);
}

.stat-label {
    margin-top: 4px;
    font-size: 14px;
    color: var(--muted);
}

/* Panel + progress bars */
.panel {
    padding: 18px 20px;
    border: 1px solid var(--border);
    border-radius: 8px;
}

.bar-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.bar-name {
    width: 90px;
    font-weight: 600;
}

.bar-track {
    flex: 1;
    height: 12px;
    overflow: hidden;
    background: var(--border);
    border-radius: 6px;
}

.bar-fill {
    height: 100%;
    border-radius: 6px;
}

.bar-pending   { background: var(--warning); }
.bar-confirmed { background: var(--success); }
.bar-rejected  { background: var(--danger); }
.bar-cancelled { background: var(--muted); }

.bar-value {
    width: 36px;
    text-align: right;
    font-weight: 600;
}

.panel-note {
    margin: 6px 0 0;
    font-size: 14px;
    color: var(--muted);
}

/* Quick links */
.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}

.quick-link {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 18px 20px;
    text-decoration: none;
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 8px;
    transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
}

.quick-link strong {
    font-size: 16px;
    color: var(--primary);
}

.quick-link span {
    font-size: 14px;
    color: var(--muted);
}

.quick-link:hover {
    color: var(--text);
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    transform: translateY(-2px);
}

/* Status badges */
.badge {
    display: inline-block;
    padding: 3px 10px;
    font-size: 13px;
    font-weight: 600;
    text-transform: capitalize;
    border-radius: 999px;
}

.badge-pending   { color: #92400e; background: #fef3c7; }
.badge-confirmed { color: #166534; background: #dcfce7; }
.badge-rejected  { color: #991b1b; background: #fee2e2; }
.badge-cancelled { color: #374151; background: #e5e7eb; }
    </style>
</head>
<body>
<div class="container">

<h2>Admin Dashboard</h2>

<p class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></p>

<!-- Summary cards -->
<div class="stats">
    <div class="stat-card">
        <span class="stat-number"><?php echo (int)$stats['students']; ?></span>
        <span class="stat-label">Students</span>
    </div>

    <div class="stat-card">
        <span class="stat-number"><?php echo (int)$stats['labs']; ?></span>
        <span class="stat-label">Labs</span>
    </div>

    <div class="stat-card">
        <span class="stat-number"><?php echo (int)$stats['upcoming']; ?></span>
        <span class="stat-label">Upcoming Sessions (of <?php echo (int)$stats['sessions']; ?>)</span>
    </div>

    <div class="stat-card highlight">
        <span class="stat-number"><?php echo $statusCounts['pending']; ?></span>
        <span class="stat-label">Pending Requests</span>
    </div>
</div>

<!-- Reservation status breakdown -->
<h3>Reservations Overview</h3>

<div class="panel">
    <?php foreach ($statusCounts as $status => $count) {
        $percent = $totalReservations > 0
            ? round($count / $totalReservations * 100)
            : 0;
    ?>
        <div class="bar-row">
            <span class="bar-name"><?php echo ucfirst($status); ?></span>
            <div class="bar-track">
                <div class="bar-fill bar-<?php echo $status; ?>"
                     style="width: <?php echo $percent; ?>%"></div>
            </div>
            <span class="bar-value"><?php echo $count; ?></span>
        </div>
    <?php } ?>

    <p class="panel-note">Total reservations: <?php echo $totalReservations; ?></p>
</div>

<!-- Quick links -->
<h3>Quick Access</h3>

<div class="quick-links">
    <a class="quick-link" href="adminStudent.php">
        <strong>Students</strong>
        <span>View and delete student accounts</span>
    </a>

    <a class="quick-link" href="adminReservation.php">
        <strong>Reservation Requests</strong>
        <span>Confirm or reject pending requests</span>
    </a>

    <a class="quick-link" href="adminSession.php">
        <strong>Manage Sessions</strong>
        <span>Add, update or delete lab sessions</span>
    </a>
</div>

<!-- Latest reservations -->
<h3>Latest Reservations</h3>

<div class="table-wrap">
<table>
    <tr>
        <th>Student</th>
        <th>Lab</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
    </tr>

    <?php
    $shown = 0;
    while ($shown < 5 && ($row = $recent->fetch(PDO::FETCH_ASSOC))) {
        $shown++;
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
            <td><?php echo htmlspecialchars($row['session_date']); ?></td>
            <td>
                <?php echo htmlspecialchars(substr($row['start_time'], 0, 5)); ?>
                -
                <?php echo htmlspecialchars(substr($row['end_time'], 0, 5)); ?>
            </td>
            <td>
                <span class="badge badge-<?php echo htmlspecialchars($row['status']); ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                </span>
            </td>
        </tr>
    <?php } ?>

    <?php if ($shown == 0) { ?>
        <tr>
            <td colspan="5">No reservations yet.</td>
        </tr>
    <?php } ?>
</table>
</div>

</div>
</body>
</html>







