# Laboratory Booking System

**Faculty of Engineering, University of Jaffna**

## Overview

The Laboratory Booking System is a web-based application designed to streamline the scheduling, booking, and management of laboratory resources for students, instructors, lab technical officers, and lecturers. The system provides secure, role-based access, real-time status updates, and user-friendly dashboards for efficient laboratory management.

## Features

- **Role-Based Access:** Separate dashboards for students, instructors, lab technical officers, and lecturers.
- **User Registration & Login:** Secure registration and authentication with automated user ID generation.
- **Lab Scheduling:** Instructors can schedule lab sessions with all required details.
- **Lab Booking:** Students can view available labs and book sessions easily.
- **Approval Workflow:** Lab technical officers can approve or reject scheduled labs and manage equipment.
- **Equipment Management:** View and manage laboratory equipment inventory.
- **Status Tracking:** Real-time status updates for bookings and schedules.
- **Responsive Design:** Clean, mobile-friendly interfaces using Bootstrap 5.

## Technologies Used

- **Front-End:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Back-End:** PHP (procedural)
- **Database:** MySQL
- **Tools:** XAMPP/WAMP/LAMP (for local server), phpMyAdmin, Git/GitHub

## Installation & Setup

1. **Clone the repository:**
    ```
    git clone https://github.com/your-github-username/your-repo-name.git
    ```
2. **Copy the project folder to your web server directory** (`htdocs` for XAMPP).
3. **Create the MySQL database** (e.g., `laboratory_booking_system`).
4. **Import the provided SQL file** (if available) using phpMyAdmin.
5. **Update database credentials** in `config.php`:
    ```
    $conn = new mysqli('localhost', 'your_db_user', 'your_db_password', 'laboratory_booking_system');
    ```
6. **Start your local server** (Apache/MySQL).
7. **Access the system** in your browser at `http://localhost/your-project-folder/index.php`.

## Usage

- **Registration:** New users can register by selecting their role and filling in the required details.
- **Login:** Registered users log in and are redirected to their role-specific dashboard.
- **Student Dashboard:** Book labs and view your bookings.
- **Instructor Dashboard:** Schedule labs and view all lab schedules.
- **Lab TO Dashboard:** Approve/reject schedules, manage equipment.
- **Lecture Dashboard:** View all lab schedules and student bookings.
- **Logout:** Use the logout button to securely end your session.

## Screenshots

_Add screenshots of your main interfaces here for better understanding._


## Contribution

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is for educational purposes at the Faculty of Engineering, University of Jaffna.

## Author

- [Thisanda Prasanjana]
- [Your GitHub Profile](https://github.com/thixxa)


---


