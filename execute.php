<?php
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$text = trim($text);
$text = strtolower($text);
header("Content-Type: application/json");

$response = '';

if(strpos($text, "/start") === 0 || $text=="ciao")
{
	$keyboard = ['inline_keyboard' => [[['text' =>  'myText', 'callback_data' => 'myCallbackText']]]];
        $parameters["reply_markup"] = json_encode($keyboard, true);
	$response = "Ciao $firstname, benvenuto! Cominciamo il nostro percorso digitale. Clicca sul link http://www.google.com per partire";
}
elseif($text=="domanda 1")
{
	$response = "risposta 1";
	$parameters = array('chat_id' => $chatId, "text" => $response);
}
elseif($text=="domanda 2")
{
	$response = "risposta 2";
	$parameters = array('chat_id' => $chatId, "text" => $response);
}
else
{
	$response = "Comando non valido!";
	$parameters = array('chat_id' => $chatId, "text" => $response);
}


$parameters["method"] = "sendMessage";
echo json_encode($parameters);
