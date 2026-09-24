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
            <h2>Personnel register</h2>

            <table class="registry-table">
                <tr>
                    <th>Photo</th>
                    <th>Employee Code</th>
                    <th>Name</th>
                    <th>Signature Date</th>
                    <th>Rank</th>
                    <th>Access Level</th>
                </tr>
                <?php
                $result = mysqli_query($conn, "SELECT id, employeecode, name, signatureDate, rank, securityAccessLevel FROM employees ORDER BY employeecode ASC");

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $photoUrl = getEmployeePhotoUrl($row["employeecode"]);
                        echo "<tr>";
                        echo "<td><img class=\"registry-photo-thumb\" src=\"" . htmlspecialchars($photoUrl) . "\" alt=\"\" /></td>";
                        echo "<td>" . htmlspecialchars($row["employeecode"]) . "</td>";
                        echo "<td><a href=\"personnelregistry_read_employee.php?id=" . urlencode($row["id"]) . "\">" . htmlspecialchars($row["name"]) . "</a></td>";
                        echo "<td>" . htmlspecialchars($row["signatureDate"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["rank"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["securityAccessLevel"]) . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>

            <?php if ($securityAccessLevel === "A") { ?>
                <p class="registry-actions"><a class="button" href="personnelregistry_add.php">Add new employee</a></p>
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
