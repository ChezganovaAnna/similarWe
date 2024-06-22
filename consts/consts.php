<?php
const ACCESS_TOKEN = 'vk1.a.yPsIXab1Jl7-OojzLQf8OSYikdNIbrAOGRnOG_HWTvSxT6QnPKEuB7hIFzrnRS73BwvBVHoy5Mv0mXidCoagVLWQNkEW9rdqieh-t1X6EMl_tjFgd4U6aI-U99IhmjCeOZh-SW8XYU6-Ygh9_mp-UHSoynU5jsZJ4zlLcuTSoZU64dKjTClNDHieJLlRTXUq';
const FIELDS = "activities,about,bdate.".
"books,can_post,can_see_all_posts,can_see_audio,".
"can_send_friend_request,can_write_private_message,".
"career,city,country,connections,contacts,counters,".
"domain,education,exports,first_name_gen,followers_count,".
"friend_status,games,has_mobile,has_photo,home_town,".
"interests,is_favorite,is_friend,is_hidden_from_feed,".
"is_no_index,last_seen,lists,maiden_name,military,".
"movies,nickname,occupation,online,personal,".
"photo_id,quotes,relation,relatives,screen_name,site,".
"status,timezone,tv,universities,verified,deactivated,music";
const METHOD_USERS_GET = "/users.get?user_ids=";
const APP_ID = 51900699;
const USER_ID = 211043234;

const URL_VK_SAMPLE = 'https://api.vk.com/method/';

const MAIN_VALUES = array(
    0 => "Семья и дети",
    1 => "Карьера и деньги",
    2 => "Развлечения и отдых",
    3 => "Наука и исследования",
    4 => "Совершенствование мира",
    5 => "Ум и креативность",
    6 => "Здоровье",
    7 => "Красота и искусство",
    8 => "Слава и влияние"
);

?>