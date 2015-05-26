<?php
/********************************************************************
 * Logs occupancy sensor state                                      *
 * Is triggered by dSS on occupancy sensor event                    *
 * Example: http://10.200.191.251/log_occupancy.php?room=G24&state1 *
 ********************************************************************/

// Caution: Do not change file name unless the event responders are adapted as well.

//---get parameters from url---//
$room  = $_GET['room'];
$state = $_GET['state'];
$dsid  = $_GET['dsid'];

$db = mysqli_connect('localhost','root','root','hpzcontrol');

$sql = 'INSERT INTO log_occupancy (`room`, `state`, `dsid`) VALUES ("'.
$room.'", '.$state.', "'.$dsid.'")';
$query = mysqli_query($db,$sql);

//---live update of occupancy status in rooms, for debugging---//
$sql = 'UPDATE live_status SET `occupancy` = '.$state.' WHERE `room` = "'.$room.'"';
$query = mysqli_query($db,$sql);

mysqli_close($db);

unset($room, $state, $dsid, $db, $sql, $query);
echo "log_occupancy.php end<br>";

?>
