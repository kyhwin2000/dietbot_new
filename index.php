<?php
$message = "남자";
// $cal_rate = 40;
// $carbo_rate = 30;
// $protein_rate = 20;
// $fat_rate = 10;
$user_key = "UYcMwWLfIPlw";

//DB 연결
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

/*
$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);

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
// Unique identifier of the workspace.
$workspace_id = '2f2619f4-b142-4e4c-a238-fb211e746dd9';
// Release date of the API version in YYYY-MM-DD format.
$release_date = '2017-10-24';
// Username of a user for the service credentials.
$username = '04ebe333-2a50-4e3a-9b09-b9a532d3b3ca';
// Password of a user for the service credentials.
$password = 'Z5HfbF6HKAtA';

// Make a request message for Watson API in json.
$data['input']['text'] = $message;
// $data['context']['cal_rate'] = $cal_rate;
// $data['context']['carbo_rate'] = $carbo_rate;
// $data['context']['protein_rate'] = $protein_rate;
// $data['context']['fat_rate'] = $fat_rate;
// $data['context']['eat_log'] = $eat_text;
$data['alternate_intents'] = false;
$json = json_encode($data, JSON_UNESCAPED_UNICODE);

// echo $json;
// echo "<BR> <BR>";


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
// echo json_encode($result, JSON_UNESCAPED_UNICODE);
print_r($result);
// $raw = json_decode($result, true);

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
