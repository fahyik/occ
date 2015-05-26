<?php
/****************************************************************************************
 * Script to blink lights as warning                               						*
 * 													               						*
 ****************************************************************************************/

require_once('lib_bundle.php');                                      						// include bundle class

//--------------------------------get RPi parameters------------------------------------//

$myRPI  = new rpiBundleClass;
$room   = $myRPI->get_room();
$rpi_ip = $myRPI->get_ip();
$token  = $myRPI->get_token();                                                             						
//--------------------------------------------------------------------------------------//

//debug
//$room = $_GET['room'];

//--------------------access to DSS to switch ON/OFF light------------------------------//

$db = mysqli_connect('localhost','root','root','hpzcontrol');
                                                           						
$sql = 'SELECT device_id FROM structure_devices WHERE room="'.$room.'" '.					// get device_id from structure_devices
'AND `device_type`="FL" LIMIT 1';

$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$device_id = $obj->device_id;

echo file_get_contents('https://10.200.186.172:8080/json/device/blink?dsid=3504175fe0000000'.$device_id.'&token='.$token);

sleep(1);                                                   						// wait some time in order to take some pressure from dss-bandwidth

//--------------------------------------------------------------------------------------//


mysqli_close($db);

?> 
 
 