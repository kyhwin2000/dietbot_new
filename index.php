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
$text = "사과 10개";
print_r(parseText($text));

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
      // 문자열 중 한글만 추출하여 단위로 저장
      preg_match_all('/( [\x{AC00}-\x{D7AF}]{2})+/', $text, $match);
      $unit = $match[0];
      // $unit = strrchr($text,$number);
      // $unit = mb_substr($unit,1,(strlen($unit)-1),'utf-8');
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
