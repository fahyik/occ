<?
/****************************************************************************************
 *  mySQL API                     		                    						    *
 *  allows Raspberry Pis to access all necessary data mySQL database on iMac server	    *
 *  to enable control.php									    						*
 *  caution: echos will disable control.php!       										*
 ****************************************************************************************/

include('lib_bundle.php');

$myRPI  = new rpiBundleClass;
$room   = $myRPI->get_room();
$data['room'] = $room;
$data['rpi_ip'] = $myRPI->get_ip();
$data['name'] = $myRPI->get_name();
$db = mysqli_connect('localhost','root','root','hpzcontrol');

// controlMode
$sql = 'SELECT `'.$room.'` FROM control_mode ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$data['controlMode'] = (int)$obj->$room;

//lux_th
$sql = 'SELECT `'.$room.'` FROM control_lux_threshold ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$data['lux_th'] = (int)$obj->$room;

//lux_upp
$sql = 'SELECT `'.$room.'` FROM control_upper_lux_threshold ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$data['lux_upp'] = (int)$obj->$room;

//TD1
$sql = 'SELECT `'.$room.'` FROM control_td1 ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$data['TD1'] = (int)$obj->$room;

//TD2
$sql = 'SELECT `'.$room.'` FROM control_td2 ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$data['TD2'] = (int)$obj->$room;

/*
//----------------returns lux value, 5 min exponential moving average-------------------//
$sql = 'SELECT `'.$room.'` FROM log_lux ORDER BY `time` DESC LIMIT 5';
$result = mysqli_query($db,$sql);

$i = 0;
$alpha = 2/(5+1);

while($obj = mysqli_fetch_object($result))
{
	$lux[$i] = (int)$obj->$room;
	$i++;
}

$data['lux'] = (int)(($lux[0] + (1-$alpha)*$lux[1] + pow((1-$alpha),2)*$lux[2] + 
pow((1-$alpha),3)*$lux[3] + pow((1-$alpha),4)*$lux[4]) / (1 + (1-$alpha) + 
pow((1-$alpha),2) + pow((1-$alpha),3) + pow((1-$alpha),4)));

unset($lux, $i);
*/

//last lux value
$sql = 'SELECT `'.$room.'` FROM log_lux ORDER BY `time` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$data['lux'] = (int)$obj->$room;


//dt
$sql = 'SELECT `time`, `state` FROM log_occupancy WHERE room="'.$room.'" ORDER BY `id` DESC LIMIT 1';  //state=1 ->motion/occupied
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
if ($obj->state == 1)
{
	$data['dt'] = 0;
}
elseif ($obj->state == 0)
{
	$trigger_time = $obj->time;
	$trigger_time = strtotime($trigger_time);
	$now = time();                                          //http://stackoverflow.com/questions/2934258/how-do-i-get-the-current-time-zone-of-mysql
	$data['dt'] = ((int)$now-$trigger_time);              //[seconds]
}

// 'lights':
if ($room == "G27")
{
	$room = "G27W";
}
$sql = 'SELECT `'.$room.'` FROM log_lighting ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$data['lights'] = (int)($obj->$room);
if ($room == "G27W")
{
	$room = "G27";
}

// 'buttonOn':
$sql = 'SELECT `time` FROM log_button WHERE state=1 AND room="'.$room.'" ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
if(!empty($obj))
{
	$data['buttonOn'] = (time()-strtotime($obj->time));
}
else
{
	$data['buttonOn'] = time();
}

//buttonOff':
$sql = 'SELECT `time` FROM log_button WHERE state=0 AND room="'.$room.'" ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
if(!empty($obj))
{
	$data['buttonOff'] = (time()-strtotime($obj->time));
}
else
{
	$data['buttonOff'] = time();
}

//lastbuttonstate':
$sql = 'SELECT `state` FROM log_button WHERE room="'.$room.'" ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
if(!empty($obj))
{
	$data['lastbuttonstate'] = (int)($obj->state);
}
else
{
	$data['lastbuttonstate'] = NULL;
}

//lastcontrolOFF:
$sql = 'SELECT `time` FROM log_control WHERE action="off" AND room="'.$room.'" ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
if(!empty($obj))
{
	$data['lastcontrolOff'] = (time()-strtotime($obj->time));
}
else
{
	$data['lastcontrolOff'] = time();
}

echo json_encode($data);
mysqli_close($db);
unset($db, $sql, $result, $obj, $data, $room, $myRPI);
?>