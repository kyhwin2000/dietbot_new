<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title> test page </title>
	<body> 
		what do you eat?
		<form method="POST" action="index.php">
			음식명 : <input type="text" name="text" />
			<input type = "submit" />
		</form>
		
		<?php
		$text = $_REQUEST['text'];
		//echo ($text);
		
		
// 쉽표 기준 잘라서 배열로 저장하기
		$f_item = explode(",",$text);
		
		for($a=0;$a<(count($f_item));$a++){		
			// 아이템 앞 뒤 공백 제거하기
			$f_item[$a] = trim($f_item[$a]);
			// 문자열 중 숫자만 추출하기 (아래 함수 사용)
			$f_number[$a] = return_number($f_item[$a]);
			// 문자열 중 숫자 나오기 전까지 잘라내서 음식명으로 저장
			$f_name[$a] = substr($f_item[$a],0,strrpos($f_item[$a],$f_number[$a]));
			// 문자열 중 숫자 다음부터 잘라내서 단위로 저장
			$f_unit[$a] = strrchr($f_item[$a],$f_number[$a]);
			$f_unit[$a] = mb_substr($f_unit[$a],1,(strlen(f_unit[$a]-1)),'utf-8');
			// 확인해보기
			echo $f_name[$a];
			echo '<BR>';	
			echo $f_number[$a];
			echo '<BR>';
			echo $f_unit[$a];
			echo '<BR>';	
		}

function return_number($string){

     for($i=0;$i<strlen($string);$i++ ){
       if(eregi("[0-9]",$string[$i])){
           $string_result = $string_result.$string[$i]; 
       }
     } 
     return $string_result; 
  }

/*
// DB에서 검색하기
		$db = mysql_connect("localhost", "kyhwin2000", "woosung13") or die (mysql_error()); 
		mysql_select_db("kyhwin2000");
		mysql_query("set names utf8");   
		$j = 0;
			$sql = "SELECT * FROM foodCal where food_Name like '.$f_name[$j].'";
			$result=mysql_query($sql, $db);
			$result_cal = mysql_result($result,0,'food_Cal');	
			// 결과값 출력하기
			echo($f_name[$j]).' '.($f_number[$j]).($f_unit[$j]);
			echo(" is ");
			echo($f_number[$j]*$result_cal);
			echo(" Kcal");		
*/
		?>
	</body>
</html>