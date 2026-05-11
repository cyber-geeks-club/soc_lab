<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

authorizeSuperAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];
    $department_id = $_POST['department_id'];

    $stmt = $pdo->prepare("
        INSERT INTO user (username, password_hash, role_id, department_id)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([$username, $password, $role_id, $department_id]);

$new_user_id = $pdo->lastInsertId();

logActivity(
    $pdo,
    "create_user",
    "Created user ID: $new_user_id, Username: $username, Role: $role_id, Dept: $department_id");

    header("Location: manage_users.php?success=user_created");
        exit;
}