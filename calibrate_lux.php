<?php
/****************************************************************************************
 * Script called to calibrate lux values of lights at night with lights on			 	*
 * 																					    *
 *											                       					 	*
 ****************************************************************************************/
set_time_limit(1200);

for ($i=0;$i<11	;$i++)
{
	$mode[$i] = 0;
}

$db = mysqli_connect('localhost','root','root','hpzcontrol');
$sql = 'INSERT INTO control_mode (`RPi_0`, `G27`,`G23`,`G24`,`G26.1`,'.
'`G25.2`,`G25.1`,`G22`,`G31.1`,`G26.2`,`G31.2`) VALUES ( '. implode(",",$mode) .')';
$query = mysqli_query($db,$sql);

file_get_contents("http://129.132.32.187/all_lights_ONOFF.php?state=1");

sleep(660);

$sql = "SELECT `room` FROM structure";
$result = mysqli_query($db,$sql);
$i = 0;
while($obj = mysqli_fetch_object($result))
{
	$rooms[$i] = $obj->room;
	$i++;
}

foreach($rooms as $room)
{
	echo "room $room:<br>";
	echo $sql = "SELECT `$room` FROM log_lux ORDER BY `id` DESC LIMIT 10"; echo "<br>";
	$result = mysqli_query($db,$sql);
	
	$i = 0;
	while($obj = mysqli_fetch_object($result))
	{
		$lux[$i] = $obj->$room;
		$i++;
	}
	//--- includes +5% allowance ---//
	echo $new_calibrate[$room] = (int)(max($lux) * 1.05); echo "<br>";
	unset($lux);
} 

$sql = "INSERT INTO control_lux_w_lights (`".implode("`, `", $rooms)."`)".
" VALUES (".implode(', ', $new_calibrate).")";
mysqli_query($db,$sql);

mysqli_close($db);

file_get_contents("http://129.132.32.187/all_lights_ONOFF.php?state=0");

unset($i, $mode, $db, $sql, $query, $result, $rooms);

echo "calibrate_lux.php end <br>";
?>