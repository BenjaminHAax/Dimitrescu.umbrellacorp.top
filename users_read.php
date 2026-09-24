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
            <h2>Users</h2>

            <table class="registry-table">
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email address</th>
                    <th>Locked out</th>
                    <th></th>
                    <th></th>
                </tr>
                <?php
                // Join users mot employees för att visa Name (namnet lagras i employees-tabellen).
                // Users som inte är kopplade till en employee (t.ex. root/system/backup) visar "-" som namn.
                $query = "SELECT u.id, u.employeecode, u.emailaddress, u.lockout, e.name
                          FROM users u
                          LEFT JOIN employees e ON u.employeecode = e.employeecode
                          ORDER BY u.employeecode ASC";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $isLockedOut = ($row["lockout"] === "x");
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["employeecode"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["name"] ?? "-") . "</td>";
                        echo "<td>" . htmlspecialchars($row["emailaddress"]) . "</td>";
                        echo "<td>" . ($isLockedOut ? "X" : "") . "</td>";
                        echo "<td><a class=\"edit-link\" href=\"users_edit.php?id=" . urlencode($row["id"]) . "\">Edit</a></td>";
                        echo "<td><a class=\"delete-link\" href=\"users_delete_doit.php?id=" . urlencode($row["id"]) . "\" onclick=\"return confirm('Are you sure you want to delete the user (" . (int)$row["id"] . ")?');\">Delete</a></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>

            <p class="registry-actions"><a class="button" href="users_add.php">Add new user</a></p>
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
