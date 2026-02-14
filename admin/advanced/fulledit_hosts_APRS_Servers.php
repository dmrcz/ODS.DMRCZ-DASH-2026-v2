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

$editorname = 'APRS Servers';
$configfile = '/root/aprs_servers.json';
$tempfile = '/tmp/hY6Q1UKUVlTj9.tmp';

// Create empty json file if we don't have one
$cmdresult = exec('sudo test -s /root/aprs_servers.json', $dummyoutput, $retvalue);
if ($retvalue != 0) {
    exec('sudo echo "create aprs_servers.json" >> /tmp/debug.txt');
    exec('sudo touch /tmp/hY6Q1UKUVlTj9.tmp');
    exec('sudo chown www-data:www-data /tmp/hY6Q1UKUVlTj9.tmp');
    exec('echo "# [" >> /tmp/hY6Q1UKUVlTj9.tmp');
    exec('echo "#   {" >> /tmp/hY6Q1UKUVlTj9.tmp');
    exec('echo "#     \"id\": \"<server_id>\"," >> /tmp/hY6Q1UKUVlTj9.tmp');
    exec('echo "#     \"fqdn\": \"<server_domain>\"" >> /tmp/hY6Q1UKUVlTj9.tmp');
    exec('echo "#   }" >> /tmp/hY6Q1UKUVlTj9.tmp');
    exec('echo "# ]" >> /tmp/hY6Q1UKUVlTj9.tmp');
    exec('sudo mv /tmp/hY6Q1UKUVlTj9.tmp /root/aprs_servers.json');
    exec('sudo chmod 644 /root/aprs_servers.json');
    exec('sudo chown root:root /root/aprs_servers.json');
}

require_once('fulledit_template.php');