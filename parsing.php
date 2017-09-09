 <?php
$json = file_get_contents("php://input");
$text = json_decode($json, true);

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

        $text = trim($text);
        $data = explode(' ', $text);  // 공백 기준으로 단어 분리하기
        $d_length = count($data);

        // 한 단어씩 단위명 매칭 여부 확인

        // 단위명 DB 쿼리값 배열로 밀어넣기
        for($i=0;$i<$d_length;$i++){
          $var = $data[$i];
          $query = "SELECT u_Name from units where u_Name = '$var'";
          $result = mysqli_query($db, $query);
          $row[] = mysqli_fetch_assoc($result);
        }

        for($j=0;$j<count($row);$j++){
          $return_unit[] = $row[$j]["u_Name"];  // 배열 안에 배열이 들어있어서 값을 빼냄
        }

        for($k=0;$k<count($return_unit);$k++){
          if(is_string($return_unit[$k])){
            $f_unit[] = $return_unit[$k]; // 문자열이 있으면 저장
          } 
        }

        // 한 단어씩 숫자명 매칭 여부 확인
        for($l=0;$l<$d_length;$l++){
          $num = $data[$l];
          $qry = "SELECT n_Number from numbers where n_Text = '$num'";
          $res = mysqli_query($db, $qry);
          $rw[] = mysqli_fetch_assoc($res);
        }
        for($m=0;$m<count($rw);$m++){
          $return_number[] = $rw[$m]["n_Number"]; // 배열 안에 배열이 들어있어서 값을 빼냄
        }
        for($n=0;$n<count($return_number);$n++){
          if(is_numeric($return_number[$n])){
            $f_number[] = $return_number[$n]; // 문자열이 있으면 저장
          } 
        }
        break;

  default    : 
        break;
}

$return['f_name'] = $f_name;
$return['f_unit'] = $f_unit;
$return['f_number'] = $f_number;

$js_return = json_encode($return);
echo $js_return;


?>