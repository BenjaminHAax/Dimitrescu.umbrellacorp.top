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
            <?php
                // DÖDA SESSIONERNA
                $logoutUser = $_SESSION["employeecode"] ?? "unknown";
                unset($_SESSION["loggedin"]);
                unset($_SESSION["employeecode"]);
                logactivity(
                    $logoutUser,
                    date("Y-m-d"),
                    date("H:i:s"),
                    "User logged out",
                    $logoutUser,
                    "",
                    "Login/logout"
                );
                echo "<h2>User Logged Out</h2>";

                /* //Veya kullanıcının tüm oturumunu tamamen sonlandırmak istersen (önerilen):
                $_SESSION = array(); // Tüm session değişkenlerini boşaltır
                session_destroy();   // Sunucudaki oturum dosyasını siler
                */
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

//Redirect to index.php after 3 seconds
<script>
    setTimeout(function() {
        window.location.href = 'index.php'; // Veya aynı sayfa için: window.location.reload();
    }, 3000); // 3000 milisaniye = 3 saniye
</script>