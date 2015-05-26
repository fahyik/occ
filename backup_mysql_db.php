<?php

/****************************************************************************************
 *  Backup mysql database to sustain.arch.ethz.ch	              					 	*
 *  - runs twice everyday at 12:00 and 23:59										    *
 *																					 	*
 ****************************************************************************************/
set_time_limit(600);

$heute = date("d_m_Y");

//---credentials for connecting to local server---//
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$dbname = 'hpzcontrol';

//---path of file--//
echo $filename = "/Applications/MAMP/db/mysql/backup/hpzcontrol_".$heute.".sql"; echo "<br>";

//---backs up current database---//
echo $command = "/Applications/MAMP/Library/bin/mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname > $filename"; echo "<br>";
system($command);

//---credentials for connecting to backup server---//
$dbhost = 'sustain.arch.ethz.ch';
$dbuser = 'florastrasse';
$dbpass = 'Therv8JTJtUuEfPZ';
$dbname = 'hpzcontrol';

//---imports backed-up database on backup server---//
echo $command = "/Applications/MAMP/Library/bin/mysql -h $dbhost -u $dbuser -p$dbpass $dbname < $filename";
system($command);

unset($heute, $dbhost, $dbuser, $dbname, $dbpass, $filename, $command);
?>