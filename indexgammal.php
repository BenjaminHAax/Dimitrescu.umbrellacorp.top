<?php echo "HELLO WORLD: ";
$dbhost = "localhost";
$dbname = "www_umbrellacorp_top";
$dbuser = "mysqluser";
$dbpassword = "umbrella";
$conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
if (!$conn)
{
die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
?> 
