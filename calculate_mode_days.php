<?php
set_time_limit(600);

$db = mysqli_connect('localhost','root','root','hpzcontrol');

//----------------------------------retrieve rooms--------------------------------------// 
$sql = "SELECT `room` FROM structure";
$result = mysqli_query($db,$sql);
$i = 0;
while($obj = mysqli_fetch_object($result))
{
	$rooms[$i] = $obj->room;
	$i++;
}
$rooms[1] = "G27F";
$rooms[11] = "G27W";
unset($i);

//----------------------------determine no. of days-------------------------------------//
$refdate = strtotime("2014-04-22");
$startday = date('z', $refdate);
$today = date('z');
$noofdays = $today - $startday + 1;

//------------------------------actual calculation--------------------------------------//
foreach($rooms as $room)
{
	for($j = 1; $j < 4; $j++)
	{
		$consumption[$j]['ONtime'] = 0;
		$count_mode[$room][$j] = 0;
	}

	if($room == "G27F" || $room == "G27W")
	{
		$roomx = "G27";
	}
	else
	{
		$roomx = $room;
	}
	echo "room is $room"; echo "<br>";
	
	for($i = 0; $i < $noofdays; $i++)
	{
		$sql = "SELECT `$roomx` FROM control_mode WHERE time >= '2014-04-22' +".
		" INTERVAL $i DAY AND time < '2014-04-22' + INTERVAL ".($i+1)." DAY";
		$result = mysqli_query($db,$sql);
		$obj = mysqli_fetch_object($result);
		$mode = $obj->$roomx;
		$count_mode[$room][$mode]++;	
	}
	
	for($j = 1; $j < 4; $j++)
	{
		echo $sql2 = 'UPDATE days_control_mode SET `mode_'.$j.'` = '.
		$count_mode[$room][$j].' WHERE `room` = "'.$room.'"'; echo "<br>";
		$query2 = mysqli_query($db,$sql2);
	}
}

$sum[1] = 0;
$sum[2] = 0;
$sum[3] = 0;

foreach($rooms as $room)
{
	$sum[1] +=  $count_mode[$room][1];
	$sum[2] +=  $count_mode[$room][2];
	$sum[3] +=  $count_mode[$room][3];
}

for($j = 1; $j < 4; $j++)
{
	$sql2 = 'UPDATE days_control_mode SET `mode_'.$j.'` = '.
	$sum[$j].' WHERE `room` = "ALL"';
	$query2 = mysqli_query($db,$sql2);
}

mysqli_close($db);

unset($db, $sql, $sql2, $result, $query2, $obj);
unset($count_mode, $sum, $rooms, $roomx);
unset($today, $refdate, $startday, $noofdays);
echo "<br><br>calculate_mode_days.php end <br>";		
?>