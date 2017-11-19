<?php
$message = "너 뭐할 수 있어?";

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
if(isset($_POST['context']) && $_POST['context']){
  $data['context'] = json_decode($_POST['context'], JSON_UNESCAPED_UNICODE);
}
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
// echo json_encode($result, JSON_UNESCAPED_UNICODE);
// print_r($result);
$raw = json_decode($result, true);

// print_r($raw);
$response = $raw['output']['text'][0];
echo $response;


?>
