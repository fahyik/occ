<?php
/****************************************************************************************
 *  Calculating time delay value for motion sensors                					 	*
 *  - runs everyday at 00:05														    *
 *	- adapts to user behaviour from occupancy data from motion sensors				 	*
 ****************************************************************************************/
 
 //------------------------setpoints for time delay calculation-------------------------//
$prob_mode1 = 0.95;
$prob_mode2 = 0.90;
//---max time set to 20 minutes, i.e. max time delay for switching is 20 mins---//
$max_time = 20*60;
//--------------------------------------------------------------------------------------//


//----------------------------------retrieve rooms--------------------------------------// 
$db = mysqli_connect('localhost','root','root','hpzcontrol');
$sql = "SELECT `room` FROM structure";
$result = mysqli_query($db,$sql);
$i = 0;
while($obj = mysqli_fetch_object($result))
{
	$rooms[$i] = $obj->room;
	$i++;
}
mysqli_close($db);
//--------------------------------------------------------------------------------------//

function calculate_td($rooms, $prob, $max_time)
{
	$db = mysqli_connect('localhost','root','root','hpzcontrol');
	$today = date("Y-m-d", time());
		
	foreach($rooms as $room)
	{
		//---get log occupancy data for room---//
		$sql = "SELECT `time`, `state` FROM log_occupancy WHERE `room`='$room'".
		" AND time >= '$today' - INTERVAL 30 DAY ORDER BY `id` DESC";
		$result = mysqli_query($db,$sql);
		
		$i = 0;
		while($obj = mysqli_fetch_object($result))
		{
			$data[$i]['time'] = strtotime($obj->time);	
			$data[$i]['state'] = $obj->state;		
			$i++;
		}
		unset($i, $sql, $result, $obj);		
		
		//---calculate time delay value for each state pair of 0 and 1---//
		$j=0;
		for($i=0; $i<(count($data)-1); $i++)
		{
			if ($data[$i]['state'] == 1 && $data[$i+1]['state'] == 0)
			{
				$timedelay = $data[$i]['time'] - $data[$i+1]['time'];
				//---neglect all values of time delay > $max_time---//
				//---currently set to 20mins or 1200secs---//
				if ($timedelay < $max_time && $timedelay > 0)
				{
					$td_array[$j] = $timedelay;
					$j++;
				}
			}
		}
		unset($i, $j);
		
		if(!empty($td_array))
		{
			//--count total no. of td values---//
			$td_size = count($td_array);
			$td_count = array_fill(0,$max_time,0);
			//---count the frequency of each time delay value from 0 to $max_time---//
			foreach($td_array as $td)
			{
				$td_count[$td]++;
			}
		
			$cum_prob = 0;
			for($i=0; $i<=$max_time; $i++)
			{
				//---calculate cumulative probability for each time delay value---//
				$cum_prob += $td_count[$i]/$td_size;
				//---select time delay value corresponding to $prob set---//
				if($cum_prob > $prob)
				{
					$td_value = ($i-1);
					break;
				}
			}
			unset($i);
		
			//---store new time delay value into array---//
			$td_new[$room] = (int)floor($td_value/60);
			unset($data, $td_array, $td_size, $td_count, $td_value);
		}
		else
		{
			$td_new[$room] = 0;
		}
	}
	
	mysqli_close($db);
	unset($db);
	//---return all new td values for each room as array---//
	return $td_new;
} 

$td1 = calculate_td($rooms,$prob_mode1,$max_time);
$td2 = calculate_td($rooms,$prob_mode2,$max_time);

$db = mysqli_connect('localhost','root','root','hpzcontrol');
echo $sql = "INSERT INTO control_td1 (`".implode("`, `", $rooms)."`)".
" VALUES (".implode(', ', $td1).")"; 

echo "<br>";
mysqli_query($db,$sql);

echo $sql = "INSERT INTO control_td2 (`".implode("`, `", $rooms)."`)".
" VALUES (".implode(', ', $td2).")";

mysqli_query($db,$sql);

mysqli_close($db);
unset($td1, $td2, $db);

unset($rooms, $prob_mode1, $max_time, $prob_mode2);
echo "<br>";
echo "control_adapt_td.php end<br>";
?>
