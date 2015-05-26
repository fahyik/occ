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
                                                                    // get parameters from url
$room  = $_GET['room'];                                             // get name of room (e.g. 'G24')
$state = $_GET['state'];                                            // get state of button ('1' or '0') ('1'->ON, '0'->OFF)

$db = mysqli_connect('localhost','root','root','hpzcontrol');       // connect to database
                                                                    // ↓ insert button state into MySQl table 'log_button'
$sql = 'SELECT `'.$room.'` FROM log_lux ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$lux = (int)$obj->$room;

//--- determine instantaneous lux from when button was pressed ---//
$sql = 'SELECT ip FROM structure WHERE room="'.$room.'"';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$ip = $obj->ip;
$lux2 = (int)file_get_contents('http://'.$ip.'/get/lux.php');

$sql = 'INSERT INTO log_button (`room`, `state`, `lux` ) '.
'VALUES ("'.$room.'", '.$state.', '.$lux.')';
$query = mysqli_query($db,$sql);

$sql2 = 'UPDATE log_lighting SET `'.$room.'` = '.$state.' ORDER BY `id` DESC LIMIT 1';
$query2 = mysqli_query($db,$sql2);

$sql2 = 'UPDATE log_lux SET `'.$room.'` = '.$lux2.' ORDER BY `id` DESC LIMIT 1';
$query2 = mysqli_query($db,$sql2);

mysqli_close($db);                                                  // disconnect from database

unset($room, $state, $lux, $db, $sql, $result, $query, $obj);

?>