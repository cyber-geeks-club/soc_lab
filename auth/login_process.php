<?php
session_start();
require_once "../config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/login.php");
    exit;
}

$username = $_POST["username"] ?? '';
$password = $_POST["password"] ?? '';

$sql = "
SELECT u.user_id, u.username, u.password_hash, u.department_id,
       u.status,
       r.role_name
FROM user u
JOIN role r ON u.role_id = r.role_id
WHERE u.username = :username
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* -------------------------
   CHECK IF USER EXISTS
-------------------------- */

if (!$user) {
    logActivity($pdo, "login_failed", "Failed login attempt for username: $username", null);

    $_SESSION["error"] = "Invalid username or password";
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
            window.location.href = '/soc_lab/public/login.php';
        }, 3000);
    </script>
</head>

<body>

    <div class='alert'>
        ❌ Invalid Username or Password!
    </div>

</body>
</html>
";
exit;
}

/* -------------------------
   CHECK IF ACCOUNT DISABLED
-------------------------- */

if ($user['status'] === 'disabled') {

    logActivity(
        $pdo,
        "blocked_login",
        "Attempted login to disabled account: {$user['username']}",
        $user['user_id']
    );

    $_SESSION["error"] = "Your account has been disabled. Contact the administrator.";
    //header("Location: ../public/login.php");
    echo "Your account has been disabled. Contact the administrator.";
    exit;
}

/* -------------------------
   VERIFY PASSWORD
-------------------------- */

if (password_verify($password, $user['password_hash'])) {

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role_name"] = $user["role_name"];
    $_SESSION["department_id"] = $user["department_id"];
    $_SESSION["LAST_ACTIVITY"] = time();

    logActivity(
        $pdo,
        "login_success",
        "User {$user['username']} logged in successfully",
        $user['user_id']
    );

    require_once "role_router.php";
    exit;

} else {

    logActivity(
        $pdo,
        "login_failed",
        "Failed login attempt for username: $username",
        $user['user_id']
    );

    $_SESSION["error"] = "Invalid username or password";
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
            window.location.href = '/soc_lab/public/login.php';
        }, 3000);
    </script>
</head>

<body>

    <div class='alert'>
        ❌ Invalid Username or Password!
    </div>

</body>
</html>
";
exit;
}
