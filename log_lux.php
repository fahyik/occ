<?php
/**************************************************************
 * Loggen der Lux-Werte                                       *
 * via TSL2561 -> Raspberry Pi -> PHP -> JSON -> PHP -> MySQL *
 * Sensorwerte können wiefolgt abgefragt werden:              *
 * XXX.XXX.XXX.XXX/get/lux.php                                *
 * XXX.XXX.XXX.XXX ist die IP des Raspberry Pi                *
 **************************************************************/
 
//duplicate new row in log_lux

$db = mysqli_connect('localhost','root','root','hpzcontrol');
$sql = 'INSERT INTO log_lux (`RPi_0`, `G27`,`G23`,`G24`,`G26.1`,`G25.2`,`G25.1`,`G22`,`G31.1`,`G26.2`,`G31.2`) SELECT `RPi_0`, `G27`,`G23`,`G24`,`G26.1`,`G25.2`,`G25.1`,`G22`,`G31.1`,`G26.2`,`G31.2` FROM log_lux ORDER BY `id` DESC LIMIT 1';
$query = mysqli_query($db,$sql);

$error = 0; 
$error = ($db == FALSE ? 1 : 0);                               // set error flag
$error = ($query == FALSE ? 2 : 0);                            // set error flag


//******************************************************/
// send e-mail with error messages
//******************************************************/
$to = "yongf@student.ethz.ch";
if ($error != 0)
{	
	$file_name = __FILE__;
	
	switch ($error)
	{
		case 1:
			$subject = "Database error";
			$message = date("d.m.Y, H:i:s")."\n";
			$message .= "\t A MySQL database error occured:\n";
			$message .= "\t In script: ".  $file_name. "\n";
			$message .= "\t print_r(mysqli_connect(...)): FALSE\n";
			mail($to, $subject, $message);
		case 2:
			$subject = "Database error";
			$message = date("d.m.Y, H:i:s")."\n";
			$message .= "\t A MySQL database error occured:\n";
			$message .= "\t In script: ".  $file_name. "\n";
			$message .= "\t print_r(mysqli_query(...)):  FALSE\n";
			mail($to, $subject, $message);	
	}
}

$room_map = [
    "129.132.32.181" => "RPi_0",
    "129.132.32.182" => "G27",
    "129.132.32.183" => "G23",
    "129.132.32.184" => "G24",
    "129.132.32.185" => "G26.1",
    "129.132.32.186" => "G25.2",
    "129.132.32.189" => "G25.1",
    "129.132.32.190" => "G22",
    "129.132.32.130" => "G31.1",
    "129.132.32.131" => "G26.2",
    "129.132.32.132" => "G31.2",
];


//******************************************************/
// get lux values from raspberries
//******************************************************/

$ctx = stream_context_create(array('http' => array('timeout' => 5))); // 5 second timeout

foreach ($room_map as $ip => $room)
	{
		$dataReceived = FALSE;
		$attemptCount = 0;
		
		while ($dataReceived != TRUE)                          // try 10 times to reach the raspberry and get the lux value
		{
			$data = file_get_contents('http://'.$ip.'/get/old_lux.php', false, $ctx);
			
			if ($data !==  FALSE)
			{
				$dataReceived = TRUE;
			}
			else
			{
				$attemptCount += 1;
				sleep(1);
				if ($attemptCount > 2)                         // was #9 after 10 attempts: abort and set lux value to error code -3
				{
					$lux = -3;
					$sql = 'UPDATE log_lux SET `'.$room.'` = '.$lux.' ORDER BY `id` DESC LIMIT 1';
					$query = mysqli_query($db,$sql);
					$sql = 'UPDATE live_status SET `lux` = '.$lux.' WHERE `room` = "'.$room.'"';
					$query = mysqli_query($db,$sql);	
					$dataReceived = TRUE;

				}
			}
			
		}
	}

mysqli_close($db);
unset($sql, $db, $ctx, $query, $data, $attemptCount, $dataReceived, $lux);
echo "log_lux.php end <br>";

?>