<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];

/*
Fetch tasks assigned to this member
*/
$stmt = $pdo->prepare("
    SELECT 
        t.task_id,
        t.title,
        t.description,
        ta.status,
        ta.report_file,
        ta.rating
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    WHERE ta.user_id = ?
    ORDER BY t.created_at DESC
");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Task Dashboard</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<style>
    h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #1e293b;
    }

    .table-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        overflow: hidden;
        background: #bedede;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    thead {
        background: #0b0f14;
    }

    th {
        text-align: left;
        padding: 15px;
        color: #bedede;
        font-weight: 600;
    }

    td {
        padding: 15px;
        border-top: 1px solid #e2e8f0;
        vertical-align: top;
        color: #222a36;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    /* STATUS BADGES */
    .status {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
    }

    .pending {
        background: #fef3c7;
        color: #92400e;
    }

    .completed {
        background: #dcfce7;
        color: #166534;
    }

    .in_progress {
        background: #dbeafe;
        color: #1d4ed8;
    }

    /* REPORT LINK */
    .report-link {
        text-decoration: none;
        color: #2563eb;
        font-weight: 600;
    }

    .report-link:hover {
        text-decoration: underline;
    }

    /* FILE UPLOAD FORM */
    .upload-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .upload-form input[type="file"] {
        font-size: 12px;
        border: 1px solid #cbd5e1;
        padding: 8px;
        border-radius: 8px;
        background: #fff;
    }

    .upload-form button {
        padding: 10px;
        border: none;
        background: #222a36;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }

    .upload-form button:hover {
        background: #1d4ed8;
    }

    .locked {
        color: #64748b;
        font-weight: bold;
    }

    .stars {
        font-size: 18px;
        letter-spacing: 2px;
    }

    .not-reviewed {
        color: #94a3b8;
        font-style: italic;
    }

    @media (max-width: 900px) {
        .table-card {
            overflow-x: auto;
        }

        table {
            min-width: 900px;
        }
    }
</style>

<div class="main-content">

    <h2>My Tasks</h2>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Report</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($task['title']); ?></strong></td>

                        <td><?= htmlspecialchars($task['description']); ?></td>

                        <td>
                            <span class="status <?= htmlspecialchars($task['status']); ?>">
                                <?= ucfirst(str_replace('_', ' ', $task['status'])); ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($task['report_file']): ?>
                                <a class="report-link"
                                   href="../../uploads/task_reports/<?= $task['report_file']; ?>"
                                   target="_blank">
                                   📄 View Report
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($task['rating']): ?>
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?= $i <= $task['rating'] ? "⭐" : "☆"; ?>
                                    <?php endfor; ?>
                                </div>
                            <?php else: ?>
                                <span class="not-reviewed">Not Reviewed</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if (!$task['rating']): ?>
                                <form class="upload-form"
                                      action="upload_report.php"
                                      method="POST"
                                      enctype="multipart/form-data">

                                    <input type="hidden"
                                           name="task_id"
                                           value="<?= $task['task_id']; ?>">

                                    <input type="file"
                                           name="report"
                                           accept=".pdf"
                                           required>

                                    <button type="submit">
                                        Submit / Update
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="locked">🔒 Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>