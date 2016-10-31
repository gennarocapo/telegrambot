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
	$response = "Ciao $firstname, benvenuto! Sei pronto per partire col percorso digitale? ";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["si"], ["no"]], "one_time_keyboard": false}';
}
elseif($text=="si")
{
	$response = "Bene, segui sul link http://www.google.com per partire";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = null;
}
elseif($text=="no")
{
	$response = "vafanculo";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = null;
}
else
{
	$response = "Comando non valido!";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = null;
}



echo json_encode($parameters);
