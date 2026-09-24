<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_f_sendmail.php") ?>
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
            // Genererar ett slumpmässigt lösenord server-side.
            // Speglar exakt samma logik/teckenuppsättning som scripts/randomPassword.js,
            // men körs i PHP eftersom lösenordet måste skapas och sparas på servern
            // (klientens JavaScript kan inte anropas från en PHP-fil).
            function generateRandomPassword($length = 12) {
                $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
                $charsetLength = strlen($charset);
                $password = "";

                for ($i = 0; $i < $length; $i++) {
                    // random_int är kryptografiskt säkert (till skillnad från Math.random i JS-varianten)
                    $password .= $charset[random_int(0, $charsetLength - 1)];
                }

                return $password;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $employeecode      = trim($_POST['employeecode'] ?? '');
                $email             = trim($_POST['email'] ?? '');
                $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

                // 1. Employeecode och email får inte vara tomma
                if ($employeecode === '') {
                    echo "<p>Employee code cannot be empty.</p>";
                } elseif ($email === '') {
                    echo "<p>Email cannot be empty.</p>";
                } elseif ($recaptchaResponse === '') {
                    echo "<p>Please verify that you are not a robot.</p>";
                } else {

                    // 2. Verifiera Google reCAPTCHA server-side
                    $recaptchaOk = false;
                    $recaptchaVerify = @file_get_contents(
                        "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($recaptcha_secret_key) .
                        "&response=" . urlencode($recaptchaResponse) .
                        "&remoteip=" . urlencode($_SERVER['REMOTE_ADDR'])
                    );

                    if ($recaptchaVerify !== false) {
                        $recaptchaResult = json_decode($recaptchaVerify, true);
                        $recaptchaOk = $recaptchaResult['success'] ?? false;
                    }

                    if (!$recaptchaOk) {
                        echo "<p>Captcha verification failed. Please try again.</p>";
                    } else {

                        // 3. Kontrollera om employeecode och email matchar i databasen
                        $stmt = $conn->prepare("SELECT id FROM users WHERE employeecode = ? AND emailaddress = ?");
                        $stmt->bind_param("ss", $employeecode, $email);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows === 1) {

                            // 4. Skaffa ett slumpmässigt lösenord
                            try {
                                $newPassword = generateRandomPassword();
                            } catch (Exception $e) {
                                $newPassword = null;
                                echo "<p>Something went wrong while generating a new password. Please try again later.</p>";
                            }

                            if ($newPassword !== null) {
                                $hashedNewPassword = hash('sha256', $newPassword);

                                // 5. Registrera det nya lösenordet i databasen
                                $updateStmt = $conn->prepare("UPDATE users SET passwd = ? WHERE employeecode = ? AND emailaddress = ?");
                                $updateStmt->bind_param("sss", $hashedNewPassword, $employeecode, $email);

                                if ($updateStmt->execute() && $updateStmt->affected_rows >= 0) {

                                    // 6. Skicka det nya lösenordet via mail
                                    $mailSent = sendmail(
                                        $email,
                                        "Password Reset",
                                        "Your password has been reset.\n\nYour new password is: " . $newPassword .
                                        "\n\nPlease log in and change it as soon as possible."
                                    );

                                    if ($mailSent) {
                                        logactivity(
                                            $employeecode,
                                            date("Y-m-d"),
                                            date("H:i:s"),
                                            "Forgot password",
                                            $employeecode,
                                            "A reset password was generated and sent to the registered email address.",
                                            "Password"
                                        );
                                        echo "<h2>New password sent!</h2>";
                                        echo "<p>A new password has been generated and sent to your email address.</p>";
                                        echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 3000);</script>";
                                    } else {
                                        // Mailet kunde inte skickas - varna användaren så de inte blir låsta ute
                                        echo "<p>Your password was reset, but the email could not be sent. Please contact an administrator.</p>";
                                    }
                                } else {
                                    echo "<p>Database error: Could not update password.</p>";
                                }

                                $updateStmt->close();
                            }
                        } else {
                            echo "<p>No user found with that employee code and email combination.</p>";
                        }

                        $stmt->close();
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
