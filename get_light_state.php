<?php
	$room = $_GET["room"];
	$token = $_GET["token"];
	
	
	$dsid_map = [
	"RPi_0"	=> "3504175fe000000000005916",
    "G27F"	=> "3504175fe000000000005a28",
    "G27W"	=> "3504175fe000000000005a0c",
    "G23"	=> "3504175fe000000000005c66",
    "G24"	=> "3504175fe000000000005c64",
    "G26.1"	=> "3504175fe000000000005a83",
    "G25.2"	=> "3504175fe0000000000059d2",
    "G25.1"	=> "3504175fe000000000005c81",
    "G22"	=> "3504175fe0000000000187cf",
    "G31.1"	=> "3504175fe0000000000059e8",
    "G26.2"	=> "3504175fe000000000005a7b",
    "G31.2"	=> "3504175fe000000000005a30",
	];

	$dsid = $dsid_map[$room];
	
	$callDSS = json_decode(file_get_contents("https://10.200.186.172:8080/json/device/getState?dsid=".$dsid."&token=".$token));

	if ($callDSS->result->isOn) {
		$light = 1;
	}
	else {
		$light = 0;
	}
	
	echo "\n";
   	$db = mysqli_connect('129.132.32.187','root','root','hpzcontrol');
	echo $sql = 'UPDATE log_lighting SET `'.$room.'` = '.$light.' ORDER BY `id` DESC LIMIT 1';
	$query = mysqli_query($db,$sql);

	echo "\n";
	if ($room = "G27F" || $room = "G27W") {
		$room = "G27";
	}
	echo $sql = 'UPDATE live_status SET `lights` = '.$light.' WHERE `room` = "'.$room.'"';
	$query = mysqli_query($db,$sql);	
	
	echo "\n";
	
	mysqli_close($db);
?>