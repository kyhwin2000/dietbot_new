<?php
header('Content-Type: application/json; charset=UTF-8');
echo <<< EOD
{
	"type" : "buttons",
	"buttons" : ["메뉴1", "메뉴2", "메뉴3"]
}

EOD;
?>