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
                $id                 = $_POST["id"] ?? null;
                $employeecodeSelect = trim($_POST["employeecode_select"] ?? "");
                $otherUsername      = trim($_POST["other_username"] ?? "");
                $realName           = trim($_POST["real_name"] ?? "");
                $email              = trim($_POST["email"] ?? "");
                $lockout            = isset($_POST["lockout"]) ? "x" : "";
                $password           = $_POST["password"] ?? "";
                $retypePassword     = $_POST["retype_password"] ?? "";

                $employeecode = ($employeecodeSelect === "other") ? $otherUsername : $employeecodeSelect;
                $isExistingEmployee = ($employeecodeSelect !== "other" && $employeecodeSelect !== "");

                if (!ctype_digit((string)$id)) {
                    echo "<p>Invalid user.</p>";
                } elseif ($employeecode === "") {
                    echo "<p>You must select or enter an employee code / username.</p>";
                } elseif ($email === "" || strpos($email, "@") === false) {
                    echo "<p>You must enter a valid email address.</p>";
                } else {

                    $passwordProvided = ($password !== "" || $retypePassword !== "");
                    $passwordOk = true;

                    if ($passwordProvided) {
                        if (strlen($password) < 8) {
                            echo "<p>Password must be at least 8 characters long.</p>";
                            $passwordOk = false;
                        } elseif ($password !== $retypePassword) {
                            echo "<p>Password and retype password must match.</p>";
                            $passwordOk = false;
                        } elseif (
                            !preg_match('/[A-Z]/', $password) ||
                            !preg_match('/[a-z]/', $password) ||
                            !preg_match('/[^a-zA-Z0-9]/', $password)
                        ) {
                            echo "<p>Password must contain uppercase, lowercase letters, and a special character.</p>";
                            $passwordOk = false;
                        }
                    }

                    if ($passwordOk) {
                        // Kolla att employeecode inte redan används av en ANNAN user
                        $checkStmt = $conn->prepare("SELECT id FROM users WHERE employeecode = ? AND id != ?");
                        $checkStmt->bind_param("si", $employeecode, $id);
                        $checkStmt->execute();
                        $checkResult = $checkStmt->get_result();

                        if ($checkResult->num_rows > 0) {
                            echo "<p>Another user with that employee code / username already exists.</p>";
                        } else {
                            if ($passwordProvided) {
                                $hashedPassword = hash("sha256", $password);
                                $updateStmt = $conn->prepare(
                                    "UPDATE users SET employeecode = ?, emailaddress = ?, lockout = ?, passwd = ? WHERE id = ?"
                                );
                                $updateStmt->bind_param("ssssi", $employeecode, $email, $lockout, $hashedPassword, $id);
                            } else {
                                $updateStmt = $conn->prepare(
                                    "UPDATE users SET employeecode = ?, emailaddress = ?, lockout = ? WHERE id = ?"
                                );
                                $updateStmt->bind_param("sssi", $employeecode, $email, $lockout, $id);
                            }

                            if ($updateStmt->execute()) {
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
                                    "User updated",
                                    (string)$id,
                                    "User account updated for " . $employeecode . ".",
                                    "Users"
                                );
                                echo "<h2>User updated successfully!</h2>";
                                echo "<p>Redirecting to user list...</p>";
                                echo "<script>setTimeout(function() { window.location.href = 'users_read.php'; }, 2000);</script>";
                            } else {
                                echo "<p>Database error: Could not update user.</p>";
                            }
                            $updateStmt->close();
                        }
                        $checkStmt->close();
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
