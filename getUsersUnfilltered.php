<?php
declare(strict_types=1);

require_once "./consts/consts.php";

function getUserInfo($user_id): array {
    $url = 'https://api.vk.com/method'. METHOD_USERS_GET . $user_id . '&access_token=' . ACCESS_TOKEN . '&fields=' . FIELDS . '&v=5.131';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    var_dump($data);
    return $data ?? [];
}

$vkId = USER_ID;
//$user_info = getUserInfo($vkId);


function getUsersFromGroup($group_id): array {
    $url = 'https://api.vk.com/method/groups.getMembers?group_id=' . $group_id . '&access_token=' . ACCESS_TOKEN . '&v=5.131';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['response']['items'];
}

function writeUsersToFile($users, $file_path) {
    foreach ($users as $user_id) {
        file_put_contents($file_path, $user_id . "\n", FILE_APPEND);
    }
}


$users = getUsersFromGroup(210114453);

foreach ($users as $user_id) {
    $user_info = getUserInfo($user_id);
    if ($user_info['city']) {
        echo $user_id . ': ' . $user_info['city']['id'] . "\n";
    }
}


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

    $users = array_merge($users, $data['response']['items']);
    $offset += $limit;
}

writeUsersToFile($users, $file_path);

function writeUserToFile($user_id) {
    $user_info = getUserInfo($user_id);
    if ($user_info['response'][0]['is_closed'] == false) {
        $file_path = './user_data.txt';
        $file_content = '';

        foreach ($user_info['response'][0] as $key => $value) {
            if ($key != 'response' && $key != 'counters') {
                $file_content .= "$key: $value\n";
            }
        }

        file_put_contents($file_path, $file_content . "\n", FILE_APPEND);
    }
}

//writeUserToFile($vkId);

function getUniversities($accessToken, $offset = 0, $count = 100, $city_id = null) {
    // Создайте URL запроса
    $url = 'https://api.vk.com/method/database.getUniversities?';
    $url .= 'access_token=' . $accessToken;
    $url .= '&offset=' . $offset;
    $url .= '&count=' . $count;
    $url .= '&v=5.199';
    if ($city_id !== null) {
        $url .= '&city_id=' . $city_id;
    }

    // Отправьте запрос
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    // Проверьте, был ли запрос выполнен успешно
    if (curl_errno($ch)) {
        echo 'Ошибка: ' . curl_error($ch) . "\n";
        return [];
    }

    // Закройте сессию
    curl_close($ch);

    // Parsed JSON response
    $responseData = json_decode($response, true);

    // Проверьте, есть ли ошибки
    if ($responseData === false) {
        echo 'Ошибка: Ошибка при получении ответа от API.' . "\n";
        return [];
    }
    var_dump($responseData);
    // Возвращает список университетов
    return $responseData['response']['items'];
}

// Получите список университетов
//$universities = getUniversities(ACCESS_TOKEN);

// Выведите список университетов
/*foreach ($universities as $university) {
    echo $university['title'] . "\n";
}*/