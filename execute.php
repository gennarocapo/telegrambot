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
$secondAnswerReal = ">1.5M";
$secondAnswerFake1 = "<1M";
$secondAnswerFake2 = ">2M";
$secondAnswerFake3 = ">5M";
if(strpos($text, "/start") === 0 || $text=="ciao" || $text=="cia" || $text=="hello" || $text=="hi" || $text=="no")
{
	$response = "Ciao $firstname, benvenuto! Sei pronto per partire col percorso digitale? ";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["si"], ["no"]], "one_time_keyboard": true}';
}
elseif($text=="si")
{
	$response = "Bene, cominciamo.. Secondo te quanti Mi Piace ha la pagina Vodafone su Facebook?";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [[">1.5M"],["<1M"],[">2M"]], "one_time_keyboard": true}';
}
elseif($text==$secondAnswerFake1 || $text==$secondAnswerFake2 || $text==$secondAnswerFake3)
{
	$response = "Putroppo no,riproviamo..Secondo te quanti Mi Piace ha la pagina Vodafone su Facebook?";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [[">1.5M"],["<1M"],[">2M"],[">5M"]], "one_time_keyboard": true}';
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
          
	  $json_url ='https://graph.facebook.com/vodafoneit?access_token=1361949993817552|b4c680b656602a4a697f45d7d0cf790e&fields=fan_count';
	  $json = file_get_contents($json_url);
          $json_output = json_decode($json);
  	  $json1_url ='https://graph.facebook.com/TimOfficialPage?access_token=1361949993817552|b4c680b656602a4a697f45d7d0cf790e&fields=fan_count';
	  $json1 = file_get_contents($json1_url);
          $json1_output = json_decode($json1);
          $json3_url ='https://graph.facebook.com/Wind?access_token=1361949993817552|b4c680b656602a4a697f45d7d0cf790e&fields=fan_count';
	  $json3 = file_get_contents($json3_url);
          $json3_output = json_decode($json3);


          $Vodafonelikes = $json_output->fan_count;
	  $Timlikes = $json1_output->fan_count;
	  $Windlikes = $json3_output->fan_count;
	   

	$response = "Esatto, la pagina Vodafone attualmente si classifica al secondo posto tra le Telco in Italia per numero di Mi Piace.\n Queste al momento sono le prime tre posizioni\nTim: " . number_format($Timlikes) . "\nVodafone: " . number_format($Vodafonelikes) . "\nWind: " . number_format($Windlikes) . "\n";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["Prosegui"]], "one_time_keyboard": true}';
}
elseif($text="Prosegui")
{
	 $TwitterSentimentAnalysis = new TwitterSentimentAnalysis("e16265e25f400c107e217ca3ba3520c3","89UEqupgMeygd721YbHwZ1DS5","GxlPEzEYTzXItnpru7E7JZw1cjuA4BkiDBMXDULkIDs2TzhPYF","17757558-1jlw4zfBLHJ74o9K4Dn7ULEW8SboHdPFdLAsHjtt9","pNf3nwMeDjonL5yPWc5h05GgvkOmuSWjbgq0TkHwMQaFU");
    //Search Tweets parameters as described at https://dev.twitter.com/docs/api/1.1/get/search/tweets
    $twitterSearchParams=array(
        'q'=>$text,
        'lang'=>'en',
        'count'=>3,
    );
	
    $results=$TwitterSentimentAnalysis->sentimentAnalysis($twitterSearchParams);
	$risultati="";
         foreach($results as $tweet) {
	     $risultati= $risultati . "- Sentiment: " . $tweet['sentiment'] . "\nTweet: " . $tweet['text'] .  "\n\n";
	 }
	
	 $response ="Gli ultimi " . sizeof($results) ." risultati della parola " .$text . " sono:\n" . $risultati;
        $parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
}

echo json_encode($parameters);
?>
