<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/auth/auth.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/soc_lab/config/db.php";

 $user_id = $_SESSION['user_id'];

 $stmt = $pdo->prepare("SELECT username FROM user WHERE user_id = ?");
 $stmt->execute([$user_id]);
 $user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    </head>
<?php include "../includes/header.php"; ?>
<?php include "../includes/navbar.php"; ?>

    <style>
        /* Container to center the card on the page */

        /* Card styling */
        .profile-card {
            background-color: #bedede;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 30px;
            border: 1px solid #e9ecef;
        }

        /* Headings */
        .profile-header {
            text-align: center;
            color: #333;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 24px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .section-subtitle {
            font-size: 13px;
            color: #888;
            margin-top: -15px;
            margin-bottom: 20px;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }

        .form-group input:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
            outline: none;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #222a36;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 30px;
        }

        .btn-submit:hover {
            background-color: #107070;
        }
    </style>

<div class="main-content">

    <div class="profile-card">
        <h2 class="profile-header">My Profile</h2>

        <form method="POST" action="process_profile_update.php">
            
            <!-- Account Details Section -->
            <div class="section-title">Account Details</div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="New Username" required>
            </div>
<div class="form-group">
    <label for="confirm_username">Confirm Username</label>
    <input type="text" id="confirm_username" name="confirm_username" placeholder="Confirm username" required>
</div>
            <!-- Password Section -->
            <div class="section-title">Change Password</div>
            <p class="section-subtitle">Leave blank if you don't want to change your password.</p>

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
            </div>

            <button type="submit" class="btn-submit">Update Profile</button>

        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
</body>
</html>