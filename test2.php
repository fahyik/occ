<?php

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

$data = array();

foreach ($dsid_map as $room) {
	$data[] = $room;
}
var_dump ($data);


?>