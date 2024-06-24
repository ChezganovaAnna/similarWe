<?php
declare(strict_types=1);
require_once "./consts/consts.php";
$user_id = USER_ID;
function getUserInfo($user_id): array {
    $url = 'https://api.vk.com/method'. METHOD_USERS_GET . $user_id . '&access_token=' . ACCESS_TOKEN . '&fields=' . FIELDS . '&v=5.131';
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    //var_dump($data);
    return $data ?? [];
}
$vkId = USER_ID;
$user_info = getUserInfo(36419);
var_dump($user_info);
$is_closed_values = array_column($user_info["response"], 'is_closed');
//echo $user_info['response']['0']['city']['id'];
function writeUsersToFile($users, $file_path) {
    foreach ($users as $user_id) {
        file_put_contents($file_path, $user_id . "\n", FILE_APPEND);
    }
}
function writeUserToFile($user_id) {
    $user_info = getUserInfo($user_id);
    if ($user_info['response'][0]['is_closed'] == false && $user_info['response'][0]['deactivated'] !== '') {
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
