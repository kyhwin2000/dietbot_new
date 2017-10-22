<?php

include_once 'Snoopy.class.php';
$snoopy = new snoopy;
$snoopy->fetch("http://blog.naver.com/myotherhalf/221116083788");
$txt = $snoopy->results;
print_r($txt);
?>
