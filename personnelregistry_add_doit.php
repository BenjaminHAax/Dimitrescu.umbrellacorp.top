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
                $employeecode       = trim($_POST["employeecode"] ?? "");
                $name                = trim($_POST["name"] ?? "");
                $signatureDate       = trim($_POST["signatureDate"] ?? "");
                $dateOfBirth         = trim($_POST["dateOfBirth"] ?? "");
                $sex                 = trim($_POST["sex"] ?? "");
                $bloodType           = trim($_POST["bloodType"] ?? "");
                $height              = trim($_POST["height"] ?? "");
                $weight              = trim($_POST["weight"] ?? "");
                $rank                = trim($_POST["rank"] ?? "");
                $securityAccessLevelNew = trim($_POST["securityAccessLevel"] ?? "C");
                $department          = trim($_POST["department"] ?? "");
                $background          = trim($_POST["Background"] ?? "");
                $strengths           = trim($_POST["Strengths"] ?? "");
                $weaknesses          = trim($_POST["Weaknesses"] ?? "");

                if ($employeecode === "" || $name === "") {
                    echo "<p>Employee Code and Name are required.</p>";
                } else {
                    $checkStmt = $conn->prepare("SELECT id FROM employees WHERE employeecode = ?");
                    $checkStmt->bind_param("s", $employeecode);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();

                    if ($checkResult->num_rows > 0) {
                        echo "<p>An employee with that Employee Code already exists.</p>";
                    } else {
                        // Tabellen employees saknar AUTO_INCREMENT på id, så nästa lediga id
                        // måste räknas fram manuellt.
                        $idResult = mysqli_query($conn, "SELECT MAX(id) AS maxId FROM employees");
                        $idRow = mysqli_fetch_assoc($idResult);
                        $newId = ($idRow["maxId"] ?? 0) + 1;

                        $insertStmt = $conn->prepare(
                            "INSERT INTO employees
                                (id, employeecode, name, signatureDate, dateOfBirth, sex, bloodType, height, weight, rank, securityAccessLevel, department, Background, Strengths, Weaknesses)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                        );
                        $insertStmt->bind_param(
                            "issssssssssssss",
                            $newId, $employeecode, $name, $signatureDate, $dateOfBirth, $sex, $bloodType,
                            $height, $weight, $rank, $securityAccessLevelNew, $department,
                            $background, $strengths, $weaknesses
                        );

                        if ($insertStmt->execute()) {
                            $photoError = null;
                            if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === UPLOAD_ERR_OK && $_FILES["photo"]["size"] > 0) {
                                $photoResult = saveEmployeePhoto($_FILES["photo"]["tmp_name"], $employeecode);
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
                                "New employee created",
                                $employeecode,
                                "Employee " . $name . " was added.",
                                "Personnel registry"
                            );
                            echo "<h2>Employee created successfully!</h2>";
                            if ($photoError !== null) {
                                echo "<p>Note: " . htmlspecialchars($photoError) . "</p>";
                            }
                            echo "<p>Redirecting to personnel register...</p>";
                            echo "<script>setTimeout(function() { window.location.href = 'personnelregistry_read.php'; }, 2000);</script>";
                        } else {
                            echo "<p>Database error: Could not create employee.</p>";
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
