<?php
include("_security.php");
include("_config.php");
include("_globals.php");
include("_security_access.php");
include_once("_f_research.php");
requireResearchWriteAccess($isLoggedIn, $securityAccessLevel);

$id = (int)($_POST["researchObjectId"] ?? 0);
if (!isset($_FILES["image"])) {
    http_response_code(400);
    die("No image was received. Select the image again in the research object form and submit it. "
        . "Maximum image size: " . htmlspecialchars(ini_get("upload_max_filesize"))
        . "; maximum request size: " . htmlspecialchars(ini_get("post_max_size")) . ".");
}

$uploadError = $_FILES["image"]["error"];
if ($uploadError !== UPLOAD_ERR_OK) {
    $messages = [
        UPLOAD_ERR_INI_SIZE => "The image exceeds the server's maximum file size (" . ini_get("upload_max_filesize") . ").",
        UPLOAD_ERR_FORM_SIZE => "The image exceeds the form's maximum file size.",
        UPLOAD_ERR_PARTIAL => "Only part of the image was received. Select the image again and retry.",
        UPLOAD_ERR_NO_FILE => "No image was selected. Select an image before uploading.",
        UPLOAD_ERR_NO_TMP_DIR => "The server's temporary upload directory is missing.",
        UPLOAD_ERR_CANT_WRITE => "The server could not write the uploaded image to temporary storage.",
        UPLOAD_ERR_EXTENSION => "A server extension stopped the image upload."
    ];
    error_log("Research image upload failed for object " . $id . ": PHP upload error " . $uploadError);
    http_response_code(400);
    die(htmlspecialchars($messages[$uploadError] ?? "An unknown image upload error occurred."));
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES["image"]["tmp_name"]);
$ext = ["image/jpeg" => "jpg", "image/png" => "png", "image/gif" => "gif", "image/webp" => "webp"][$mime] ?? null;
if (!$ext) die("Unsupported image type.");

$dir = RESEARCH_IMAGE_DIR . "/" . $id;
if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
    die("Could not create the research image directory.");
}
if (!researchRenumberImages($id)) die("Could not renumber research images.");
$n = count(researchImageFiles($id)) + 1;
if (!move_uploaded_file($_FILES["image"]["tmp_name"], $dir . "/" . $n . "." . $ext)) {
    error_log("Research image upload could not be saved in " . $dir);
    http_response_code(500);
    die("The image was received but could not be saved in the research image directory. Check server storage permissions.");
}
researchLog("Uploaded", "Research image", (string)$id);
header("Location: research_read_object.php?id=" . $id);
exit;
