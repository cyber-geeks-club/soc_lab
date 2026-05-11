<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

$user_id = $_SESSION['user_id'];

$username = trim($_POST['username'] ?? '');
$confirm_username = trim($_POST['confirm_username'] ?? '');

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

/* =============================
   GET CURRENT USER DATA
============================= */

$stmt = $pdo->prepare("SELECT username, password_hash FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

/* =============================
   VALIDATION ERRORS COLLECTOR
============================= */

$errors = [];

/* =============================
   USERNAME VALIDATION
============================= */

if (!empty($username) || !empty($confirm_username)) {

    if ($username !== $confirm_username) {
        $errors[] = "Username and Confirm Username do not match.";
    }

    if ($username === $user['username']) {
        $errors[] = "New username is the same as current username.";
    }
}

/* =============================
   PASSWORD VALIDATION
============================= */

$changingPassword = !empty($new_password) || !empty($confirm_password) || !empty($current_password);

if ($changingPassword) {

    if (empty($current_password)) {
        $errors[] = "Current password is required to change password.";
    } elseif (!password_verify($current_password, $user['password_hash'])) {
        $errors[] = "Current password is incorrect.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation do not match.";
    }

}

/* =============================
   IF ERRORS EXIST → STOP
============================= */

if (!empty($errors)) {

    $errorList = "";

    foreach ($errors as $error) {
        $errorList .= "<li>" . htmlspecialchars($error) . "</li>";
    }

    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Profile Update Failed</title>

        <style>
            body {
                margin: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background: #0b0f14;
                font-family: Arial, sans-serif;
            }

            .alert-box {
                background: #fee2e2;
                color: #991b1b;
                padding: 28px;
                border-radius: 14px;
                width: 420px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            }

            .alert-box h3 {
                margin-top: 0;
                margin-bottom: 15px;
                font-size: 22px;
            }

            .alert-box ul {
                margin: 0;
                padding-left: 20px;
                line-height: 1.7;
            }

            .back-btn {
                display: inline-block;
                margin-top: 20px;
                padding: 12px 18px;
                background: #991b1b;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
            }

            .back-btn:hover {
                background: #7f1d1d;
            }
        </style>
    </head>

    <body>
        <div class='alert-box'>
            <h3>Profile Update Failed</h3>
            <ul>
                $errorList
            </ul>

            <a href='/soc_lab/public/profile.php' class='back-btn'>
                Go Back
            </a>
        </div>
    </body>
    </html>
    ";

    exit;
}

/* =============================
   UPDATE USERNAME (ONLY IF VALID)
============================= */

if ($username !== $user['username']) {
    $stmt = $pdo->prepare("UPDATE user SET username = ? WHERE user_id = ?");
    $stmt->execute([$username, $user_id]);

    $_SESSION['username'] = $username;

    logActivity($pdo, "profile_update", "User updated username");
}

/* =============================
   UPDATE PASSWORD (ONLY IF PROVIDED)
============================= */

if ($changingPassword && empty($errors)) {

    $newHash = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE user SET password_hash = ? WHERE user_id = ?");
    $stmt->execute([$newHash, $user_id]);

    logActivity($pdo, "password_change", "User changed password");
}

/* =============================
   SUCCESS MESSAGE
============================= */

logActivity($pdo, "profile_update", "User updated profile");

echo "
<!DOCTYPE html>
<html>
<head>
    <title>Success</title>
    <style>
        body {
            margin:0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#0b0f14;
            font-family: Arial;
        }
        .alert {
            background:#dcfce7;
            color:#166534;
            padding:20px;
            border-radius:10px;
            font-weight:600;
        }
    </style>
    <script>
        setTimeout(() => {
            window.location.href = '/soc_lab/public/profile.php';
        }, 3000);
    </script>
</head>
<body>
    <div class='alert'>Profile updated successfully!</div>
</body>
</html>
";

exit;