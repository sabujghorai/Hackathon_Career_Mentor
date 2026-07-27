<?php
session_start();

// Database Connection Check Guard
if (file_exists("db.php")) {
    include("db.php");
} else {
    die("Database configuration file (db.php) missing.");
}

// Check if $conn is defined properly
if (!isset($conn) || !$conn) {
    die("Database Connection failed: Please check your db.php file for \$conn variable.");
}

// Initialize SweetAlert variable for errors
$sweetAlert = null;

// Role Passwords Dictionary
$rolePasswords = [
    "student" => "STUDENT",
    "teacher" => "TEACHER",
    "admin"   => "ADMIN"
];

/* -------------------------------------------------------------------------- */
/*                                SIGNUP LOGIC                                */
/* -------------------------------------------------------------------------- */
if (isset($_POST['signup'])) {

    $fullname        = trim($_POST['fullname'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role            = $_POST['role'] ?? '';
    $rolePassword    = $_POST['role_password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        $sweetAlert = [
            'icon' => 'warning',
            'title' => 'Missing Fields',
            'text' => 'All fields are required.'
        ];
    } elseif ($password !== $confirmPassword) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Mismatch',
            'text' => 'Passwords do not match.'
        ];
    } elseif (!isset($rolePasswords[$role])) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Invalid Role',
            'text' => 'Selected role is invalid.'
        ];
    } elseif ($rolePasswords[$role] !== $rolePassword) {
        $sweetAlert = [
            'icon' => 'error',
            'title' => 'Wrong Role Key',
            'text' => 'Incorrect role password provided.'
        ];
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $sweetAlert = [
                    'icon' => 'error',
                    'title' => 'Email Exists',
                    'text' => 'This email is already registered.'
                ];
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);

                $hashPassword = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = mysqli_prepare($conn, "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
                if ($insertStmt) {
                    mysqli_stmt_bind_param($insertStmt, "ssss", $fullname, $email, $hashPassword, $role);

                    if (mysqli_stmt_execute($insertStmt)) {
                        $sweetAlert = [
                            'icon' => 'success',
                            'title' => 'Account Created!',
                            'text' => 'Your account has been created successfully. Please log in.'
                        ];
                    } else {
                        $sweetAlert = [
                            'icon' => 'error',
                            'title' => 'Database Error',
                            'text' => 'Something went wrong. Please try again.'
                        ];
                    }
                    mysqli_stmt_close($insertStmt);
                }
            }
        } else {
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Query Failed',
                'text' => 'Database query preparation failed.'
            ];
        }
    }
}

/* -------------------------------------------------------------------------- */
/*                                 LOGIN LOGIC                                */
/* -------------------------------------------------------------------------- */
if (isset($_POST['login'])) {

    $email    = trim($_POST['login_email'] ?? '');
    $password = $_POST['login_password'] ?? '';

    if (empty($email) || empty($password)) {
        $sweetAlert = [
            'icon' => 'warning',
            'title' => 'Empty Input',
            'text' => 'Please enter both email and password.'
        ];
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, fullname, email, password, role FROM users WHERE email = ?");

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {

                if (password_verify($password, $row['password'])) {

                    $_SESSION['id']       = $row['id'];
                    $_SESSION['fullname'] = $row['fullname'];
                    $_SESSION['role']     = $row['role'];
                    $_SESSION['email']    = $row['email'];

                    $redirectUrl = "Student/student_dashboard.php";

                    if ($row['role'] === "admin") {
                        $redirectUrl = "Admin/admin_dashboard.php";
                    } elseif ($row['role'] === "teacher") {
                        $redirectUrl = "Teacher/teacher_dashboard.php";
                    }

                    header("Location: " . $redirectUrl);
                    exit();
                } else {
                    $sweetAlert = [
                        'icon' => 'error',
                        'title' => 'Authentication Failed',
                        'text' => 'Wrong Password.'
                    ];
                }
            } else {
                $sweetAlert = [
                    'icon' => 'error',
                    'title' => 'Not Found',
                    'text' => 'Email ID not found in system.'
                ];
            }
            mysqli_stmt_close($stmt);
        } else {
            $sweetAlert = [
                'icon' => 'error',
                'title' => 'Query Failed',
                'text' => 'Database query execution error.'
            ];
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login || Signup</title>

    <!-- SweetAlert2 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap");

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Roboto", sans-serif;
            background: linear-gradient(to right, #e0eafc, #9ebde9);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            width: 800px;
            max-width: 100%;
            min-height: 520px;
            position: relative;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
        }

        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
            opacity: 0;
            z-index: 1;
        }

        form {
            background: transparent;
            display: flex;
            flex-direction: column;
            padding: 0 40px;
            height: 100%;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        form h1 {
            font-size: 28px;
            margin-bottom: 15px;
            text-decoration: underline;
        }

        input,
        select {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid #ccc;
            padding: 10px 14px;
            margin: 6px 0;
            width: 100%;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
        }

        button {
            border-radius: 20px;
            border: 1px solid #0066cc;
            background-color: #0066cc;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 40px;
            letter-spacing: 1px;
            margin-top: 12px;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }

        button:active {
            transform: scale(0.95);
        }

        button.ghost {
            background-color: transparent;
            border-color: #ffffff;
        }

        a {
            color: #333;
            font-size: 14px;
            margin-top: 10px;
            text-decoration: none;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: linear-gradient(to right, #1d3557, #457b9d);
            color: #ffffff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            padding: 0 20px;
            transition: transform 0.6s ease-in-out;
        }

        .overlay-left {
            transform: translateX(-20%);
            left: 0;
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #333;
            user-select: none;
            font-weight: bold;
            font-size: 14px;
        }

    </style>
</head>

<body>
    <div class="container" id="container">

        <!-- Signup Form -->
        <div class="form-container sign-up-container">
            <form action="" method="POST" id="signupForm">
                <h1>Create Account </h1>

                <input type="text" name="fullname" placeholder="Full Name" required />
                <input type="email" name="email" placeholder="Email" required />

                <div style="position: relative; width: 100%;">
                    <input type="password" name="password" id="signup_password" placeholder="Password" required style="padding-right: 50px;" />
                    <span class="toggle-btn" onclick="togglePassword('signup_password', this)">Show</span>
                </div>

                <div style="position: relative; width: 100%;">
                    <input type="password" name="confirm_password" id="signup_confirm_password" placeholder="Confirm Password" required style="padding-right: 50px;" />
                    <span class="toggle-btn" onclick="togglePassword('signup_confirm_password', this)">Show</span>
                </div>

                <select name="role" id="role" onchange="handleRoleChange()" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>

                <input type="hidden" name="role_password" id="role_password" />
                <button type="submit" name="signup">Create Account</button>
            </form>
        </div>

        <!-- Login Form -->
        <div class="form-container sign-in-container">
            <form action="" method="POST" id="loginForm">
                <h1>Login</h1>
                <input type="email" name="login_email" placeholder="Email ID" required />

                <div style="position: relative; width: 100%;">
                    <input type="password" name="login_password" id="login_password" placeholder="Password" required style="padding-right: 50px;" />
                    <span class="toggle-btn" onclick="togglePassword('login_password', this)">Show</span>
                </div>

                <a href="#" id="needHelp">Need help?</a>
                <button type="submit" name="login">Proceed</button>
            </form>
        </div>

        <!-- Sliding Overlays -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your details and start your journey with us</p>
                    <button class="ghost" id="signIn"><span>Login</span></button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Welcome Back!</h1>
                    <p>To stay connected, login with your personal info</p>
                    <button class="ghost" id="signUp"><span>Sign Up</span></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Panel Toggle Animations
        const container = document.getElementById("container");
        document.getElementById("signUp").addEventListener("click", () => {
            container.classList.add("right-panel-active");
        });
        document.getElementById("signIn").addEventListener("click", () => {
            container.classList.remove("right-panel-active");
        });

        // Prompt for Role Password on Signup
        function handleRoleChange() {
            const role = document.getElementById("role").value;
            if (!role) {
                document.getElementById("role_password").value = "";
                return;
            }

            Swal.fire({
                title: 'Security Verification',
                text: `Enter Secret Passkey for ${role.toUpperCase()}:`,
                input: 'password',
                inputPlaceholder: 'Enter passkey',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    document.getElementById("role_password").value = result.value;
                } else {
                    document.getElementById("role").value = "";
                    document.getElementById("role_password").value = "";
                }
            });
        }

        // Toggle Password Visibility
        function togglePassword(fieldId, element) {
            const input = document.getElementById(fieldId);
            if (input.type === "password") {
                input.type = "text";
                element.innerText = "Hide";
            } else {
                input.type = "password";
                element.innerText = "Show";
            }
        }

        // Help Alert Modal
        document.getElementById("needHelp").addEventListener("click", function(e) {
            e.preventDefault();
            Swal.fire({
                icon: "info",
                title: "Login Assistance",
                text: "If you have lost your credentials, please contact administrator or reset password.",
                showCancelButton: true,
                confirmButtonText: "Okay",
                cancelButtonText: "Forgot Password?",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Redirecting', 'Navigating to Forgot Password page...', 'info');
                }
            });
        });

        // Context menu / devtools security overrides
        document.addEventListener("contextmenu", (e) => e.preventDefault());
        document.addEventListener("keydown", (e) => {
            if (
                e.key === "F12" ||
                (e.ctrlKey && e.shiftKey && ["I", "J", "C"].includes(e.key)) ||
                (e.ctrlKey && ["U", "S"].includes(e.key))
            ) {
                e.preventDefault();
            }
        });
    </script>

    <!-- Trigger SweetAlert ONLY for Signup Messages & Login Errors -->
    <?php if ($sweetAlert): ?>
        <script>
            Swal.fire({
                icon: '<?= $sweetAlert['icon'] ?>',
                title: '<?= $sweetAlert['title'] ?>',
                text: '<?= $sweetAlert['text'] ?>',
                confirmButtonColor: '<?= $sweetAlert['icon'] === "success" ? "#3085d6" : "#d33" ?>'
            });
            <?php if (isset($_POST['signup']) && $sweetAlert['icon'] !== 'success'): ?>
                // Keep signup panel active if signup failed
                container.classList.add("right-panel-active");
            <?php endif; ?>
        </script>
    <?php endif; ?>
</body>

</html>