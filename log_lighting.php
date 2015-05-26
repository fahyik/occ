<?php

/*******************************************
 * log of lighting (only main FL is logged *
 *******************************************/
//To Do:
//  Mehere Leuchten pro Raum -> unsauber
//  bundle class integrieren
include('addon_multirequests.php');

$ctx = stream_context_create(array('http' => array('timeout' => 5))); // 5 second timeout
$token_check = FALSE;

while ($token_check != TRUE)
		{
			$token_data = file_get_contents('https://10.200.186.172:8080/json/'.
			'system/loginApplication?loginToken=17e329deefdc8ed1b0a35416081b14'.
			'24d18783c69e22a3b2a9805f1d5a8a708d', FALSE, $ctx);
			
			if ($token_data !==  FALSE)
			{
				$obj = json_decode($token_data, TRUE);
				$token_check = TRUE;
			}
			else
			{
				$attemptCount += 1;
				sleep(1);
				if ($attemptCount > 4)                         // after 5 attempts: abort and set lux value to error code -3
				{
					$token = -1;
					$token_check = TRUE;
					//echo $i."\n";
				}
			}
			
		}

$obj = json_decode($token_data,true);                                      // extract session token. true sets it to assoc array.
$token =  $obj['result']['token'];

echo "token is $token <br>";

$db = mysqli_connect('localhost','root','root','hpzcontrol');
$sql = 'INSERT INTO log_lighting (`G22`,`G23`,`RPi_0`, `G24`,`G25.1`,`G25.2`,`G26.1`,`G26.2`,`G27W`,`G27F`, `G31.1`, `G31.2`) SELECT `G22`,`G23`,`RPi_0`, `G24`,`G25.1`,`G25.2`,`G26.1`,`G26.2`,`G27W`,`G27F`, `G31.1`, `G31.2` FROM log_lighting ORDER BY `id` DESC LIMIT 1';
$query = mysqli_query($db,$sql);
mysqli_close($db);

$room_list = [
"RPi_0",
"G27F",
"G27W",
"G23",
"G24",
"G26.1",
"G25.2",
"G25.1",
"G22",
"G31.1",
"G26.2",
"G31.2",
];

$url = array();

foreach ($room_list as $room) {
	echo $url[] = "http://localhost/get_light_state.php?token=".$token."&room=".$room;
	echo "<br>";
}

$request = multiRequest($url);
print_r($request);

echo "log_lighting.php end <br>";                                    // output for debugging purposes

?>