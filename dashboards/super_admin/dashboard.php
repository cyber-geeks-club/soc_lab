<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeSuperAdmin();

/* ========================
   FETCH METRICS
======================== */

// Total Users
$totalUsers = $pdo->query("SELECT COUNT(*) FROM user")->fetchColumn();

// Total Departments
$totalDepartments = $pdo->query("SELECT COUNT(*) FROM department")->fetchColumn();

// Total Tasks
$totalTasks = $pdo->query("SELECT COUNT(*) FROM task")->fetchColumn();

// Active Users
$activeUsers = $pdo->query("SELECT COUNT(*) FROM user WHERE status = 'active'")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>

<?php include "../../includes/header.php"; ?>
 <?php include "../../includes/navbar.php"; ?>


<div class="container">
    
   
    <main class="main-content">

        <h1>Super Admin Dashboard</h1>
<p>Welcome, <?php echo $_SESSION['username']; ?> 👑</p>

<div class="card-grid">
    <!-- DATA CARDS -->

    <div class="card">
        <h3>Total Users</h3>
        <p><?= $totalUsers ?></p>
    </div>

    <div class="card">
        <h3>Active Users</h3>
        <p><?= $activeUsers ?></p>
    </div>

    <div class="card">
        <h3>Total Departments</h3>
        <p><?= $totalDepartments ?></p>
    </div>

    <div class="card">
        <h3>Total Tasks</h3>
        <p><?= $totalTasks ?></p>
    </div>


    <!-- ACTION CARDS -->
<a href="/soc_lab/public/profile.php" class="card action-card">
        <h3>Profile Page</h3>
        <p>Change your username and Password</p>
    </a>

    <a href="manage_users.php" class="card action-card">
        <h3>Manage Users</h3>
        <p>Delete and control user accounts</p>
    </a>

    <a href="all_tasks.php" class="card action-card">
    <h3>All Tasks Overview</h3>
    <p>View tasks across all departments</p>
</a>

    <a href="activity_logs.php" class="card action-card">
        <h3>Activity Logs</h3>
        <p>Monitor all system activities</p>
    </a>

</div>
</div>

</div>

    </main>
</div>

<?php include "../../includes/footer.php"; ?>

</body>
</html>