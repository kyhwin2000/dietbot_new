<?php
//첫 채팅방 입장 시 띄워주는 키보드
header("Content-Type: application/json; charset=UTF-8");

echo <<< EOD
{
	"type" : "buttons",
	"buttons" : ["먹은 음식 적기", "오늘의 통계", "도움말"]
}

EOD;

?>