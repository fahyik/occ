<?php
/**********************************************************************
 * Bootstrap for all cronjobs  on iMac server                         *
 * This script gets executed by cronjob on iMac Server every minute   *
 * Also see: crontab -l                                               *
 *           crontab -e                                               *
 **********************************************************************/
set_time_limit(0);                               // no execution time limit, because a lot of dSS accesses with sleep(1), takes a lot of time
$now = date("H:i");                              // get current time


include('addon_multirequests.php');

$url = array(
	"http://localhost/log_lux.php",
	"http://localhost/log_lighting.php",
	//"http://localhost/log_lighting_new.php",
);

$request = multiRequest($url);


                                                 // This will be executed only once a day
if ($now=="00:05")
{
	//include("control_mode.php");
	include("control_adapt_td.php");
}

if ($now=="23:59")
{
	include("calculate_mode_days.php");
	include("calculate_mode_days_weekdays.php");
}

	                                            // No need to include log_occupancy.php or log_lux_threshold.php.
                                                 // These scripts get triggered by dSS (->trigger_motion_sensor.php, ->trigger_push_button.php)
echo "cron.php end";                             // Output for debugging

?>