<?php
// _security_access.php
// Hjälpfunktioner för behörighetskontroll (Security Access Level) i
// användar- och personalregistret. Kräver att _security.php redan
// har körts (så att $isLoggedIn och session finns tillgängliga).

// Håller koll på inloggad users Security Access Level, default "" (ingen åtkomst)
$securityAccessLevel = $_SESSION["securityaccesslevel"] ?? "";

// Avbryter sidan med ett felmeddelande om användaren inte är inloggad
// eller inte har Security Access Level A.
function requireAccessLevelA($isLoggedIn, $securityAccessLevel) {
    if (!$isLoggedIn) {
        die("<h2>Access denied</h2><p>You must be logged in to view this page.</p>");
    }
    if ($securityAccessLevel !== "A") {
        die("<h2>Access denied</h2><p>You need Security Access Level A to view this page.</p>");
    }
}

// Avbryter sidan med ett felmeddelande om användaren inte är inloggad
// eller inte har Security Access Level A eller B (används av
// forskningsdatabasen där både A och B får skapa/redigera/radera).
function requireAccessLevelAB($isLoggedIn, $securityAccessLevel) {
    if (!$isLoggedIn) {
        die("<h2>Access denied</h2><p>You must be logged in to view this page.</p>");
    }
    if ($securityAccessLevel !== "A" && $securityAccessLevel !== "B") {
        die("<h2>Access denied</h2><p>You need Security Access Level A or B to view this page.</p>");
    }
}

// Avbryter sidan om användaren inte är inloggad överhuvudtaget.
function requireLoggedIn($isLoggedIn) {
    if (!$isLoggedIn) {
        die("<h2>Access denied</h2><p>You must be logged in to view this page.</p>");
    }
}

// Returnerar true om den inloggade användaren antingen har Security Access
// Level A, eller om employeecode i sessionen matchar den employeecode som
// hör till employees.id = $employeeId (dvs. man tittar på/redigerar sin
// egen profil i personalregistret).
function canAccessEmployee($conn, $isLoggedIn, $securityAccessLevel, $loggedInUser, $employeeId) {
    if (!$isLoggedIn) {
        return false;
    }
    if ($securityAccessLevel === "A") {
        return true;
    }

    $stmt = $conn->prepare("SELECT employeecode FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return ($row !== null && $row["employeecode"] === $loggedInUser);
}
?>
