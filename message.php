<?php
$data =  json_decode(file_get_contents('php://input'));
//입력을 받아서$data변수에 모두저장

//$data->user_key : 사용자의 고유키
//$data->type : text/photo 로 나뉘어지며 문자인지 미디어인지 구분
//$data->content : 메시지 내용(text일 경우 메시지가,photo일 경우 미디어의 주소가 들어있다.)

if("$data->content"== "메뉴1"){

echo <<< EOD
{
  "message": {
    "text": "메뉴1입력시 출력할 내용"
  },
  "keyboard": {	//선택창 표시
    "type": "buttons",
    "buttons": [
      "메뉴1",
      "메뉴2",
      "메뉴3"
    ]
  }
}
EOD;