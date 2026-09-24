<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php  // ----- Locals: ------ ?>
<?php requireAccessLevelA($isLoggedIn, $securityAccessLevel); ?>
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
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $employeecodeSelect = trim($_POST["employeecode_select"] ?? "");
                $otherUsername      = trim($_POST["other_username"] ?? "");
                $realName           = trim($_POST["real_name"] ?? "");
                $email              = trim($_POST["email"] ?? "");
                $lockout            = isset($_POST["lockout"]) ? "x" : "";
                $password           = $_POST["password"] ?? "";
                $retypePassword     = $_POST["retype_password"] ?? "";

                $employeecode = ($employeecodeSelect === "other") ? $otherUsername : $employeecodeSelect;
                $isExistingEmployee = ($employeecodeSelect !== "other" && $employeecodeSelect !== "");

                if ($employeecode === "") {
                    echo "<p>You must select or enter an employee code / username.</p>";
                } elseif ($email === "" || strpos($email, "@") === false) {
                    echo "<p>You must enter a valid email address.</p>";
                } elseif (strlen($password) < 8) {
                    echo "<p>Password must be at least 8 characters long.</p>";
                } elseif ($password !== $retypePassword) {
                    echo "<p>Password and retype password must match.</p>";
                } elseif (
                    !preg_match('/[A-Z]/', $password) ||
                    !preg_match('/[a-z]/', $password) ||
                    !preg_match('/[^a-zA-Z0-9]/', $password)
                ) {
                    echo "<p>Password must contain uppercase, lowercase letters, and a special character.</p>";
                } else {
                    // Kontrollera att employeecode inte redan finns som user
                    $checkStmt = $conn->prepare("SELECT id FROM users WHERE employeecode = ?");
                    $checkStmt->bind_param("s", $employeecode);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();

                    if ($checkResult->num_rows > 0) {
                        echo "<p>A user with that employee code / username already exists.</p>";
                    } else {
                        $hashedPassword = hash("sha256", $password);

                        $insertStmt = $conn->prepare(
                            "INSERT INTO users (employeecode, passwd, logintimes, lastlogin, lockout, emailaddress, lastlogintime, loginhost)
                             VALUES (?, ?, 0, '', ?, ?, '', '')"
                        );
                        $insertStmt->bind_param("ssss", $employeecode, $hashedPassword, $lockout, $email);

                        if ($insertStmt->execute()) {
                            // Om användaren är kopplad till en befintlig anställd och namnet ändrats, uppdatera employees-tabellen
                            if ($isExistingEmployee && $realName !== "") {
                                $nameUpdateStmt = $conn->prepare("UPDATE employees SET name = ? WHERE employeecode = ?");
                                $nameUpdateStmt->bind_param("ss", $realName, $employeecode);
                                $nameUpdateStmt->execute();
                                $nameUpdateStmt->close();
                            }

                            logactivity(
                                $_SESSION["employeecode"] ?? "unknown",
                                date("Y-m-d"),
                                date("H:i:s"),
                                "New user created",
                                $employeecode,
                                "User created with email address " . $email . ".",
                                "Users"
                            );
                            echo "<h2>User created successfully!</h2>";
                            echo "<p>Redirecting to user list...</p>";
                            echo "<script>setTimeout(function() { window.location.href = 'users_read.php'; }, 2000);</script>";
                        } else {
                            echo "<p>Database error: Could not create user.</p>";
                        }
                        $insertStmt->close();
                    }
                    $checkStmt->close();
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
