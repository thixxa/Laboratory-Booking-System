<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome - Faculty of Engineering, University of Jaffna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: #003366;
            background-image: url('44.jpg'); 
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-card {
            background-color: rgba(217, 207, 207, 0.8);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            padding: 40px 32px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .uni-header {
            color: #003366;
            font-weight: bold;
            font-size: 2rem;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .uni-subheader {
            color: #0055a5;
            font-size: 1.2rem;
            margin-bottom: 24px;
            letter-spacing: 2px;
        }
        .welcome-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #222;
        }
        .welcome-desc {
            color: #444;
            font-size: 1.05rem;
            margin-bottom: 32px;
        }
        .login-btn {
            font-size: 1.1rem;
            padding: 10px 36px;
            border-radius: 24px;
            background: linear-gradient(90deg, #003366 0%, #0055a5 100%);
            color: #fff;
            border: none;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: linear-gradient(90deg, #0055a5 0%, #003366 100%);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <div class="uni-header">Faculty of Engineering</div>
        <div class="uni-subheader">University of Jaffna</div>
        <div class="welcome-title">Welcome to the Laboratory Booking System</div>
        <div class="welcome-desc">
            Book, manage, and track laboratory sessions for students and staff.<br>
            Please proceed to login to access your dashboard.
        </div>
        <a href="login.php" class="btn login-btn">Login</a>
    </div>
</body>
</html>
