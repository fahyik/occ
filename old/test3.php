<?
$room = G24;

echo date('Y-m-d'); echo "<br>";
echo date('z'); echo "<br>";
$refdate = strtotime("2014-04-22");
echo date('z', $refdate); echo "<br>";
$i = 0;
echo 		$sql = "SELECT `$room` FROM control_mode WHERE time >= '2014-04-22' +".
		" INTERVAL $i DAY AND time < '2014-04-22' + INTERVAL ".($i+1)." DAY";
?>