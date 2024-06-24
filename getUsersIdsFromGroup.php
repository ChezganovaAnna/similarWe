<?php
declare(strict_types = 1);

require_once "getUsersUnfilltered.php";
require_once "consts/consts.php";


$group_members = file_get_contents('group_members.txt');
$group_members = explode("\n", $group_members);

//набор групп в которых как я считаю могут находиться похожие люди
$group_ispring = 32877529;
$group_ispring_institute = 210114453;
$group_ispring_tech = 189778848;

//эта функция вытаскивает только тысячу пользвателей из-за ограничений по количеству запросов
function getUsersFromGroup($group_id): array {
    $url = 'https://api.vk.com/method/groups.getMembers?group_id=' . $group_id . '&access_token=' . ACCESS_TOKEN . '&v=5.131';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['response']['items'];
}

function getCityInfo($user_info) {
    if (isset($user_info['response']['0']['city'])) {
        $city_id = $user_info['response']['0']['city']['id'];
        $city_title = $user_info['response']['0']['city']['title'];
        return [
            'city_id' => $city_id,
            'city_title' => $city_title
        ];
    }
    return null;
}

function ifUserBlocked($user_info): bool {
    return $user_info['response'][0]['is_closed'] == false ? false : true;
}

function writeCityData($group_members) {
    file_put_contents('info/city_data.txt', '');
    file_put_contents('info/city_ids.txt', '');
    for ($i = 0; $i < 20 && $i < count($group_members); $i++) {
        $user_id = $group_members[$i];
        $user_info = getUserInfo($user_id);
        $city_info = getCityInfo($user_info);
        if ($city_info !== null) {
            file_put_contents('city_data.txt', "{$city_info['city_id']} {$city_info['city_title']}\n", FILE_APPEND);
            file_put_contents('city_ids.txt', "{$city_info['city_id']}\n", FILE_APPEND);
        }
    }
}

//дописать вызов

function readCityIds($file_path) {
    $city_ids = file_get_contents($file_path);
    $city_data_lines = explode("\n", $city_ids);
    $unique_city_data_lines = array_unique($city_data_lines);
    return $unique_city_data_lines;
}

function writeCityIds($file_path, $city_ids) {
    file_put_contents($file_path, implode("\n", $city_ids));
}

function readCityData($file_path) {
    $city_data = file_get_contents($file_path);
    $city_data_lines = explode("\n", $city_data);
    $city_ids = [];
    foreach ($city_data_lines as $line) {
        list($city_id, $city_title) = explode(' ', $line);
        $city_ids[$city_id] = $city_title;
    }
    return $city_ids;
}

function validateCityIdsFile($file_path) {
    $city_ids = file_get_contents($file_path);
    if ($city_ids === false) {
        echo "Error: Unable to read $file_path.\n";
        exit;
    }
    $city_ids = explode("\n", $city_ids);
    if (empty($city_ids)) {
        echo "Error: $file_path is empty.\n";
        exit;
    }
    return $city_ids;
}

$city_ids_file = 'info/city_ids.txt';
$city_data_file = 'info/city_data.txt';

$unique_city_ids = readCityIds($city_ids_file);
writeCityIds($city_ids_file, $unique_city_ids);

$city_ids = readCityData($city_data_file);
$validated_city_ids = validateCityIdsFile($city_ids_file);
function generateUserVectors($group_members, $city_ids) {
    $users_vectors = [];

    for ($i = 0; $i < 20 && $i < count($group_members); $i++) {
        $user_id = $group_members[$i];
        $user_info = getUserInfo($user_id);
        $city_info = getCityInfo($user_info);
        if ($city_info !== null) {
            $user_vector = array_fill_keys($city_ids, 0);
            $user_vector[$city_info['city_id']] = 1;
            $users_vectors[] = "$user_id " . implode(' ', $user_vector) . "\n";
        }
    }
    return $users_vectors;
}

$users_vectors = generateUserVectors($group_members, $city_ids);

file_put_contents('users_vectors.txt', implode('', $users_vectors));

$users = getUsersFromGroup(210114453);

$city_ids = [];
$city_titles = [];

for ($i = 0; $i < 20 && $i < count($users); $i++) {
    $user_id = $users[$i];
    $user_info = getUserInfo($user_id);
    if (isset($user_info['response']['0']['city'])) {
        $city_ids[] = $user_info['response']['0']['city']['id'];
        $city_titles[] = $user_info['response']['0']['city']['title'];
    }
}

$city_ids = array_unique($city_ids);
$city_titles = array_unique($city_titles);

file_put_contents('info/city_data.txt', implode("\n", array_map(function($id, $title) {
    if ($id && $title) {
        return "$id $title\n";
    }
}, $city_ids, $city_titles)));

$group_id = 210114453;
$file_path = './group_members.txt';

$offset = 0;
$limit = 500;
$users = [];

while (true) {
    $url = 'https://api.vk.com/method/groups.getMembers?group_id=' . $group_id . '&offset=' . $offset . '&count=' . $limit . '&access_token=' . ACCESS_TOKEN . '&v=5.131';
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (empty($data['response']['items'])) {
        break;
    }
    if ($data['response']['0']['city']['id']) {
        echo $user_id . ': ' . $data['response']['0']['city']['id'] . "\n";
    }

    $users = array_merge($users, $data['response']['items']);
    $offset += $limit;
}

//writeUsersToFile($users, $file_path);


file_put_contents('users_vectors.txt', implode('', $users_vectors));

$users_vectors = file_get_contents('users_vectors.txt');
$users_vectors = explode("\n", $users_vectors);

/*$hamming_distances = [];

for ($i = 0; $i < count($users_vectors); $i++) {
    for ($j = $i + 1; $j < count($users_vectors); $j++) {
        $vector1 = explode(' ', $users_vectors[$i]);
        $vector2 = explode(' ', $users_vectors[$j]);

        $distance = hammingDistance($vector1, $vector2);
        $hamming_distances[] = "$i $j $distance\n";
    }
}

file_put_contents('hamming_distances.txt', implode('', $hamming_distances));

function hammingDistance($vector1, $vector2) {
    $distance = 0;
    for ($i = 0; $i < count($vector1); $i++) {
        if ($vector1[$i] !== $vector2[$i]) {
            $distance++;
        }
    }
    return $distance;
}*/


$manhattan_distances = [];

for ($i = 0; $i < count($users_vectors); $i++) {
    for ($j = $i + 1; $j < count($users_vectors); $j++) {
        $vector1 = explode(' ', $users_vectors[$i]);
        $vector2 = explode(' ', $users_vectors[$j]);
        $user_id1 = array_shift($vector1);
        $user_id2 = array_shift($vector2);
        $distance = manhattanDistance($vector1, $vector2);
        $manhattan_distances[] = "$user_id1 $user_id2 $distance\n";
    }
}

file_put_contents('manhattan_distances.txt', implode('', $manhattan_distances));

function manhattanDistance($point1, $point2) {
    $distance = 0;
    for ($i = 0; $i < count($point1); $i++) {
        $distance += abs(intval($point1[$i]) - intval($point2[$i]));
    }
    return $distance;
}