<?php
/**************************************************************************
 * Bootstrap, is called by dSS motion sensor                              *
 *                                                                        *
 **************************************************************************/
 
 // Log occupancy
include('log_occupancy.php');                                               // log motion sensor state into MySQL table 'log_occupancy'

// Trigger control script
include('lib_bundle.php');                                                  // include bundle class
$myRPI  = new rpiBundleClass;
$rpi_ip = $myRPI->get_ip();
file_get_contents("http://$rpi_ip/control/control.php?trigger=motion");     // call controller on RPi
?>