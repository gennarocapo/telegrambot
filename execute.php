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
$secondAnswerFake1 = "<1m";
$secondAnswerFake2 = ">2m";
$secondAnswerFake3 = ">5m";
$sentimentParola= "ReferendumItaly";
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
	   

	$response = "Esatto, la pagina Vodafone attualmente si classifica al secondo posto tra le Telco in Italia per numero di Mi Piace.\n Queste sono le prime tre posizioni in tempo reale.\nTim: "
		. number_format($Timlikes) . "Mi Piace\nVodafone: " . number_format($Vodafonelikes) . " Mi Piace\nWind: " . number_format($Windlikes) . " Mi Piace\nSarà lo stesso anche su Twitter? Clicca per scoprirlo";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["Avanti"]], "one_time_keyboard": true}';
}
elseif($text=="avanti"){
	  $ta_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    	$TIMgetfield = '?screen_name=TIM_Official';
	$VODAgetfield = '?screen_name=VodafoneIT';
   	 $requestMethod = 'GET';
  	  $twitter = new TwitterAPIExchange($settings);
   	 $TIMfollow_count=$twitter->setGetfield($TIMgetfield)
    		->buildOauth($ta_url, $requestMethod)
    		->performRequest();
          $TIMdata = json_decode($TIMfollow_count, true);
          $TIMfollowers_count=$TIMdata[0]['user']['followers_count'];
	
	   $VODAfollow_count=$twitter->setGetfield($VODAgetfield)
    		->buildOauth($ta_url, $requestMethod)
    		->performRequest();
          $VODAdata = json_decode($VODAfollow_count, true);
          $VODAfollowers_count=$VODAdata[0]['user']['followers_count'];
	
	$response = "Su Twitter la pagina Vodafone ha " . number_format($VODAfollowers_count) . " followers\nmentre TIM ne ha " . number_format($TIMfollowers_count) . " però Vodafone è molto piu attiva come numero di tweet rispetto a TIM, circa 176K contro 125K. Clicca su Prosegui per altri insight";
	$parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["Prosegui"]], "one_time_keyboard": true}';
	
}
elseif($text=="prosegui"){
	$url = 'https://api.twitter.com/1.1/trends/place.json';
	$requestMethod = 'GET';
	$getfield = '?id=23424853';

	// Perform the request
	$twitter = new TwitterAPIExchange($settings);
	$twres="";
	
	$string = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);
	//$ciao =$twitter->setGetfield($getfield)
//->buildOauth($url, $requestMethod)
//->performRequest(),$assoc = TRUE);

	//$ciaostampa=json_encode($ciao, JSON_PRETTY_PRINT);
	//$response ="Stringa " . $ciaostampa . "\n";
	$response ="I primi tre trend di oggi in Italia sono:\n";
	$stampatrend=""; 
	$i=0;
	$j=$i+1;
	$stack = array();
	foreach($string[0]['trends'] as $trend) {
	     if ($i<=2){
		     $stack[$i] = $trend['name'];
		$stampatrend=$stampatrend . "Trend " . $j.": ". $trend['name'] . "\n";
		     $i=$i + 1;
		     
	     } else break;
	 }
	 $response=$response . $stampatrend . "Clicca su uno dei tre trend per trovarne dei tweet.";
	
	$keyboard = [
                'keyboard' => [[['text' => $stack[0], 'callback_data' => $stack[0]]], [['text' =>  $stack[1], 'callback_data' => $stack[1]]], [['text' =>  $stack[2], 'callback_data' => $stack[2]]]],
		'one-time-keyboard' => "true",
            ];
                $markup = json_encode($keyboard, true);
	$parameters = array('chat_id' => $chatId, "text" => $response,'reply_markup' => $markup);
	$parameters["method"] = "sendMessage";
	
}
elseif( substr($text, 0, 1) === "#")
{

	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$requestMethod = 'GET';
	$query=substr($text, 1) ;
	$query="%23". $query;
	$getfield = '?q=' . $query . '&count=3&lang=it';

	// Perform the request
	$twitter = new TwitterAPIExchange($settings);
	$twres="";
	$twres= $twitter->setGetfield($getfield)
		     ->buildOauth($url, $requestMethod)
		     ->performRequest();
	$tuitti= json_decode($twres);
	$rispostatuitti ="\n";
	foreach($tuitti->statuses as $t) {
	       $rispostatuitti= $rispostatuitti . "-Utente: " . $t->user->screen_name ." Tweet: " . $t->text . "\n\n";
	 }
	 $response ="Ecco alcuni tweet della parola " .$text . " sono:\n" . $rispostatuitti. "\n\n Chiudiamo il nostro percorso con una sentment analysis su una dei seguenti hot-topic:";
        $parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
	$parameters["reply_markup"] = '{ "keyboard": [["referendumitaly"],["vodafone"],["vodafonenetwork"]], "one_time_keyboard": true}';
}
elseif($text=="referendum italy" || $text=="vodafone" || $text=="donaldtrump" || $text=="matteorenzi")
{
	if ($text == "referendum italy"){
		$sentimentParola = "referendum%20italy"; }
	else {$sentimentParola = $text; }
	 $TwitterSentimentAnalysis = new TwitterSentimentAnalysis("e16265e25f400c107e217ca3ba3520c3","89UEqupgMeygd721YbHwZ1DS5","GxlPEzEYTzXItnpru7E7JZw1cjuA4BkiDBMXDULkIDs2TzhPYF","17757558-1jlw4zfBLHJ74o9K4Dn7ULEW8SboHdPFdLAsHjtt9","pNf3nwMeDjonL5yPWc5h05GgvkOmuSWjbgq0TkHwMQaFU");
    //Search Tweets parameters as described at https://dev.twitter.com/docs/api/1.1/get/search/tweets
    $twitterSearchParams=array(
        'q'=>$sentimentParola,
        'lang'=>'en',
        'count'=>3,
    );
	
    $results=$TwitterSentimentAnalysis->sentimentAnalysis($twitterSearchParams);
	$risultati="";
         foreach($results as $tweet) {
	     $risultati= $risultati . "- Sentiment: " . $tweet['sentiment'] . "\nTweet: " . $tweet['text'] .  "\n\n";
	 }
	 $response ="Gli ultimi " . sizeof($results) . " sono:\n" . $risultati;
        $parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
}
else{
	 $response ="Comando errato, Riprova";
        $parameters = array('chat_id' => $chatId, "text" => $response);
	$parameters["method"] = "sendMessage";
}

echo json_encode($parameters);
?>
