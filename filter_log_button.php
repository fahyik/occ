<?php
/****************************************************************************************
 * Script to filter button presses logged in log_button            					 	*
 * - in order to filter data to calculate lux threshold								    *
 *											                       					 	*
 ****************************************************************************************/


$db = mysqli_connect('localhost','root','root','hpzcontrol');

//---find the last read id of log_button---//
$sql = "SELECT `index` FROM log_lux_threshold ORDER BY `id` DESC LIMIT 1";
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
if(empty($obj))
{	
	$start_index = 0;
}
else
{
	$start_index = $obj->index;
}

//---only read new data---//
$sql = "SELECT * FROM log_button WHERE `id` > $start_index";
$result = mysqli_query($db,$sql);

$i = 0;
while($obj = mysqli_fetch_object($result))
{
	//---result contains id, time, room, state, lux---//
	$log_button[$i]["id"] = $obj->id;
	$log_button[$i]["time"] = strtotime($obj->time);
	$log_button[$i]["room"] = $obj->room;
	$log_button[$i]["state"] = $obj->state;
	$i++;
}
$size = $i;
unset($i, $obj, $sql, $result, $start_index);
//echo "size is equal to $size.<br>";

if(!empty($log_button))
{
	$j = 0;
	for($i=0; $i < $size; $i++)
	{
		if($i == ($size-1))
		{
			if($log_button[$i]["state"] == 1)
			{
				$insert[$j] = $log_button[$i]["id"];
				$j++;
			}
		}
		else
		{
			//echo "i is equal to $i.<br>";
			if($log_button[$i]["state"] == 1)
			{
				//---skip if next row is same room and time is within 5 secs---//
				if( $log_button[$i+1]["room"] == $log_button[$i]["room"] &&
				($log_button[$i+1]["time"] - $log_button[$i]["time"]) <= 5 )
				{
					//do nothing; next $i
				}
				//---otherwise capture row as valid button press---//
				else
				{
					$insert[$j] = $log_button[$i]["id"];
					$j++;
				}
			}
			else
			{
				//do nothing; next $i
			}
		}
	}
	unset($log_button, $size, $i, $j);
}

//---insert into log_lux_threshold---//
if(!empty($insert))
{
	foreach($insert as $insert_id)
	{
		echo $sql = "INSERT INTO log_lux_threshold (`time`, `room`, `state`, `lux`, `index`) ".
		"SELECT `time`, `room`, `state`, `lux`, `id` FROM log_button WHERE id=$insert_id;";
		echo "<br>";
		$result = mysqli_query($db,$sql);
		$data_filtered = TRUE;
	}
}
else
{
	$data_filtered = FALSE;
}

mysqli_close($db);

unset($db, $sql, $result, $insert);

echo "filter_log_button.php end <br>";

?>