<?php
require_once '../../auth/auth.php';
require_once '../../auth/authorize.php';
require_once '../../config/db.php';

authorizeRole(['member']);

$user_id = $_SESSION['user_id'];
$department_id = $_SESSION['department_id'];

/*
Fetch user skills
*/
$stmt = $pdo->prepare("
    SELECT skill_id, skill_description, created_at
    FROM user_skill
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        color: #1e293b;
        margin-bottom: 20px;
    }

    /* CARD */
    .card {
        background: white;
        padding: 24px;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        margin-bottom: 25px;
    }

    .card h4 {
        margin-bottom: 15px;
        color: #334155;
    }

    textarea {
        width: 100%;
        min-height: 120px;
        padding: 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        resize: vertical;
        outline: none;
    }

    textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* BUTTONS */
    .btn-primary,
    .btn-danger {
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-primary {
        background: #222a36;
        color: white;
    }

    .btn-primary:hover {
        background: #47949e;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    /* TABLE */
    .table-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f8fafc;
    }

    th {
        text-align: left;
        padding: 16px;
        color: #334155;
        font-weight: 600;
    }

    td {
        padding: 16px;
        border-top: 1px solid #e2e8f0;
        vertical-align: top;
        color: #222a36;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    /* SKILL BADGE NUMBER */
    .skill-number {
        background: #dbeafe;
        color: #222a36;
        font-weight: bold;
        padding: 6px 10px;
        border-radius: 999px;
        display: inline-block;
        min-width: 30px;
        text-align: center;
    }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 30px;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .table-card {
            overflow-x: auto;
        }

        table {
            min-width: 700px;
        }
    }
</style>

<div class="main-content">

    <h2>My Skills</h2>

    <!-- Add Skill Form -->
    <div class="card">
        <h4>Add New Skill</h4>

        <form action="process_add_skill.php" method="POST">
            <textarea
                name="skill_description"
                placeholder="Describe the skill you acquired..."
                required></textarea>

            <br><br>

            <button type="submit" class="btn-primary">
                Add Skill
            </button>
        </form>
    </div>

    <!-- Skills Table -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Skill Description</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($skills) > 0): ?>
                    <?php foreach ($skills as $index => $skill): ?>
                        <tr>
                            <td>
                                <span class="skill-number">
                                    <?= $index + 1; ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($skill['skill_description']); ?>
                            </td>

                            <td>
                                <?= date('M d, Y', strtotime($skill['created_at'])); ?>
                            </td>

                            <td>
                                <form action="delete_skill.php"
                                      method="POST"
                                      onsubmit="return confirm('Delete this skill?');">

                                    <input type="hidden"
                                           name="skill_id"
                                           value="<?= $skill['skill_id']; ?>">

                                    <button type="submit" class="btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-state">
                            No skills documented yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>