<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php  // ----- Locals: ------ ?>
<?php requireLoggedIn($isLoggedIn); ?>
<?php
$id = $_GET["id"] ?? null;

if ($id === null || !ctype_digit((string)$id)) {
    header("Location: personnelregistry_read.php");
    exit;
}

if (!canAccessEmployee($conn, $isLoggedIn, $securityAccessLevel, $loggedInUser, $id)) {
    die("<h2>Access denied</h2><p>You may only edit your own profile, unless you have Security Access Level A.</p>");
}

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

if (!$employee) {
    header("Location: personnelregistry_read.php");
    exit;
}

// Egen profil-redigering (icke Level A) får inte ändra Employee Code eller Security Access Level.
$isOwnProfileOnly = ($securityAccessLevel !== "A");
?>
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
            <h2>Edit employee</h2>

            <div class="registry-form">
                <form action="personnelregistry_edit_doit.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($employee["id"]) ?>" />

                    <div class="registry-columns">
                        <div class="registry-photo-col">
                            <div class="registry-photo-box">
                                <img src="<?= htmlspecialchars(getEmployeePhotoUrl($employee["employeecode"])) ?>" alt="<?= htmlspecialchars($employee["name"]) ?>" />
                            </div>
                            <p class="registry-photo-upload">Photo: <br />
                                <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" /></p>

                            <p>Employee Code: <br />
                                <?php if ($isOwnProfileOnly) { ?>
                                    <input type="text" value="<?= htmlspecialchars($employee["employeecode"]) ?>" disabled />
                                <?php } else { ?>
                                    <input type="text" name="employeecode" maxlength="7" value="<?= htmlspecialchars($employee["employeecode"]) ?>" required />
                                <?php } ?>
                            </p>
                            <p>Security Access Level: <br />
                                <?php if ($isOwnProfileOnly) { ?>
                                    <input type="text" value="<?= htmlspecialchars($employee["securityAccessLevel"]) ?>" disabled />
                                <?php } else { ?>
                                    <select name="securityAccessLevel">
                                        <?php foreach (["A", "B", "C"] as $level) { ?>
                                            <option value="<?= $level ?>" <?= ($employee["securityAccessLevel"] === $level) ? "selected" : "" ?>><?= $level ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="registry-fields-col">
                            <p>Name: <br />
                                <input type="text" name="name" maxlength="24" value="<?= htmlspecialchars($employee["name"]) ?>" required /></p>
                            <p>Signature Date: <br />
                                <input type="text" name="signatureDate" maxlength="10" placeholder="YYYY-MM-DD" value="<?= htmlspecialchars($employee["signatureDate"]) ?>" /></p>
                            <p>Date of Birth: <br />
                                <input type="text" name="dateOfBirth" maxlength="10" placeholder="YYYY-MM-DD" value="<?= htmlspecialchars($employee["dateOfBirth"]) ?>" /></p>
                            <p>Sex: <br />
                                <input type="text" name="sex" maxlength="1" value="<?= htmlspecialchars($employee["sex"]) ?>" /></p>
                            <p>Blood Type: <br />
                                <input type="text" name="bloodType" maxlength="7" value="<?= htmlspecialchars($employee["bloodType"]) ?>" /></p>
                            <p>Height: <br />
                                <input type="text" name="height" maxlength="6" value="<?= htmlspecialchars($employee["height"]) ?>" /></p>
                            <p>Weight: <br />
                                <input type="text" name="weight" maxlength="7" value="<?= htmlspecialchars($employee["weight"]) ?>" /></p>
                            <p>Rank: <br />
                                <input type="text" name="rank" maxlength="19" value="<?= htmlspecialchars($employee["rank"]) ?>" /></p>
                            <p>Department: <br />
                                <input type="text" name="department" maxlength="23" value="<?= htmlspecialchars($employee["department"]) ?>" /></p>
                        </div>
                    </div>

                    <p>Background: <br />
                        <textarea name="Background" rows="4" maxlength="1332"><?= htmlspecialchars($employee["Background"]) ?></textarea></p>
                    <p>Strengths: <br />
                        <textarea name="Strengths" rows="4" maxlength="1101"><?= htmlspecialchars($employee["Strengths"]) ?></textarea></p>
                    <p>Weaknesses: <br />
                        <textarea name="Weaknesses" rows="4" maxlength="1133"><?= htmlspecialchars($employee["Weaknesses"]) ?></textarea></p>

                    <p>
                        <input type="submit" class="button" value="Update" />
                        <input class="button" type="reset" value="Reset" />
                    </p>
                </form>
            </div>
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
