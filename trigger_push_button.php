<?php
/****************************************************************************************
 * Script called by dSS when button is pushed                      					 	*
 * - logs button press state and lux value at that instance							    *
 *											                       					 	*
 ****************************************************************************************/

set_time_limit(600);
//To Do:
// - Fehler abfangen
// - Error Log

//--- get room name and state ---//
$room  = $_GET['room'];
$state = $_GET['state'];

//--- connects to sql ---//
$db = mysqli_connect('localhost','root','root','hpzcontrol');

//--- determine lasts lux value at which button was pressed ---//
$sql = 'SELECT `'.$room.'` FROM log_lux ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$lux = (int)$obj->$room;

//---logs button press---//
$sql = 'INSERT INTO log_button (`room`, `state`, `lux` ) '.
'VALUES ("'.$room.'", '.$state.', '.$lux.')';
$query = mysqli_query($db,$sql);

//---updates log_lighting---//
$sql2 = 'UPDATE log_lighting SET `'.$room.'` = '.$state.' ORDER BY `id` DESC LIMIT 1';
$query2 = mysqli_query($db,$sql2);

$sql2 = 'UPDATE log_lighting_copy SET `'.$room.'` = '.$state.' ORDER BY `id` DESC LIMIT 1';
$query2 = mysqli_query($db,$sql2);

//--- update log_lux ---//
$sql = 'SELECT ip FROM structure WHERE room="'.$room.'"';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$ip = $obj->ip;
file_get_contents('http://'.$ip.'/get/lux.php');

mysqli_close($db);

unset($room, $state, $lux, $db, $sql, $result, $query, $obj);

?>