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

$canEdit = ($securityAccessLevel === "A");
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
            <h2><?= htmlspecialchars($employee["name"]) ?></h2>

            <img class="registry-photo-large" src="<?= htmlspecialchars(getEmployeePhotoUrl($employee["employeecode"])) ?>" alt="<?= htmlspecialchars($employee["name"]) ?>" />

            <table class="registry-table">
                <tr><th>Employee Code</th><td><?= htmlspecialchars($employee["employeecode"]) ?></td></tr>
                <tr><th>Name</th><td><?= htmlspecialchars($employee["name"]) ?></td></tr>
                <tr><th>Signature Date</th><td><?= htmlspecialchars($employee["signatureDate"]) ?></td></tr>
                <tr><th>Date of Birth</th><td><?= htmlspecialchars($employee["dateOfBirth"]) ?></td></tr>
                <tr><th>Sex</th><td><?= htmlspecialchars($employee["sex"]) ?></td></tr>
                <tr><th>Blood Type</th><td><?= htmlspecialchars($employee["bloodType"]) ?></td></tr>
                <tr><th>Height</th><td><?= htmlspecialchars($employee["height"]) ?></td></tr>
                <tr><th>Weight</th><td><?= htmlspecialchars($employee["weight"]) ?></td></tr>
                <tr><th>Rank</th><td><?= htmlspecialchars($employee["rank"]) ?></td></tr>
                <tr><th>Security Access Level</th><td><?= htmlspecialchars($employee["securityAccessLevel"]) ?></td></tr>
                <tr><th>Department</th><td><?= htmlspecialchars($employee["department"]) ?></td></tr>
                <tr><th>Background</th><td><?= nl2br(htmlspecialchars($employee["Background"])) ?></td></tr>
                <tr><th>Strengths</th><td><?= nl2br(htmlspecialchars($employee["Strengths"])) ?></td></tr>
                <tr><th>Weaknesses</th><td><?= nl2br(htmlspecialchars($employee["Weaknesses"])) ?></td></tr>
            </table>

            <div style="clear: both;"></div>

            <?php if ($canEdit) { ?>
                <p class="registry-actions">
                    <a class="button" href="personnelregistry_edit.php?id=<?= urlencode($employee["id"]) ?>">Edit</a>
                    <a class="button" href="personnelregistry_delete_doit.php?id=<?= urlencode($employee["id"]) ?>"
                       onclick="return confirm('Are you sure you want to delete the employee (<?= (int)$employee["id"] ?>)?');">Delete</a>
                </p>
            <?php } ?>
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
