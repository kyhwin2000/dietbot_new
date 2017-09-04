<?php
header("Content-Type: application/json; charset=UTF-8");
// 사용자 입력 문장을 파싱하는 처리부(핵심)

// 요청을 받아 저장
$data = json_decode(file_get_contents('php://input'), true);
 
// 받은 요청에서 content 항목 설정
$text = $data["content"];
$user_key = $data["user_key"];

//유저 키가 이미 존재하는지 user db에서 검색
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

// 신규 회원이면 설문부터 진행
if(count($row)<1){
$usr_query = "INSERT INTO users(user_key) VALUES ('$user_key')";  //먼저 유저 DB에 키값 집어넣기
mysqli_query($db,$usr_query);
echo <<<EOD
  {
        "message": { "text": "하루 권장 열량 계산을 위해서 몇 가지만 여쭤볼게요. 성별이 어떻게 되시나요? (남자 또는 여자로 적어주세요) "},
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
// 기존 회원인 경우
else {
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
  else if( strpos($text, "안녕") !== false )
  {
echo <<< EOD
    {
        "message": {
            "text": "안녕하세요 저는 먹은 음식을 적으면 칼로리를 알려주는 다이어트 봇입니다. ^^"
        }
    }    
EOD;
  }

// 나이 
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
echo <<<EOD
  {
    "message" : {
      "text" : "수고하셨습니다!"
    }
  }
EOD;
}


// 성별
else if($text == "남자" or "여자") {
  if ($text == "남자"){
    $gender = "M";
  } else {
    $gender = "F";
  }
  $gender_query = "update users set user_gender = '$gender' where user_key = '$user_key'";  //성별 저장  
  mysqli_query($db,$gender_query);
echo <<<EOD
  {
    "message" : {
      "text" : "넵. 초면에 죄송합니다만 몇 살이세요? 예) 26살"
    }
  }
EOD;
}

else if (strpos($text, "가벼운활동") !== false){
    $activity = 2;  
    $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
    mysqli_query($db,$activity_query);
echo <<<EOD
  {
    "message" : {
      "text" : "수고하셨습니다!"
    }
  }
EOD;
}

else if (strpos($text, "보통활동") !== false){
    $activity = 3;  
    $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
    mysqli_query($db,$activity_query);
echo <<<EOD
  {
    "message" : {
      "text" : "수고하셨습니다!"
    }
  }
EOD;    

}

else if (strpos($text, "적극적활동") !== false){
    $activity = 4;  
    $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
    mysqli_query($db,$activity_query);
echo <<<EOD
  {
    "message" : {
      "text" : "수고하셨습니다!"
    }
  }
EOD;    

}
else if (strpos($text, "운동선수") !== false){
    $activity = 5;  
    $activity_query = "update users set user_activity = '$activity' where user_key='$user_key'";
    mysqli_query($db,$activity_query);
echo <<<EOD
  {
    "message" : {
      "text" : "수고하셨습니다!"
    }
  }
EOD;    

} 


  // 그밖의 문장일때 
  else
  { 
    global $f_item, $f_name, $f_number, $f_unit;

    $f_item = array();
    $f_name = array();
    $f_number = array();
    $f_unit = array();
    $f_id = array();

    // 공백과 쉼표를 제외한 나머지 특수 문자 제거하기
    $text = str_replace( "<br/>","",$text); //줄바꿈
    $text = str_replace( "." ,"",$text); //마침표
    $text = str_replace( "!" ,"",$text); //느낌표
    $text = str_replace( "?" ,"",$text); //물음표
    $text = str_replace( "$#34" ,"",$text); //큰 따옴표
    $text = str_replace( "&#39" ,"",$text); //작은 따옴표
    $text = str_replace( "+" ,"",$text); //더하기
    $text = str_replace( ":" ,"",$text); //콜론
    $text = str_replace( ";" ,"",$text); //세미콜론
    $text = str_replace( "^^" ,"",$text); //웃음표시
    $text = str_replace( "~" ,"",$text); //물결표시

    // &인 경우 쉼표로 바꿔주기
    $text = str_replace( "&" ,",",$text);

    // 전체 스트링에 숫자가 포함되었는지 검사  
    for($f=0;$f<strlen($text);$f++){
      $char = mb_substr($text,$f,1);
      if(is_numeric($char)){
        $case = "numeric";
        break;
      }
      $case = "string";
    }


    switch ($case) {
    // 스트링에 숫자가 있는 경우
      case "numeric"  : 
          // echo "숫자가 있는 케이스!<BR>";
        // 쉼표가 포함되어 있는 경우
        if (strlen(stristr($text,","))>0){
          // 쉽표 기준 잘라서 배열로 저장하기
            $f_item = explode(",",$text);
            for($a=0;$a<(count($f_item));$a++){   
              // 아이템 앞 뒤 공백 제거하기
              $f_item[$a] = trim($f_item[$a]);
              // 문자열 중 숫자만 추출하여 섭취량으로 저장
              $f_number[$a] = preg_replace("/[^0-9]*/s", "", $f_item[$a]);
              // 문자열 중 숫자 나오기 전까지 잘라내서 음식명으로 저장
              $f_name[$a] = substr($f_item[$a],0,strrpos($f_item[$a],$f_number[$a]));
              // 문자열 중 숫자 다음부터 잘라내서 단위로 저장
              $f_unit[$a] = strrchr($f_item[$a],$f_number[$a]);
              $f_unit[$a] = mb_substr($f_unit[$a],1,(strlen(f_unit[$a]-1)),'utf-8');
              
            }
        }
        // 쉼표가 포함되지 않은 경우
        else {
          //더 이상 스트링 내에 숫자가 없을 때까지 반복
          for($k=0;$k<strlen($text);$k++){
            
            //문자열 중 숫자 나오기 전까지 잘라내서 음식명으로 저장
            for($i=0;$i<strlen($text);$i++){
              $char = mb_substr($text,$i,1);
              if(is_numeric($char)){
                array_push($f_name,trim(mb_substr($text,0,($i-1))));
                $text = mb_substr($text,$i,strlen($text));
                break;
              }
            }
            //나머지 문자열 중 첫번째 공백이 나오기 전까지 잘라내기
            for($j=0;$j<strlen($text);$j++){
              $char = mb_substr($text,$j,1);
              if($char == " "){
                $cache = mb_substr($text,0,$j);
                // 숫자만 추출해서 섭취량 배열에 집어넣기
                array_push($f_number,preg_replace("/[^0-9]*/s", "",($cache)));    
                $text = mb_substr($text,$j,strlen($text));
                break;
              }
            }
            
            if(is_numeric(mb_substr($text,0,1))){
              array_push($f_number,mb_substr($text,0,1));
              break;  
            }
          }
        }

       break;


      // 스트링에 숫자가 없는 경우
      case "string"  : 
            // echo "숫자가 없는 케이스!<BR>";

            // DB에 연결
            $hostname = 'localhost';
            $username = 'root';
            $password = 'Dntjd13!';
            $dbname = 'dietbot';

            $db = new mysqli($hostname,$username,$password,$dbname);
            if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
            // 유사한 음식명이 있는지 풀텍스트 검색
            mysqli_query($db,"set names utf8");   
            $query = "SELECT food_Name, MATCH (food_Name)
                    AGAINST ('$text' IN NATURAL LANGUAGE MODE) AS score
                    FROM foodCal WHERE MATCH (food_Name) AGAINST
                    ('$text' IN NATURAL LANGUAGE MODE) LIMIT 5";
            $result=mysqli_query($db, $query);
            // 검색 결과가 원문과 동일한 문자열이 있으면 음식명으로 저장
            while($data = mysqli_fetch_array($result)){
              if(strpos($text,$data['food_Name'])!==false){
                array_push($f_name,$data['food_Name']); //음식명 저장
              }
              $text = str_replace($data['food_Name'],"",$text); //음식명 삭제해서 단위랑 숫자만 남기기
            } 

            $mysqli->close($db);  //DB 접속 끊기

            //남은 텍스트(단위와 숫자)를 json 형태로 변환
            $json_data = json_encode($text);      

            $url = "http://220.230.115.39/units.php";
              
            //남은 텍스트 units.php로 보내기
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
            
            $f_number = $decode["f_number"];
            $f_unit = $decode["f_unit"];
            curl_close($ch);  

                   break;

      default    : 
              // print "그냥 디폴트<br />\n";
                   break;
      }
  }

} 






  


//쿼리에 공백이 들어가지 않게 트림해주기
for($a=0;$a<count($f_name);$a++){
  $f_name[$a] = trim($f_name[$a]);
}


// DB에서 검색하기
$cal = array();
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

$db = new mysqli($hostname,$username,$password,$dbname);
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
mysqli_query($db,"set names utf8");   

// 음식 별 칼로리 결과값 불러오기
for($i=0;$i<count($f_name);$i++){
  $sql = "SELECT food_id, food_Cal FROM foodCal where food_Name like "."'$f_name[$i]'";
  $result=mysqli_query($db, $sql);
  $row = mysqli_fetch_array($result);
  $cal[$i] = $f_number[$i]*$row['food_Cal'];  //1인분 칼로리와 수량 곱하기
  $f_id[$i] = $row['food_id'];  //음식 별 id 값 저장
}

$cal_total = array_sum($cal); //총 칼로리 계산

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
  $record=mysqli_query($db, $meal_data);
}

mysqli_close($db);
echo <<< EOD
    {
        "message": {
            "text": "$response"
        }
    }    
EOD;

// $mysqli->close($db);  //DB 접속 끊기   



?>