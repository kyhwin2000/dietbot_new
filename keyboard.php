<?php
header('Content-Type: application/json; charset=UTF-8');
echo <<< EOD
{
	"type" : "buttons",
	"buttons" : ["먹은 음식 적기", "남은 칼로리 보기", "도움말"]
}

EOD;
?>