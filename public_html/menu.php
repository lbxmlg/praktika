<?php
// токен бота
$bot_token = "5404096231:AAH12Q9OMIkGw00g0M5p_PL_W4ZsyCnveok";

// админ бота
$bot_admin = 365200261;

// получаем данные от телеграм
$data = json_decode(file_get_contents("php://input"));


ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define("TG_TOKEN", "5404096231:AAH12Q9OMIkGw00g0M5p_PL_W4ZsyCnveok");
define("TG_USER_ID", 365200261);

/*$getQuery = array(
    "chat_id" => $bot_admin,
    "text" => "message buttons",
    'reply_markup' => json_encode(
       array(
        'keyboard' => array(
        array(
	    array(
		'text' => 'Тестовая кнопка 1',
		'callback_data' => 'test_1',
	    ),
	    array(
		'text' => 'Тестовая кнопка 2',
		'callback_data' => 'test_2',
	    ),
    )),
    'one_time_keyboard' => TRUE,
    'resize_keyboard' => TRUE,
)),

// tg://resolve?domain=iMakeBot&start=s_3_0-1

/**
 * Контент
 */
 
$content = [
    "steps" => [
        [
            "name" => "start",
            "line" => 0,
            "type" => "text",
            "text" => "Данный бот собирает обратную связь от сотрудников",
            "media" => null,
            "steps" => [
          
                [
                    "name" => "нажмите что бы оставить обратную связь",
                    "line" => 1,
                    "type" => "text",
                    "text" => "выберите из какого вы направления",
                    "media" => null,
                    "steps" => [
                        [
                            "name" => "back-end",
                            "line" => 0,
                            "type" => "text",
                            "text" => "Позже здесь будет форма для оставления обратной связи",
                            "media" => null,
                            "steps" => [
                                [
                                    "name" => "Личный чат",
                                    "line" => 0,
                                    "type" => "photo",
                                    "text" => "Проверка действия 1--0",
                                    "media" => "",
                                    "steps" => []
                                ],
                                [
                                    "name" => "Общий чат (по тегу)",
                                    "line" => 0,
                                    "type" => "photo",
                                    "text" => "Проверка действия 1--1",
                                    "media" => "",
                                    "steps" => []
                                ],
                            ]
                        ],
                        [
                            "name" => "front-end",
                            "line" => 0,
                            "type" => "text",
                            "text" => "Позже здесь будет форма для оставления обратной связи",
                            "media" => "null",
                            "steps" => [
                                [
                                    "name" => "Личный чат",
                                    "line" => 0,
                                    "type" => "photo",
                                    "text" => "Проверка действия 1--0",
                                    "media" => "",
                                    "steps" => []
                                ],
                                [
                                    "name" => "Общий чат (по тегу)",
                                    "line" => 0,
                                    "type" => "photo",
                                    "text" => "Проверка действия 1--1",
                                    "media" => "",
                                    "steps" => []
                                ],]
                        ],
                        
                    ]
                ]
            ]
        ]
    ]
];

/** Запрос в Телеграм
 * @param $method
 * @param array $fields
 * @return mixed
 */
$query = function ($method, $fields = []) use ($bot_token) {
    // откроем соединение
    $ch = curl_init("https://api.telegram.org/bot" . $bot_token . "/" . $method);
    // определим опции
    curl_setopt_array($ch, [
        CURLOPT_POST => count($fields),
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 10
    ]);
    // спарсим в объект результат запроса
    $result = json_decode(curl_exec($ch), true);
    // закроем соединение
    curl_close($ch);
    // вернем результат
    return $result;
};

/**
 * Выводим уведомление
 * @param $cbq_id
 * @param $text
 */
$notice = function ($cbq_id, $text = null) use ($query) {
    // определим данные
    $data = [
        "callback_query_id" => $cbq_id,
        "alert" => false
    ];
    // если есть текст то добавим
    if (!is_null($text)) {
        $data['text'] = $text;
    }
    // отправим в Телеграм
    $query("answerCallbackQuery", $data);
};

/** Получаем контент
 * @param $step_idx
 * @param $parents
 * @param $data
 * @return array
 */
$getContent = function ($step_idx, $parents, $data) use (&$getContent) {
    // определим результат по умолчанию
    $result = null;
    // проверим
    if (!is_null($parents)) {
        // получим родителя
        $parent = array_shift($parents);
        // проверим значение
        if (isset($data['steps'][$parent])) {
            // проверим путь
            if (count($parents)) {
                // отрпавим на рекурсию
                $result = $getContent($step_idx, $parents, $data['steps'][$parent]);
            } else {
                // определим результат
                $result = $data['steps'][$parent]['steps'][$step_idx];
            }
        }
    } else {
        // определим результат
        $result = $data["steps"][$step_idx];
    }
    // вернем результат
    return $result;
};

/** Выводим сообщение по запросу
 * @param $step_idx
 * @param $parents
 * @param $chat_id
 * @param null $cbq_id
 * @param null $message_id
 */
$printUpdate = function ($step_idx, $parents, $chat_id, $cbq_id = null, $message_id = null) use ($getContent, $query, $notice, $content) {
    // переопределим вложенность
    $parents = !is_null($parents) ? explode("-", $parents) : null;
    // получаем шаг
    $step = $getContent($step_idx, $parents, $content);
    // проверим
    if (!is_null($step)) {
        // готовим данные
        $data = [
            "chat_id" => $chat_id,
        ];
        // если это нажатие по кнопке то удалим текущее сообщение
        if (!is_null($cbq_id)) {
            // гасим запрос
            $notice($cbq_id);
            // удаляем сообщение
            $query("deleteMessage", array_merge($data, ["message_id" => $message_id]));
        }
        // дополним данные
        $data["parse_mode"] = "html";
        // определим кнопки если они есть
        $buttons = [];
        // проверим
        if (count($step['steps'])) {
            // определим начало
            $line = 0;
            // определим путь
            $parents_ = !is_null($parents) ? implode("-", array_merge($parents, [$step_idx])) : $step_idx;
            // переберем
            foreach ($step['steps'] as $key => $next) {
                // добавим кнопку
                $buttons[$next['line']][] = [
                    "text" => $next['name'],
                    "callback_data" => "s_" . $key . "_" . $parents_
                ];
            }
        }
        // кнопка вернуться
        if (!is_null($parents)) {
            // получим первого
            $parent = array_pop($parents);
            // добавим кнопку последним рядом
            $buttons[count($buttons)][] = [
                "text" => "Вернуться",
                "callback_data" => "s_" . $parent . "_" . implode("-", $parents)
            ];
        }
        // проверим добавление кнопок
        if (count($buttons)) {
            // добавим кнопки
            $data["reply_markup"] = json_encode(['inline_keyboard' => array_values($buttons)]);
        }
        // поддерживаемые типы
        if (!is_null($step['media']) && in_array($step['type'], ['photo', 'video', 'audio', 'document'])) {
            // проверим описание
            if (!empty($step['text'])) {
                $data['caption'] = $step['text'];
            }
            // добавим медиа
            $data[$step['type']] = $step['media'];
            // отправим сообщение
            $query("send" . ucfirst($step['type']), $data);
        } elseif ($step['type'] === "text" && !empty($step['text'])) {
            // добавим текст
            $data['text'] = $step['text'];
            // отправим сообщение
            $query("sendMessage", $data);
        } else {
            // выведем ошибку о не поддерживаемом методе
            $query("sendMessage", array_merge($data, ["text" => "Sorry, error 405"]));
        }
    } else {
        // проверим на нажатие кнопки
        if (!is_null($cbq_id)) {
            // выведем уведомление
            $notice($cbq_id, "Error 404 STEP");
        }
    }
};

/**
 * Простой роутер бота
 */
if (isset($data->message)) {
    // получим id чата
    $chat_id = $data->message->from->id;
    // если это текстовое сообщение
    if (isset($data->message->text)) {
        // проверим что это старт бота
        if ($data->message->text == "/start") {
            // выводим сообщение
            $printUpdate(0, null, $chat_id);
        }
        // если это старт по ссылке
        elseif (preg_match("~\/start s_([\d]+)_?([\d-]*)~", $data->message->text, $matches)) {
            // выведем сообщение по ссылке
            $printUpdate($matches[1], $matches[2], $chat_id);
        }
    }
    // другие типы сообщений
    else {
        // если это админ бота направляет сообщение
        if ($chat_id === $bot_admin) {
            // по умолчанию
            $file_id = null;
            // если это картинка
            if (isset($data->message->photo)) {
                // file_id последней картикни
                $file_id = end($data->message->photo)->file_id;
            } // если это фидео-файл
            elseif (isset($data->message->video)) {
                // file_id видео-файла
                $file_id = $data->message->video->file_id;
            } // если это аудио-файл
            elseif (isset($data->message->audio)) {
                // file_id аудио-файла
                $file_id = $data->message->audio->file_id;
            } // если это документ
            elseif (isset($data->message->document)) {
                // file_id документа
                $file_id = $data->message->document->file_id;
            }
            // проверим необходимость отправки
            if (!is_null($file_id)) {
                // отправим file_id
                $query("sendMessage", [
                    "chat_id" => $chat_id,
                    "text" => $file_id
                ]);
            }
        }
    }
// если это нажатие по кнопке
} elseif (isset($data->callback_query)) {
    // получим id чата
    $chat_id = $data->callback_query->from->id;
    // получим callBackQuery_id
    $cbq_id = $data->callback_query->id;
    // получим переданное значение в кнопке
    $c_data = $data->callback_query->data;
    // спарсим значения
    $params = explode("_", $c_data);
    // если это переход по шагам
    if ($params[0] == "s") {
        // выводим сообщение
        $printUpdate(
            $params[1],
            ($params[2] !== "")
                ? $params[2]
                : null,
            $chat_id,
            $cbq_id,
            $data->callback_query->message->message_id
        );
    }
    // если это другие кнопки
    else {
        // заглушим просто запрос
        $notice($cbq_id, "This is notice for bot");
    }
}