<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php  // ----- Locals: ------ ?>
<?php requireAccessLevelA($isLoggedIn, $securityAccessLevel); ?>
<?php include("_master_head.php") ?>

<script language="JavaScript" src="scripts/usersFormCheck.js"></script>

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
            <h2>Add new user</h2>

            <div class="registry-form">
                <form action="users_add_doit.php" method="post" onSubmit="return usersFormCheck(false)">
                    <p>
                        Employee Code: <br />
                        <select name="employeecode_select" id="employeecode_select" onChange="toggleOtherUsername()">
                            <option value="">-- Select employee --</option>
                            <?php
                            $empResult = mysqli_query($conn, "SELECT employeecode, name FROM employees ORDER BY employeecode ASC");
                            while ($emp = mysqli_fetch_assoc($empResult)) {
                                echo "<option value=\"" . htmlspecialchars($emp["employeecode"]) . "\">"
                                    . htmlspecialchars($emp["employeecode"]) . " - " . htmlspecialchars($emp["name"])
                                    . "</option>";
                            }
                            ?>
                            <option value="other">or other user name</option>
                        </select>
                    </p>

                    <p id="other_username_wrapper" style="display:none;">
                        Username: <br />
                        <input type="text" name="other_username" id="other_username" size="7" maxlength="7" />
                    </p>

                    <p>
                        Real Name: <br />
                        <input type="text" name="real_name" id="real_name" size="25" maxlength="24" />
                    </p>

                    <p>
                        Email address: <br />
                        <input type="text" name="email" id="email" size="25" maxlength="250" />
                    </p>

                    <p>
                        <label>
                            <input type="checkbox" name="lockout" value="x" />
                            Locked out
                        </label>
                    </p>

                    <p>
                        Password: <br />
                        <input type="password" name="password" id="password" size="25" maxlength="50" />
                    </p>

                    <p>
                        Retype Password: <br />
                        <input type="password" name="retype_password" id="retype_password" size="25" maxlength="50" />
                    </p>

                    <p>
                        <input type="submit" class="button" value="Add user" />
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
