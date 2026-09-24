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
            <h2>Add employee</h2>

            <div class="registry-form">
                <form action="personnelregistry_add_doit.php" method="post" enctype="multipart/form-data">
                    <div class="registry-columns">
                        <div class="registry-photo-col">
                            <div class="registry-photo-box">
                                <img src="<?= htmlspecialchars(getEmployeePhotoUrl("")) ?>" alt="" />
                            </div>
                            <p class="registry-photo-upload">Photo: <br />
                                <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" /></p>

                            <p>Employee Code: <br />
                                <input type="text" name="employeecode" maxlength="7" required /></p>

                            <p>Security Access Level: <br />
                                <select name="securityAccessLevel">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C" selected>C</option>
                                </select></p>
                        </div>
                        <div class="registry-fields-col">
                            <p>Name: <br />
                                <input type="text" name="name" maxlength="24" required /></p>
                            <p>Signature Date: <br />
                                <input type="text" name="signatureDate" maxlength="10" placeholder="YYYY-MM-DD" /></p>
                            <p>Date of Birth: <br />
                                <input type="text" name="dateOfBirth" maxlength="10" placeholder="YYYY-MM-DD" /></p>
                            <p>Sex: <br />
                                <input type="text" name="sex" maxlength="1" /></p>
                            <p>Blood Type: <br />
                                <input type="text" name="bloodType" maxlength="7" /></p>
                            <p>Height: <br />
                                <input type="text" name="height" maxlength="6" /></p>
                            <p>Weight: <br />
                                <input type="text" name="weight" maxlength="7" /></p>
                            <p>Rank: <br />
                                <input type="text" name="rank" maxlength="19" /></p>
                            <p>Department: <br />
                                <input type="text" name="department" maxlength="23" /></p>
                        </div>
                    </div>

                    <p>Background: <br />
                        <textarea name="Background" rows="4" maxlength="1332"></textarea></p>
                    <p>Strengths: <br />
                        <textarea name="Strengths" rows="4" maxlength="1101"></textarea></p>
                    <p>Weaknesses: <br />
                        <textarea name="Weaknesses" rows="4" maxlength="1133"></textarea></p>

                    <p>
                        <input type="submit" class="button" value="Add" />
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
