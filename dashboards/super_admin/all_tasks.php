<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['super_admin']);

/*
Fetch ALL task assignments system-wide
*/
$stmt = $pdo->query("
    SELECT 
        d.department_name,
        t.title,
        u.username,
        ta.status,
        ta.rating,
        t.created_at,
        ta.completed_at
    FROM task_assignments ta
    JOIN task t ON ta.task_id = t.task_id
    JOIN user u ON ta.user_id = u.user_id
    JOIN department d ON t.department_id = d.department_id
    ORDER BY d.department_name ASC, t.created_at DESC
");

$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tasks</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    </head>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<style>
    .page-container {
        max-width: 1300px;
        margin: 40px auto;
        padding: 20px;
        font-family: Arial, sans-serif;
        
    }

    .page-title {
        font-size: 2rem;
        color: #bedede;
        margin-bottom: 10px;
    }

    .admin-badge {
        display: inline-block;
       background: #e0f2fe;
        color: #0369a1;
        padding: 8px 14px;
        border-radius: 20px;
        font-weight: bold;
        margin-bottom: 25px;
    }

    .card {
        background: #bedede;
        border-radius: 14px;
        padding: 25px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th {
        background: #f8fafc;
        text-align: left;
        padding: 14px;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
    }

    td {
        padding: 14px;
        border-top: 1px solid #e2e8f0;
        color: #334155;
    }

    tr:hover {
        background: #f8fafc;
    }

    .status-pill {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-in_progress {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-completed {
        background: #dcfce7;
        color: #166534;
    }

    .empty-state {
        text-align: center;
        padding: 30px;
        color: #64748b;
        font-style: italic;
    }

    .task-title {
        font-weight: 600;
        color: #1e293b;
    }

    .timestamp {
        font-size: 14px;
        color: #64748b;
    }

    .rating-stars {
        font-size: 16px;
        letter-spacing: 2px;
    }
</style>

<div class="main-content">

    <h2 class="page-title">System-Wide Task Overview</h2>
    <div class="admin-badge">Super Admin Analytics</div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Task</th>
                    <th>Member</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Created</th>
                    <th>Completed</th>
                </tr>
            </thead>

            <tbody>

            <?php if (count($assignments) > 0): ?>
                <?php foreach ($assignments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['department_name']); ?></td>

                        <td>
                            <span class="task-title">
                                <?= htmlspecialchars($a['title']); ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($a['username']); ?></td>

                        <td>
                            <span class="status-pill status-<?= $a['status']; ?>">
                                <?= ucfirst(str_replace('_', ' ', $a['status'])); ?>
                            </span>
                        </td>

                        <td class="rating-stars">
                            <?php if ($a['rating']): ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $a['rating'] ? "⭐" : "☆"; ?>
                                <?php endfor; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>

                        <td class="timestamp">
                            <?= date('d M Y, H:i', strtotime($a['created_at'])); ?>
                        </td>

                        <td class="timestamp">
                            <?= $a['completed_at'] 
                                ? date('d M Y, H:i', strtotime($a['completed_at'])) 
                                : '-'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        No task activity found.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>