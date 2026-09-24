<?php
// ---------------------------------------------------------------------
// Helper functions for the Research Database (research_objects /
// research_entries). PDFs are stored as public/pdf/{objectNumber}.pdf,
// following the same convention as public/photos/ for employee photos.
// ---------------------------------------------------------------------

define("RESEARCH_PDF_DIR", __DIR__ . "/public/pdf");
define("RESEARCH_PDF_URL", "public/pdf");
define("RESEARCH_IMAGE_DIR", __DIR__ . "/public/virusphoto");
define("RESEARCH_IMAGE_URL", "public/virusphoto");
define("RESEARCH_ATTACHMENT_DIR", __DIR__ . "/public/researchfiles");
define("RESEARCH_ATTACHMENT_URL", "public/researchfiles");

function requireResearchWriteAccess($isLoggedIn, $securityAccessLevel) {
    if (!$isLoggedIn || !in_array($securityAccessLevel, ["A", "B"], true)) {
        die("<h2>Access denied</h2><p>Security Access Level A or B is required.</p>");
    }
}

function researchNow() {
    return [date("Y-m-d"), date("H:i")];
}

function researchLog($activity, $object, $info = "") {
    $user = $_SESSION["employeecode"] ?? "";
    [$date, $time] = researchNow();
    logactivity($user, $date, $time, $activity, $object, $info, "Research");
}

// Returns the path on disk to the PDF belonging to an objectNumber.
function getResearchPdfPath($objectNumber) {
    $safeNumber = preg_replace("/[^A-Za-z0-9_#-]/", "_", (string)$objectNumber);
    return RESEARCH_PDF_DIR . "/" . $safeNumber . ".pdf";
}

// Returns a relative URL to the object's PDF if it exists on disk,
// otherwise null (so callers can decide whether to show a link).
function getResearchPdfUrl($objectNumber) {
    if ($objectNumber === "" || $objectNumber === null) {
        return null;
    }
    $path = getResearchPdfPath($objectNumber);
    if (file_exists($path)) {
        $safeNumber = preg_replace("/[^A-Za-z0-9_#-]/", "_", (string)$objectNumber);
        return RESEARCH_PDF_URL . "/" . rawurlencode($safeNumber) . ".pdf?v=" . filemtime($path);
    }
    return null;
}

// Only allow http(s) links to be rendered as clickable video links, to
// avoid javascript: or other unsafe URL schemes ending up in href="".
function isSafeResearchVideoLink($url) {
    if ($url === null || trim($url) === "") {
        return false;
    }
    return (bool) preg_match('#^https?://#i', trim($url));
}

function getResearchImageUrls($objectId) {
    $directory = RESEARCH_IMAGE_DIR . "/" . (int)$objectId;
    $files = researchImageFiles($objectId);

    $urls = [];
    foreach ($files as $file) {
        $urls[] = RESEARCH_IMAGE_URL . "/" . (int)$objectId . "/" . rawurlencode($file);
    }
    return $urls;
}

function researchImageFiles($objectId) {
    $directory = RESEARCH_IMAGE_DIR . "/" . (int)$objectId;
    if (!is_dir($directory)) return [];

    $files = glob($directory . "/*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
    if ($files === false) return [];

    $files = array_map("basename", $files);
    usort($files, function ($a, $b) {
        $numA = (int)pathinfo($a, PATHINFO_FILENAME);
        $numB = (int)pathinfo($b, PATHINFO_FILENAME);
        if ($numA === $numB) return strnatcasecmp($a, $b);
        return $numA <=> $numB;
    });
    return $files;
}

function researchRenumberImages($objectId) {
    $directory = RESEARCH_IMAGE_DIR . "/" . (int)$objectId;
    if (!is_dir($directory)) return true;

    $files = researchImageFiles($objectId);
    $temporaryNames = [];
    foreach ($files as $index => $file) {
        $oldPath = $directory . "/" . $file;
        $temporaryName = "__renumber_" . ($index + 1) . "_" . $file;
        $temporaryPath = $directory . "/" . $temporaryName;
        if (!rename($oldPath, $temporaryPath)) return false;
        $temporaryNames[] = $temporaryName;
    }

    foreach ($temporaryNames as $index => $temporaryName) {
        $extension = strtolower(pathinfo($temporaryName, PATHINFO_EXTENSION));
        $newPath = $directory . "/" . ($index + 1) . "." . $extension;
        if (!rename($directory . "/" . $temporaryName, $newPath)) return false;
    }

    return true;
}

function researchUploadPdf($tmpName, $objectNumber) {
    if (!$tmpName || !is_uploaded_file($tmpName)) return false;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($finfo->file($tmpName) !== "application/pdf") return false;
    $safeNumber = preg_replace("/[^A-Za-z0-9_#-]/", "_", $objectNumber);
    return move_uploaded_file($tmpName, RESEARCH_PDF_DIR . "/" . $safeNumber . ".pdf");
}

function researchDeletePdf($objectNumber) {
    $path = getResearchPdfPath($objectNumber);
    return is_file($path) ? unlink($path) : true;
}

function researchFiles($objectId) {
    $dir = RESEARCH_ATTACHMENT_DIR . "/" . (int)$objectId;
    if (!is_dir($dir)) return [];
    return array_values(array_filter(scandir($dir), function ($name) use ($dir) {
        return $name !== "." && $name !== ".." && is_file($dir . "/" . $name);
    }));
}
?>
