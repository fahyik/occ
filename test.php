<?php


$login = json_decode(file_get_contents('https://10.200.186.172:8080/json/system/loginApplication?loginToken=17e329deefdc8ed1b0a35416081b1424d18783c69e22a3b2a9805f1d5a8a708d'));
$token = $login->result->token;

$callDSS = json_decode(file_get_contents("https://10.200.186.172:8080/json/device/getState?dsid=3504175fe000000000005916&token=".$token));

if ($callDSS->result->isOn) {
	$light = 1;
}
else {
	$light = 0;
}


?>