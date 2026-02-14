<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

$editorname = 'APRS Hosts';
$configfile = '/root/APRSHosts.txt';
$tempfile = '/tmp/siKHha43QJDf6b1.tmp';

// Create empty host file if we don't have one
$cmdresult = exec('sudo test -s /root/APRSHosts.txt', $dummyoutput, $retvalue);
if ($retvalue != 0) {
    exec('sudo echo "create APRSHosts.txt" >> /tmp/debug.txt');
    exec('sudo touch /tmp/siKHha43QJDf6b1.tmp');
    exec('sudo chown www-data:www-data /tmp/siKHha43QJDf6b1.tmp');
    exec('echo "#	   Custom APRS Servers    #" >> /tmp/siKHha43QJDf6b1.tmp');
    exec('echo "###############################" >> /tmp/siKHha43QJDf6b1.tmp');
    exec('echo "#	  host:port;server_name   #" >> /tmp/siKHha43QJDf6b1.tmp');
    exec('echo "###############################" >> /tmp/siKHha43QJDf6b1.tmp');
    exec('sudo mv /tmp/siKHha43QJDf6b1.tmp /root/APRSHosts.txt');
    exec('sudo chmod 644 /root/APRSHosts.txt');
    exec('sudo chown root:root /root/APRSHosts.txt');
}

require_once('fulledit_template.php');