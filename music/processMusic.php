<?php
$input = fopen('341430232_alexander_semenov.txt', 'r');
$output = fopen('common_music.txt', 'w');

$unique_songs = array();

while (($line = fgets($input)) !== false) {
    $parts = explode(' - ', $line);
    $artist = trim($parts[0]);
    $song = trim($parts[1]);

    $key = "$artist - $song";

    if (!file_exists('unique_songs.txt')) {
        touch('unique_songs.txt');
    }

    if (!file_get_contents('unique_songs.txt') || !in_array($key, file('unique_songs.txt', FILE_IGNORE_NEW_LINES))) {
        $newLine = "$artist - $song \n";
        fwrite($output, $newLine);
        file_put_contents('unique_songs.txt', $key . "\n", FILE_APPEND);
    }
}

fclose($input);
fclose($output);
?>