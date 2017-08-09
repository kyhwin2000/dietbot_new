<?php
	
	
				 
	//curl 활용 남은 텍스트 units.php로 보내기
	$text = "한 잔 두 개";
	$json_data = json_encode($text);
	//echo($json_data);
			$url = "http://220.230.115.39/units.php";
	  			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/x-www-form-urlencoded', 'Content-Type: application/json; charset=UTF-8'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$response =curl_exec($ch);
		if (curl_errno($ch)) { 
  			 print curl_error($ch); 
		} 
		print_r($response);

		//$decode = json_decode($response,true);
		//echo $decode['string'];

		curl_close($ch);
	
/*
	$rest = "한 잔 두 개";
    $url = "http://220.230.115.39/units.php?";
    $url.= $rest;
    echo($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    var_dump($response);
    curl_close($ch);
*/  
				
?>