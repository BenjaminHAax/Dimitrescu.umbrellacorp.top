<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php  // ----- Locals: ------ ?>
<?php requireLoggedIn($isLoggedIn); ?>
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
                $id = $_POST["id"] ?? null;

                if (!ctype_digit((string)$id) || !canAccessEmployee($conn, $isLoggedIn, $securityAccessLevel, $loggedInUser, $id)) {
                    echo "<h2>Access denied</h2><p>You may only edit your own profile, unless you have Security Access Level A.</p>";
                } else {
                    $isOwnProfileOnly = ($securityAccessLevel !== "A");

                    // Behövs för att kunna byta namn på fotofilen om employeecode ändras.
                    $originalStmt = $conn->prepare("SELECT employeecode FROM employees WHERE id = ?");
                    $originalStmt->bind_param("i", $id);
                    $originalStmt->execute();
                    $originalEmployeecode = $originalStmt->get_result()->fetch_assoc()["employeecode"] ?? "";
                    $originalStmt->close();

                    $name          = trim($_POST["name"] ?? "");
                    $signatureDate = trim($_POST["signatureDate"] ?? "");
                    $dateOfBirth   = trim($_POST["dateOfBirth"] ?? "");
                    $sex           = trim($_POST["sex"] ?? "");
                    $bloodType     = trim($_POST["bloodType"] ?? "");
                    $height        = trim($_POST["height"] ?? "");
                    $weight        = trim($_POST["weight"] ?? "");
                    $rank          = trim($_POST["rank"] ?? "");
                    $department    = trim($_POST["department"] ?? "");
                    $background    = trim($_POST["Background"] ?? "");
                    $strengths     = trim($_POST["Strengths"] ?? "");
                    $weaknesses    = trim($_POST["Weaknesses"] ?? "");

                    if ($name === "") {
                        echo "<p>Name is required.</p>";
                    } else {
                        if ($isOwnProfileOnly) {
                            // Egen profil: Employee Code och Security Access Level får INTE ändras.
                            $updateStmt = $conn->prepare(
                                "UPDATE employees SET
                                    name = ?, signatureDate = ?, dateOfBirth = ?, sex = ?, bloodType = ?,
                                    height = ?, weight = ?, rank = ?, department = ?,
                                    Background = ?, Strengths = ?, Weaknesses = ?
                                 WHERE id = ?"
                            );
                            $updateStmt->bind_param(
                                "ssssssssssssi",
                                $name, $signatureDate, $dateOfBirth, $sex, $bloodType,
                                $height, $weight, $rank, $department,
                                $background, $strengths, $weaknesses, $id
                            );
                        } else {
                            $employeecode = trim($_POST["employeecode"] ?? "");
                            $securityAccessLevelNew = trim($_POST["securityAccessLevel"] ?? "C");

                            if ($employeecode === "") {
                                echo "<p>Employee Code is required.</p>";
                                $updateStmt = null;
                            } else {
                                // Kolla att koden inte redan används av en ANNAN anställd
                                $checkStmt = $conn->prepare("SELECT id FROM employees WHERE employeecode = ? AND id != ?");
                                $checkStmt->bind_param("si", $employeecode, $id);
                                $checkStmt->execute();
                                $checkResult = $checkStmt->get_result();

                                if ($checkResult->num_rows > 0) {
                                    echo "<p>Another employee with that Employee Code already exists.</p>";
                                    $updateStmt = null;
                                } else {
                                    $updateStmt = $conn->prepare(
                                        "UPDATE employees SET
                                            employeecode = ?, name = ?, signatureDate = ?, dateOfBirth = ?, sex = ?, bloodType = ?,
                                            height = ?, weight = ?, rank = ?, securityAccessLevel = ?, department = ?,
                                            Background = ?, Strengths = ?, Weaknesses = ?
                                         WHERE id = ?"
                                    );
                                    $updateStmt->bind_param(
                                        "ssssssssssssssi",
                                        $employeecode, $name, $signatureDate, $dateOfBirth, $sex, $bloodType,
                                        $height, $weight, $rank, $securityAccessLevelNew, $department,
                                        $background, $strengths, $weaknesses, $id
                                    );
                                }
                                $checkStmt->close();
                            }
                        }

                        if (isset($updateStmt) && $updateStmt) {
                            if ($updateStmt->execute()) {
                                // Om employeecode ändrades (endast möjligt för Level A), byt namn på ev. befintligt foto.
                                $finalEmployeecode = $isOwnProfileOnly ? $originalEmployeecode : $employeecode;
                                if (!$isOwnProfileOnly && $finalEmployeecode !== $originalEmployeecode) {
                                    renameEmployeePhoto($originalEmployeecode, $finalEmployeecode);
                                }

                                $photoError = null;
                                if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK && $_FILES["photo"]["size"] > 0) {
                                    $photoResult = saveEmployeePhoto($_FILES["photo"]["tmp_name"], $finalEmployeecode);
                                    if ($photoResult !== true) {
                                        $photoError = $photoResult;
                                    }
                                } elseif (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_INI_SIZE) {
                                    $photoError = "The photo was too large (max " . ini_get("upload_max_filesize") . "B) and was not saved.";
                                }

                                logactivity(
                                    $_SESSION["employeecode"] ?? "unknown",
                                    date("Y-m-d"),
                                    date("H:i:s"),
                                    "Employee updated",
                                    (string)$id,
                                    "Employee profile updated for " . $finalEmployeecode . ".",
                                    "Personnel registry"
                                );
                                echo "<h2>Employee updated successfully!</h2>";
                                if ($photoError !== null) {
                                    echo "<p>Note: " . htmlspecialchars($photoError) . "</p>";
                                }
                                echo "<p>Redirecting to personnel register...</p>";
                                echo "<script>setTimeout(function() { window.location.href = 'personnelregistry_read.php'; }, 2000);</script>";
                            } else {
                                echo "<p>Database error: Could not update employee.</p>";
                            }
                            $updateStmt->close();
                        }
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
