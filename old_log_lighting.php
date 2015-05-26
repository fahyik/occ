<?php

/*******************************************
 * log of lighting (only main FL is logged *
 *******************************************/
//To Do:
//  Mehere Leuchten pro Raum -> unsauber
//  bundle class integrieren

$ctx = stream_context_create(array('http' => array('timeout' => 5))); // 5 second timeout
$token_check = FALSE;

while ($token_check != TRUE)                          // try 10 times to reach the raspberry and get the lux value
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
                                                                     // get lighting data
$dsid = array('3504175fe0000000000187cf',	                      	 // 0  G22 Kitchen
		      '3504175fe000000000005c66', 	                      	 // 1  G23
		      '3504175fe000000000005916',	                      	 // 2  G24 Wall - RPi_0
		      '3504175fe000000000005c64',	                      	 // 3  G24 Window
		      '3504175fe000000000005c81',	                      	 // 4  G25.1
		      '3504175fe0000000000059d2',	                      	 // 5  G25.2
		      '3504175fe000000000005a83',	                      	 // 6  G26.1
		      '3504175fe000000000005a7b',	                      	 // 7  G26.2
		      '3504175fe000000000005a0c',	                      	 // 8  G27 Wall
		      '3504175fe000000000005a28',                     		 // 9  G27 Window
		      '3504175fe0000000000059e8',	                     	 // 10 G31.1
		      '3504175fe000000000005a30');		                     // 11 G31.2

$lightState = array_fill(0,12,-1);

for ($i=0;$i<12;$i++)
{
	if ($token == -1)
	{
		break;
	}
	$str = "https://10.200.186.172:8080/json/device/getState?dsid=".$dsid[$i]."&token=".$token;
	
	echo "string is $str<br>";
	
	$msg = file_get_contents($str);
	
	//sleep(1);  // Take some stress from dSS
	$light = json_decode($msg,true);

	$isOn = $light['result']['isOn'];

	if($isOn)
	{
		$lightState[$i]=1;
	}else{
		$lightState[$i]=0;
	}
	//sleep(1);                                                        // wait some time in order to take some pressure from dss-bandwidth
}

                                                                     // enter into DB
$sql = 'INSERT INTO log_lighting (`G22`,`G23`,`RPi_0`,`G24`,`G25.1`,`G25.2`,`G26.1`,'.
'`G26.2`,`G27W`,`G27F`,`G31.1`, `G31.2`) VALUES ( '. implode(",",$lightState) .')';
$db = mysqli_connect('localhost','root','root','hpzcontrol');
mysqli_query($db,$sql);
mysqli_close($db);

echo "log_lighting.php end <br>";                                    // output for debugging purposes

?>