<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php  // ----- Locals: ------ ?>
<?php include("_master_head.php") ?>

<!-- Google reCAPTCHA API script (endast behövd på denna sida) -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script language="JavaScript" src="scripts/passwordForgotCheck.js"></script>

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
            <!-- Forgot Password Form som tar in både employyecode och email med google Captcha -->
            <!-- Email och employeeccode får inte vara tomma -->
            <div class="password-form">
                <h2>Forgot Password</h2>
                <form action="password_forgot_doit.php" method="post" onSubmit="return forgotPassword()">
                    <p>
                        Employee Code: <br />
                        <input type="text" name="employeecode" id="employeecode" size="25" maxlength="50" />
                    </p>
                    <p>
                        Email: <br />
                        <input type="text" name="email" id="email" size="25" maxlength="100" />
                    </p>
                    <p>
                        <!-- Google reCAPTCHA v2 ("I'm not a robot") -->
                        <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptcha_site_key) ?>"></div>
                    </p>
                    <p>
                        <input type="submit" class="button" value="Reset Password" />
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