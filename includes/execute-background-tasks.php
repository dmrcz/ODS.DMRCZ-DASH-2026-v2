<?php

session_name("WPSD_Session");
session_id('wpsdsession');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';

$UUID = $_SESSION['WPSDrelease']['WPSD']['UUID'];
$CALL = $_SESSION['WPSDrelease']['WPSD']['Callsign'];
$UA = "$CALL $UUID";

exec('sudo /usr/local/sbin/.wpsd-background-tasks > /dev/null 2>&1 &');
