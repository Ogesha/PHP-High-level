<?php
function generate_image($name, $size) {
    $sizes = [
        "big" => [800, 600],
        "med" => [600, 400],
        "min" => [300, 200],
        "mic" => [101, 101]
    ];

    if (!array_key_exists($size, $sizes)) {
        die("Invalid size specified.");
    }

    $original_image_path = "gallery/$name.jpg";
    if (!file_exists($original_image_path)) {
        die("Image not found.");
    }

    $cache_dir = 'cache';
    if (!file_exists($cache_dir)) {
        mkdir($cache_dir, 0777, true);
    }

    $cache_image_path = "$cache_dir/{$name}_{$size}.jpg";
    if (file_exists($cache_image_path)) {
        header('Content-Type: image/jpeg');
        readfile($cache_image_path);
        exit;
    }

    list($original_width, $original_height) = getimagesize($original_image_path);
    $aspect_ratio = $original_width / $original_height;
    $new_width = $sizes[$size][0];
    $new_height = $sizes[$size][1];

    if ($new_width / $new_height > $aspect_ratio) {
        $new_width = $new_height * $aspect_ratio;
    } else {
        $new_height = $new_width / $aspect_ratio;
    }

    $image = imagecreatefromjpeg($original_image_path);
    $new_image = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    imagejpeg($new_image, $cache_image_path, 100);

    header('Content-Type: image/jpeg');
    imagejpeg($new_image);
    imagedestroy($image);
    imagedestroy($new_image);
}

if (isset($_GET['name']) && isset($_GET['size'])) {
    generate_image($_GET['name'], $_GET['size']);
}
?>
