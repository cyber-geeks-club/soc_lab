<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/authorize.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

authorizeSuperAdmin();

// Fetch roles
$roles = $pdo->query("SELECT role_id, role_name FROM role")->fetchAll();

// Fetch departments
$departments = $pdo->query("SELECT department_id, department_name FROM department")->fetchAll();

// Fetch users with role and department
try {
    $stmt = $pdo->query("
        SELECT 
            u.user_id, 
            u.username, 
            u.status,
            r.role_name,
            d.department_name
        FROM user u
        LEFT JOIN role r ON u.role_id = r.role_id
        LEFT JOIN department d ON u.department_id = d.department_id
        ORDER BY u.username ASC
    ");

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    $users = []; // fallback so foreach doesn’t break
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    </head>
<?php include "../../includes/header.php"; ?>
<?php include "../../includes/navbar.php"; ?>

<style>
    .page-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .page-title {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #bedede;
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
        margin-bottom: 30px;
    }

    .card h3 {
        margin-top: 0;
        color: #334155;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 18px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
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
        padding: 10px 18px;
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
        background: #2998b1;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .btn-success {
        background: #22c55e;
        color: white;
    }

    .btn-success:hover {
        background: #16a34a;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th {
        background: #f8fafc;
        text-align: left;
        padding: 14px;
        color: #475569;
        font-size: 14px;
    }

    td {
        padding: 14px;
        border-top: 1px solid #e2e8f0;
        color: #222a36;
    }

    tr:hover {
        background: #f8fafc;
    }

    .status-active {
        color: #16a34a;
        font-weight: bold;
    }

    .status-disabled {
        color: #dc2626;
        font-weight: bold;
    }

    .you-label {
        color: #64748b;
        font-style: italic;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-left: 5px solid #22c55e;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 5px solid #ef4444;
    }
</style>
<div class="main-content">

    <h2 class="page-title">Manage Users</h2>

    <div class="admin-badge">Super Admin</div>

    <?php
    $successMessages = [
        'user_created' => 'User account created successfully.',
        'user_disabled' => 'User disabled successfully.',
        'user_enabled' => 'User enabled successfully.'
    ];

    $errorMessages = [
        'user_exists' => 'Username already exists.',
        'invalid_action' => 'Something went wrong.',
        'cannot_disable_self' => 'You cannot disable your own account.'
    ];
    ?>

    <?php if (isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
        <div class="alert alert-success">
            ✅ <?= $successMessages[$_GET['success']] ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && isset($errorMessages[$_GET['error']])): ?>
        <div class="alert alert-error">
            ❌ <?= $errorMessages[$_GET['error']] ?>
        </div>
    <?php endif; ?>

    <!-- CREATE USER -->
    <div class="card">
        <h3>Create New User</h3>

        <form method="POST" action="process_create_user.php">

            <div class="form-grid">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['role_id'] ?>">
                                <?= htmlspecialchars($role['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id" required>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['department_id'] ?>">
                                <?= htmlspecialchars($dept['department_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Create User
            </button>
        </form>
    </div>

    <!-- USERS TABLE -->
    <div class="card">
        <h3>All System Users</h3>

        <table>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Department</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['role_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($u['department_name'] ?? 'N/A') ?></td>

                <td>
                    <span class="<?= $u['status'] === 'active' ? 'status-active' : 'status-disabled' ?>">
                        <?= htmlspecialchars($u['status']) ?>
                    </span>
                </td>

                <td>
                    <?php if ($u['user_id'] != $_SESSION['user_id']): ?>

                        <?php if ($u['status'] === 'active'): ?>
                            <form method="POST" action="toggle_user_status.php"
                                  onsubmit="return confirm('Disable this user?');">
                                <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                <input type="hidden" name="action" value="disable">
                                <button type="submit" class="btn btn-danger">Disable</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="toggle_user_status.php"
                                  onsubmit="return confirm('Enable this user?');">
                                <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                <input type="hidden" name="action" value="enable">
                                <button type="submit" class="btn btn-success">Enable</button>
                            </form>
                        <?php endif; ?>

                    <?php else: ?>
                        <span class="you-label">(You)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>

<script>
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.display = 'none';
    });
}, 4000);
</script>

<?php include "../../includes/footer.php"; ?>