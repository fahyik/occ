<?
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

//----------------------------------determine date--------------------------------------//
$date = time();//strtotime("2014-05-01");
$today = date('Y-m-d', $date);


//------------------------------actual calculation--------------------------------------//
foreach($rooms as $room)
{	

	for($j = 1; $j < 4; $j++)
	{
		$consumption[$j]['ONtime'] = 0;
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
	
	//---determine $today's date---//
	$sql = "SELECT `$roomx` FROM control_mode WHERE time >= '".$today.
	"' AND time < '".$today."' + INTERVAL 1 DAY";
	$result = mysqli_query($db,$sql);
	$obj = mysqli_fetch_object($result);
	$mode = $obj->$roomx;
	
	//---retrieve lighting data from $today---//
	$sql = "SELECT `$room` FROM log_lighting WHERE time >= '".$today.
	"' AND time < '".$today."' + INTERVAL 1 DAY";
	$result = mysqli_query($db,$sql);
	while($obj = mysqli_fetch_object($result))
	{
		if($obj->$room == 1)
		{
			$consumption[$mode]['ONtime']++;
		}
	}
	
	
	
	echo $sql = 'INSERT INTO daily_energy_consumption (`day`, `room`, `mode_'.$mode.
	'`) VALUES ("'.$today.'", "'.$room.'", '.$consumption[$mode]['ONtime'].')'; echo "<br>";
	$query = mysqli_query($db,$sql);
	
	unset($consumption);
}
	
	
	
	
	
	
	
	
	
	
mysqli_close($db);

echo "<br><br>calculate_daily_energyconsumption.php end <br>";	

?>