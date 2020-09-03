<?php
function httpPost($url, $data)
{
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
	curl_setopt($ch,CURLOPT_TIMEOUT, 20);
	$response = curl_exec($ch);
	curl_close ($ch);
	return $response;
}

echo httpPost('http://localhost/document/action', 'ac=createFile&document_id=');
?>

