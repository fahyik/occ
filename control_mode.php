<?php
/****************************************************************************************
 * Script called by cron.php				                       					 	*
 * - change control_mode once everyday at time defined in cron.php 					    *
 *											                       					 	*
 ****************************************************************************************/
 
for ($i=0;$i<11	;$i++)
{
	$mode[$i] = rand(1,3);
}

$db = mysqli_connect('localhost','root','root','hpzcontrol');
$sql = 'INSERT INTO control_mode (`RPi_0`, `G27`,`G23`,`G24`,`G26.1`,'.
'`G25.2`,`G25.1`,`G22`,`G31.1`,`G26.2`,`G31.2`) VALUES ( '. implode(",",$mode) .')';
$query = mysqli_query($db,$sql);

mysqli_close($db);

unset($i, $mode, $db, $sql, $query);
echo "control_mode.php end <br>";
?>