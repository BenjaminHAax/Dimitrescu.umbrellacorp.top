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
            $employeecode = $_POST["employeecode"];
            $password = $_POST["password"];

            // Parola hash'lenmediyse SHA-256 yap
            if (strlen($password) !== 64) {
                $password = hash("sha256", $password);
            }

            $loggedin = "no";
            $lockout = "";

            if ($result = mysqli_query($conn, "SELECT * FROM users WHERE employeecode='$employeecode' AND passwd='$password'"))
            {
                while ($row = mysqli_fetch_assoc($result)) 
                {
                    $id = $row["id"];
                    $logintimes = (int)$row["logintimes"];
                    $lastlogin = $row["lastlogin"];
                    $lockout = $row["lockout"];
                    $loggedin = "ok";
                }
            }

            // INLOGGNINGEN LYCKADES
            if ($loggedin == "ok" && $lockout != "x")
            {
                // SKAPA SESSIONERNA
                $_SESSION["loggedin"] = "ok";
                $_SESSION["employeecode"] = $employeecode;
                echo "<h2>SUCCESS</h2>";

                //Aktuella Data
                date_default_timezone_set('Europe/Helsinki'); // Set the default timezone for date and time functions
                $currentDate = date("Y-m-d");
                $currentTime = date("H:i:s");
                
                //Registrera user ipv4
                $clientIP = $_SERVER['REMOTE_ADDR'];

                //Lägga in de i databasen
                $updateSQL = "UPDATE users SET 
                    logintimes = COALESCE(NULLIF(logintimes, ''), 0) + 1, 
                    lastlogin = '$currentDate', 
                    lastlogintime = '$currentTime',
                    loginhost = '$clientIP'
                  WHERE employeecode = '$employeecode'";
                mysqli_query($conn, $updateSQL);

                //Registrera nya data i sessionen
                $_SESSION["logintimes"] = $logintimes + 1;
                $_SESSION["lastlogin"] = $currentDate;
                $_SESSION["lastlogintime"] = $currentTime;
                $_SESSION["loginhost"] = $clientIP;

                // Hämta och spara Security Access Level i sessionen (används av
                // användar- och personalregistret för behörighetskontroll).
                // Användare som inte är kopplade till en anställd (t.ex. root/system/backup)
                // får automatiskt Level A enligt kravspecifikationen.
                $securityAccessLevel = "A";
                $empStmt = $conn->prepare("SELECT securityAccessLevel FROM employees WHERE employeecode = ?");
                $empStmt->bind_param("s", $employeecode);
                $empStmt->execute();
                $empResult = $empStmt->get_result();
                if ($empRow = $empResult->fetch_assoc()) {
                    if (!empty($empRow["securityAccessLevel"])) {
                        $securityAccessLevel = $empRow["securityAccessLevel"];
                    }
                }
                $empStmt->close();
                $_SESSION["securityaccesslevel"] = $securityAccessLevel;
                logactivity(
                    $employeecode,
                    $currentDate,
                    $currentTime,
                    "User logged in",
                    $employeecode,
                    "Login from " . $clientIP . ".",
                    "Login/logout"
                );
            }
            else 
            {
                logactivity(
                    $employeecode ?? "unknown",
                    date("Y-m-d"),
                    date("H:i:s"),
                    "Failed login",
                    $employeecode ?? "",
                    "Login rejected because the credentials were invalid or the account was locked.",
                    "Login/logout"
                );
                echo "<h2>SOMETHING WENT WRONG</h2>";
                //Skapa button för att glömt lösenord
                echo '<p><span class="button button-secondary" onclick="window.location.href=\'password_forgot.php\'">Forgot Password?</span></p>';
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

//Redirect to index.php after 3 seconds om inloggning lyckades
<?php
if ($loggedin == "ok") {
    echo '<script>
        setTimeout(function() {
            window.location.href = "index.php";
        }, 3000);
    </script>';
}
?>