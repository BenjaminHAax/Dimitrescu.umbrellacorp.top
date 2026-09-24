<?php
if($isLoggedIn)
{
    //Hämta Employee Code, Login times, Last login date and time, Login host från sessionen
    $employeecode = $_SESSION["employeecode"];
    $logintimes = $_SESSION["logintimes"];
    $lastlogin = $_SESSION["lastlogin"];
    $lastlogintime = $_SESSION["lastlogintime"];
    $loginhost = $_SESSION["loginhost"];
    ?>
    <div class="rightloginform">
        

        <!-- Visa användarinformation -->
        <p style = "color: white;"><strong>Employee Code:</strong> <?= htmlspecialchars($employeecode) ?></p>
        <br />
        <p style = "color: white;"><strong>Login Times:</strong> <?= htmlspecialchars($logintimes) ?></p>
        <p style = "color: white;"><strong>Last Login:</strong> <?= htmlspecialchars($lastlogin . " " . $lastlogintime) ?></p>
        <p style = "color: white;"><strong>Login Host:</strong> <?= htmlspecialchars($loginhost) ?></p>

        <form class="right-account-actions">
            <input class="button" type="button" value="Logout" onClick="window.location.href='logout_doit.php'" />
            <input class="button" type="button" value="Change Password" onClick="window.location.href='password_change.php'" />
            <?php
            // Hitta employees.id för den inloggade användaren, så "My Profile" kan länka dit.
            $ownEmployeeResult = mysqli_query($conn, "SELECT id FROM employees WHERE employeecode = '" . mysqli_real_escape_string($conn, $employeecode) . "'");
            $ownEmployeeRow = $ownEmployeeResult ? mysqli_fetch_assoc($ownEmployeeResult) : null;
            if ($ownEmployeeRow) {
                ?>
                <input class="button" type="button" value="My Profile" onClick="window.location.href='personnelregistry_edit.php?id=<?= (int)$ownEmployeeRow["id"] ?>'" />
                <?php
            }
            ?>
        </form>

    </div>
<?php } else { ?>
    <div class="rightloginform">
        <form name="loginform" action="login_doit.php" onSubmit="return loginFormCheck()" 
        enctype="multipart/form-data" method="post">

            Employee Code: 
            <input type="text" name="employeecode" id="employeecode" size="7" maxlength="7" />
            <p />
            Password: 
            <input type="password" name="password" id="password" size="10" maxlength="50" />
            <p />
            <input type="submit" class="button" value="Login" onClick="javascript:hashing()" />
            <input class="button" type="reset" value="Reset" />
            <p />
        </form>
    </div>
<?php } ?>

<?php if($isLoggedIn) { ?>
<?php if(($_SESSION["securityaccesslevel"] ?? "") === "A") { ?>
<div class="rightmenuitem" onClick="window.location.href='activitylog_read.php'" style="cursor: pointer;">Activity Log</div>
<?php } ?>
<div class="rightmenuitem" onClick="window.location.href='personnelregistry_read.php'" style="cursor: pointer;">Personnel Register</div>
<div class="rightmenuitem" onClick="window.location.href='research_read.php'" style="cursor: pointer;">Research Database</div>
<?php if(($_SESSION["securityaccesslevel"] ?? "") === "A") { ?>
<div class="rightmenuitem" onClick="window.location.href='users_read.php'" style="cursor: pointer;">Users Register</div>
<?php } ?>
<?php } ?>