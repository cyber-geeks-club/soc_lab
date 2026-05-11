<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeSuperAdmin();

// Fetch roles
$roles = $pdo->query("SELECT role_id, role_name FROM role")->fetchAll();

// Fetch departments
$departments = $pdo->query("SELECT department_id, department_name FROM department")->fetchAll();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    </head>
<?php include "../../includes/header.php"; ?>
 <?php include "../../includes/navbar.php"; ?>
<div class="main-content">

<h2>Create New User</h2>

<form method="POST" action="process_create_user.php">

    <label>Username</label>
    <input type="text" name="username" required><br><br>

    <label>Password</label>
    <input type="password" name="password" required><br><br>

    <label>Role</label>
    <select name="role_id" required>
        <?php foreach ($roles as $role): ?>
            <option value="<?= $role['role_id'] ?>">
                <?= $role['role_name'] ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Department</label>
    <select name="department_id" required>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['department_id'] ?>">
                <?= $dept['department_name'] ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Create User</button>

</form>

<?php include "../../includes/footer.php"; ?>