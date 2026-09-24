<?php
//echo phpinfo();

// ----------------- DISPLAY ALL ERRORS ------------------------
ini_set ('display_errors', 1);
ini_set ('display_startup_errors', 1);
error_reporting (E_ALL);


// ----------------- DATABASE CONNECTION -----------------------
$dbhost = "localhost";
$dbname = "dimitrescu_umbrellacorp_top"; 
$dbuser = "mysqluser";
$dbpassword = "Umbrella";
$conn = new mysqli($dbhost, $dbuser, $dbpassword, $dbname); 
if($conn->connect_error)
{
    die("Connection failed: ".$conn->connect_error);
}

// ----------------- GOOGLE RECAPTCHA (v2 "I'm not a robot") ---------
// Riktiga nycklar registrerade för domänen dimitrescu.umbrellacorp.top
// via https://www.google.com/recaptcha/admin
$recaptcha_site_key = "6Lf9NLYtAAAAALSkD0EJlcX8EVwDlHUt6aYcmlUz";
$recaptcha_secret_key = "6Lf9NLYtAAAAAO1A5mLZ0yr15Uullk0c2eUnXhDb";

?>
