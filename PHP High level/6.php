<?php
function reorganize_directories($base_dir) {
    $images = glob("$base_dir/*/*.jpg");
    $dirs = range('A', 'Z');
    foreach ($dirs as $dir) {
        if (!file_exists("$base_dir/$dir")) {
            mkdir("$base_dir/$dir");
        }
    }

    foreach ($images as $image) {
        $initial = strtoupper($image[0]);
        if (file_exists("$base_dir/$initial")) {
            rename($image, "$base_dir/$initial/" . basename($image));
        }
    }

    foreach ($dirs as $dir) {
        $files = glob("$base_dir/$dir/*.jpg");
        if (count($files) > 20) {
            $subdirs = array_chunk($files, 20);
            foreach ($subdirs as $i => $subdir) {
                $new_dir = "$base_dir/$dir" . chr(65 + $i);
                mkdir($new_dir);
                foreach ($subdir as $file) {
                    rename($file, "$new_dir/" . basename($file));
                }
            }
            rmdir("$base_dir/$dir");
        }
    }
}

function upload_image($base_dir) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
        $file = $_FILES['image'];
        if ($file['error'] == UPLOAD_ERR_OK && getimagesize($file['tmp_name'])) {
            $target_dir = $base_dir . '/' . strtoupper($file['name'][0]);
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . '/' . basename($file['name']);
            if (!file_exists($target_file)) {
                move_uploaded_file($file['tmp_name'], $target_file);
                reorganize_directories($base_dir);
                echo "Image uploaded successfully!";
            } else {
                echo "Image already exists.";
            }
        } else {
            echo "File is not a valid image.";
        }
    }
}

// Example usage
upload_image('uploads');
?>
