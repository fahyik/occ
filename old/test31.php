<?php
/*
//----------------------------determine no. of days-------------------------------------//
echo $refdate = strtotime("2014-05-1"); echo "<br>";
echo $startday = date('z', $refdate); echo "<br>";
echo $today = date('z'); echo "<br>";
echo $noofdays = $today - $startday + 1; echo "<br>";


echo $today = strtotime(date("Y-m-d")); echo "<br>";

echo ($today - $refdate) / 86400;
*/


echo $data = file_get_contents('https://10.200.186.172:8080/json/system/loginApplication?loginToken=17e329deefdc8ed1b0a35416081b1424d18783c69e22a3b2a9805f1d5a8a708d');

?>