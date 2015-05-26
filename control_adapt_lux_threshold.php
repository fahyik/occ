<?php
/****************************************************************************************
 *  Calculating lux threshold values				              					 	*
 *  - runs everyday at 00:05														    *
 *	- adapts to user behaviour from lux data gathered from button presses			 	*
 ****************************************************************************************/
include('filter_log_button.php');

function calc_median($array)                                        // median function: http://codereview.stackexchange.com/questions/220/calculate-a-median-too-much-code
{
  $iCount = count($array);
  if ($iCount == 0) 
  {
    return -7;
  }
  $middle_index = floor($iCount / 2);                               // if we're down here it must mean $array has at least 1 item in the array.
  sort($array, SORT_NUMERIC);
  $median = $array[$middle_index];                                  // assume an odd # of items
  if ($iCount % 2 == 0)                                             // handle the even case by averaging the middle 2 items
  {
    $median = ($median + $array[$middle_index - 1]) / 2;
  }
  return $median;
}

function calc_mean($array)
{
	$iCount = count($array);
	$sum = 0;
	foreach($array as $lux_value)
	{
		$sum += $lux_value;
	}
	return $mean = (int)($sum/$iCount);
}

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
//--------------------------------------------------------------------------------------//

foreach($rooms as $room)
{
	echo "room $room:<br>";
	
	//---retrieve max lux value, so any button presses above this value is ignore---//
	$sql = "SELECT `".$room."` FROM control_lux_w_lights ORDER BY `id` DESC LIMIT 1";
	$result = mysqli_query($db,$sql);
	$obj = mysqli_fetch_object($result);
	$max_lux = $obj->$room;
	
	echo "room max lux: $max_lux<br>";
	
	$sql = "SELECT `lux`, `time` FROM log_lux_threshold WHERE `room`='$room'"; 
	$result = mysqli_query($db,$sql);
	$i = 0;
	while($obj = mysqli_fetch_object($result))
	{
		$data[$i]['time'] = strtotime($obj->time);
		$data[$i]['lux'] = $obj->lux;
		$i++;
	}
	$data_size = $i;

	if(!empty($data))
	{
		echo "data not empty.<br>";
		$j = 0;
		for($i=0; $i < $data_size; $i++)
		{
			if ( (time() - $data[$i]['time']) < 60*60*24*7*4 && $data[$i]['lux'] > 0
			&& $data[$i]['lux'] <= $max_lux*1.05)
			{
				echo "lux: ";
				echo $lux_th[$j] = $data[$i]['lux']; echo "<br>";
				$j++;
			}
		}
		if(!empty($lux_th))
		{
			$mean = calc_mean($lux_th);
			$median = calc_median($lux_th);
			echo "mean is $mean<br>median is $median<br>";
			echo "lux_threshold is: ";
			echo $new_lux_threshold[$room] = min($mean,$median); echo "<br><br>";
		}
		else
		{
			echo "lux empty. <br>";
			$sql = "SELECT `".$room."` FROM control_lux_threshold ORDER BY `id` DESC LIMIT 1";
			$result = mysqli_query($db,$sql);
			$obj = mysqli_fetch_object($result);
			echo "lux_threshold is: ";
			echo $new_lux_threshold[$room] = $obj->$room; echo "<br><br>";
		}
		unset($max_lux, $data, $data_size, $lux_th, $median, $mean);
	}
	else
	{
		echo "data empty. <br>";
		$sql = "SELECT `".$room."` FROM control_lux_threshold ORDER BY `id` DESC LIMIT 1";
		$result = mysqli_query($db,$sql);
		$obj = mysqli_fetch_object($result);
		echo "lux_threshold is: ";
		echo $new_lux_threshold[$room] = $obj->$room; echo "<br><br>";
	}
}

//----------------------------------update sql tables------------------------------------// 
echo $sql = "INSERT INTO control_lux_threshold (`".implode("`, `", $rooms)."`)".
" VALUES (".implode(', ', $new_lux_threshold).")";
echo "<br>";
$query = mysqli_query($db,$sql);

//---update upper lux values with lux with lightsON values---//
$sql = "SELECT * FROM control_lux_w_lights ORDER BY `id` DESC LIMIT 1";
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);

foreach($rooms as $room)
{
	$new_upp_lux_threshold[$room] = $new_lux_threshold[$room] + $obj->$room ;
}

echo $sql = "INSERT INTO control_upper_lux_threshold (`".implode("`, `", $rooms)."`)".
" VALUES (".implode(', ', $new_upp_lux_threshold).")";
echo "<br>";

$query = mysqli_query($db,$sql);

//--------------------------------------------------------------------------------------//

mysqli_close($db);
unset($db, $sql, $result, $query);
echo "control_adapt_lux_th.php end<br>";
?>