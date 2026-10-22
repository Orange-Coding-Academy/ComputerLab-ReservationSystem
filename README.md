# ComputerLab-ReservationSystem
1. Project Overview
 System  that allows students to view computer lab sessions and request reservations. An administrator can manage users, labs, sessions, and reservation requests.

2. Project Objectives
- Allow students to create an account and log in securely.
- Display available computer lab sessions.
- Allow students to request a reservation for a session.
- Allow students to view their reservation status.
- Allow the administrator to manage labs, sessions, users, and reservations.
- Support reservation statuses such as pending, confirmed, rejected, and cancelled.
- Prevent students from submitting duplicate reservation records for the same session.


3. Technologies Used
- HTML: page structure and forms.
- CSS :page styling and layout.
- PHP : server-side logic, authentication, and reservation management.
- MySQL:database storage.
- XAMPP: local development environment.
- phpMy:Admindatabase administration.

4. User Roles
Student
A student can:
- Register for an account.
- Log in and log out.
- View lab sessions, including their dates and times.
- Request a reservation.
- View their own reservations and their current statuses.
- Cancel a reservation if cancellation is allowed by the system.
Administrator
An administrator can:
- Log in to the admin area.
- View and manage registered users.
- Add, edit, and delete labs.
- Create and manage lab sessions.
- View reservation requests.
- Confirm, reject, or cancel reservations.

6. Database Structure

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    student_number VARCHAR(20) UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student'
);


CREATE TABLE labs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  capacity INT NOT NULL         
);


CREATE TABLE sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lab_id INT NOT NULL,
  session_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  FOREIGN KEY (lab_id) REFERENCES labs(id)
);

CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  session_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'rejected', 'cancelled')
        DEFAULT 'pending', 
      UNIQUE (user_id, session_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (session_id) REFERENCES sessions(id)
);