<?php

// Load WPSD Core Includes
include('wifi/phpincs.php');

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    unset($_SESSION['CSSConfigs']);
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'] . '/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'] . '/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config/language.php';        // Translation Code
    checkSessionValidity();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/version.php';

// Defaults
$backgroundContent = "#1a1a1a";
$textContent = "inherit";
$textContent = "#ffffff";
$tableBorderColor = "#333";
$backgroundServiceCellActiveColor = "#27ae60";
$backgroundServiceCellInactiveColor = "#8C0C26";
$tableRowOddBg = "#333";
$tableRowEvenBg = "#444";
$textLinks = "#2196F3";

if (isset($_SESSION['CSSConfigs'])) {
    if (isset($_SESSION['CSSConfigs']['Background']['ContentColor'])) $backgroundContent = $_SESSION['CSSConfigs']['Background']['ContentColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['ServiceCellActiveColor'])) $backgroundServiceCellActiveColor = $_SESSION['CSSConfigs']['Background']['ServiceCellActiveColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['ServiceCellInactiveColor'])) $backgroundServiceCellInactiveColor = $_SESSION['CSSConfigs']['Background']['ServiceCellInactiveColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['TableRowBgOddColor'])) $tableRowOddBg = $_SESSION['CSSConfigs']['Background']['TableRowBgOddColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'])) $tableRowEvenBg = $_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['TextColor'])) $textContent = $_SESSION['CSSConfigs']['Text']['TextColor'];
    if (isset($_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'])) $tableBorderColor = $_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['TextLinkColor'])) $textLinks = $_SESSION['CSSConfigs']['Text']['TextLinkColor'];
}

// --- DATA GATHERING ---
$strIPAddress = "N/A";
$strNetMask = "N/A";
$strHWAddress = "N/A";
$strRxPackets = "0";
$strRxBytes = "0";
$strTxPackets = "0";
$strTxBytes = "0";
$strSSID = "N/A";
$strBSSID = "N/A";
$strBitrate = "N/A";
$strTxPower = "N/A";
$strLinkQuality = "N/A";
$strSignalLevel = "N/A";
$strWifiFreq = "N/A";
$strWifiChan = "N/A";
$isActive = false;

exec('ifconfig wlan0', $return);
exec('iwconfig wlan0', $return);
exec('iw dev wlan0 link', $return);
$strWlan0 = implode(" ", $return);
$strWlan0 = preg_replace('/\s\s+/', ' ', $strWlan0);

if (strpos($strWlan0, 'HWaddr') !== false) {
    preg_match('/HWaddr ([0-9a-f:]+)/i', $strWlan0, $result);
    $strHWAddress = $result[1];
} elseif (strpos($strWlan0, 'ether') !== false) {
    preg_match('/ether ([0-9a-f:]+)/i', $strWlan0, $result);
    $strHWAddress = $result[1];
}

if (strpos($strWlan0, "UP") !== false && strpos($strWlan0, "RUNNING") !== false) {
    $isActive = true;
    if (strpos($strWlan0, 'inet addr:') !== false) {
        preg_match('/inet addr:([0-9.]+)/i', $strWlan0, $result);
        $strIPAddress = $result[1];
    } elseif (preg_match('/inet ([0-9.]+)/i', $strWlan0, $result)) {
        $strIPAddress = $result[1];
    }

    if (strpos($strWlan0, 'Mask:') !== false) {
        preg_match('/Mask:([0-9.]+)/i', $strWlan0, $result);
        $strNetMask = $result[1];
    } elseif (preg_match('/netmask ([0-9.]+)/i', $strWlan0, $result)) {
        $strNetMask = $result[1];
    }

    preg_match('/RX packets.(\d+)/', $strWlan0, $result);
    $strRxPackets = $result[1] ?? 0;
    preg_match('/TX packets.(\d+)/', $strWlan0, $result);
    $strTxPackets = $result[1] ?? 0;
    if (strpos($strWlan0, 'RX bytes') !== false) {
        preg_match('/RX [B|b]ytes:(\d+ \(\d+.\d+ [K|M|G]iB\))/i', $strWlan0, $result);
        $strRxBytes = $result[1] ?? 0;
    } else {
        preg_match('/RX packets \d+ bytes (\d+ \(\d+.\d+ [K|M|G]iB\))/i', $strWlan0, $result);
        $strRxBytes = $result[1] ?? 0;
    }
    if (strpos($strWlan0, 'TX bytes') !== false) {
        preg_match('/TX [B|b]ytes:(\d+ \(\d+.\d+ [K|M|G]iB\))/i', $strWlan0, $result);
        $strTxBytes = $result[1] ?? 0;
    } else {
        preg_match('/TX packets \d+ bytes (\d+ \(\d+.\d+ [K|M|G]iB\))/i', $strWlan0, $result);
        $strTxBytes = $result[1] ?? 0;
    }

    if (preg_match('/Access Point: ([0-9a-f:]+)/i', $strWlan0, $result)) {
        $strBSSID = $result[1];
    }
    if (preg_match('/Connected to\ ([0-9a-f:]+)/i', $strWlan0, $result)) {
        $strBSSID = $result[1];
    }
    if (preg_match('/Bit Rate([=:0-9\.]+ Mb\/s)/i', $strWlan0, $result)) {
        $strBitrate = str_replace([':', '='], '', $result[1]);
    }
    if (preg_match('/tx bitrate:\ ([0-9\.]+ Mbit\/s)/i', $strWlan0, $result)) {
        $strBitrate = str_replace([':', '='], '', $result[1]);
    }
    if (preg_match('/Tx-Power=([0-9]+ dBm)/i', $strWlan0, $result)) {
        $strTxPower = $result[1];
    }
    if (preg_match('/ESSID:\"([a-zA-Z0-9-_.\s]+)\"/i', $strWlan0, $result)) {
        $strSSID = str_replace('"', '', $result[1]);
    }
    if (preg_match('/SSID:\ ([a-zA-Z0-9-_.\s]+)/i', $strWlan0, $result)) {
        $strSSID = str_replace(' freq', '', $result[1]);
    }

    if (preg_match('/Link Quality=([0-9]+\/[0-9]+)/i', $strWlan0, $result)) {
        $strLinkQuality = $result[1];
        if (strpos($strLinkQuality, "/")) {
            $arrLinkQuality = explode("/", $strLinkQuality);
            if ($arrLinkQuality[1] > 0) {
                $strLinkQuality = number_format(($arrLinkQuality[0] / $arrLinkQuality[1]) * 100) . "%";
            }
        }
    }
    if (preg_match('/Signal Level=(-[0-9]+ dBm)/i', $strWlan0, $result)) {
        $strSignalLevel = $result[1];
    }
    if (preg_match('/Signal Level=([0-9]+\/[0-9]+)/i', $strWlan0, $result)) {
        $strSignalLevel = $result[1];
    }
    if (preg_match('/Frequency:([0-9.]+ GHz)/i', $strWlan0, $result)) {
        $strWifiFreq = $result[1];
        $strWifiChan = str_replace(" GHz", "", $strWifiFreq);
        $strWifiChan = str_replace(".", "", $strWifiChan);
        $strWifiChan = ConvertToChannel(str_replace(".", "", $strWifiChan));
    }
}

// --- HELPERS ---
function executeCommand($command)
{
    $output = shell_exec($command);
    return $output;
}

function getWiFiBand($channel)
{
    $wifiBands = array('2.4 GHz' => range(1, 14), '5 GHz' => range(36, 165));
    foreach ($wifiBands as $band => $channels) {
        if (in_array($channel, $channels)) {
            return $band;
        }
    }
    return 'Unknown';
}

function signalStrengthBars($signalStrength)
{
    $bars = "";
    $maxStrength = 100;
    $numBars = 5;
    $strengthPerBar = $maxStrength / $numBars;
    $filledBars = round($signalStrength / $strengthPerBar);
    $color = "#2ecc71";
    if ($signalStrength <= 60) {
        $color = "#e67e22";
    }
    if ($signalStrength <= 30) {
        $color = "#e74c3c";
    }
    for ($i = 0; $i < $filledBars; $i++) {
        $bars .= "<span style='color:$color;'>&#x2588;</span>";
    }
    for ($i = $filledBars; $i < $numBars; $i++) {
        $bars .= "<span style='color:#555;'>&#x2588;</span>";
    }
    return $bars;
}

function parseNetworkInfo($line)
{
    $parts = explode(':', $line);
    if (count($parts) < 4) {
        return null;
    }
    return [
        'ssid' => trim($parts[0]),
        'signalStrength' => trim($parts[1]),
        'channel' => trim($parts[2]),
        'securityType' => trim($parts[3])
    ];
}

function getAvailableRegulatoryDomains()
{
    $crdaFile = '/usr/local/etc/regulatory.txt';
    if (file_exists($crdaFile)) {
        $fileContent = file_get_contents($crdaFile);
        preg_match_all('/country ([A-Z]+)/', $fileContent, $matches);
        return isset($matches[1]) ? array_unique($matches[1]) : [];
    }
    return [];
}

$availableDomains = getAvailableRegulatoryDomains();
$currentGlobalDomain = "";
$fileContent = file_get_contents('/boot/firmware/cmdline.txt');
if (preg_match('/\bcfg80211\.ieee80211_regdom=([^ ]+)/', $fileContent, $matches)) {
    $currentGlobalDomain = $matches[1];
}

// --- ACTION HANDLING ---
$msg = "";
$scanResults = [];
$showScan = false;

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add' && isset($_POST['ssid'], $_POST['passphrase'])) {
        $ssid = $_POST['ssid'];
        $pass = $_POST['passphrase'];
        executeCommand("sudo nmcli connection add type wifi con-name " . escapeshellarg($ssid) . " ifname '*' ssid " . escapeshellarg($ssid) . " wifi-sec.key-mgmt wpa-psk wifi-sec.psk " . escapeshellarg($pass) . " ; sleep 1");
        $msg = "Connection added. Please wait for refresh.";
    } elseif ($action == 'delete' && isset($_POST['connection'])) {
        executeCommand("sudo nmcli connection delete " . escapeshellarg(trim($_POST['connection'])) . "; sleep 1");
        $msg = "Connection deleted.";
    } elseif ($action == 'connect' && isset($_POST['connection'])) {
        executeCommand("sudo nmcli connection up " . escapeshellarg(trim($_POST['connection'])) . "; sleep 2");
        $msg = "Connecting...";
    } elseif ($action == 'set_domain' && isset($_POST['regulatory_domain'])) {
        $dom = $_POST['regulatory_domain'];
        executeCommand("sudo iw reg set " . escapeshellarg($dom));
        executeCommand("sudo sed -i 's/cfg80211\.ieee80211_regdom=.*/cfg80211.ieee80211_regdom=" . $dom . "/' /boot/firmware/cmdline.txt ; sleep 1");
        $msg = "WiFi Country Updated.";
    } elseif ($action == 'scan') {
        $showScan = true;
        $out = shell_exec("sudo nmcli -t -f SSID,SIGNAL,CHAN,SECURITY device wifi list --rescan yes");
        $lines = explode("\n", trim($out));
        foreach ($lines as $l) {
            $info = parseNetworkInfo($l);
            if ($info && !empty($info['ssid']) && $info['ssid'] !== '--') {
                $scanResults[] = $info;
            }
        }
    } elseif ($action == 'upload' && isset($_FILES['connection_file'])) {
        // --- FILE UPLOAD LOGIC ---
        $file = $_FILES['connection_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            if ($ext === 'nmconnection') {
                $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
                // Sanitize Filename: Allow only A-Z, a-z, 0-9, dash, underscore
                $safeName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $baseName);
                if (empty($safeName)) {
                    $safeName = 'imported-connection-' . time();
                }
                $finalName = $safeName . '.' . $ext;
                $tmpPath = '/tmp/' . $finalName;
                $destPath = '/etc/NetworkManager/system-connections/' . $finalName;

                if (move_uploaded_file($file['tmp_name'], $tmpPath)) {
                    // Move to destination and set permissions via sudo
                    executeCommand("sudo mv " . escapeshellarg($tmpPath) . " " . escapeshellarg($destPath));
                    executeCommand("sudo chown root:root " . escapeshellarg($destPath));
                    executeCommand("sudo chmod 600 " . escapeshellarg($destPath));
                    executeCommand("sudo nmcli connection reload"); // Reload NM to recognize the new file
                    $msg = "Profile imported successfully: " . htmlspecialchars($finalName);
                } else {
                    $msg = "Failed to move uploaded file.";
                }
            } else {
                $msg = "Invalid file type. Only .nmconnection files are allowed.";
            }
        } else {
            $msg = "Upload error: Code " . $file['error'];
        }
    }
    elseif ($action == 'priority_up' && isset($_POST['connection'])) {
        $conn = trim($_POST['connection']);
        // Get current priority and increment
        $curr = shell_exec("nmcli -g connection.autoconnect-priority connection show " . escapeshellarg($conn));
        $new = intval(trim($curr)) + 1;
        executeCommand("sudo nmcli connection modify " . escapeshellarg($conn) . " connection.autoconnect-priority $new");
        $msg = "Priority increased for $conn.";
    } 
    elseif ($action == 'priority_down' && isset($_POST['connection'])) {
        $conn = trim($_POST['connection']);
        // Get current priority and decrement
        $curr = shell_exec("nmcli -g connection.autoconnect-priority connection show " . escapeshellarg($conn));
        $new = intval(trim($curr)) - 1;
        executeCommand("sudo nmcli connection modify " . escapeshellarg($conn) . " connection.autoconnect-priority $new");
        $msg = "Priority decreased for $conn.";
    }
}

// Get Configured Connections & Sort by Priority
shell_exec('sudo nmcli connection reload');
$cmd = 'sudo nmcli -t -f UUID,TYPE,ACTIVE,AUTOCONNECT-PRIORITY,NAME connection show';
$out = shell_exec($cmd);
$lines = explode("\n", trim($out));
$validConnections = [];

foreach ($lines as $line) {
    if (empty(trim($line))) continue;
    $parts = explode(':', $line);
    // Ensure we have enough parts (UUID:TYPE:ACTIVE:PRIO:NAME)
    if (count($parts) >= 5) {
        $active = $parts[2];
        $prio = intval($parts[3]);
        // Reassemble Name (in case it contains colons)
        $name = implode(':', array_slice($parts, 4));
        
        if ($parts[1] === '802-11-wireless' || $parts[1] === 'wifi') {
            $validConnections[] = [
                'name' => $name, 
                'active' => ($active === 'yes'),
                'priority' => $prio
            ];
        }
    }
}

// Sort: Highest Priority First
usort($validConnections, function($a, $b) {
    if ($a['priority'] === $b['priority']) {
        return strcasecmp($a['name'], $b['name']); // Tie-break alphabetically
    }
    return $b['priority'] - $a['priority'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
    <title>WPSD Dashboard - WiFi Connection Manager</title>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/config/browserdetect.php'; ?>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/functions.js"></script>
    <script type="text/javascript" src="/admin/wifi/functions.js"></script>
    <style>
        /* CORE LAYOUT */
        .profile-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-card {
            width: 100%;
            background-color: <?php echo $backgroundContent; ?>;
            border: 1px solid <?php echo $tableBorderColor; ?>;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            color: <?php echo $textContent; ?>;
        }

        .profile-header {
            padding: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
            text-align: center;
            background-color: <?php echo $backgroundBanners; ?>;
            color: <?php echo $textBanners; ?>;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .profile-body {
            padding: 20px;
        }

        /* TABLES - STRICT LEFT */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left !important;
            border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
            padding: 10px;
            vertical-align: middle;
        }

        th {
            opacity: 0.8;
            font-size: 0.85em;
            text-transform: uppercase;
            border-bottom: 2px solid <?php echo $tableBorderColor; ?>;
            font-family: 'Source Sans Pro', sans-serif;
            color: <?php echo $textContent; ?>;
        }

        td {
            font-family: 'Inconsolata', monospace;
            font-size: 1.05em;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Forced Row Colors with Smart Contrast Calculation */
        tr:nth-child(odd) {
            background-color: <?php echo $tableRowOddBg; ?> !important;
            color: inherit !important;
        }
        tr:nth-child(odd) td {
            color: inherit !important;
        }

        tr:nth-child(even) {
            background-color: <?php echo $tableRowEvenBg; ?> !important;
            color: inherit !important;
        }
        tr:nth-child(even) td {
            color: inherit !important;
        }

        /* BUTTONS */
        .profile-btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            height: 32px; /* Fixed height for all buttons */
            padding: 0 16px;
            background-color: <?php echo $backgroundNavbar; ?>;
            color: <?php echo $textNavbar; ?>;
            border: 1px solid <?php echo $tableBorderColor; ?>;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 0.85em;
            transition: all 0.2s;
            font-family: 'Source Sans Pro', sans-serif;
            box-sizing: border-box;
            vertical-align: middle;
        }

        .profile-btn:hover {
            background-color: <?php echo $backgroundNavbarHover; ?>;
        }

        .btn-small {
            margin: 0 2px;
            /* Icon buttons need less padding to look square-ish but same height */
        }
        
        .btn-icon {
            padding: 0 12px;
        }

        .btn-connect {
            color: #2ecc71;
            border-color: #2ecc71;
            background: transparent;
        }

        .btn-connect:hover {
            background: #2ecc71;
            color: white;
        }

        .btn-danger {
            color: #e74c3c;
            border-color: #e74c3c;
            background: transparent;
        }

        .btn-danger:hover {
            background: #e74c3c;
            color: white;
        }

        /* INPUTS - Forced High Contrast */
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            background-color: <?php echo $tableRowEvenBg; ?> !important;
            border: 1px solid <?php echo $tableBorderColor; ?>;
            color: <?php echo $textContent; ?> !important;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: 'Inconsolata', monospace;
        }

        /* Input Validation Fix */
        input.legacy-val {
            background-color: #fff !important;
            color: #000 !important;
            border: 1px solid #ccc;
        }

        /* UI HELPERS */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7em;
            text-transform: uppercase;
            font-weight: bold;
            margin-left: 5px;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .badge-active {
            background-color: <?php echo $backgroundModeCellActiveColor; ?>;
            color: <?php echo $textModeCellActiveColor; ?>;
            border: 1px solid <?php echo $textModeCellActiveColor; ?>;
        }

        .badge-inactive {
            background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
            color: <?php echo $textModeCellInactiveColor; ?>;
            border: 1px solid <?php echo $textModeCellInactiveColor; ?>;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed <?php echo $tableBorderColor; ?>;
            padding: 6px 0;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.8;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .stat-value {
            font-family: 'Inconsolata', monospace;
            font-weight: bold;
        }

        .stat-header {
            font-size: 0.85em;
            text-transform: uppercase;
            color: <?php echo $textModeCellActiveColor; ?>;
            border-bottom: 2px solid <?php echo $tableBorderColor; ?>;
            margin-bottom: 10px;
            padding-bottom: 5px;
            font-weight: bold;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .scan-results {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
            border-top: 1px solid <?php echo $tableBorderColor; ?>;
        }

        .scan-results::-webkit-scrollbar {
            width: 8px;
        }

        .scan-results::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .scan-results::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .alert {
            padding: 15px;
            background: <?php echo $backgroundModeCellActiveColor; ?>;
            border: 1px solid <?php echo $textModeCellActiveColor; ?>;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            color: <?php echo $textContent; ?>;
            width: 100%;
            box-sizing: border-box;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .manual-form {
            background: rgba(0, 0, 0, 0.1);
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            border: 1px solid <?php echo $tableBorderColor; ?>;
        }

        /* CUSTOM FILE UPLOAD STYLE */
        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px dashed <?php echo $tableBorderColor; ?>;
            border-radius: 6px;
            background-color: rgba(0,0,0,0.2);
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
            font-family: 'Source Sans Pro', sans-serif;
            color: <?php echo $textContent; ?>;
        }
        .file-upload-label:hover {
            border-color: <?php echo $textModeCellActiveColor; ?>;
            background-color: rgba(0,0,0,0.3);
        }
        .inputfile {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        /* MODAL */
        #scanning-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            backdrop-filter: blur(4px);
        }

        .scan-box {
            background: <?php echo $backgroundContent; ?>;
            border: 1px solid <?php echo $tableBorderColor; ?>;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            color: <?php echo $textContent; ?>;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            max-width: 400px;
            width: 80%;
        }

        .scan-box h3 {
            margin-top: 0;
            font-weight: 600;
            font-size: 1.4rem;
            color: <?php echo $textModeCellActiveColor; ?>;
        }

        .spinner {
            margin: 30px auto;
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-top: 5px solid <?php echo $textModeCellActiveColor; ?>;
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fade-in-text {
            opacity: 0.7;
            font-size: 0.95em;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        /* MOBILE */
        .mobile-back-btn {
            display: none;
        }

        @media screen and (max-width: 768px) {
            .noMob, .headerClock, .SmallHeader {
                display: none !important;
            }

            .header h1 {
                font-size: 1.2em;
                margin: 0;
                padding: 0;
                text-align: center;
            }

            .header {
                padding: 10px 0;
            }

            .container {
                text-align: center;
            }

            .content {
                margin: 0;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .profile-wrapper {
                padding: 10px;
            }

            .profile-card {
                border-radius: 4px;
            }

            .mobile-back-btn {
                display: block;
                width: 100%;
                margin-bottom: 15px;
            }

            .mobile-back-btn a {
                display: block;
                width: 100%;
                padding: 12px;
                text-align: center;
                background-color: <?php echo $backgroundNavbar; ?>;
                color: <?php echo $textNavbar; ?>;
                border: 1px solid <?php echo $tableBorderColor; ?>;
                border-radius: 4px;
                text-decoration: none;
                font-weight: bold;
                box-sizing: border-box;
                font-family: 'Source Sans Pro', sans-serif;
            }
        }
    </style>
</head>
<body>

    <div id="scanning-overlay">
        <div class="scan-box">
            <h3>Scanning Networks</h3>
            <div class="spinner"></div>
            <p class="fade-in-text">Please wait while we search for available WiFi networks...</p>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <div class="SmallHeader shLeft noMob">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
            <div class="SmallHeader shRight noMob">
                <div id="CheckUpdate"><?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/checkupdates.php'; ?></div><br />
            </div>
            <h1>WPSD <?php echo __('Dashboard'); ?> - WiFi Manager</h1>
            <div class="navbar">
                <script type="text/javascript">
                    window.time_format = '<?php echo constant("TIME_FORMAT"); ?>';

                    function reloadDateTime() {
                        $('#timer').html(_getDatetime(window.time_format));
                        setTimeout(reloadDateTime, 1000);
                    }
                    reloadDateTime();
                </script>
                <div class="headerClock"><span id="timer"></span></div>
                <a class="menuconfig noMob" href="/admin/configure.php"><?php echo __('Configuration'); ?></a>
                <a class="menuadmin noMob" href="/admin/"><?php echo __('Admin'); ?></a>
                <a class="menudashboard" href="/"><?php echo __('Dashboard'); ?></a>
            </div>
        </div>

        <div class="profile-wrapper">
            <?php if ($msg) echo "<div class='alert'>$msg</div>"; ?>

            <div class="mobile-back-btn">
                <a href="/"><i class="fa fa-chevron-left"></i> Back to Dashboard</a>
            </div>

            <?php if (!empty($validConnections)) { ?>
                <div class="profile-card">
                    <div class="profile-header">Saved Connections</div>
                    <div class="profile-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Connection Name</th>
                                    <th style="text-align:right !important;">Actions</th> </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalConnections = count($validConnections);
                                foreach ($validConnections as $index => $conn) : 
                                    $isFirst = ($index === 0);
                                    $isLast = ($index === $totalConnections - 1);
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($conn['name']); ?>
                                            <?php if ($conn['active']) echo ' <span class="badge badge-active">Active</span>'; ?>
                                        </td>
                                        <td style="padding: 5px 10px;">
                                            <div style="display:flex; justify-content:flex-end; align-items:center; width:100%; height:100%;">
                                                
                                                <div style="display:flex; justify-content:flex-end; align-items:center; min-width:85px; margin-right:15px; border-right:1px solid rgba(255,255,255,0.1); padding-right:10px; height:32px;">
                                                    <?php if (!$isFirst) : ?>
                                                    <form method="post" style="display:inline; margin:0;">
                                                        <input type="hidden" name="action" value="priority_up">
                                                        <input type="hidden" name="connection" value="<?php echo htmlspecialchars($conn['name']); ?>">
                                                        <button class="profile-btn btn-small btn-icon" type="submit" title="Move Up (Higher Priority)">
                                                            <i class="fa fa-arrow-up"></i>
                                                        </button>
                                                    </form>
                                                    <?php else: ?>
                                                        <?php endif; ?>

                                                    <?php if (!$isLast) : ?>
                                                    <form method="post" style="display:inline; margin:0;">
                                                        <input type="hidden" name="action" value="priority_down">
                                                        <input type="hidden" name="connection" value="<?php echo htmlspecialchars($conn['name']); ?>">
                                                        <button class="profile-btn btn-small btn-icon" type="submit" title="Move Down (Lower Priority)">
                                                            <i class="fa fa-arrow-down"></i>
                                                        </button>
                                                    </form>
                                                    <?php else: ?>
                                                        <?php endif; ?>
                                                </div>

                                                <div style="display:flex; align-items:center; gap:5px;">
                                                    <?php if (!$conn['active']) : ?>
                                                        <form method="post" style="display:inline; margin:0;"><input type="hidden" name="action" value="connect"><input type="hidden" name="connection" value="<?php echo htmlspecialchars($conn['name']); ?>"><button class="profile-btn btn-small btn-connect" type="submit">Connect</button></form>
                                                    <?php endif; ?>
                                                    <form method="post" style="display:inline; margin:0;"><input type="hidden" name="action" value="delete"><input type="hidden" name="connection" value="<?php echo htmlspecialchars($conn['name']); ?>"><button class="profile-btn btn-small btn-danger" type="submit">Forget</button></form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <p style="font-size:0.85em; opacity:0.6; margin-top:10px; text-align:center;">
                            <i class="fa fa-info-circle"></i> Networks at the top of the list are prioritized if multiple networks are available.
                        </p>
                    </div>
                </div>
            <?php } ?>

            <div class="profile-card">
                <div class="profile-header">WiFi Region Settings</div>
                <div class="profile-body">
                    <form method="post" style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
                        <div style="flex-grow:1;">
                            <label style="font-weight:bold; display:block; margin-bottom:5px;">Regulatory Domain (Country):</label>
                            <select name="regulatory_domain" style="margin:0;">
                                <?php foreach ($availableDomains as $domain) : ?>
                                    <option value="<?= $domain; ?>" <?= (trim($domain) === trim($currentGlobalDomain)) ? 'selected' : ''; ?>><?= $domain; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="action" value="set_domain" class="profile-btn" style="width:auto; margin-top:20px;">Set Region</button>
                    </form>
                    <p style="font-size:0.85em; opacity:0.6; margin-top:10px;"><i class="fa fa-info-circle"></i> Setting the correct region is required for WiFi to function properly and comply with local laws.</p>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-header">WiFi Management</div>
                <div class="profile-body">
                    <div class="action-bar">
                        <form method="post" style="flex:1;" onsubmit="$('#scanning-overlay').css('display', 'flex');">
                            <input type="hidden" name="action" value="scan">
                            <button class="profile-btn" style="width:100%"><i class="fa fa-search"></i> Scan for Networks</button>
                        </form>
                        <button class="profile-btn" style="flex:1; background-color: #2980b9;" onclick="$('#manual_add').slideToggle()"><i class="fa fa-plus"></i> Add Manually</button>
                    </div>

                    <div id="manual_add" class="manual-form" style="display:none;">
                        <form method="post">
                            <input type="hidden" name="action" value="add">
                            <label>SSID</label>
                            <input class="legacy-val" type="text" name="ssid" placeholder="Enter SSID" onkeyup="CheckSSID(this)">
                            <label>Passphrase</label>
                            <input class="legacy-val" type="text" name="passphrase" placeholder="Enter Passphrase" onkeyup="CheckPSK(this)">
                            <button class="profile-btn" type="submit">Save & Connect</button>
                        </form>
                    </div>

                    <?php if ($showScan && !empty($scanResults)) { ?>
                        <div class="scan-results">
                            <table>
                                <thead>
                                    <tr>
                                        <th>SSID</th>
                                        <th>Signal Strength</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($scanResults as $net): ?>
                                        <tr>
                                            <td><?php echo $net['ssid']; ?></td>
                                            <td><?php echo signalStrengthBars($net['signalStrength']); ?></td>
                                            <td>
                                                <button class="profile-btn btn-small" type="button" onclick="$('#add_<?php echo md5($net['ssid']); ?>').toggle()">Select</button>
                                            </td>
                                        </tr>
                                        <tr id="add_<?php echo md5($net['ssid']); ?>" style="display:none; background:rgba(0,0,0,0.2);">
                                            <td colspan="3" style="padding:15px;">
                                                <form method="post" style="display:flex; gap:10px; align-items:center;">
                                                    <input type="hidden" name="action" value="add">
                                                    <input type="hidden" name="ssid" value="<?php echo $net['ssid']; ?>">
                                                    <input class="legacy-val" type="text" name="passphrase" placeholder="Passphrase for <?php echo $net['ssid']; ?>" style="margin:0;">
                                                    <button class="profile-btn btn-small" style="height:38px;" type="submit">Join</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-header">Import Connection Profile</div>
                <div class="profile-body">
                    <form method="post" enctype="multipart/form-data" style="display:flex; flex-direction: column; align-items:center; gap:15px;">
                        <input type="hidden" name="action" value="upload">
                        
                        <div style="width:100%; max-width:500px; position:relative;">
                            <input type="file" name="connection_file" id="connection_file" accept=".nmconnection" class="inputfile" onchange="$('#file-chosen').text(this.files[0].name)" />
                            
                            <label for="connection_file" class="file-upload-label">
                                <i class="fa fa-cloud-upload" style="font-size:1.5em; margin-right:10px;"></i>
                                <span id="file-chosen" style="font-weight:bold;">Click to select .nmconnection file</span>
                            </label>
                        </div>

                        <button class="profile-btn" type="submit" style="width:100%; max-width: 200px;">Import Profile</button>
                    </form>
                    <p style="font-size:0.85em; opacity:0.6; margin-top:10px;">
                        <i class="fa fa-info-circle"></i> Upload a pre-configured <code>.nmconnection</code> file.
                    </p>
                </div>
            </div>

            <div class="profile-card noMob">
                <div class="profile-header">Current Status & Statistics</div>
                <div class="profile-body">
                    <div class="stat-grid">
                        <div>
                            <div class="stat-header">Interface Info</div>
                            <div class="stat-row"><span class="stat-label">State</span> <span class="stat-value"><?php echo $isActive ? '<span class="badge badge-active">Active</span>' : '<span class="badge badge-inactive">Inactive</span>'; ?></span></div>
                            <div class="stat-row"><span class="stat-label">Device</span> <span class="stat-value">wlan0</span></div>
                            <div class="stat-row"><span class="stat-label">IP Address</span> <span class="stat-value"><?php echo $strIPAddress; ?></span></div>
                            <div class="stat-row"><span class="stat-label">Subnet Mask</span> <span class="stat-value"><?php echo $strNetMask; ?></span></div>
                            <div class="stat-row"><span class="stat-label">MAC Address</span> <span class="stat-value"><?php echo $strHWAddress; ?></span></div>

                            <div class="stat-header" style="margin-top:15px;">Traffic Stats</div>
                            <div class="stat-row"><span class="stat-label">RX Packets</span> <span class="stat-value"><?php echo $strRxPackets; ?></span></div>
                            <div class="stat-row"><span class="stat-label">RX Data</span> <span class="stat-value"><?php echo $strRxBytes; ?></span></div>
                            <div class="stat-row"><span class="stat-label">TX Packets</span> <span class="stat-value"><?php echo $strTxPackets; ?></span></div>
                            <div class="stat-row"><span class="stat-label">TX Data</span> <span class="stat-value"><?php echo $strTxBytes; ?></span></div>
                        </div>

                        <div>
                            <div class="stat-header">Wireless Info</div>
                            <div class="stat-row"><span class="stat-label">Connected To</span> <span class="stat-value"><?php echo $strSSID; ?></span></div>
                            <div class="stat-row"><span class="stat-label">AP MAC</span> <span class="stat-value"><?php echo $strBSSID; ?></span></div>
                            <div class="stat-row"><span class="stat-label">Channel</span> <span class="stat-value"><?php echo $strWifiChan; ?> (<?php echo $strWifiFreq; ?>)</span></div>
                            <div class="stat-row"><span class="stat-label">Signal Level</span> <span class="stat-value"><?php echo $strSignalLevel; ?></span></div>
                            <div class="stat-row"><span class="stat-label">Link Quality</span> <span class="stat-value"><?php echo $strLinkQuality; ?></span></div>
                            <div class="stat-row"><span class="stat-label">Bitrate</span> <span class="stat-value"><?php echo $strBitrate; ?></span></div>
                            <div class="stat-row"><span class="stat-label">TX Power</span> <span class="stat-value"><?php echo $strTxPower; ?></span></div>
                            <div class="stat-row"><span class="stat-label">Reg Domain</span> <span class="stat-value"><?php echo $currentGlobalDomain; ?></span></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
    </div>
</body>
</html>
