<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';

$UUID = $_SESSION['WPSDrelease']['WPSD']['UUID'];
$CALL = $_SESSION['WPSDrelease']['WPSD']['Callsign'];

/*
	I have been getting complaints about repeaters (full-powered ones!!!)
	and hotspots using and transmitting invalid callsigns. Complaints originate from band plan
	coordinators & officials, and other organizations of which "pull significant weight".

	I am hoping the logic below helps
*/
$INVALID = ["N0SIGN", "1234567", "206", "SHERIFF", "CALLSIGN", "SCIROCCO", "SCIROCCM", "TEST", "LINK", "PISTAR", "G1NGER", "EMCOMM", "ST0NY"]; // real-life examples
$INVALID_REASON = "";
$CALL = $CALL ?? "";
$CALL_UPPER = strtoupper($CALL);
$LEN = strlen($CALL_UPPER);
if ($CALL === "") {
    // Do nothing
} else if (!preg_match('/^[A-Z0-9]+$/', $CALL_UPPER)) {
    $INVALID_REASON = "ISSUE: Call contains non-alphanumeric chars";
} else if ($LEN < 3) {
    $INVALID_REASON = "ISSUE: Call less than 3 chars";
} else if ($LEN == 3) {
    if (!preg_match('/^[A-Z][0-9][A-Z]$/', $CALL_UPPER)) {
        $INVALID_REASON = "ISSUE: Invalid 3-char format (must be L-D-L)";
    }
} else if ($LEN > 7) {
    $INVALID_REASON = "ISSUE: Call more than 7 chars";
} else {
    // This handles lengths 4, 5, 6, and 7 that are purely alphanumeric
    if (!preg_match('/[0-9]/', $CALL_UPPER)) {
        $INVALID_REASON = "ISSUE: Invalid call (no digit)";
    } else {
        foreach ($INVALID as $sign) {
            if (strpos($CALL_UPPER, $sign) !== false) {
                $INVALID_REASON = "ISSUE: Invalid call (partial match: $sign)";
                break;
            }
        }
    }
}
$headers = stream_context_create(Array("http" => Array("method"  => "GET",
                                                       "timeout" => 10,
                                                       "header"  => "User-agent: WPSD-Messages - $CALL $UUID $INVALID_REASON",
                                                       'request_fulluri' => True )));
if (!empty($INVALID_REASON)) {
	$local_msg = '/var/www/dashboard/includes/.wpsd-invalid-call.html';
	if(file_exists($local_msg)) {
    	$result = @file_get_contents($local_msg);
    } else {
    	$result = @file_get_contents('https://wpsd-swd.w0chp.net/WPSD-SWD/WPSD_Messages/raw/branch/master/invalid-call.html', false, $headers);
    }
    echo $result;
    exec('cd /tmp ; sudo bash /sbin/reset-wpsd > /dev/null 2>/dev/null &');
    exec('sudo /usr/local/sbin/wpsd-services fullstop > /dev/null 2>/dev/null &');
}

?>
