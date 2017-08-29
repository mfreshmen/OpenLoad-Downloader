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
	$request->addHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X) AppleWebKit/534.34 (KHTML, like Gecko) PhantomJS/1.9.2 Safari/534.34');
	
	$response = $client->getMessageFactory()->createResponse();
	$client->send($request, $response);

	if($response->getStatus() === 200) {
		$openload = $response->getContent();
		if(strpos($openload, 'We are sorry!') !== False){
			echo json_encode(array('error' => '404', 'msg' => 'File not found'));
			exit();
		}
	    	$openload = explode('<span id="streamurl">', $openload)[1];
	    	$file = 'https://oload.tv/stream/'.explode('</span>', $openload)[0];
    		$headers = get_headers($file,1);
    		
    		//Final Args
    		$filename = explode('?', end(explode('/',$headers['Location'])))[0];
	   	$file = explode('?', $headers['Location'])[0];
	   	$size = $headers['Content-Length'];
	   	
	   	//Download Code
	   	set_time_limit(0);
	   	header('Content-Type: video/mp4');
		header('Content-Length: '.$size);
	   	
	   	$f = fopen($file, "rb");
	   	while (!feof($f)) {
		 	echo fread($f, 8*1024);
		   	flush();
		   	ob_flush();
	   	}
	   	exit();
	}else{
		echo json_encode(array('error' => $response->getStatus(), 'msg' => 'Server error'));
		exit();
	}
}
