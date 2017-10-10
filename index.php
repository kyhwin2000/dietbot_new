<!-- <!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title> test page </title>
	</head>

	<body> 
		what do you eat?
		<form name="myform" method="POST" action="test02.php">
			음식명 : <input type="textfield" name="text" />
			<input type = "submit" value = "제출" />
		</form>
	</body>
</html> -->
<?php
$text = "고구마세개바나나2개";
// print_r(parseText($text));
$f_item = array();
$f_name = array();
$f_number = array();
$f_unit = array();

//DB 연결
$db = new mysqli('localhost','root','Dntjd13!','dietbot');
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);

//특수문자 제거 
$text = str_replace( ",","",$text);     //쉼표
$text = str_replace( "&","",$text);     //앤드
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

// 음식 DB에서 자연어 검색
$f_query = "SELECT food_Name, MATCH (food_Name)
              AGAINST ('$text' IN NATURAL LANGUAGE MODE) AS score
              FROM foodCal WHERE MATCH (food_Name) AGAINST
              ('$text' IN NATURAL LANGUAGE MODE) LIMIT 5";
$f_result=mysqli_query($db, $f_query);
$pos = array();
// DB와 매칭되는 음식명 문자열 위치 찾기
while($f_row = mysqli_fetch_array($f_result)){
	array_push($pos,mb_strpos($text,$f_row['food_Name'],0,'UTF-8'));
	print_r($f_row);
	echo "<BR>";
}      
$pos = array_filter($pos,'is_numeric');
sort($pos);	
print_r($pos);
echo("<BR>");

// 음식명 문자열 위치부터 그 다음 음식명 문자열 위치까지 잘라서 아이템 분리하기
for($q=0;$q<count($pos)-1;$q++){
	array_push($f_item,mb_substr($text,$pos[$q],$pos[$q+1]-$pos[$q]));	
}
array_push($f_item,mb_substr($text,$pos[count($pos)-1],strlen($text)-$pos[count($pos)-1]));

print_r($f_item);
echo("<BR>");
// 한 아이템마다 함수에 던져서 음식명, 섭취량, 단위 추출하기
for($r=0;$r<count($f_item);$r++){
	array_push($f_name,parseText($f_item[$r])[0]);
	array_push($f_number,parseText($f_item[$r])[1]);
	array_push($f_unit,parseText($f_item[$r])[2]);
}

print_r($f_name);
print_r($f_number);
print_r($f_unit);

function parseText($text){
	//DB 연결
	$db = new mysqli('localhost','root','Dntjd13!','dietbot');
	mysqli_query($db,"set names utf8");   
	if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);

  $item = array();
  // 1. 풀텍스트 검색하여 음식명 추출
  mysqli_query($db,"set names utf8");   
  $query = "SELECT food_Name, MATCH (food_Name)
          AGAINST ('$text' IN NATURAL LANGUAGE MODE) AS score
          FROM foodCal WHERE MATCH (food_Name) AGAINST
          ('$text' IN NATURAL LANGUAGE MODE) LIMIT 5";
  $result=mysqli_query($db, $query);
  // 검색 결과가 원문과 동일한 문자열이 있으면 음식명으로 저장
  while($data = mysqli_fetch_array($result)){
    if(strpos($text,$data['food_Name'])!==false){
      $name = $data['food_Name']; //음식명 저장
    }
    $text = str_replace($data['food_Name'],"",$text); //음식명 삭제해서 단위랑 숫자만 남기기
  }
  $text = trim($text);

  // 전체 스트링에 숫자가 포함되었는지 검사  
  for($f=0;$f<strlen($text);$f++){
    $char = mb_substr($text,$f,1);
    if(is_numeric($char)){
      $case = "numeric";
      break;
    }
    $case = "string";
  }  

  switch($case) {
    case "numeric"  :
      // 문자열 중 숫자만 추출하여 섭취량으로 저장
      $number = preg_replace("/[^0-9]*/s", "", $text);
      // 문자열 중 한글, 영문만 추출하여 단위로 저장
      $pattern = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z])+/';
    	preg_match_all($pattern, $text, $match);
    	$unit = implode('', $match[0]);
      break;
    case "string" :
      // 문자열을 잘라내서 숫자 DB에 있는지 검사하여 섭취량 추출
      for($i=0;$i<strlen($text);$i++){
      	$val = mb_substr($text, 0, $i);
      	$query02 = "select * from numbers where n_Text = '$val'";
      	$row02 = mysqli_fetch_array(mysqli_query($db, $query02));
        if(count($row02)>0){
          $number = $row02['n_Number'];
          $key_count = $i;
          break;
        }
      }
      // 섭취량을 찾은 키값에서부터 잘라내서 단위 DB에 있는지 검사하여 단위 추출
      for($j=$key_count;$j<strlen($text);$j++){
        $value = mb_substr($text, $j, strlen($text));
        $qry = "select * from units where u_Name = '$value'";
        $raw = mysqli_fetch_array(mysqli_query($db, $qry));
        if(count($raw)>0){
          $unit = $raw['u_Name'];
          break;
        }
      }
      break;
    default :
      break;
  }
  $item = [$name, $number, $unit];
  return $item;
}



?>
