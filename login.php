<?php 
session_start();
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laboratory Booking System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #003366 0%, #0055a5 100%);
            background-image: url('44.jpg');
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(246, 242, 242, 0.7);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            padding: 38px 32px 28px 32px;
            max-width: 610px;
            width: 100%;
            margin: 32px auto;
    
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-header text-center">
            <h1>Laboratory Booking System</h1>
            <h4>Login to your Account</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" action="auth.php" id="loginForm">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Login As</label>
                    <select name="role" class="form-select form-select-lg" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="lab_to">Lab TO</option>
                        <option value="lecture">Lecture</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-4">
                <span>Don't have an account?</span>
                <a href="register.php" class="btn btn-link">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
