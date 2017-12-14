<?php
$message = "귤";
// $cal_rate = 40;
// $carbo_rate = 30;
// $protein_rate = 20;
// $fat_rate = 10;
$user_key = "UYcMwWLfIPlw";

$f_item = array();
$f_name = array();
$f_number = array();
$f_unit = array();

//DB 연결
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';
$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);

$raw = send_watson($message);
$num_entity = count($raw['entities']);
echo $num_entity;

/*
$eatlog_qry = "SELECT food_name, number, unit from meals where user_key = '$user_key' && time > CURDATE()";
$eat_items = mysqli_query($db,$eatlog_qry);

$eat_text = "";
while ($row = mysqli_fetch_array($eat_items, MYSQLI_BOTH)){
  $eat_text .= $row['food_name']." ".$row['number']." ".$row['unit']." ";
}

echo $eat_text;
echo "<BR>";
*/
// 

$intent = check_intent($message);
echo $intent;

function send_watson($text){
  // 왓슨 대화 API로 보내서 처리하기
  $workspace_id = '2f2619f4-b142-4e4c-a238-fb211e746dd9';
  $release_date = '2017-10-24';
  $username = '04ebe333-2a50-4e3a-9b09-b9a532d3b3ca';
  $password = 'Z5HfbF6HKAtA';

  // 인텐트 파악을 위해 왓슨에 던지기
  $data['input']['text'] = $text;
  $data['context']['user_key'] = $user_key;
  $data['alternate_intents'] = false;
  $json = json_encode($data, JSON_UNESCAPED_UNICODE);
  // Post the json to the Watson API via cURL.
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, 'https://watson-api-explorer.mybluemix.net/conversation/api/v1/workspaces/'.$workspace_id.'/message?version='.$release_date);
  curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
  curl_setopt($ch, CURLOPT_POST, true );
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
  $result = trim( curl_exec( $ch ) );
  curl_close($ch);
  // Responce the result.
  $raw = json_decode($result, true);
  // $intent = $raw['intents'][0]['intent'];
  return $raw;
}

/*
$max = count($raw['entities']);

for($i=0;$i<$max;$i++){
	if($raw['entities'][$i]['entity']=="음식"){
		// echo "wow <BR>";
		array_push($f_name,$raw['entities'][$i]['value']);
	}
	if($raw['entities'][$i]['entity']=="단위"){
		// echo "wow <BR>";
		array_push($f_unit,$raw['entities'][$i]['value']);
	}
	if($raw['entities'][$i]['entity']=="sys-number"){
		// echo "wow <BR>";
		array_push($f_number,$raw['entities'][$i]['value']);
	}

}

print_r($f_name);
echo "<BR>";
print_r($f_unit);
echo "<BR>";
print_r($f_number);
echo "<BR>";

// $num = 0;

// for($i==0;$i<$max;$i++){
// 	if($raw['entities'][$i]["음식"]!==""){
// 		$num++;
// 	}
// }

// echo $num;
// echo $raw['entities'][0]['value'];
// echo "<BR>";
// echo $raw['entities'][2]['value'];
// // echo "<BR>";
// echo $raw['entities'][1]['value'];
// echo "<BR>";
// echo $raw['entities'][3]['value'];
// echo "<BR>";
// echo $raw['entities'][4]['value'];
// echo "<BR>";
// echo $raw['entities'][5]['value'];

// echo "<BR>";
// echo $f_name[1];



// $fooname = $raw['entities'][0]['value'];
// $cal_check = "SELECT * FROM foods where food_Name like "."'$fooname'";
// $result_cal = mysqli_query($db,$cal_check);
// $row_cal = mysqli_fetch_array($result_cal);
// $fooCal = $row_cal['food_Cal'];

// echo $fooCal;

// print_r($raw);
// $response = $raw['output']['text'][0];
// echo $response;

/*
// Responce the result.
$raw = json_decode($result, true);
$response = $raw['output']['text'][0];


    
//응답하기
echo <<< EOD
    {
        "message": {
            "text": "$response"
        }
    }    
EOD;
 */


?>
