<?php
require_once('getid3/getid3.php');

function search_audio_files($dir, $search_criteria) {
    $getID3 = new getID3;
    $audio_files = [];
    foreach (glob("$dir/*.{mp3,wav,flac}", GLOB_BRACE) as $file) {
        $info = $getID3->analyze($file);
        foreach ($search_criteria as $key => $value) {
            if (strpos(strtolower($info['tags']['id3v2'][$key][0]), strtolower($value)) !== false) {
                $audio_files[] = $file;
                break;
            }
        }
    }
    return $audio_files;
}

// Example usage
$search_criteria = [
    'title' => 'song title',
    'artist' => 'artist name'
];
$audio_files = search_audio_files('path/to/audio/files', $search_criteria);
print_r($audio_files);
?>
