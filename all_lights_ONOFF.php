<?php
/****************************************************************************************
 * Script to turn ALL lights on or off                                					*
 * Scirpt is called to turn ALL light on/off                  						*
 * Example: http://10.200.191.251/lights.php?light=voute&state=1 						*
 *          turns voute in the room of the RPi on                						*
 ****************************************************************************************/

require_once('lib_bundle.php');
$myRPI  = new rpiBundleClass;
$token  = $myRPI->get_token(); 

$state = $_GET['state'];
if($state != 0 && $state != 1)
{
	exit("no state defined");
}

//--------------------access to DSS to switch ON/OFF light------------------------------//

$db = mysqli_connect('localhost','root','root','hpzcontrol');                                                           						
$sql = 'SELECT device_id FROM structure_devices WHERE (`device_type`="FL" OR `device_type`="Voute")';

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
                echo "<br>";
            }
        }
        if ($state == 0)
        {
            if($device_id = $obj->device_id)
            {
                echo file_get_contents('https://10.200.186.172:8080/json/device/turnOff?dsid=3504175fe0000000'.$device_id.'&token='.$token);
            	echo "<br>";
            }
        }
        
        sleep(1);                                                   						// wait some time in order to take some pressure from dss-bandwidth
    }
}
//--------------------------------------------------------------------------------------//


mysqli_close($db);


?> 
 
 