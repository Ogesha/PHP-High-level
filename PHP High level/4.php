<?php
function crop_image($src, $dest) {
    list($width, $height) = getimagesize($src);
    $image = imagecreatefromjpeg($src);
    $cropped = imagecropauto($image, IMG_CROP_DEFAULT);
    if ($cropped !== FALSE) {
        $cropped_width = imagesx($cropped);
        $cropped_height = imagesy($cropped);
        $final_image = imagecreatetruecolor(100, 100);
        imagecopyresampled($final_image, $cropped, 0, 0, 0, 0, 100, 100, $cropped_width, $cropped_height);
        imagejpeg($final_image, $dest);
        imagedestroy($final_image);
    }
    imagedestroy($image);
}

function mirror_image($src, $dest) {
    $image = imagecreatefromjpeg($src);
    $width = imagesx($image);
    $height = imagesy($image);
    $mirrored = imagecreatetruecolor($width, $height + 25);
    imagecopy($mirrored, $image, 0, 0, 0, 0, $width, $height);
    imagecopy($mirrored, $image, 0, $height, 0, $height - 25, $width, 25);
    imageflip($mirrored, IMG_FLIP_VERTICAL);
    imagejpeg($mirrored, $dest);
    imagedestroy($image);
    imagedestroy($mirrored);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    if ($file['error'] == UPLOAD_ERR_OK && getimagesize($file['tmp_name'])) {
        $target_dir = 'uploads/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $target_file);

        $cropped_file = $target_dir . 'cropped_' . basename($file['name']);
        crop_image($target_file, $cropped_file);

        $mirrored_file = $target_dir . 'mirrored_' . basename($file['name']);
        mirror_image($cropped_file, $mirrored_file);

        echo "Image processed successfully!";
    } else {
        echo "File is not a valid image.";
    }
}
?>
