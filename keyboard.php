<?php
//첫 채팅방 입장 시 띄워주는 키보드
header("Content-Type: application/json; charset=UTF-8");

echo <<< EOD
{
	"type" : "buttons",
	"buttons" : ["먹은 음식 적기", "통계", "오늘 뭐 먹었지", "도움말"]
}

EOD;

?>