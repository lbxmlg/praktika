<?php

const TOKEN = '5404096231:AAH12Q9OMIkGw00g0M5p_PL_W4ZsyCnveok';
const BASE_URL = 'https://api.telegram.org/bot' . TOKEN . '/';
include('menu.php');

$update = json_decode(file_get_contents('php://input'));

file_put_contents(__DIR__ .'/logs.txt', print_r($update, 1),FILE_APPEND);

$getQuery = array(
    "chat_id" => TG_USER_ID,
    "text" => $textMessage,
    "parse_mode" => "html",
    );



?>

