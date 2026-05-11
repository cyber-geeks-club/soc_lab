<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeSuperAdmin();

/* ========= FILTERS ========= */

$action_type = $_GET['action_type'] ?? '';
$username    = $_GET['username'] ?? '';

$query = "
    SELECT activity_log.*, user.username 
    FROM activity_log
    LEFT JOIN user ON activity_log.user_id = user.user_id
    WHERE 1
";

$params = [];

if (!empty($action_type)) {
    $query .= " AND action_type = ?";
    $params[] = $action_type;
}

if (!empty($username)) {
    $query .= " AND user.username LIKE ?";
    $params[] = "%$username%";
}

$query .= " ORDER BY created_at DESC LIMIT 100";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    </head>
<?php include "../../includes/header.php"; ?>
<?php include "../../includes/navbar.php"; ?>

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

    .security-badge {
        display: inline-block;
        background: #fee2e2;
        color: #b91c1c;
        padding: 8px 14px;
        border-radius: 20px;
        font-weight: bold;
        margin-bottom: 25px;
    }

    .card {
        background: #ffffff;
        border-radius: 14px;
        padding: 25px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        overflow-x: auto;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 220px;
    }

    label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #475569;
    }

    input,
    select {
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 15px;
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    .btn {
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .btn-primary {
        background: #222a36;
        color: white;
    }

    .btn-primary:hover {
        background: #4e96bd;
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

    .action-pill {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        background: #e0f2fe;
        color: #0369a1;
    }

    .timestamp {
        font-size: 14px;
        color: #64748b;
        white-space: nowrap;
    }

    .ip-address {
        font-family: monospace;
        color: #475569;
    }

    .empty-state {
        text-align: center;
        padding: 30px;
        color: #64748b;
        font-style: italic;
    }
</style>

<div class="main-content">

    <h2 class="page-title">Activity Logs</h2>
    <div class="security-badge">Security Monitoring</div>

    <!-- FILTERS -->
    <div class="card">
        <form method="GET" class="filter-form">

            <div class="form-group">
                <label>Search Username</label>
                <input 
                    type="text"
                    name="username"
                    placeholder="Search by username"
                    value="<?= htmlspecialchars($username) ?>"
                >
            </div>

            <div class="form-group">
                <label>Action Type</label>
                <select name="action_type">
                    <option value="">All Actions</option>
                    <option value="login" <?= $action_type === 'login' ? 'selected' : '' ?>>Login</option>
                    <option value="failed_login" <?= $action_type === 'failed_login' ? 'selected' : '' ?>>Failed Login</option>
                    <option value="create_user" <?= $action_type === 'create_user' ? 'selected' : '' ?>>Create User</option>
                    <option value="delete_user" <?= $action_type === 'delete_user' ? 'selected' : '' ?>>Delete User</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Filter Logs
            </button>
        </form>
    </div>

    <!-- LOG TABLE -->
    <div class="card">
        <table>
            <tr>
                <th>Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP Address</th>
            </tr>

            <?php if (count($logs) > 0): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="timestamp">
                            <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                        </td>

                        <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>

                        <td>
                            <span class="action-pill">
                                <?= htmlspecialchars($log['action_type']) ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($log['action_description']) ?></td>

                        <td class="ip-address">
                            <?= htmlspecialchars($log['ip_address']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-state">
                        No activity logs found.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

</div>

<?php include "../../includes/footer.php"; ?>