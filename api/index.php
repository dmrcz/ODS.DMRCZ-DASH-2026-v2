<?php

/*
//
// Simple API to return a json array of last heard rows from the MMDVM...
//
// Returns the n most-recent transmissions based on `limit` query string
// parameter, up to the maximum in the existing $lastHeard array...
//   e.g. http://wpsd.local/api/?limit=10
//   OR: use "limit=1" for Live Caller-like functionality and increased perf.
//
// Optional: `names` query string parameter (true/false) to include/exclude
// name lookup. Defaults to true.
//   e.g. http://wpsd.local/api/?limit=10&names=false
//
// Optional: `country` query string parameter (true/false) to include/exclude
// country name lookup. Defaults to true.
//   e.g. http://wpsd.local/api/?limit=10&country=false
//
*/

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';

header('Content-type: application/json');
$json_response = array();

$trans_history_count = count($lastHeard);

$num_transmissions = isset($_GET['limit']) ? intval($_GET['limit']) : $trans_history_count;
$transmissions = array_slice($lastHeard, 0, min($num_transmissions, $trans_history_count));

// Check for names parameter, default to true
$include_names = true;
if (isset($_GET['names'])) {
    if ($_GET['names'] === 'false' || $_GET['names'] === '0') {
        $include_names = false;
    }
}

// Check for country parameter, default to true
$include_country = true;
if (isset($_GET['country'])) {
    if ($_GET['country'] === 'false' || $_GET['country'] === '0') {
        $include_country = false;
    }
}

// Initialize GeoLookup if country lookup is enabled
$Flags = null;
if ($include_country) {
    if (!class_exists('xGeoLookup')) require_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.GeoLookup.php');
    $Flags = new xGeoLookup();
    $Flags->SetFlagFile("/usr/local/etc/countries.json");
    $Flags->LoadFlags();
}

foreach ($transmissions as $transmission) {
    $transmission_json = array();
    $transmission_json['time_utc'] = trim($transmission[0]);
    $transmission_json['mode'] = trim($transmission[1]);
    $transmission_json['callsign'] = trim($transmission[2]);

    if ($include_names) {
        // Name Lookup Logic
        $name = "";
        $lookupCall = $transmission_json['callsign'];
        
        // Clean up callsign for lookup (remove suffixes)
        if (strpos($lookupCall, "-") !== false) { $lookupCall = substr($lookupCall, 0, strpos($lookupCall, "-")); }
        if (strpos($lookupCall, " ") !== false) { $lookupCall = substr($lookupCall, 0, strpos($lookupCall, " ")); }

        // Proceed if not a numeric ID
        if (!is_numeric($lookupCall)) {
            $found = false;

            // 1. Search stripped.csv
            $dbFile = "/usr/local/etc/stripped.csv";
            if (file_exists($dbFile)) {
                $cmd = "grep -w ".escapeshellarg($lookupCall)." ".$dbFile." | head -1";
                $line = exec($cmd);
                if ($line) {
                    $parts = explode(",", $line);
                    if (isset($parts[2])) {
                        $name = sentence_cap(" ", $parts[2]);
                        $found = true;
                    }
                }
            }

            // 2. If not found, search NXDN.csv
            if (!$found) {
                $dbFile = "/usr/local/etc/NXDN.csv";
                if (file_exists($dbFile)) {
                    $cmd = "grep -w ".escapeshellarg($lookupCall)." ".$dbFile." | head -1";
                    $line = exec($cmd);
                    if ($line) {
                        $parts = explode(",", $line);
                        if (isset($parts[2])) {
                            $name = sentence_cap(" ", $parts[2]);
                            $found = true;
                        }
                    }
                }
            }

            // 3. If not in any local CSV, use the API lookup function
            if (!$found) {
                $name = getName($lookupCall);
            }
        }
        $transmission_json['name'] = $name;
    }

    if ($include_country) {
        // Country Lookup Logic
        // GetFlag returns [ImageName, CountryName]. We only use index 1 (CountryName).
        $geoResult = $Flags->GetFlag($transmission_json['callsign']);
        $transmission_json['country'] = $geoResult[1];
    }

    $transmission_json['callsign_suffix'] = trim($transmission[3]);
    $transmission_json['target'] = trim($transmission[4]);
    $transmission_json['src'] = trim($transmission[5]);
    $transmission_json['duration'] = trim($transmission[6]);
    $transmission_json['loss'] = trim($transmission[7]);

    $json_response[] = $transmission_json;
}
echo json_encode($json_response);
exit();
?>
