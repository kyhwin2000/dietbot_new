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
            "text": "하루 권장 열량 계산을 위해서 몇 가지만 여쭤볼게요. \\r\\n 성별, 나이, 키, 몸무게를 아래와 같이 써 주세요. (쉼표 꼭 붙여주세요) \\r\\n 예) 여, 24, 165, 70"
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
          "text" : "알려주셔서 감사합니다. ^^ \\r\\n 고객님의 하루 권장 열량은 $daily_cal kCal입니다. \\r\\n 이제 드신 음식을 적어주시면 제가 권장 열량에서 얼마나 남았는지 알려 드려요~\\r\\n 언제든지 다이어트봇의 기능이 궁금하시면 채팅창에 도움말이라고 적어주세요"
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
          "text" : "알려주셔서 감사합니다. :-) \\r\\n 고객님의 하루 권장 열량은 $daily_cal kCal입니다. \\r\\n 이제 드신 음식을 적어주시면 제가 권장 열량에서 얼마나 남았는지 알려 드려요~\\r\\n 언제든지 다이어트봇의 기능이 궁금하시면 채팅창에 도움말이라고 적어주세요"
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

// 기존 회원이면
else {
  //그래프를 만들기 위한 오늘의 칼로리, 영양소 값 조회
  $cal_rate = $row['eat_calorie'] / $row['recommended_calorie']*100;
  $carbo_rate = $row['eat_carbo'] * 4 / $row['eat_calorie'] * 100;
  $protein_rate = $row['eat_protein'] * 4 / $row['eat_calorie'] * 100;
  $fat_rate = $row['eat_fat'] * 9 / $row['eat_calorie'] * 100;

  // 음식명 포함 여부를 보기 위해서 풀 텍스트 검색
  $f_query = "SELECT food_Name, MATCH (food_Name)
                AGAINST ('$text' IN NATURAL LANGUAGE MODE) AS score
                FROM foodCal WHERE MATCH (food_Name) AGAINST
                ('$text' IN NATURAL LANGUAGE MODE) LIMIT 5";
  $f_result=mysqli_query($db, $f_query);
  $f_row = mysqli_fetch_array($f_result);      

  // 음식명이 포함되지 않으면
  if(count($f_row)<1){

// '먹은 음식 적기' 버튼 처리
if($text == "먹은 음식 적기" ){  
echo <<<EOD
  {
      "message": {
          "text": "무엇을 드셨나요? 고구마 1개, 바나나 2개와 같이 적어주세요!"
      }
  }
EOD;
} 
 
// '오늘의 통계' 버튼 처리
else if( strpos($text, "통계") !== false ){

echo <<< EOD
  {
  "message": {
    "text": "http://220.230.115.39/chart.php?cal_rate=$cal_rate&carbo_rate=$carbo_rate&protein_rate=$protein_rate&fat_rate=$fat_rate"
    }
  }    
EOD;
}

// '도움말' 버튼 처리
else if( $text == "도움말"){
echo <<< EOD
    {
        "message": {
            "text": "이렇게 말해 보세요~! 고구마 1개, 바나나 2개 or 오늘 통계 or 버튼"
        }
    }    
EOD;
}

// '버튼' 누르면 키보드 돌아오기
else if($text == "버튼"){
echo <<< EOD
{
  "message": {
    "text": "아래 버튼을 눌러 보세요"
  },
  "keyboard": {
      "type": "buttons",
      "buttons": ["먹은 음식 적기", "통계", "오늘 뭐 먹었지" , "도움말"]
  }
}
EOD;
}

// '오늘 뭐 먹었지' 처리하기
else if($text == "오늘 뭐 먹었지"){
$qry_today = "select * from meals where time > CURRENT_DATE() && user_key='$user_key'";
$result = mysqli_query($db,$qry_today);
while ($meal_today = mysqli_fetch_array($result)){
  $f_nam[] = $meal_today['food_name'];
  $f_num[] = $meal_today['number'];
  $f_uni[] = $meal_today['unit']; 
}
$response = "네, 오늘";
for($i=0;$i<count($f_nam);$i++){
  $response = $response." $f_nam[$i] $f_num[$i] $f_uni[$i]";
}
$response = $response."드셨어요";

echo <<< EOD
    {
        "message": {
            "text": "$response"
        }
    }    
EOD;

}
// 나머지 모든 예외 케이스들 
else {
echo <<< EOD
    {
        "message": {
            "text": "죄송해요. 제가 잘 모르겠네요 :-("
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

$hostname02 = 'localhost';
$username02 = 'root';
$password02 = 'Dntjd13!';
$dbname02 = 'dietbot';

$db02 = new mysqli($hostname02,$username02,$password02,$dbname02);
if ( $db02->connect_error ) exit('접속 실패 : '.$db02->connect_error);
mysqli_query($db02,"set names utf8");   

// 음식 별 칼로리 결과값 불러오기
for($i=0;$i<count($f_name);$i++){
  $sql = "SELECT food_id, food_Cal FROM foodCal where food_Name like "."'$f_name[$i]'";
  $result02=mysqli_query($db02, $sql);
  $row02 = mysqli_fetch_array($result02);
  $cal[$i] = $f_number[$i]*$row02['food_Cal'];  //1인분 칼로리와 수량 곱하기
  $f_id[$i] = $row02['food_id'];  //음식 별 id 값 저장
}

$cal_total = array_sum($cal); //총 칼로리 계산

// 음식 별 영양소 결과값 불러오기
for($h=0;$h<count($f_name);$h++){
  $sql_h = "SELECT * FROM foodNutrient where food_Name like "."'$f_name[$h]'";
  $result03=mysqli_query($db02, $sql_h);
  $row03 = mysqli_fetch_array($result03);
  $carbo[$h] = $f_number[$h]*$row03['Carbohydrate'];  //1인분 탄수화물과 수량 곱하기
  $protein[$h] = $f_number[$h]*$row03['Protein'];  //1인분 단백질과 수량 곱하기
  $fat[$h] = $f_number[$h]*$row03['Fat'];  //1인분 지방과 수량 곱하기
}

$carbo_total = array_sum($carbo); //총 탄수화물 계산
$protein_total = array_sum($protein); //총 단백질 계산
$fat_total = array_sum($fat); //총 지방 계산

//응답 문장 만들기
$response = " ";
for($j=0;$j<count($f_name);$j++){
  $response .= " $f_name[$j] $f_number[$j] $f_unit[$j] $cal[$j]kcal ";  
}
$response = "기록되었습니다! 총 $cal_total 칼로리입니다! (".$response.")" ;

$dateTime = new DateTime("now", new DateTimeZone('Asia/Seoul'));
$time = $dateTime->format("Y-m-d H:i:s");

//meals DB에 시간과 함께 기록하기
for($k=0;$k<count($f_name);$k++){
  $meal_data = "INSERT INTO meals(user_key,food_id,food_name,number,unit,cal,time) VALUES ('$user_key', '$f_id[$k]', '$f_name[$k]', '$f_number[$k]', '$f_unit[$k]','$cal[$k]','$time')";
  $record=mysqli_query($db02, $meal_data);
}

//누적 칼로리, 영양소 user DB에 기록하기
$add_Cal = "update users set eat_calorie = eat_calorie+$cal_total where user_key = '$user_key'";
mysqli_query($db02, $add_Cal);
$add_Carbo = "update users set eat_carbo = eat_carbo+$carbo_total where user_key = '$user_key'";
mysqli_query($db02, $add_Carbo);
$add_Protein = "update users set eat_protein = eat_protein+$protein_total where user_key = '$user_key'";
mysqli_query($db02, $add_Protein);
$add_Fat = "update users set eat_fat = eat_fat+$fat_total where user_key = '$user_key'";
mysqli_query($db02, $add_Fat);

//남은 칼로리 계산해서 DB에 업로드하기
$re_query = "select * from users where user_key='$user_key'";
$row04 = mysqli_fetch_array(mysqli_query($db02,$re_query));
$remain_calorie = $recommended_calorie-$row04['eat_calorie'];
$remain = "update users set remain_calorie = $remain_calorie where user_key = '$user_key'";
mysqli_query($db02, $remain);

// 열량 경고
if($remain_calorie<0){
  $remain_calorie = -$remain_calorie;
  $response = $response." $remain_calorie 초과하셨네요 ㅜ.ㅜ 살 빼려면 이제 그만 드시는게 좋겠어요~";
} else {
  $response = $response." 오늘 $remain_calorie 칼로리 남으셨네요 ^o^";  
}

mysqli_close($db02);

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