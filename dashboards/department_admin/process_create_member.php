<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

authorizeDepartmentAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $passwordRaw = $_POST['password'];
    $department_id = $_SESSION['department_id'];

    if (empty($username) || empty($passwordRaw)) {
        header("Location: manage_department_users.php?error=invalid_action");
        exit;
    }

    // Check if username already exists
    $check = $pdo->prepare("SELECT user_id FROM user WHERE username = ?");
    $check->execute([$username]);

    if ($check->fetch()) {
        header("Location: manage_department_users.php?error=user_exists");
        exit;
    }

    $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

    // Get MEMBER role ID
    $stmt = $pdo->prepare("SELECT role_id FROM role WHERE role_name = 'member'");
    $stmt->execute();
    $role = $stmt->fetch();

    if (!$role) {
        header("Location: manage_department_users.php?error=invalid_action");
        exit;
    }

    $role_id = $role['role_id'];

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO user (username, password_hash, role_id, department_id)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $username,
        $password,
        $role_id,
        $department_id
    ]);

    logActivity(
        $pdo,
        "create_member",
        "Department admin created member: $username"
    );

    header("Location: manage_department_users.php?success=member_created");
    exit;
}