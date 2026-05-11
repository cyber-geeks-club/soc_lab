<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['department_admin']);

$department_id = $_SESSION['department_id'];

/*
Fetch members in this department
*/
$stmt = $pdo->prepare("
    SELECT user_id, username
    FROM user
    WHERE department_id = ?
    AND role_id = 3
    AND status = 'active'
");
$stmt->execute([$department_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<style>
    .page-container {
        max-width: 850px;
        margin: 40px auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .page-title {
        font-size: 2rem;
        color: #bedede;
        margin-bottom: 10px;
    }

    .task-badge {
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
        padding: 28px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }

    .form-group {
        margin-bottom: 22px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #475569;
    }

    input[type="text"],
    textarea,
    select {
        width: 100%;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 15px;
        box-sizing: border-box;
        font-family: inherit;
    }

    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }

    textarea {
        min-height: 140px;
        resize: vertical;
    }

    select[multiple] {
        min-height: 180px;
    }

    .helper-text {
        font-size: 14px;
        color: #64748b;
        margin-top: 6px;
    }

    .btn {
        border: none;
        padding: 12px 20px;
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
        background: #2d7e9c;
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


    <h2 class="page-title">Create Task</h2>
    <div class="task-badge">Task Assignment Panel</div>

    <?php
$successMessages = [
    'task_created' => 'Task created successfully.'
];

$errorMessages = [
    'invalid_request' => 'Something went wrong. Please try again.',
    'no_users_selected' => 'Please select at least one member.'
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

    <div class="card">
        <form action="process_create_task.php" method="POST">

            <div class="form-group">
                <label>Task Title</label>
                <input type="text" name="title" placeholder="Enter task title" required>
            </div>

            <div class="form-group">
                <label>Task Description</label>
                <textarea 
                    name="description"
                    placeholder="Describe the task, expectations, or instructions..."
                    required
                ></textarea>
            </div>

            <div class="form-group">
                <label>Assign Members</label>
                <select name="assigned_users[]" multiple required>
                    <?php foreach ($members as $member): ?>
                        <option value="<?= $member['user_id']; ?>">
                            <?= htmlspecialchars($member['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="helper-text">
                    Hold <strong>CTRL</strong> (Windows) or <strong>CMD</strong> (Mac) to select multiple members.
                </div>
            </div>

<div id="selectedCount" class="helper-text">0 members selected</div>

<script>
const select = document.querySelector('select[name="assigned_users[]"]');
const counter = document.getElementById('selectedCount');

select.addEventListener('change', () => {
    counter.textContent =
        select.selectedOptions.length + ' member(s) selected';
});
</script>
<br>
            <button type="submit" class="btn btn-primary">
                Create Task
            </button>

        </form>
    </div>

</div>
<script>
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.display = 'none';
    });
}, 4000);
</script>

<?php include '../../includes/footer.php'; ?>