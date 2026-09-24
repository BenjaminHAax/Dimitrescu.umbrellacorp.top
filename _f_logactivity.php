<?php
/**
 * Store an application activity without changing the operation that caused it.
 *
 * The activitylog table is installed once during deployment; this helper only
 * inserts rows and removes entries older than one year.
 */
function cleanupActivityLog()
{
    global $conn;

    if (!isset($conn) || !($conn instanceof mysqli)) {
        error_log("Activity log cleanup unavailable: database connection is not ready.");
        return false;
    }

    $cleanupStmt = $conn->prepare("DELETE FROM activitylog WHERE `date` < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)");
    if ($cleanupStmt === false) {
        error_log("Activity log cleanup failed: " . $conn->error);
        return false;
    }
    $success = $cleanupStmt->execute();
    if (!$success) {
        error_log("Activity log cleanup failed: " . $cleanupStmt->error);
    }
    if ($cleanupStmt instanceof mysqli_stmt) {
        $cleanupStmt->close();
    }
    return $success;
}

function logactivity($user, $date, $time, $activity, $object = "", $info = "", $category = "")
{
    global $conn;

    if (!isset($conn) || !($conn instanceof mysqli)) {
        error_log("Activity log unavailable: database connection is not ready.");
        return false;
    }

    cleanupActivityLog();

    $stmt = $conn->prepare(
        "INSERT INTO activitylog (`user`, `date`, `time`, activity, object, info, category)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if ($stmt === false) {
        error_log("Activity log insert preparation failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param("sssssss", $user, $date, $time, $activity, $object, $info, $category);
    $success = $stmt->execute();
    if (!$success) {
        error_log("Activity log insert failed: " . $stmt->error);
    }
    $stmt->close();

    return $success;
}
?>
