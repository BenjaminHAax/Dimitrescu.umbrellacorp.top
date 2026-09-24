<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php
// Enkel AJAX-endpoint som returnerar en anställds namn (name) baserat på employeecode.
// Används av scripts/usersFormCheck.js för att auto-fylla "Real Name" i users_add.php/users_edit.php.
header("Content-Type: application/json");

$employeecode = $_GET["employeecode"] ?? "";
$name = "";

if ($employeecode !== "") {
    $stmt = $conn->prepare("SELECT name FROM employees WHERE employeecode = ?");
    $stmt->bind_param("s", $employeecode);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $name = $row["name"];
    }
    $stmt->close();
}

echo json_encode(["name" => $name]);
?>
