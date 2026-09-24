<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php  // ----- Locals: ------ ?>
<?php requireAccessLevelA($isLoggedIn, $securityAccessLevel); ?>
<?php
$id = $_GET["id"] ?? null;

if ($id !== null && ctype_digit((string)$id)) {
    $empStmt = $conn->prepare("SELECT employeecode FROM employees WHERE id = ?");
    $empStmt->bind_param("i", $id);
    $empStmt->execute();
    $employeecodeToDelete = $empStmt->get_result()->fetch_assoc()["employeecode"] ?? null;
    $empStmt->close();

    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    if ($employeecodeToDelete !== null) {
        deleteEmployeePhoto($employeecodeToDelete);
        logactivity(
            $_SESSION["employeecode"] ?? "unknown",
            date("Y-m-d"),
            date("H:i:s"),
            "Employee deleted",
            (string)$id,
            "Deleted employee " . $employeecodeToDelete . ".",
            "Personnel registry"
        );
    }
}

header("Location: personnelregistry_read.php");
exit;
?>
