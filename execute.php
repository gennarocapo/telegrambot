<?php
 require_once('TwitterAPIExchange.php'); //get it from https://github.com/J7mbo/twitter-api-php
include_once('TwitterSentimentAnalysis.php');
    /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
    $settings = array(
        'oauth_access_token' => "17757558-1jlw4zfBLHJ74o9K4Dn7ULEW8SboHdPFdLAsHjtt9",
        'oauth_access_token_secret' => "pNf3nwMeDjonL5yPWc5h05GgvkOmuSWjbgq0TkHwMQaFU",
        'consumer_key' => "89UEqupgMeygd721YbHwZ1DS5",
        'consumer_secret' => "GxlPEzEYTzXItnpru7E7JZw1cjuA4BkiDBMXDULkIDs2TzhPYF"
    );


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
$secondAnswerReal = "1.676M";
$secondAnswerFake1 = "1.8M";
$secondAnswerFake2 = "1.02M";
if(strpos($text, "/start") === 0 || $text=="ciao" || $text=="cia" || $text=="hello" || $text=="hi" || $text=="no")
{
	$response = "Ciao $firstname, benvenuto! Sei pronto per partire col percorso digitale? ";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["si"], ["no"]], "one_time_keyboard": true}';
}
elseif($text=="si")
{
	$response = "Bene, cominciamo.. Quanti Mi Piace ha la pagina Vodafone su Facebook?";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["1.676M"],["1.33M"]], "one_time_keyboard": true}';
}
elseif($text==strtolower($secondAnswerReal))
{
	    $ta_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    	$getfield = '?screen_name=TIM_Official';
   	 $requestMethod = 'GET';
  	  $twitter = new TwitterAPIExchange($settings);
   	 $follow_count=$twitter->setGetfield($getfield)
    		->buildOauth($ta_url, $requestMethod)
    		->performRequest();
          $data = json_decode($follow_count, true);
          $followers_count=$data[0]['user']['followers_count'];
          
	$response = "Esatto, la pagina Vodafone attualmente si classifica al secondo posto tra le Telco in Italia per numero di Mi Piace, subito dietro Tim con piu di 2M.
	 I follower di tim sono in tutto: " . $followers_count;
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
}
elseif ($text=="rete4g")
{
	 $TwitterSentimentAnalysis = new TwitterSentimentAnalysis("e16265e25f400c107e217ca3ba3520c3","89UEqupgMeygd721YbHwZ1DS5","GxlPEzEYTzXItnpru7E7JZw1cjuA4BkiDBMXDULkIDs2TzhPYF","17757558-1jlw4zfBLHJ74o9K4Dn7ULEW8SboHdPFdLAsHjtt9","pNf3nwMeDjonL5yPWc5h05GgvkOmuSWjbgq0TkHwMQaFU");
    //Search Tweets parameters as described at https://dev.twitter.com/docs/api/1.1/get/search/tweets
    $twitterSearchParams=array(
        'q'=>$text,
        'lang'=>'en',
        'count'=>3,
    );
    $results=$TwitterSentimentAnalysis->sentimentAnalysis($twitterSearchParams);
        $response1 = $results[0]['text'] . $results[0]['sentiment'] . "\n"; 
	 $response2 = $results[1]['text'] . $results[1]['sentiment'] . "\n"; 
	$response3 = $results[2]['text'] . $results[2]['sentiment'] . "\n"; 
	 $response ="Gli ultimi 3 risultati della parola " .$text . " sono:\n" . $response1 . $response2 . $response3;
        $parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
}
else
{
	$response = "Comando non valido!";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
}
echo json_encode($parameters);
