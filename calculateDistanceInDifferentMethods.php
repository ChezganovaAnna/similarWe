<?php
function cosineDistance($vector1, $vector2) {
    $dotProduct = 0;
    $magnitude1 = 0;
    $magnitude2 = 0;

    for ($i = 1; $i < count($vector1); $i++) {
        $dotProduct += $vector1[$i] * $vector2[$i];
        $magnitude1 += pow($vector1[$i], 2);
        $magnitude2 += pow($vector2[$i], 2);
    }

    $magnitude1 = sqrt($magnitude1);
    $magnitude2 = sqrt($magnitude2);

    if ($magnitude1 == 0 && $magnitude2 == 0) {
        return 1; // Если оба вектора нулевые, расстояние равно 1
    }

    if ($magnitude1 == 0 || $magnitude2 == 0) {
        return 0; // Если один из векторов нулевой, расстояние равно 0
    }

    return $dotProduct / ($magnitude1 * $magnitude2);
}

function readVectorsFromFile($file_path) {
    $vectors = [];
    $file_content = file_get_contents($file_path);
    $lines = explode("\n", $file_content);
    foreach ($lines as $line) {
        $parts = explode(' ', $line);
        $id = array_shift($parts);
        $vectors[] = array_merge([$id], array_map('intval', $parts));
    }
    return $vectors;
}

function calculateDistances($vectors) {
    $distances = [];
    for ($i = 0; $i < count($vectors); $i++) {
        for ($j = $i; $j < count($vectors); $j++) {
            $cosineDistance = cosineDistance($vectors[$i], $vectors[$j]);
            $hammingDistance = hammingDistance($vectors[$i], $vectors[$j]);
            $manhattanDistance = manhattanDistance($vectors[$i], $vectors[$j]);
            $distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $cosineDistance . " " . $hammingDistance . " " . $manhattanDistance . "\n";
        }
    }
    return $distances;
}

function hammingDistance($vector1, $vector2) {
    $distance = 0;
    for ($i = 1; $i < count($vector1); $i++) {
        if ($vector1[$i] != $vector2[$i]) {
            $distance++;
        }
    }
    return $distance;
}

function manhattanDistance($vector1, $vector2) {
    $distance = 0;
    for ($i = 1; $i < count($vector1); $i++) {
        $distance += abs($vector1[$i] - $vector2[$i]);
    }
    return $distance;
}


$file_path = 'user_vectors.txt';
$vectors = readVectorsFromFile($file_path);
$cosine_distances = calculateDistances($vectors);

file_put_contents('cosine_distances.txt', implode('', $cosine_distances));