<?php include("_security.php") ?>
<?php include("_config.php") ?>
<?php include("_globals.php") ?>
<?php include("_security_access.php") ?>
<?php requireAccessLevelA($isLoggedIn, $securityAccessLevel); ?>
<?php
$id = $_GET["id"] ?? null;

if ($id !== null && ctype_digit((string)$id)) {
    $stmt = $conn->prepare("SELECT `user`, activity, object FROM activitylog WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $entry = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($entry) {
        $deleteStmt = $conn->prepare("DELETE FROM activitylog WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        if ($deleteStmt->execute()) {
            logactivity(
                $_SESSION["employeecode"] ?? "unknown",
                date("Y-m-d"),
                date("H:i:s"),
                "Activity log entry deleted",
                (string)$id,
                "Deleted activity '" . $entry["activity"] . "' for " . $entry["user"] . " (" . $entry["object"] . ").",
                "Activity log"
            );
        }
        $deleteStmt->close();
    }
}

header("Location: activitylog_read.php");
exit;
?>
