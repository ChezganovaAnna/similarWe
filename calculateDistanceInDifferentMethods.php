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
        return 1;
    }

    if ($magnitude1 == 0 || $magnitude2 == 0) {
        return 0;
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
    $cosine_distances = [];
    $hamming_distances = [];
    $manhattan_distances = [];
    $euclidean_distances = [];
    $chebyshev_distances = [];
    $minkowski_distances = [];
    $jaccard_distances = [];

    for ($i = 0; $i < count($vectors); $i++) {
        for ($j = $i; $j < count($vectors); $j++) {
            $cosineDistance = cosineDistance($vectors[$i], $vectors[$j]);
            $hammingDistance = hammingDistance($vectors[$i], $vectors[$j]);
            $manhattanDistance = manhattanDistance($vectors[$i], $vectors[$j]);
            $euclideanDistance = euclideanDistance($vectors[$i], $vectors[$j]);
            $chebyshevDistance = chebyshevDistance($vectors[$i], $vectors[$j]);
            $minkowskiDistance = minkowskiDistance($vectors[$i], $vectors[$j], 3); // p = 3
            $jaccardDistance = jaccardDistance($vectors[$i], $vectors[$j]);

            $cosine_distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $cosineDistance . "\n";
            $hamming_distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $hammingDistance . "\n";
            $manhattan_distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $manhattanDistance . "\n";
            $euclidean_distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $euclideanDistance . "\n";
            $chebyshev_distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $chebyshevDistance . "\n";
            $minkowski_distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $minkowskiDistance . "\n";
            $jaccard_distances[] = $vectors[$i][0] . " " . $vectors[$j][0] . " " . $jaccardDistance . "\n";
        }
    }

    file_put_contents('distances/cosine_distances.txt', implode('', $cosine_distances));
    file_put_contents('distances/hamming_distances.txt', implode('', $hamming_distances));
    file_put_contents('distances/manhattan_distances.txt', implode('', $manhattan_distances));
    file_put_contents('distances/euclidean_distances.txt', implode('', $euclidean_distances));
    file_put_contents('distances/chebyshev_distances.txt', implode('', $chebyshev_distances));
    file_put_contents('distances/minkowski_distances.txt', implode('', $minkowski_distances));
    file_put_contents('distances/jaccard_distances.txt', implode('', $jaccard_distances));
}

function hammingDistance($vector1, $vector2) {
    if (count($vector1) !== count($vector2)) {
        return null; // или вернуть другое значение, указывающее на ошибку
    }

    $distance = 0;
    for ($i = 1; $i < count($vector1); $i++) {
        if ($vector1[$i] != $vector2[$i]) {
            $distance++;
        }
    }
    return $distance;
}

function euclideanDistance($vector1, $vector2) {
    $sum = 0;
    for ($i = 1; $i < count($vector1); $i++) {
        $sum += pow($vector1[$i] - $vector2[$i], 2);
    }
    return sqrt($sum);
}

function chebyshevDistance($vector1, $vector2) {
    $maxDiff = 0;
    for ($i = 1; $i < count($vector1); $i++) {
        $diff = abs($vector1[$i] - $vector2[$i]);
        if ($diff > $maxDiff) {
            $maxDiff = $diff;
        }
    }
    return $maxDiff;
}

function minkowskiDistance($vector1, $vector2, $p) {
    $sum = 0;
    for ($i = 1; $i < count($vector1); $i++) {
        $sum += pow(abs($vector1[$i] - $vector2[$i]), $p);
    }
    return pow($sum, 1 / $p);
}

function jaccardDistance($vector1, $vector2) {
    $intersection = 0;
    $union = 0;
    for ($i = 1; $i < count($vector1); $i++) {
        $intersection += min($vector1[$i], $vector2[$i]);
        $union += max($vector1[$i], $vector2[$i]);
    }
    if ($union == 0) {
        return 0;
    }
    return 1 - $intersection / $union;
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
calculateDistances($vectors);