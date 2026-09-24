<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
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
            <!-- Skaffa formulär old password, new password x2 -->
            <!-- Old passowrd får inte vara tomt eller mindre än 8 tecken -->
            <!-- New password och Retype new password måste vara samma -->
            <!-- New password får inte vara mindre än 8 tecken -->
            <!-- New password måste innehålla både gemener och versaler, samt ett specialtecken -->
            <!-- New password hashas med SHA256 innan postning -->

            <div class="password-form">
                <h2>Change Password</h2>
                <form action="password_change_doit.php" method="post" onSubmit="return passwordChange()">
                    <p>
                        Old Password: <br />
                        <input type="password" name="old_password" id="old_password" size="25" maxlength="50" />
                    </p>
                    <p>
                        New Password: <br />
                        <input type="password" name="new_password" id="new_password" size="25" maxlength="50" />
                    </p>
                    <p>
                        Retype New Password: <br />
                        <input type="password" name="retype_new_password" id="retype_new_password" size="25" maxlength="50" />
                    </p>
                    <p>
                        <input type="submit" class="button" value="Change Password" />
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