<?php
function delete_empty_directories($dir) {
    $is_empty = true;
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file)) {
            $is_empty &= delete_empty_directories($file);
        } else {
            $is_empty = false;
        }
    }
    if ($is_empty) {
        rmdir($dir);
    }
    return $is_empty;
}

// Example usage
delete_empty_directories('/path/to/directory');
?>
