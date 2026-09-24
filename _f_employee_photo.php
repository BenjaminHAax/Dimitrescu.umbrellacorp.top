<?php
// ---------------------------------------------------------------------
// Hjälpfunktioner för att hantera anställdas foton (public/photos/).
// Foton sparas alltid som {employeecode}.jpg (konverteras med GD).
// Om ingen bild finns för en anställd visas public/photos/default.jpg.
// ---------------------------------------------------------------------

define("EMPLOYEE_PHOTO_DIR", __DIR__ . "/public/photos");
define("EMPLOYEE_PHOTO_URL", "public/photos");

function getEmployeePhotoPath($employeecode)
{
    return EMPLOYEE_PHOTO_DIR . "/" . $employeecode . ".jpg";
}

// Returnerar en relativ URL som kan användas direkt i en <img src="...">.
// Lägger till filens ändringstid som query-param så webbläsaren inte visar en gammal cachad bild.
function getEmployeePhotoUrl($employeecode)
{
    $path = getEmployeePhotoPath($employeecode);
    if ($employeecode !== "" && file_exists($path)) {
        return EMPLOYEE_PHOTO_URL . "/" . rawurlencode($employeecode) . ".jpg?v=" . filemtime($path);
    }
    return EMPLOYEE_PHOTO_URL . "/default.jpg";
}

// Sparar en uppladdad bild (från $_FILES[...]) som employeecode.jpg.
// Returnerar true vid lyckad uppladdning, annars en felsträng.
function saveEmployeePhoto($tmpFilePath, $employeecode)
{
    $imageInfo = @getimagesize($tmpFilePath);
    if ($imageInfo === false) {
        return "The uploaded file is not a valid image.";
    }

    if (!is_dir(EMPLOYEE_PHOTO_DIR)) {
        mkdir(EMPLOYEE_PHOTO_DIR, 0755, true);
    }

    $image = null;
    switch ($imageInfo[2]) {
        case IMAGETYPE_JPEG:
            $image = @imagecreatefromjpeg($tmpFilePath);
            break;
        case IMAGETYPE_PNG:
            $image = @imagecreatefrompng($tmpFilePath);
            break;
        case IMAGETYPE_GIF:
            $image = @imagecreatefromgif($tmpFilePath);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists("imagecreatefromwebp")) {
                $image = @imagecreatefromwebp($tmpFilePath);
            }
            break;
    }

    if (!$image) {
        return "Unsupported image type. Please use JPG, PNG, GIF or WEBP.";
    }

    // Fyll ev. transparens med vit bakgrund innan JPEG-konvertering.
    $width = imagesx($image);
    $height = imagesy($image);
    $flattened = imagecreatetruecolor($width, $height);
    imagefill($flattened, 0, 0, imagecolorallocate($flattened, 255, 255, 255));
    imagecopy($flattened, $image, 0, 0, 0, 0, $width, $height);
    imagedestroy($image);

    $saved = imagejpeg($flattened, getEmployeePhotoPath($employeecode), 90);
    imagedestroy($flattened);

    return $saved ? true : "Could not save the uploaded photo.";
}

function deleteEmployeePhoto($employeecode)
{
    $path = getEmployeePhotoPath($employeecode);
    if (file_exists($path)) {
        @unlink($path);
    }
}

// Byter namn på fotot om employeecode ändras vid redigering.
function renameEmployeePhoto($oldEmployeecode, $newEmployeecode)
{
    if ($oldEmployeecode === $newEmployeecode) {
        return;
    }
    $oldPath = getEmployeePhotoPath($oldEmployeecode);
    if (file_exists($oldPath)) {
        rename($oldPath, getEmployeePhotoPath($newEmployeecode));
    }
}
?>
