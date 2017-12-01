<?php
header("Content-Type: application/json; charset=UTF-8");
// 사용자 입력 문장을 파싱하는 처리부(핵심)

// 요청을 받아 저장 (user_key와 type, content로 구성)
$data = json_decode(file_get_contents('php://input'), true);
 
// 받은 요청에서 content 항목 설정
$text = $data["content"];
$user_key = $data["user_key"];
// $type = $data["type"];

//DB 연결
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
$query = "SELECT * from users where user_key like '$user_key'"; 
$result=mysqli_query($db, $query);
$row=mysqli_fetch_array($result);

// 모든 유저 메시지 DB에 기록하기
$dateTime = new DateTime("now", new DateTimeZone('Asia/Seoul'));
$time = $dateTime->format("Y-m-d H:i:s");
$qry_log = "INSERT INTO message(user_key,msg,time) VALUES ('$user_key', '$text', '$time')";
mysqli_query($db,$qry_log);

// 왓슨 대화 API로 보내서 처리하기
$workspace_id = '2f2619f4-b142-4e4c-a238-fb211e746dd9';
$release_date = '2017-10-24';
$username = '04ebe333-2a50-4e3a-9b09-b9a532d3b3ca';
$password = 'Z5HfbF6HKAtA';

// 신규회원이면
if($row['new'] == 'y' ){
  $gender = $row['user_gender'];
  $age = $row['user_age'];
  $height = $row['user_height'];
  $weight = $row['user_weight'];
  $activity = $row['user_activity'];
  // 아직 개인 정보 값이 비어 있으면 입력 받기
  if ($gender == '0'){
echo <<<EOD
    {
        "message": {
            "text": "하루 권장 열량 계산을 위해서 몇 가지만 여쭤볼게요.(아잉) \\r\\n성별, 나이, 키, 몸무게를 아래와 같이 써 주세요. \\r\\n(쉼표 꼭 붙여주세요(제발)) \\r\\n예) 여, 24, 165, 70"
        }
    }
EOD;
  $gen_query = "update users set user_gender = 1 where user_key = '$user_key'";
  mysqli_query($db,$gen_query);
  } else {  // 아직 성별 및 다른 값들 입력 전이면
      if($activity == '10'){
        if($gender == "M"){   // 남자면
          switch($text) {
            case "비활동적(운동 거의 안 함)" : 
              $activity = '1';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) + 5;
              $daily_cal *= 1.2;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            case "가벼운활동(가벼운 운동 - 주1~3회)" : 
              $activity = '2';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) + 5;
              $daily_cal *= 1.375;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            case "보통활동(보통 - 주3~5회)" :
              $activity = '3';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) + 5;
              $daily_cal *= 1.555;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;  
            case "적극적활동(적극적으로 운동함 - 매일)" :
              $activity = '4';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) + 5;
              $daily_cal *= 1.725;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            case "운동선수수준" :
              $activity = '5';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) + 5;
              $daily_cal *= 1.9;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            }  
echo <<<EOD
      {
        "message" : {
          "text" : "알려주셔서 감사합니다. (굿) \\r\\n 고객님의 하루 권장 열량은 $daily_cal kCal입니다. \\r\\n \\r\\n 이제 '고구마 1개, 바나나 1개'와 같이 먹은 음식을 적으면 제가 권장 열량에서 몇 칼로리 남았는지 알려 드려요~\\r\\n 그 외 다른 기능이 궁금하면 언제든 '도움말'이라고 적어주세요 (컴온)"
        }
      }
EOD;
              // 입력을 다 받고 나면 기존 회원으로 변경
              $usr_update = "update users set new = 'n' where user_key = '$user_key'";
              mysqli_query($db, $usr_update);
      } else {  // 여자면
          switch($text) {
            case "비활동적(운동 거의 안 함)" : 
              $activity = '1';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) - 161;
              $daily_cal *= 1.2;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            case "가벼운활동(가벼운 운동 - 주1~3회)" : 
              $activity = '2';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) - 161;
              $daily_cal *= 1.375;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            case "보통활동(보통 - 주3~5회)" :
              $activity = '3';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) - 161;
              $daily_cal *= 1.555;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            case "적극적활동(적극적으로 운동함 - 매일)" :
              $activity = '4';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) - 161;
              $daily_cal *= 1.725;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            case "운동선수수준" :
              $activity = '5';  
              $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
              mysqli_query($db,$activity_query);
              $daily_cal = ($weight* 10) + ($height * 6.25) - ($age * 5) - 161;
              $daily_cal *= 1.9;
              $daily_cal = round($daily_cal,2);
              $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
              mysqli_query($db,$cal_query);    
              break;
            }  
  echo <<<EOD
      {
        "message" : {
          "text" : "알려주셔서 감사합니다. (굿) \\r\\n 고객님의 하루 권장 열량은 $daily_cal kCal입니다. \\r\\n \\r\\n 이제 '고구마 1개, 바나나 1개'와 같이 먹은 음식을 적으면 제가 권장 열량에서 몇 칼로리 남았는지 알려 드려요~\\r\\n 그 외 다른 기능이 궁금하면 언제든 '도움말'이라고 적어주세요 (컴온)"
        }
      }
EOD;
              // 입력을 다 받고 나면 기존 회원으로 변경
              $usr_update = "update users set new = 'n' where user_key = '$user_key'";
              mysqli_query($db, $usr_update);
        }
      } else {  // activity 값이 10이 아니면 
          // 쉽표 기준 잘라서 배열로 저장하기
          $user_info = explode(",",$text);

          //잘못 입력한 경우
          if(count($user_info)<2){
echo <<<EOD
      {
        "message" : {
          "text" : "죄송하지만 '여, 24, 165, 70' 와 같이 쉼표 붙여서 다시 한 번 부탁드릴게요 (제발)"
        }
      }
EOD;
          } else {  // 제대로 입력한 경우
            for($a=0;$a<(count($user_info));$a++){   
            // 앞 뒤 공백 제거하기
            $user_info[$a] = trim($user_info[$a]);
          }
          if(strpos($user_info[0], "남") !== false){ 
              $gender = "M";
          } else {
            $gender = "F";
          }
          $age = preg_replace("/[^0-9]*/s", "", $user_info[1]);
          $height = floatval($user_info[2]);
          $weight = floatval($user_info[3]);
          
          $p_query = "update users set user_gender = '$gender', user_age = '$age', user_height = '$height', user_weight = '$weight' where user_key = '$user_key'";
          mysqli_query($db,$p_query);
echo <<<EOD
  {
        "message": { "text": "평소에 운동을 얼마나 하시는 편인가요?"},
        "keyboard": {
      "type": "buttons",
      "buttons": [
        "비활동적(운동 거의 안 함)",
        "가벼운활동(가벼운 운동 - 주1~3회)",
        "보통활동(보통 - 주3~5회)",
        "적극적활동(적극적으로 운동함 - 매일)",
        "운동선수수준"
      ]
    }
  }
EOD;
          $act_query = "update users set user_activity = '10' where user_key = '$user_key'";
          mysqli_query($db,$act_query);
          }
          
      }         
  } 
}

// 기존 회원이면
else {
  
  // 음식명 포함 여부를 보기 위해서 풀 텍스트 검색
  $f_query = "SELECT food_Name, MATCH (food_Name)
                AGAINST ('$text' IN NATURAL LANGUAGE MODE) AS score
                FROM foods WHERE MATCH (food_Name) AGAINST
                ('$text' IN NATURAL LANGUAGE MODE) LIMIT 5";
  $f_result=mysqli_query($db, $f_query);
  $f_row = mysqli_fetch_array($f_result);      

    // 음식명이 포함되지 않으면
  if(count($f_row)<1){

    if($row['eat_calorie']==0){ // 먹은 음식이 없으면
echo <<<EOD
      {
        "message" : {
          "text" : "아직 오늘 드신 게 없네요(힘듦) 먼저 드신 음식을 알려주세요~"
        }
      }
EOD;
    } else {  // 먹은 음식이 있으면
      //그래프를 만들기 위한 오늘의 칼로리, 영양소 값 조회
      $cal_rate = $row['eat_calorie'] / $row['recommended_calorie']*100;
      $carbo_rate = $row['eat_carbo'] * 4 / $row['eat_calorie'] * 100;
      $protein_rate = $row['eat_protein'] * 4 / $row['eat_calorie'] * 100;
      $fat_rate = $row['eat_fat'] * 9 / $row['eat_calorie'] * 100;
      $recommended_calorie = $row['recommended_calorie'];
      $eat_calorie = $row['eat_calorie'];
      $remain_calorie = $row['remain_calorie'];
      $meal_qry = "select * from meals where user_key='$user_key'";
      $meal_result = mysqli_query($db,$meal_qry);
      // 먹은 기록 미리 불러오기
      $eatlog_qry = "SELECT food_name, number, unit from meals where user_key = '$user_key' && time > CURDATE()";
      $eat_items = mysqli_query($db,$eatlog_qry);
      $eat_log = "";
      while ($row = mysqli_fetch_array($eat_items, MYSQLI_BOTH)){
        $eat_log .= $row['food_name']." ".$row['number']." ".$row['unit']." ";
      }
      // Make a request message for Watson API in json.
      $data['input']['text'] = $text;
      $data['context']['user_key'] = $user_key;
      $data['context']['cal_rate'] = $cal_rate;
      $data['context']['carbo_rate'] = $carbo_rate;
      $data['context']['protein_rate'] = $protein_rate;
      $data['context']['fat_rate'] = $fat_rate;
      $data['context']['recommended_calorie'] = $recommended_calorie;
      $data['context']['eat_calorie'] = $eat_calorie;
      $data['context']['remain_calorie'] = $remain_calorie;
      $data['context']['eat_log'] = $eat_log;
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
      $response = $raw['output']['text'][0];
      
      //DB 닫기
      mysqli_close($db);
      //응답하기
echo <<< EOD
    {
        "message": {
            "text": "$response"
        }
    }    
EOD;
    }
    
    
    
  } 

 
  // 음식명이 포함되면
  else {
//남은 텍스트(단위와 숫자)를 json 형태로 변환
$json_data = json_encode($text);      

$url = "http://220.230.115.39/parsing.php";
  
//남은 텍스트 parsing.php로 보내기
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/x-www-form-urlencoded', 'Content-Type: application/json; charset=UTF-8'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response =curl_exec($ch);
$decode = json_decode($response,1);
// print_r($decode);
//echo json_last_error_msg();

$f_name = $decode["f_name"];  
$f_number = $decode["f_number"];
$f_unit = $decode["f_unit"];
curl_close($ch);  

//쿼리에 공백이 들어가지 않게 트림해주기
for($a=0;$a<count($f_name);$a++){
  $f_name[$a] = trim($f_name[$a]);
}

$recommended_calorie = $row['recommended_calorie']; //권장 칼로리 미리 불러오기

// DB에서 검색하기
$cal = array();
$carbo = array();
$protein = array();
$fat = array();

// 음식 별 칼로리 결과값 불러오기
for($i=0;$i<count($f_name);$i++){
  $sql02 = "SELECT * FROM foods where food_Name like "."'$f_name[$i]'";
  $result02=mysqli_query($db, $sql02);
  $row02 = mysqli_fetch_array($result02);
  $cal[$i] = $f_number[$i]*$row02['food_Cal'];  //1인분 칼로리와 수량 곱하기
  // 음식 별 영양소 결과값 불러오기
  $carbo[$i] = $f_number[$i]*$row02['Carbohydrate'];  //1인분 탄수화물과 수량 곱하기
  $protein[$i] = $f_number[$i]*$row02['Protein'];  //1인분 단백질과 수량 곱하기
  $fat[$i] = $f_number[$i]*$row02['Fat'];  //1인분 지방과 수량 곱하기
  //음식 별 id 값 저장
  $f_id[$i] = $row02['food_id'];  
}

$cal_total = array_sum($cal); //총 칼로리 계산
$carbo_total = array_sum($carbo); //총 탄수화물 계산
$protein_total = array_sum($protein); //총 단백질 계산
$fat_total = array_sum($fat); //총 지방 계산

//응답 문장 만들기
$response = " ";
for($j=0;$j<count($f_name);$j++){
  $response .= "$f_name[$j] $f_number[$j] $f_unit[$j] $cal[$j]kcal ";  
}
$response = "기록되었습니다! 총 $cal_total 칼로리입니다! \\r\\n(".$response.")\\r\\n" ;

//meals DB에 시간과 함께 기록하기
for($k=0;$k<count($f_name);$k++){
  $meal_data = "INSERT INTO meals(user_key,food_name,number,unit,cal,time) VALUES ('$user_key', '$f_name[$k]', '$f_number[$k]', '$f_unit[$k]','$cal[$k]','$time')";
  mysqli_query($db, $meal_data);
}

//누적 칼로리, 영양소 user DB에 기록하기
$add_Cal = "update users set eat_calorie = eat_calorie+$cal_total where user_key = '$user_key'";
mysqli_query($db, $add_Cal);
$add_Carbo = "update users set eat_carbo = eat_carbo+$carbo_total where user_key = '$user_key'";
mysqli_query($db, $add_Carbo);
$add_Protein = "update users set eat_protein = eat_protein+$protein_total where user_key = '$user_key'";
mysqli_query($db, $add_Protein);
$add_Fat = "update users set eat_fat = eat_fat+$fat_total where user_key = '$user_key'";
mysqli_query($db, $add_Fat);

//남은 칼로리 계산해서 DB에 업로드하기
$re_query = "select * from users where user_key='$user_key'";
$row04 = mysqli_fetch_array(mysqli_query($db,$re_query));
$remain_calorie = $recommended_calorie-$row04['eat_calorie'];
$remain = "update users set remain_calorie = $remain_calorie where user_key = '$user_key'";
mysqli_query($db, $remain);

// 열량 경고
if($remain_calorie<0){
  $remain_calorie = -$remain_calorie;
  $response = $response." $remain_calorie 칼로리 초과하셨네요 (깜짝) \\r\\n오늘은 이제 그만 드시는게 좋겠어요 (정색)";
} else {
  $response = $response." 오늘 $remain_calorie 칼로리 남으셨네요 (씨익)";  
}

mysqli_close($db);

//응답하기
echo <<< EOD
    {
        "message": {
            "text": "$response"
        }
    }    
EOD;

  }

}

?>