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
// $percent = $row['eat_calorie'] / $row['recommended_calorie']*100;
$carbo_rate = $row['eat_carbo'] * 4 / $row['eat_calorie'] * 100;
$protein_rate = $row['eat_protein'] * 4 / $row['eat_calorie'] * 100;
$fat_rate = $row['eat_fat'] * 9 / $row['eat_calorie'] * 100;

// 성별 버튼 처리
if($text == "남자" ){  
$gender_query = "update users set user_gender = 'M' where user_key = '$user_key'";  //성별 저장  
mysqli_query($db,$gender_query);
echo <<<EOD
  {
      "message": {
          "text": "초면에 죄송합니다만 몇 살이세요? (36살 이렇게 꼭 적어주세요)"
      }
  }
EOD;
} 

else if($text == "여자" ){  
$gender_query = "update users set user_gender = 'F' where user_key = '$user_key'";  //성별 저장  
mysqli_query($db,$gender_query);
echo <<<EOD
  {
      "message": {
          "text": "초면에 죄송합니다만 몇 살이세요? (36살 이렇게 꼭 적어주세요)"
      }
  }
EOD;
}

else if($row['user_gender'] == "0"){
echo <<<EOD
  {
        "message": { "text": "하루 권장 열량 계산을 위해서 몇 가지만 여쭤볼게요. 성별이 어떻게 되시나요?"},
        "keyboard": {
      "type": "buttons",
      "buttons": [
        "남자",
        "여자"
      ]
    }
  }
EOD;
}

//나이
else if(strpos($text, "살") !== false){
    $age = preg_replace("/[^0-9]*/s", "", $text); //숫자만 뽑아내기 
    $age_query = "update users set user_age = '$age' where user_key = '$user_key'";  //나이 저장
    mysqli_query($db,$age_query);
echo <<<EOD
  {
    "message" : {
      "text" : "아 넵, 키는 몇 센치세요? 예) 175센치"
    }
  }
EOD;
}

//키
else if(strpos($text, "센치") !== false){
    $height = preg_replace("/[^0-9]*/s", "", $text); 
    $height_query = "update users set user_height = '$height' where user_key = '$user_key'";  //키 저장
    mysqli_query($db,$height_query);
echo <<<EOD
  {
    "message" : {
      "text" : "혹시 몸무게는 몇 키로그람이신가요? 예) 72키로"
    }
  }
EOD;
  }
//몸무게
else if(strpos($text, "키로") !== false){
    $weight = preg_replace("/[^0-9]*/s", "", $text);
    $weight_query = "update users set user_weight = '$weight' where user_key = '$user_key'";  //몸무게 저장
    mysqli_query($db,$weight_query);
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
  }

//활동 수준
else if (strpos($text, "비활동적") !== false){
    $activity = 1;  
    $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
    mysqli_query($db,$activity_query);

    $daily_cal = ($row['user_weight'] * 10) + ($row['user_height'] * 6.25) - ($row['user_age'] * 5) + 5;
    $daily_cal *= 1.2;
    $cal_query = "update users set recommended_calorie = '$daily_cal' where user_key='$user_key'";
    mysqli_query($db,$cal_query);    
echo <<<EOD
  {
    "message" : {
      "text" : "알려주셔서 감사합니다. ^^ \\r\\n 고객님의 하루 권장 열량은 $daily_cal kCal입니다. \\r\\n 이제 드신 음식을 적어주시면 제가 권장 열량에서 얼마나 남았는지 알려 드려요~\\r\\n 언제든지 다이어트봇의 기능이 궁금하시면 채팅창에 도움말이라고 적어주세요"
    }
  }
EOD;
}


// '먹은 음식 적기' 버튼 처리
else if($text == "먹은 음식 적기" ){  
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
    "text": "http://220.230.115.39/chart.php?carbo_rate=$carbo_rate&protein_rate=$protein_rate&fat_rate=$fat_rate"
    }
  }    
EOD;
}

// '도움말' 버튼 처리
else if( $text == "도움말"){
echo <<< EOD
    {
        "message": {
            "text": "이렇게 말해 보세요~! 고구마 1개, 바나나 2개 or 오늘 통계 알려줘"
        }
    }    
EOD;
}

// '안녕'이란 단어가 포함되었을때 처리
else if( strpos($text, "안녕") !== false ){
echo <<< EOD
    {
        "message": {
            "text": "안녕하세요 저는 먹은 음식을 적으면 칼로리를 알려주는 다이어트 봇입니다. ^^"
        }
    }    
EOD;
}

// 그밖의 문장일때
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
$response = "기록되었습니다! 총 $cal_total 칼로리입니다!".$response ;

$timestamp = date("Y-m-d H:i:s"); //현재 시각 저장하기
//meals DB에 기록하기
for($k=0;$k<count($f_name);$k++){
  $meal_data = "INSERT INTO meals(user_key,food_id,food_name,number,unit,cal,time) VALUES ('$user_key', '$f_id[$k]', '$f_name[$k]', '$f_number[$k]', '$f_unit[$k]','$cal[$k]','$timestamp')";
  $record=mysqli_query($db02, $meal_data);
}

//user DB에 기록하기
$add_Cal = "update users set eat_calorie = eat_calorie+$cal_total where user_key = '$user_key'";
mysqli_query($db02, $add_Cal);
$add_Carbo = "update users set eat_carbo = eat_carbo+$carbo_total where user_key = '$user_key'";
mysqli_query($db02, $add_Carbo);
$add_Protein = "update users set eat_protein = eat_protein+$protein_total where user_key = '$user_key'";
mysqli_query($db02, $add_Protein);
$add_Fat = "update users set eat_fat = eat_fat+$fat_total where user_key = '$user_key'";
mysqli_query($db02, $add_Fat);

//남은 칼로리 계산하기
$remain_calorie = $row['recommended_calorie']-$row['eat_calorie'];
$remain = "update users set remain_calorie = $remain_calorie where user_key = '$user_key'";
mysqli_query($db02, $remain);

$response = $response."오늘 $remain_calorie 칼로리 남으셨네요 (권장 열량 : $recommended_calorie)";

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

?>