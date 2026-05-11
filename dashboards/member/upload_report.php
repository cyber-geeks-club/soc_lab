<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];
$task_id = $_POST['task_id'];

/*
Verify assignment and lock status
*/
$stmt = $pdo->prepare("
    SELECT * FROM task_assignments
    WHERE task_id = ?
    AND user_id = ?
    AND rating IS NULL
");
$stmt->execute([$task_id, $user_id]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    die("Invalid task or already reviewed.");
}

$file = $_FILES['report'];

if ($file['error'] !== 0) {
    die("Upload error code: " . $file['error']);
}
if ($file['size'] > 5 * 1024 * 1024) die("File too large.");

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') die("Only PDF allowed.");

$new_name = "task_{$task_id}_user_{$user_id}_" . time() . ".pdf";

$uploadDir = realpath(__DIR__ . "/../../uploads") . "/task_reports/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$path = $uploadDir . $new_name;

if (!is_uploaded_file($file['tmp_name'])) {
    die("Invalid upload.");
}

if (!move_uploaded_file($file['tmp_name'], $path)) {

    echo "<pre>";

    echo "TMP NAME: " . $file['tmp_name'] . "\n";
    echo "DESTINATION: " . $path . "\n";

    echo "UPLOAD DIR EXISTS: ";
    var_dump(is_dir($uploadDir));

    echo "UPLOAD DIR WRITABLE: ";
    var_dump(is_writable($uploadDir));

    echo "TMP FILE EXISTS: ";
    var_dump(file_exists($file['tmp_name']));

    print_r(error_get_last());

    die("Upload failed.");
}

/*
Update assignment
*/
$stmt = $pdo->prepare("
    UPDATE task_assignments
    SET report_file = ?,
        report_uploaded_at = NOW(),
        status = 'completed',
        completed_at = NOW()
    WHERE task_id = ? AND user_id = ?
");
$stmt->execute([$new_name, $task_id, $user_id]);

/*
Log activity
*/
$log = $pdo->prepare("
    INSERT INTO activity_log
    (user_id, action_type, action_description)
    VALUES (?, ?, ?)
");
$log->execute([
    $user_id,
    'upload_report',
    "Uploaded report for task ID $task_id"
]);

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
            window.location.href = 'tasks.php';
        }, 3000);
    </script>
</head>

<body>

    <div class='alert'>
        ✅ Report uploaded successfully!
    </div>

</body>
</html>
";
exit;