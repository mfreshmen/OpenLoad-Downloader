<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "./vendor/autoload.php";
use JonnyW\PhantomJs\Client;

$video = $_GET['id'];
if($video == ''){
	echo "Where is the media ID?"; //https://openload.co/embed/7zLUwKrlQqCk  (The ID is "7zLUwKrlQqCk" in this case)
	exit();
}else{
	$client = Client::getInstance();

	if(strpos($video, '.') !== False){
		$video = explode('.', $video)[0];
	}

	$request = $client->getMessageFactory()->createRequest("https://oload.tv/embed/$video", 'GET');
	$response = $client->getMessageFactory()->createResponse();
	$client->send($request, $response);

	if($response->getStatus() === 200) {
		$openload = $response->getContent();
		echo $openload;
	   	exit();
	}else{
		echo json_encode(array('error' => $response->getStatus(), 'msg' => 'Server error'));
		exit();
	}
}
