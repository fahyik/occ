<?php
echo "room is ";
echo $room  = "G24"; echo "<br>";

$db = mysqli_connect('localhost','root','root','hpzcontrol'); 

$sql = 'SELECT `'.$room.'` FROM log_lux ORDER BY `time` DESC LIMIT 5';
$result = mysqli_query($db,$sql);

$i = 0;
$alpha = 2/(5+1);

while($obj = mysqli_fetch_object($result))
{
	$lux[$i] = (int)$obj->$room;
	$i++;
}

$data['lux'] = ($lux[0] + (1-$alpha)*$lux[1] + pow((1-$alpha),2)*$lux[2] + 
pow((1-$alpha),3)*$lux[3] + pow((1-$alpha),4)*$lux[4]) / (1 + (1-$alpha) + 
pow((1-$alpha),2) + pow((1-$alpha),3) + pow((1-$alpha),4));


$sql = 'SELECT `'.$room.'` FROM log_lux ORDER BY `time` DESC LIMIT 5';
$result = mysqli_query($db,$sql);
$lux = 0;
$i = 0;
while($obj = mysqli_fetch_object($result))
{
	$lux = $lux + (int)$obj->$room;
	$i++;
}
echo "SMA = ";
echo $data['lux'] = (int)($lux/$i);
unset($lux, $i);

unset($lux, $i, $alpha);

mysqli_close($db);
?>