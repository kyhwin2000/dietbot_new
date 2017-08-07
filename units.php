<?php
$data = $_POST['string'];
$string = json_decode($data);
echo($string['string']);
?>