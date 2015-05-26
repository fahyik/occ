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
		$consumptionF[$j]['ONtime'] = 0;
		$consumptionW[$j]['ONtime'] = 0;
	}
	
	for($i = 0; $i < $noofdays; $i++)
	{
		$sql = "SELECT `$room` FROM control_mode WHERE time >= '2014-04-22' +".
		" INTERVAL $i DAY AND time < '2014-04-22' + INTERVAL ".($i+1)." DAY";
		$result = mysqli_query($db,$sql);
		$obj = mysqli_fetch_object($result);
		$mode = $obj->$room;
		
		if($room == "G27")
		{
			$room = "G27F";
			$sql = "SELECT `$room` FROM log_lighting WHERE time >= '2014-04-22' +".
			" INTERVAL $i DAY AND time < '2014-04-22' + INTERVAL ".($i+1)." DAY";
			$result = mysqli_query($db,$sql);
			while($obj = mysqli_fetch_object($result))
			{
				if($obj->$room == 1)
				{
					$consumptionF[$mode]['ONtime']++;
				}
			}
			
			$room = "G27W";
			$sql = "SELECT `$room` FROM log_lighting WHERE time >= '2014-04-22' +".
			" INTERVAL $i DAY AND time < '2014-04-22' + INTERVAL ".($i+1)." DAY";
			$result = mysqli_query($db,$sql);
			while($obj = mysqli_fetch_object($result))
			{
				if($obj->$room == 1)
				{
					$consumptionW[$mode]['ONtime']++;
				}
			}
			
			$room = "G27";
		}
		else
		{
			$sql = "SELECT `$room` FROM log_lighting WHERE time >= '2014-04-22' +".
			" INTERVAL $i DAY AND time < '2014-04-22' + INTERVAL ".($i+1)." DAY";
			$result = mysqli_query($db,$sql);
			while($obj = mysqli_fetch_object($result))
			{
				if($obj->$room == 1)
				{
					$consumption[$mode]['ONtime']++;
				}
			}
		}
			
		//---code to see cumulative consumption---//
		/*echo "cumulative consumption in room: $room running mode: $mode";
		echo " on date: "; echo date("Y-m-d", strtotime("2014-04-22 + $i days")); 
		echo " is ".$consumption[$mode]['ONtime']." mins.<br>";*/
		
	}
	
	
	if($room == "G27")
	{
		for($j = 1; $j < 4; $j++)
		{
			echo "total consumption in room G27F, mode $j : ";
			echo $total_consumption["G27F"][$j] = $consumptionF[$j]['ONtime']; echo " mins.<br>";
			echo "total consumption in room G27W, mode $j : ";
			echo $total_consumption["G27W"][$j] = $consumptionW[$j]['ONtime']; echo " mins.<br>";
				
			$sql2 = 'UPDATE total_energy_consumption SET `mode_'.$j.'` = '.
			$total_consumption["G27F"][$j].' WHERE `room` = "G27F"';
			$query2 = mysqli_query($db,$sql2);
			
			$sql2 = 'UPDATE total_energy_consumption SET `mode_'.$j.'` = '.
			$total_consumption["G27W"][$j].' WHERE `room` = "G27W"';
			$query2 = mysqli_query($db,$sql2);
		}
		unset($consumptionF);
		unset($consumptionW);
	}
	else
	{
		for($j = 1; $j < 4; $j++)
		{
			echo "total consumption in room $room, mode $j : ";
			echo $total_consumption[$room][$j] = $consumption[$j]['ONtime']; echo " mins.<br>";
	
			$sql2 = 'UPDATE total_energy_consumption SET `mode_'.$j.'` = '.
			$total_consumption[$room][$j].' WHERE `room` = "'.$room.'"';
			$query2 = mysqli_query($db,$sql2);
		}
		unset($consumption);
	}
	
	echo "<br>";

}

$sum[1] = 0;
$sum[2] = 0;
$sum[3] = 0;

$rooms[1] = "G27F";
$rooms[11] = "G27W";
foreach($rooms as $room)
{
	$sum[1] +=  $total_consumption[$room][1];
	$sum[2] +=  $total_consumption[$room][2];
	$sum[3] +=  $total_consumption[$room][3];
}

echo "<br>";
for($j = 1; $j < 4; $j++)
{
	echo "Mode $j total consumption: ".$sum[$j]." mins.<br>";
	$sql2 = 'UPDATE total_energy_consumption SET `mode_'.$j.'` = '.
	$sum[$j].' WHERE `room` = "ALL"';
	$query2 = mysqli_query($db,$sql2);
}
	
mysqli_close($db);

echo "<br><br>calculate_energy_consumption.php end <br>";		
?>