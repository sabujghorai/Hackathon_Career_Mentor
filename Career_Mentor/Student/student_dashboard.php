<?php
session_start();

// Check if user is logged in and is a Student
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Career Mentor</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
        .navbar { background: #0066cc; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .logout-btn { background: #ff4d4d; color: white; text-decoration: none; padding: 8px 18px; border-radius: 5px; font-weight: bold; }
        .logout-btn:hover { background: #cc0000; }
        .container { padding: 40px; max-width: 800px; margin: auto; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .role-badge { background: #e0f0ff; color: #0066cc; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Career Mentor AI</h2>
        <!-- Working Logout Link pointing to root logout.php -->
        <a href="../logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
    </div>

    <div class="container">
        <div class="card">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['fullname']); ?>! 👋</h1>
            <p><strong>Role:</strong> <span class="role-badge"><?= htmlspecialchars($_SESSION['role']); ?></span></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']); ?></p>
            <hr>
            <h3>Student Dashboard Features</h3>
            <p>Aapka student portal ready hai. Yahan aap apne AI career mentorship tools access kar sakte hain.</p>
        </div>
    </div>

</body>
</html>