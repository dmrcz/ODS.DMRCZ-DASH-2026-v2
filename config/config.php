<?php

define("MMDVMLOGPATH", "/var/log/pi-star");
define("MMDVMLOGPREFIX", "MMDVM");
define("YSFGATEWAYLOGPATH", "/var/log/pi-star");
define("YSFGATEWAYLOGPREFIX", "YSFGateway");
define("YSFGATEWAYINIPATH", "/etc");
define("YSFGATEWAYINIFILENAME", "ysfgateway");
define("P25GATEWAYLOGPATH", "/var/log/pi-star");
define("P25GATEWAYLOGPREFIX", "P25Gateway");
define("P25GATEWAYINIPATH", "/etc");
define("P25GATEWAYINIFILENAME", "p25gateway");
define("LINKLOGPATH", "/var/log/pi-star");

$config_file = '/etc/WPSD-Dashboard-Config.ini';

if (file_exists($config_file)) {
    $config = parse_ini_file($config_file, true);

    $callsign = $config['WPSD']['Callsign'] ?? 'M1ABC';

    if (!empty($config['WPSD']['Timezone'])) {
        date_default_timezone_set($config['WPSD']['Timezone']);
    } else {
        date_default_timezone_set('UTC');
    }

    if (isset($config['WPSD']['TimeFormat'])) {
        define('TIME_FORMAT', $config['WPSD']['TimeFormat']);
    } else {
        define('TIME_FORMAT', '24');
    }

    if (isset($config['WPSD']['UpdateNotifier'])) {
        define('AUTO_UPDATE_CHECK', $config['WPSD']['UpdateNotifier'] === '1' ? 'true' : 'false');
    } else {
        define('AUTO_UPDATE_CHECK', 'true');
    }

    if (isset($config['WPSD']['OptIntoUserMap'])) {
        define('MAP_OPTED', $config['WPSD']['OptIntoUserMap'] === '1' ? 'true' : 'false');
    } else {
        define('MAP_OPTED', 'false');
    }

    if (isset($config['WPSD']['OptIntoDiags'])) {
        define('DIAGS_OPTED', $config['WPSD']['OptIntoDiags'] === '1' ? 'true' : 'false');
    } else {
        define('DIAGS_OPTED', 'true');
    }

    $DashLanguage = !empty($config['WPSD']['DashLanguage']) ? $config['WPSD']['DashLanguage'] : 'english_us';
}