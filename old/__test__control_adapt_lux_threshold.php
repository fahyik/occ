<?php

include('filter_log_button.php');

if($data_filtered)
{
	include('control_adapt_lux.th.php');
}

unset($data_filtered);
echo "control_adapt_lux_threshold.php end<br>";

?>