<?php 
function send_firebase($device_token, $content)
{
	$url = 'https://fcm.googleapis.com/fcm/send';
	$field = array(
		'registration_ids'=>$device_token,
		'data'=>$content
	);

	$headers = array(
		/* Key didapat dari project di website firebase */
		'Authorization:key = AAAArE77Uoo:APA91bGaJXaslRSPQ9rJBuB3KxOQ6QNDRLEaefaVyC4Jsn4IbF7Zc81cDv9C9PtHiLJMt3vHZCJZ4WRfmRFmLYaKJPURksgF6AkZ_0NazOp6WnnojOT0oGCk_HWyyobq6EhZ0QJHXuIi',
		'Content-Type: application/json'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($field));

	$result = curl_exec($ch);
	if($result === FALSE){
		die('Curl failed: '. curl_error($ch));
	}

	curl_close($ch);
	return $result;
}
?>