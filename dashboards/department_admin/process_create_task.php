<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';
require_once '../../includes/logger.php';

authorizeRole(['department_admin']);

$department_id = $_SESSION['department_id'];
$admin_id = $_SESSION['user_id'];

$title = trim($_POST['title']);
$description = trim($_POST['description']);
$assigned_users = $_POST['assigned_users'] ?? [];

if (empty($title) || empty($assigned_users)) {
    die("All fields required.");
}

try {

    $pdo->beginTransaction();

    /*
    Insert task definition
    */
    $stmt = $pdo->prepare("
        INSERT INTO task (title, description, department_id, created_by)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$title, $description, $department_id, $admin_id]);

    $task_id = $pdo->lastInsertId();

    /*
    Insert assignments
    */
    $assignStmt = $pdo->prepare("
        INSERT INTO task_assignments (task_id, user_id)
        VALUES (?, ?)
    ");

    foreach ($assigned_users as $user_id) {

        // Security check: ensure user belongs to department
        $check = $pdo->prepare("
            SELECT user_id FROM user
            WHERE user_id = ?
            AND department_id = ?
            AND role_id = 3
        ");
        $check->execute([$user_id, $department_id]);

        if (!$check->fetch()) {
            throw new Exception("Invalid user assignment.");
        }

        $assignStmt->execute([$task_id, $user_id]);
    }

    /*
    Log activity
    */
    foreach ($assigned_users as $user_id) {
    logActivity(
        $pdo,
        'ASSIGN_TASK',
        "Task #$task_id assigned to user #$user_id",
        $admin_id
    );
}

    $pdo->commit();

    header("Location: create_task.php?success=task_created");
        exit;
    
if (empty($_POST['assigned_users'])) {
    header("Location: create_task.php?error=no_users_selected");
    exit;
}
else {header("Location: create_task.php?error=invalid_request");
exit;}

} catch (Exception $e) {

    $pdo->rollBack();
    die("Error creating task: " . $e->getMessage());
}