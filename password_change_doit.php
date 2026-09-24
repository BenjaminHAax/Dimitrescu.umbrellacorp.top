<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php  // ----- Locals: ------ ?>
<?php include("_master_head.php") ?>

<div class="grid-container">
    <!---- GRID ROW 1 START ------------------------------------------>
    <div class="grid-emptyblack"></div>
    <div class="grid-header">
        <?php include("_master_header.php") ?>
    </div>
    <div class="grid-emptyblack"></div>
    <!---- GRID ROW 1 END --------------------------------------------->

    <!---- GRID ROW 2 START ------------------------------------------->
    <?php $breadcrumimage = randomBreadcrumImage(); ?>
    <div class="grid-breadcrum" style="background-image: url(images/<?= $breadcrumimage ?>);">
        <?php include("_master_breadcrum.php") ?>
    </div>
    <!---- GRID ROW 2 END --------------------------------------------->

    <!---- GRID ROW 3 START ------------------------------------------->
    <div class="grid-topmenu">
        <?php include("_master_menu.php") ?>
    </div>
    <!---- GRID ROW 3 END -------------------------------------------->

    <!---- GRID ROW 4 START ------------------------------------------>
    <div class="grid-empty"></div>
    <div class="grid-main">
        <div class="main">
            <?php
            if ($isLoggedIn == false) {
                echo "<p>You must be logged in to change your password.</p>";
                exit;
            }

            $employeecode = $_SESSION['employeecode'] ?? '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $oldPassword       = $_POST['old_password'] ?? '';
                $newPassword       = $_POST['new_password'] ?? '';
                $retypeNewPassword = $_POST['retype_new_password'] ?? '';

                


                // Büyük/küçük harf ve özel karakter denetimi (şifre JS tarafından hashlenmemişse)
                $hasComplexity = (strlen($newPassword) === 64) || (
                    preg_match('/[A-Z]/', $newPassword) && 
                    preg_match('/[a-z]/', $newPassword) && 
                    preg_match('/[^a-zA-Z0-9]/', $newPassword)
                );

                // 1. Şifre kurallarını doğrula
                if (strlen($oldPassword) < 8) {
                    echo "<p>Old password must be at least 8 characters long.</p>";
                } elseif (strlen($newPassword) < 8) {
                    echo "<p>New password must be at least 8 characters long.</p>";
                } elseif ($newPassword !== $retypeNewPassword) {
                    echo "<p>New password and retype new password must match.</p>";
                } elseif (!$hasComplexity) {
                    echo "<p>New password must contain uppercase, lowercase letters, and a special character.</p>";
                } else {
                    // 2. Çift hash koruması (JS hash yaptıysa dokunmaz, düz metinse SHA-256 yapar)
                    $hashedOldPassword = (strlen($oldPassword) === 64) ? $oldPassword : hash('sha256', $oldPassword);
                    $hashedNewPassword = (strlen($newPassword) === 64) ? $newPassword : hash('sha256', $newPassword);

                    // 3. Veritabanındaki eski parolayı sorgula
                    $query = "SELECT passwd FROM users WHERE employeecode = '$employeecode'";
                    $result = mysqli_query($conn, $query);

                    if ($row = mysqli_fetch_assoc($result)) {
                        if ($row['passwd'] !== $hashedOldPassword) {
                            echo "<p>Old password is incorrect.</p>";
                        } else {
                            // 4. Yeni şifreyi güncelle
                            $updateQuery = "UPDATE users SET passwd = '$hashedNewPassword' WHERE employeecode = '$employeecode'";
                            if (mysqli_query($conn, $updateQuery)) {
                                logactivity(
                                    $employeecode,
                                    date("Y-m-d"),
                                    date("H:i:s"),
                                    "Password changed",
                                    $employeecode,
                                    "The logged-in user changed their password.",
                                    "Password"
                                );
                                echo "<h2>Password changed successfully!</h2>";
                                echo "<p>Redirecting to home page...</p>";
                                echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2500);</script>";
                            } else {
                                echo "<p>Database error: Could not update password.</p>";
                            }
                        }
                    } else {
                        echo "<p>User not found.</p>";
                    }
                }
            }
            ?>
        </div>
    </div>
    <div class="grid-rightmenu">
        <?php include("_master_info-menu.php") ?>
    </div>
    <div class="grid-empty"></div>
    <!---- GRID ROW 4 END --------------------------------------------->

    <div class="grid-emptyblack"></div>
    <div class="grid-footer">
        <?php include("_master_footer.php") ?>
    </div>
    <div class="grid-emptyblack"></div>
</div>

<?php include("_master_bottom.php") ?>