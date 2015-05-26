<?php
/****************************************************************************************
 * Script turns lights on and off                                						*
 * Scirpt is called by RPi to turn light on/off                  						*
 * Example: http://10.200.191.251/lights.php?light=voute&state=1 						*
 *          turns voute in the room of the RPi on                						*
 ****************************************************************************************/

require_once('lib_bundle.php');                                      						// include bundle class

//--------------------------------get RPi parameters------------------------------------//

$myRPI  = new rpiBundleClass;
$room   = $myRPI->get_room();
$rpi_ip = $myRPI->get_ip();
$token  = $myRPI->get_token();                                                             						
$state  = $_GET['state'];		// get $_GET variables

//--------------------------------------------------------------------------------------//

//debug
//$room = $_GET['room'];

//--------------------access to DSS to switch ON/OFF light------------------------------//

$db = mysqli_connect('localhost','root','root','hpzcontrol');

$sql = 'UPDATE log_lighting SET '.$room.' = '.$state.' ORDER BY `id` DESC LIMIT 1';
$query = mysqli_query($db,$sql);
$sql = 'UPDATE log_lighting_copy SET '.$room.' = '.$state.' ORDER BY `id` DESC LIMIT 1';
$query = mysqli_query($db,$sql);
                                              						
$sql = 'SELECT device_id FROM structure_devices WHERE room="'.$room.'" '.					// get device_id from structure_devices
'AND (`device_type`="FL" OR `device_type`="Voute")';

$result = mysqli_query($db,$sql);
if ($result = mysqli_query($db,$sql))                                						// if id's of devices available switch lights according to state
{
    while($obj = mysqli_fetch_object($result))
    {
        if ($state == 1)
        {
            if($device_id = $obj->device_id)
            {
                echo file_get_contents('https://10.200.186.172:8080/json/device/turnOn?dsid=3504175fe0000000'.$device_id.'&token='.$token);
                $lux = file_get_contents('http://'.$rpi_ip.'/get/lux.php');
            }
        }
        if ($state == 0)
        {
            if($device_id = $obj->device_id)
            {
                echo file_get_contents('https://10.200.186.172:8080/json/device/turnOff?dsid=3504175fe0000000'.$device_id.'&token='.$token);
                $lux = file_get_contents('http://'.$rpi_ip.'/get/lux.php');
            }
        }
        
        sleep(1);                                                   						// wait some time in order to take some pressure from dss-bandwidth
    }
}
//--------------------------------------------------------------------------------------//



mysqli_close($db);                                                  						// not sure if working properly with bundle class


?> 
 
 