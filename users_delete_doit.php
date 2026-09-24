<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php  // ----- Locals: ------ ?>
<?php requireAccessLevelA($isLoggedIn, $securityAccessLevel); ?>
<?php
$id = $_GET["id"] ?? null;

if ($id !== null && ctype_digit((string)$id)) {
    $lookupStmt = $conn->prepare("SELECT employeecode FROM users WHERE id = ?");
    $lookupStmt->bind_param("i", $id);
    $lookupStmt->execute();
    $deletedUser = $lookupStmt->get_result()->fetch_assoc()["employeecode"] ?? "";
    $lookupStmt->close();

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        logactivity(
            $_SESSION["employeecode"] ?? "unknown",
            date("Y-m-d"),
            date("H:i:s"),
            "User deleted",
            (string)$id,
            "Deleted user " . $deletedUser . ".",
            "Users"
        );
    }
    $stmt->close();
}

header("Location: users_read.php");
exit;
?>
