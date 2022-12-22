<?php

const TOKEN = '5404096231:AAH12Q9OMIkGw00g0M5p_PL_W4ZsyCnveok';
$method = 'setWebhook';

$url = 'https://api.telegram.org/bot' . TOKEN . '/' . $method;
$options = [
'url' => 'https://students2022.ru/index.php',];

$response = file_get_contents($url . '?' . http_build_query($options));

var_dump($response);