<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/includes/logger.php";

if (!isset($_SESSION['role_name'])) {
    header("Location: manage_department_users.php?error=invalid_action");
    exit;
}

$currentRole = $_SESSION['role_name'];

if ($currentRole !== 'super_admin' && $currentRole !== 'department_admin') {
    header("Location: manage_department_users.php?error=invalid_action");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'] ?? null;
    $action  = $_POST['action'] ?? null;

    if (!$user_id || !$action) {
        header("Location: manage_department_users.php?error=invalid_action");
        exit;
    }

    // Prevent self-disable
    if ($user_id == $_SESSION['user_id']) {
        header("Location: manage_department_users.php?error=cannot_disable_self");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            u.username,
            u.status,
            u.department_id,
            r.role_name
        FROM user u
        LEFT JOIN role r ON u.role_id = r.role_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        header("Location: manage_department_users.php?error=invalid_action");
        exit;
    }

    // Department admin restrictions
    if ($currentRole === 'department_admin') {

        if ($user['department_id'] != $_SESSION['department_id']) {
            header("Location: manage_department_users.php?error=invalid_action");
            exit;
        }

        if ($user['role_name'] !== 'member') {
            header("Location: manage_department_users.php?error=invalid_action");
            exit;
        }
    }

    // Determine status
    if ($action === "disable") {
        $newStatus = "disabled";
        $logAction = "disable_user";
        $success = "user_disabled";
    } elseif ($action === "enable") {
        $newStatus = "active";
        $logAction = "enable_user";
        $success = "user_enabled";
    } else {
        header("Location: manage_department_users.php?error=invalid_action");
        exit;
    }

    // Update user
    $update = $pdo->prepare("UPDATE user SET status = ? WHERE user_id = ?");
    $update->execute([$newStatus, $user_id]);

    logActivity(
        $pdo,
        $logAction,
        ucfirst($newStatus) . " account: " . $user['username']
    );

    // Redirect based on role
    if ($currentRole === 'super_admin') {
        header("Location: manage_users.php?success=$success");
    } else {
        header("Location: manage_department_users.php?success=$success");
    }

    exit;
}