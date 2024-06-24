<?php
require_once "consts/consts.php";
require_once "getUsersUnfilltered.php";

//TODO: починить получение юзеров из группы (сейчас оно потерялось)
//ладно, мы открываем сейчас юхеров, я их трогать не буду, кто-то валидный, кто нет да и бог с ним
//мне по ним надо выборку сделать
//все-таки сначала отфильтруем
function isActive($is_active): bool
{
    return $is_active == false ? true : false;
}
function GetUsersGroupInfo($users): array
{
    $usersInfo = [];

    for ($i = 0; $i < 20 && $i < count($users); $i++) {
        $user_id = $users[$i];
        $user_info = getUserInfo($user_id);
        if ((isActive($user_info['response'][0]['is_closed'])) && ((!isset($user_info['response'][0]['deactivated']))))
        {
            $usersInfo[$user_id] = $user_info;
        };
    }
    // на этом этапе мы отсеяли неактивные или deactivated аккуант
    return $usersInfo;
}

function getInformationToBuiltVector($user_info) {
    getInfoAboutEducation($user_info);
    getInfoAboutPersonal($user_info);
}

function getInfoAboutEducation($user_info) {
    $university_file_path = 'info/universities.txt';
    $graduation_file_path = 'info/graduation.txt';
    $education_status_file_path = 'info/education_status.txt';
    file_put_contents($university_file_path, '');

    foreach ($user_info as $user_id => $user_info_data) {
        if (isset($user_info_data['response'][0]['universities'])) {
            foreach ($user_info_data['response'][0]['universities'] as $university) {
                $university_info = "";
                if (isset($university['id'])) {
                    $university_info .= "ID: {$university['id']}\n";
                }
                if (isset($university['name'])) {
                    $university_info .= "Name: {$university['name']}\n";
                } else
                    $university_info .= "Name:\n";
                if (isset($university['city'])) {
                    $university_info .= "City ID: {$university['city']}\n";
                }
                if (isset($university['faculty'])) {
                    $university_info .= "Faculty ID: {$university['faculty']}\n";
                }
                if (isset($university['faculty_name'])) {
                    $university_info .= "Faculty Name: {$university['faculty_name']}\n";
                } else
                    $university_info .= "Faculty Name:\n";
                if (isset($university['graduation'])) {
                    $university_info .= "Graduation: {$university['graduation']}\n";
                } else  $university_info .= "Graduation: \n";
                if (isset($university['education_status'])) {
                    $university_info .= "Education Status: {$university['education_status']}\n";
                } else  $university_info .= "Education Status: \n";
                file_put_contents($university_file_path, $university_info, FILE_APPEND);
            }
            $attributes = getUniqueUniversityAttributes($university_file_path);
            writeAttributesToFile($attributes, 'info/university_attributes.txt');
        }

    }
}

function getInfoAboutPersonal($user_info)
{
    $personal_file_path = 'info/personal.txt';
    file_put_contents($personal_file_path, '');
    foreach ($user_info as $user_id => $user_info_data) {
        echo 'my statement';

        var_dump($user_info_data['response'][0]['personal']['alcohol']);


        if (isset($user_info_data['response'][0]['personal'])) {
            foreach ($user_info_data['response'][0]['personal'] as $personal) {
                $personal_info = "";
                if (isset($personal['alcohol']) == 0) {
                    $personal_info .= "Alcohol: 0\n";
                } elseif (isset($personal['alcohol'])) {
                    $personal_info .= "Alcohol: {$personal['alcohol']}\n";
                } else
                    $personal_info .= "Alcohol: \n";

                file_put_contents($personal_file_path, $personal_info, FILE_APPEND);
            }
            $attributes = getUniquePersonalAttributes($personal_file_path);
            writeAttributesToFile($attributes, 'info/personal_attributes.txt');
        }
    }
}

function buildVector($user_info): array {
    $attributes_file_path = 'info/university_attributes.txt';

    $vector = [];
    foreach ($user_info as $user_id => $user_info_data) {
        $user_vector = generateUserVectors([$user_id => $user_info_data], $attributes_file_path)[0];
        echo "hjkhk";
        var_dump($user_vector);
        echo "reryt";
        //тут набирается массив
        var_dump($user_vector);
        $vector[$user_id] = explode(' ', $user_vector);
    }

    echo "gegeggege";
    var_dump($vector);
    return $vector;
}

function generateUserVectors($user_info_data, $attributes_file_path): array {
    $attributes = readAttributesFromFile($attributes_file_path);
    $unique_university_ids = array_map('intval', explode(',', $attributes['University IDs']));
    $unique_faculty_ids = array_map('intval', explode(',', $attributes['Faculty IDs']));
    $unique_graduation_years = array_map('intval', explode(',', $attributes['Graduation Years']));
    $unique_city_ids = array_map('intval', explode(',', $attributes['City IDs']));
    $unique_education_status = array_map('intval', explode(',', $attributes['Education Status']));

    $users_vectors = [];
    foreach ($user_info_data as $user_id => $user_info) {
        $user_vector = array_fill_keys($unique_university_ids, 0);
        $faculty_vector = array_fill_keys($unique_faculty_ids, 0);
        $graduation_vector = array_fill_keys($unique_graduation_years, 0);
        $city_vector = array_fill_keys($unique_city_ids, 0);
        $education_vector = array_fill_keys($unique_education_status, 0);

        if (isset($user_info['response'][0]['university']) && in_array($user_info['response'][0]['university'], $unique_university_ids)) {
            $user_vector[$user_info['response'][0]['university']] = 1;
        }

        if (isset($user_info['response'][0]['faculty']) && in_array($user_info['response'][0]['faculty'], $unique_faculty_ids)) {
            $faculty_vector[$user_info['response'][0]['faculty']] = 1;
        }

        if (isset($user_info['response'][0]['graduation']) && in_array($user_info['response'][0]['graduation'], $unique_graduation_years)) {
            $graduation_vector[$user_info['response'][0]['graduation']] = 1;
        }

        if (isset($user_info['response'][0]['city']['id']) && in_array($user_info['response'][0]['city']['id'], $unique_city_ids)) {
            $city_vector[$user_info['response'][0]['city']['id']] = 1;
        }

        if (isset($user_info['response'][0]['universities']['education_status']) && in_array($user_info['response'][0]['universities']['education_status'], $unique_education_status)) {
            $education_vector[$user_info['response'][0]['universities']['education_status']] = 1;
        }

        $combined_vector = array_merge($user_vector, $faculty_vector, $graduation_vector, $city_vector, $education_vector);
        $users_vectors[] = "$user_id " . implode(' ', array_values($combined_vector)) . "\n";
    }
    return $users_vectors;
}

function readAttributesFromFile($file_path): array {
    $attributes = [];
    $file_content = file_get_contents($file_path);
    $lines = explode("\n", $file_content);
    foreach ($lines as $line) {
        $parts = explode(':', $line, 2);
        if (count($parts) == 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            $attributes[$key] = $value;
        }
    }
    return $attributes;
}

function writeVectorToFile($vector, $file_path) {
    file_put_contents($file_path, '');
    $output_content = '';
    foreach ($vector as $user_id => $user_vector) {
        $output_content .= implode(' ', $user_vector);
    }
    file_put_contents($file_path, $output_content);
}

function getUniqueUniversityAttributes($university_file_path): array {
    $university_info = file_get_contents($university_file_path);
    $university_info_lines = explode("\n", $university_info);

    $unique_university_ids = [];
    $unique_faculty_ids = [];
    $unique_graduation_years = [];
    $unique_city_ids = [];
    $unique_education_status = [];

    foreach ($university_info_lines as $line) {
        list($key, $value) = explode(':', $line);
        $key = trim($key);
        $value = trim($value);

        if ($key == 'ID') {
            $unique_university_ids[] = $value;
        } elseif ($key == 'Faculty ID') {
            $unique_faculty_ids[] = $value;
        } elseif ($key == 'Graduation') {
            $unique_graduation_years[] = $value;
        } elseif ($key == 'City ID') {
            $unique_city_ids[] = $value;
        } elseif ($key === 'Education Status'){
            $unique_education_status[] = $value;
        }
    }

    $unique_university_ids = array_unique($unique_university_ids);
    $unique_faculty_ids = array_unique($unique_faculty_ids);
    $unique_graduation_years = array_unique($unique_graduation_years);
    $unique_city_ids = array_unique($unique_city_ids);
    $unique_education_status = array_unique($unique_education_status);

    return [
        'university_ids' => implode(',', $unique_university_ids),
        'faculty_ids' => implode(',', $unique_faculty_ids),
        'graduation_years' => implode(',', $unique_graduation_years),
        'city_ids' => implode(',', $unique_city_ids),
        'education_status' => implode(',', $unique_education_status),
    ];
}
function getUniquePersonalAttributes($personal_file_path): array {
    $personal_info = file_get_contents($personal_file_path);
    $personal_info_lines = explode("\n", $personal_info);
    $unique_alcohol_ids = [];
    foreach ($personal_info_lines as $line) {
        list($key, $value) = explode(':', $line);
        $key = trim($key);
        $value = trim($value);
        if ($key == 'Alcohol') {
            $unique_alcohol_ids[] = $value;
        }
    }
    $unique_alcohol_ids = array_unique($unique_alcohol_ids);
    return [
        'alcohol' => implode(',', $unique_alcohol_ids)
    ];
}


function writeAttributesToFile($attributes, $file_path) {
    $file_content = '';
    if ($file_path == 'info/university_attributes.txt')
    {
        $file_content .= "University IDs: " . $attributes['university_ids'] . "\n";
        $file_content .= "Faculty IDs: " . $attributes['faculty_ids'] . "\n";
        $file_content .= "Graduation Years: " . $attributes['graduation_years'] . "\n";
        $file_content .= "City IDs: " . $attributes['city_ids'] . "\n";
        $file_content .= "Education Status: " . $attributes['education_status'] . "\n";
    } elseif ($file_path == 'info/personal_attributes.txt')
    {
        $file_content .= 'Alcohol: ' . $attributes['alcohol'] . "\n";
    }
    file_put_contents($file_path, $file_content);
}

$group_members = file_get_contents('group_members.txt');
$group_members = explode("\n", $group_members);
//сделаем с ограничением на 100 человек
$user_info = GetUsersGroupInfo($group_members);
//паралельно мы должны получить информацию о том, исходя из какой информации должны будем получить информацию для анализа профилей

//т.е. мне отдельно надо собрать всю информацию в отдельный файл по всем критериям
getInformationToBuiltVector($user_info);

$vector = buildVector($user_info);
$output_file = 'user_vectors.txt';
writeVectorToFile($vector, $output_file);

?>