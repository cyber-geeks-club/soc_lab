<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

$user_id = $_SESSION['user_id'];

$username = $_POST['username'];
$current_password = $_POST['current_password'] ?? null;
$new_password = $_POST['new_password'] ?? null;

/* =============================
   GET CURRENT USER DATA
============================= */

$stmt = $pdo->prepare("SELECT password_hash FROM user WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

/* =============================
   UPDATE USERNAME
============================= */

$updateUsername = $pdo->prepare("UPDATE user SET username = ? WHERE user_id = ?");
$updateUsername->execute([$username, $user_id]);

/* =============================
   UPDATE PASSWORD (IF PROVIDED)
============================= */

if (!empty($current_password) && !empty($new_password)) {

    if (!password_verify($current_password, $user['password_hash'])) {
        die("Current password is incorrect.");
    }

    $newHash = password_hash($new_password, PASSWORD_DEFAULT);

    $updatePassword = $pdo->prepare("UPDATE user SET password_hash = ? WHERE user_id = ?");
    $updatePassword->execute([$newHash, $user_id]);

    logActivity($pdo, "password_change", "User changed their password");
}

if (!empty($_POST['new_password'])) {
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        // Error: Passwords do not match
    } else {
        // Verify current password, then hash and save new password
    }
}

/* =============================
   UPDATE SESSION USERNAME
============================= */

$_SESSION['username'] = $username;

logActivity($pdo, "profile_update", "User updated profile");

 echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Upload Success</title>

    <style>
        body {
        margin:0;
        height:100vh;
        display:flex;
        justify-content:center;
        align-items:center;
        background-color: #0b0f14;
        margin: 0;
        font-family: Arial, sans-serif;
}

        .alert{
            background:#dcfce7;
            color:#166534;
            padding:20px 30px;
            border-radius:12px;
            font-size:18px;
            font-weight:600;
            box-shadow:0 4px 12px rgba(0,0,0,0.1);
            animation:fadeIn 0.3s ease;
        }

        @keyframes fadeIn{
            from{
                opacity:0;
                transform:translateY(-10px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }
    </style>

    <script>
        setTimeout(() => {
            window.location.href = '/soc_lab/public/profile.php';
        }, 3000);
    </script>
</head>

<body>

    <div class='alert'>
        ✅Login Credentials Succesfully Updated!
    </div>

</body>
</html>
";
exit;