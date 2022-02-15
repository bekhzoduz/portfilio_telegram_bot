<?php

/**
 * @date 14.02.22 21:31
 * @author Bekhzodjon
 * @contact https://t.me/Bekhzodjon
 * @rules ruxsatsiz koddan muallif nomini o'zgartirish yaxshi oqibatga olib kelmaydi!
 */

ob_start();
ini_set('date.timezone', 'ASIA/Tashkent');

//sozlash
require 'Telegram.php';
require 'conf.php';
include 'language.php';

$telegram = new BekTG($token);
$Bekhzodjon = $telegram->getData();
$chat_id = $telegram->ChatID();
$text = $telegram->Text();
$type = $Bekhzodjon["message"]["chat"]["type"];
$username = "@" . $Bekhzodjon['message']['chat']['username'];
$ufname = $Bekhzodjon['message']['from']['first_name'];
$type = $Bekhzodjon["message"]["chat"]["type"];
$user_id = $Bekhzodjon['message']['from']['id'];
$mesid = $Bekhzodjon["message"]["message_id"];
$data = $Bekhzodjon['callback_query']['data'];
$Callback_ID = $Bekhzodjon['callback_query']['id'];
$calldata = $Bekhzodjon['callback_query']['data'];
$Callback_ID = $Bekhzodjon['callback_query']['id'];
$Callback_msgID = $Bekhzodjon['callback_query']['message']['message_id'];
$Callback_FromID = $Bekhzodjon['callback_query']['from']['id'];


$user = users($chat_id);
$userlang = $user["lang"];
$lang = (object)$langs[$userlang];

$date = date("H:i:s d.m.Y");
if ($type == "private") {
    $result = $db->query("SELECT * FROM users WHERE `user_id` ='$user_id'");
    $row = $result->fetch_assoc();
    if ($row["user_id"] == false) {
        $db->query("INSERT INTO users(`name`,`username`,`lang`,`user_id`,`date`) VALUES ('$ufname','$username','1','$user_id','$date')");
    }
}
$keyb = inline([[$lang->project => 'project'], [$lang->feed => 'feedback']]);
if ($text == "/start") {
    if (!$user['lang']) {
        $content = ['chat_id' => $chat_id, 'text' => "*Пожалуйста, выберите язык / Iltimos, tilingizni tanlang ⬇️*", 'parse_mode' => 'markdown', 'reply_markup' => inline([["🇺🇿O'zbekcha" => "lang#uz", "🇷🇺Русский" => "lang#ru"]])];
        $telegram->bot('SendMessage', $content);
    } else {

        $content = ['chat_id' => $chat_id, 'text' => $lang->start, 'parse_mode' => 'markdown', 'reply_markup' => $keyb];
        $telegram->bot('SendMessage', $content);
    }
} elseif (mb_stripos($data, "lang#") !== false) {
    $selected_lang = explode("#", $data)[1];
    $db->query("UPDATE `users` SET `lang` ='$selected_lang' WHERE user_id='$chat_id'");
    $content = ['message_id' => $Callback_msgID, 'chat_id' => $Callback_FromID, 'text' => $langs[$selected_lang]['start'], 'parse_mode' => 'markdown', 'reply_markup' => $keyb];
    $telegram->bot('EditMessageText', $content);
}

if ($data == 'project') {
    $content = ['message_id' => $Callback_msgID, 'chat_id' => $Callback_FromID];
    $telegram->bot('DeleteMessage', $content);
    $arr = getData(1, 1);
    $caption = $arr['caption'];
    $techno = $arr['techno'];

    $cap = "_ $lang->pro_about _ \n *$caption*\n\n _ $lang->pro_teh _ \n $techno";
    $content = ['chat_id' => $Callback_FromID, 'photo' => $arr['photo'], 'caption' => $cap, 'parse_mode'=>'markdown','reply_markup' => $arr['inline']];
    $telegram->bot('SendPhoto', $content);
    exit;
} elseif (mb_stripos($data, 'next') !== false) {
    $id = explode('_', $data)[1];
    $arr = getData($id, 1);
    $media = ['type' => "photo", 'media' => $arr['photo'], 'caption' => $arr['caption']];
    $content = ['message_id' => $Callback_msgID, 'chat_id' => $Callback_FromID, 'media' => json_encode($media), 'reply_markup' => $arr['inline']];
    $th = $telegram->bot('EditMessageMedia', $content);
    exit;
}
if ($data == 'home') {
    $content = ['callback_query_id' => $Callback_ID, 'text' => $lang->home, 'show_alert' => false];
    $telegram->bot('answerCallbackQuery', $content);
    exit;
}
if ($data == 'end') {
    $content = ['callback_query_id' => $Callback_ID, 'text' => $lang->end, 'show_alert' => false];
    $telegram->bot('answerCallbackQuery', $content);
    exit;
}

if ($data == 'feedback') {
    $key = json_encode([
        'inline_keyboard' => [
            [['text' => "🚀 Telegram", 'url' => "https://t.me/Bekhzodjon"]],
            [['text' => "📷 Instagram", 'url' => "https://instagram.com/isomidinov_bekzod"]],
            [['text' => "🧑‍💻 Github", "url" => "https://github.com/bekzodshax"]],
        ]
    ]);

    $content = [
        'chat_id' => $Callback_FromID, 'message_id' => $Callback_msgID, 'reply_markup' => $key, 'parse_mode' => 'html', 'text' => "<b>$lang->feedback   </b>"
    ];
    $telegram->bot('EditMessageText', $content);
}
if (isset($data)) {
    $content = ['callback_query_id' => $Callback_ID];
    $telegram->bot('answerCallbackQuery', $content);
}
