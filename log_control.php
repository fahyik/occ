<?php
/***************************************************************************************************************************************************************************
 * Log of control action                                                                                                                                                   *
 * Example: call following url to log int log_control-table                                                                                                                *
 *          http://129.132.32.187/log_control.php?lux=382&TD=2700&dt=16&occupied=1&dark=1&bright=0&lights=0&buttonOnTime=1259&buttonOffTime=1426&trigger=crontab&action=on *
 ***************************************************************************************************************************************************************************/


echo $lux    	    = $_GET['lux'];           	echo '<br>';     // get parameters from url
echo $TD     	    = $_GET['TD'];            	echo '<br>';     // echos are just for debugging
echo $dt            = $_GET['dt'];            	echo '<br>';
echo $action        = $_GET['action'];        	echo '<br>';
echo $occupied      = $_GET['occupied'];      	echo '<br>';
echo $dark          = $_GET['dark'];          	echo '<br>';
echo $bright        = $_GET['bright'];        	echo '<br>';
echo $lights        = $_GET['lights'];        	echo '<br>';
echo $buttonOnTime  = $_GET['buttonOnTime'];  	echo '<br>';
echo $buttonOffTime = $_GET['buttonOffTime']; 	echo '<br>';
echo $trigger       = $_GET['trigger'];       	echo '<br>';
echo $mode	        = $_GET['mode']; 	      	echo '<br>';
echo $timetaken     = $_GET['timetaken'];     	echo '<br>';
echo $name   		= urldecode($_GET['name']); echo '<br>';
echo $rpi_ip 		= $_GET['rpiip'];			echo '<br>';
echo $room   		= $_GET['room']; 			echo '<br>';


$db = mysqli_connect('localhost','root','root','hpzcontrol');
$sql = "INSERT INTO log_control (`name`,`ip`,`room`,`lux`,`TD`,`dt`,`action`,`occupied`,".
"`dark`, `bright`, `lights`, `buttonOnTime`, `buttonOffTime`, `trigger`, `mode`, "
."timetaken) VALUES ('$name', '$rpi_ip', '$room', $lux, $TD, $dt, '$action', $occupied, ".
"$dark, $bright, $lights, $buttonOnTime, $buttonOffTime, '$trigger', $mode, $timetaken)";
$query = mysqli_query($db,$sql);                             // query to database


mysqli_close($db);
echo "log_control.php end <br>";                             // output for debugging
?>