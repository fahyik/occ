<?php

set_time_limit(0);
$hour = date("G");

echo "current hour is $hour"."<br>";

$db = mysqli_connect('localhost','root','root','hpzcontrol');
$sql = 'SELECT `id` FROM control_lux_threshold ORDER BY `id` DESC LIMIT 1';
$result = mysqli_query($db,$sql);
$obj = mysqli_fetch_object($result);
$index = (int)$obj->id;
mysqli_close($db);

if ($index <= 210)
{
	if ($hour >= 8 && $hour <= 22)
	{
		include("control_adapt_lux_threshold.php");
	}
	else
	{
		echo "within 2 weeks, but not between 08 and 22 hours<br>";
	}
}

else
{
	if ($hour == 0 || $hour == 8 || $hour == 12 || $hour == 16 || $hour == 20)
	{
		include("control_adapt_lux_threshold.php");
	}
	else
	{
		echo "after 2 weeks, but not on the specified hours<br>";
	}
}

//no need to close mysqli because script unsets $db.
echo "cron2.php end";


?>