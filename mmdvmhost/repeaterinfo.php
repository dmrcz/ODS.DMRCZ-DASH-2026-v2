<?php
if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
    checkSessionValidity();
}
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php');

if (isset($_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'])) {
    $tableRowEvenBg = $_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'];
} else {
    $tableRowEvenBg = "inherit";
}
if (isset($_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'])) {
    $tableBorderColor = $_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'];
} else {
    $tableBorderColor = "inherit";
}

// Helper function to output a pill
if (!function_exists('outputPill')) {
    function outputPill($label, $statusClass, $iconClass = '', $value = '', $title = '') {
        if (empty($iconClass)) {
            if ($statusClass === 'active') $iconClass = 'fa fa-check-circle text-success';
            elseif ($statusClass === 'paused') $iconClass = 'fa fa-pause-circle text-warning';
            elseif ($statusClass === 'error') $iconClass = 'fa fa-exclamation-triangle text-danger';
            elseif ($statusClass === 'inactive') $iconClass = 'fa fa-circle-o text-muted';
            else $iconClass = 'fa fa-info-circle';
        }
        
        echo "<div class='status-pill $statusClass' title=\"$title\">";
        echo "<span>$label</span>";
        echo "<div class='pill-data'>";
        if ($value !== '') echo "<span class='pill-value'>$value</span> ";
        echo "<i class='$iconClass'></i>";
        echo "</div></div>";
    }
}

if (!function_exists('FillConnectionHosts')) {
    function FillConnectionHosts(&$destArray, $remoteEnabled, $remotePort) {
        if (($remoteEnabled == 1) && ($remotePort != 0)) {
            $remoteOutput = null;
            $remoteRetval = null;
            exec('cd /var/log/pi-star; /usr/local/bin/RemoteCommand '.$remotePort.' hosts', $remoteOutput, $remoteRetval);
            if (($remoteRetval == 0) && (count($remoteOutput) >= 2)) {
                $expOutput = preg_split('/"[^"]*"(*SKIP)(*F)|\x20/', $remoteOutput[1]);
                foreach ($expOutput as $entry) {
                    $keysValues = explode(":", $entry);
                    $destArray[$keysValues[0]] = $keysValues[1];
                }
            }
        }
    }
}

if (!function_exists('FillConnectionStatus')) {
    function FillConnectionStatus(&$destArray, $remoteEnabled, $remotePort) {
        if (($remoteEnabled == 1) && ($remotePort != 0)) {
            $remoteOutput = null;
            $remoteRetval = null;
            exec('cd /var/log/pi-star; /usr/local/bin/RemoteCommand '.$remotePort.' status', $remoteOutput, $remoteRetval);
            if (($remoteRetval == 0) && (count($remoteOutput) >= 2)) {
                $tok = strtok($remoteOutput[1], " \n\t");
                while ($tok !== false) {
                    $keysValues = explode(":", $tok);
                    $destArray[$keysValues[0]] = $keysValues[1];
                    $tok = strtok(" \n\t");
                }
            }
        }
    }
}

if (!function_exists('GetActiveConnectionStyle')) {
    function GetActiveConnectionStyle($masterStates, $key) {
        if (count($masterStates)) {
            if (isset($masterStates[$key])) {
                if (getDMRnetStatus("$key") == "disabled") {
                    return "paused";
                } else if ($masterStates[$key] !== "conn") {
                    return "error";
                }
            }
        }
        return "active";
    }
}

// Grab networks status from remote commands
$remoteMMDVMResults = [];
$remoteDMRgwResults = [];
$remoteYSFGResults = [];
$remoteP25GResults = [];
$remoteNXDNGResults = [];

if (isProcessRunning("MMDVMHost")) {
    $cfgItemEnabled = getConfigItem("Remote Control", "Enable", $_SESSION['MMDVMHostConfigs']);
    $cfgItemPort = getConfigItem("Remote Control", "Port", $_SESSION['MMDVMHostConfigs']);
    FillConnectionStatus($remoteMMDVMResults, (isset($cfgItemEnabled) ? $cfgItemEnabled : 0), (isset($cfgItemPort) ? $cfgItemPort : 0));
}

if (isProcessRunning("DMRGateway")) {
    $remoteCommandEnabled = (isset($_SESSION['DMRGatewayConfigs']['Remote Control']) ? $_SESSION['DMRGatewayConfigs']['Remote Control']['Enable'] : 0);
    $remoteCommandPort = (isset($_SESSION['DMRGatewayConfigs']['Remote Control']) ? $_SESSION['DMRGatewayConfigs']['Remote Control']['Port'] : 0);
    FillConnectionStatus($remoteDMRgwResults, $remoteCommandEnabled, $remoteCommandPort);
    $_SESSION['remoteDMRgwResults'] = $remoteDMRgwResults;
}

if (isProcessRunning("YSFGateway")) {
    $remoteCommandEnabled = (isset($_SESSION['YSFGatewayConfigs']['Remote Commands']) ? $_SESSION['YSFGatewayConfigs']['Remote Commands']['Enable'] : 0);
    $remoteCommandPort = (isset($_SESSION['YSFGatewayConfigs']['Remote Commands']) ? $_SESSION['YSFGatewayConfigs']['Remote Commands']['Port'] : 0);
    FillConnectionStatus($remoteYSFGResults, $remoteCommandEnabled, $remoteCommandPort);
}

if (isProcessRunning("P25Gateway")) {
    $remoteCommandEnabled = (isset($_SESSION['P25GatewayConfigs']['Remote Commands']) ? $_SESSION['P25GatewayConfigs']['Remote Commands']['Enable'] : 0);
    $remoteCommandPort = (isset($_SESSION['P25GatewayConfigs']['Remote Commands']) ? $_SESSION['P25GatewayConfigs']['Remote Commands']['Port'] : 0);
    FillConnectionStatus($remoteP25GResults, $remoteCommandEnabled, $remoteCommandPort);
}

if (isProcessRunning("NXDNGateway")) {
    $remoteCommandEnabled = (isset($_SESSION['NXDNGatewayConfigs']['Remote Commands']) ? $_SESSION['NXDNGatewayConfigs']['Remote Commands']['Enable'] : 0);
    $remoteCommandPort = (isset($_SESSION['NXDNGatewayConfigs']['Remote Commands']) ? $_SESSION['NXDNGatewayConfigs']['Remote Commands']['Port'] : 0);
    FillConnectionStatus($remoteNXDNGResults, $remoteCommandEnabled, $remoteCommandPort);
}

$numDMRmasters = exec('cd /var/log/pi-star ; /usr/local/bin/RemoteCommand '.$_SESSION['DMRGatewayConfigs']['Remote Control']['Port']. ' status | grep -o "conn" | wc -l');
?>

<div class="sidebar-section-title"><?php echo __( 'Modes Enabled' );?></div>
<div class="sidebar-status-grid" id="rptInfoTable">
    <?php 
    // D-Star
    if (isPaused("D-Star")) { outputPill("D-Star", "paused"); } 
    elseif (getEnabled("D-Star", $_SESSION['MMDVMHostConfigs']) == 1) { 
        outputPill("D-Star", isProcessRunning("MMDVMHost") ? "active" : "error"); 
    } else { outputPill("D-Star", "inactive"); }

    // DMR
    if (isPaused("DMR")) { outputPill("DMR", "paused"); } 
    elseif (getEnabled("DMR", $_SESSION['MMDVMHostConfigs']) == 1) { 
        outputPill("DMR", isProcessRunning("MMDVMHost") ? "active" : "error"); 
    } else { outputPill("DMR", "inactive"); }

    // YSF
    if (isPaused("YSF")) { outputPill("YSF", "paused"); } 
    elseif (getEnabled("System Fusion", $_SESSION['MMDVMHostConfigs']) == 1) { 
        outputPill("YSF", isProcessRunning("MMDVMHost") ? "active" : "error"); 
    } else { outputPill("YSF", "inactive"); }

    // P25 (if not DVMegaCast)
    if (isDVmegaCast() == 0) {
        if (isPaused("P25")) { outputPill("P25", "paused"); } 
        elseif (getEnabled("P25", $_SESSION['MMDVMHostConfigs']) == 1) { 
            outputPill("P25", isProcessRunning("MMDVMHost") ? "active" : "error"); 
        } else { outputPill("P25", "inactive"); }
    }

    // NXDN (if not DVMegaCast)
    if (isDVmegaCast() == 0) {
        if (isPaused("NXDN")) { outputPill("NXDN", "paused"); } 
        elseif (getEnabled("NXDN", $_SESSION['MMDVMHostConfigs']) == 1) { 
            outputPill("NXDN", isProcessRunning("MMDVMHost") ? "active" : "error"); 
        } else { outputPill("NXDN", "inactive"); }
    }

    // POCSAG (if not DVMegaCast)
    if (isDVmegaCast() == 0) {
        if (isPaused("POCSAG")) { outputPill("POCSAG", "paused"); } 
        elseif (getEnabled("POCSAG", $_SESSION['MMDVMHostConfigs']) == 1) { 
            outputPill("POCSAG", isProcessRunning("MMDVMHost") ? "active" : "error"); 
        } else { outputPill("POCSAG", "inactive"); }
    }

    // X-Modes
    if (getEnabled("DMR", $_SESSION['MMDVMHostConfigs']) == 1) {
        $dmrXModeActive = (isProcessRunning("MMDVMHost") && (isProcessRunning("DMR2YSF") || isProcessRunning("DMR2NXDN")));
        $dmrXModeEnabled = ($_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'] || $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled']);
        if ($dmrXModeEnabled) {
            outputPill("DMR X-Mode", $dmrXModeActive ? "active" : "error");
        }
    }
    
    if (getEnabled("System Fusion", $_SESSION['MMDVMHostConfigs']) == 1) {
        $ysfXModeActive = (isProcessRunning("MMDVMHost") && (isProcessRunning("YSF2DMR") || isProcessRunning("YSF2NXDN") || isProcessRunning("YSF2P25")));
        $ysfXModeEnabled = ($_SESSION['YSF2DMRConfigs']['Enabled']['Enabled'] || $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'] || $_SESSION['YSF2P25Configs']['Enabled']['Enabled']);
        if ($ysfXModeEnabled) {
            outputPill("YSF X-Mode", $ysfXModeActive ? "active" : "error");
        }
    }
    ?>
</div>

<br />

<div class="sidebar-section-title"><?php echo __( 'Network Status' );?></div>
<div class="sidebar-status-grid">
    <?php 
    // D-Star Net
    if (isPaused("D-Star")) { outputPill("D-Star Net", "paused"); }
    elseif (getEnabled("D-Star Network", $_SESSION['MMDVMHostConfigs']) == 1) {
        outputPill("D-Star Net", isProcessRunning("ircddbgatewayd") ? "active" : "error");
    } else { outputPill("D-Star Net", "inactive"); }

    // DMR Net
    if (isPaused("DMR")) { outputPill("DMR Net", "paused"); }
    elseif (getEnabled("DMR Network", $_SESSION['MMDVMHostConfigs']) == 1) {
        if (getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']) == '127.0.0.1') {
            outputPill("DMR Net", isProcessRunning("DMRGateway") ? "active" : "error");
        } else {
            outputPill("DMR Net", isProcessRunning("MMDVMHost") ? "active" : "error");
        }
    } else { outputPill("DMR Net", "inactive"); }

    // YSF Net
    if (isPaused("YSF")) { outputPill("YSF Net", "paused"); }
    elseif (getEnabled("System Fusion Network", $_SESSION['MMDVMHostConfigs']) == 1) {
        outputPill("YSF Net", isProcessRunning("YSFGateway") ? "active" : "error");
    } else { outputPill("YSF Net", "inactive"); }

    // P25 Net
    if (isDVmegaCast() == 0) {
        if (isPaused("P25")) { outputPill("P25 Net", "paused"); }
        elseif (getEnabled("P25 Network", $_SESSION['MMDVMHostConfigs']) == 1) {
            outputPill("P25 Net", isProcessRunning("P25Gateway") ? "active" : "error");
        } else { outputPill("P25 Net", "inactive"); }
    }

    // NXDN Net
    if (isDVmegaCast() == 0) {
        if (isPaused("NXDN")) { outputPill("NXDN Net", "paused"); }
        elseif (getEnabled("NXDN Network", $_SESSION['MMDVMHostConfigs']) == 1) {
            outputPill("NXDN Net", isProcessRunning("NXDNGateway") ? "active" : "error");
        } else { outputPill("NXDN Net", "inactive"); }
    }

    // POCSAG Net
    if (isDVmegaCast() == 0) {
        if (isPaused("POCSAG")) { outputPill("POCSAG Net", "paused"); }
        elseif (getEnabled("POCSAG Network", $_SESSION['MMDVMHostConfigs']) == 1) {
            outputPill("POCSAG Net", isProcessRunning("DAPNETGateway") && (isDAPNETGatewayConnected() == 1) ? "active" : "error");
        } else { outputPill("POCSAG Net", "inactive"); }
    }

    // Cross Modes
    if (getEnabled("DMR", $_SESSION['MMDVMHostConfigs']) == 1) {
        if ($_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'] == 1) outputPill("DMR2NXDN", isProcessRunning("DMR2NXDN") ? "active" : "error");
        if ($_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'] == 1) outputPill("DMR2YSF", isProcessRunning("DMR2YSF") ? "active" : "error");
    }
    if (getEnabled("System Fusion", $_SESSION['MMDVMHostConfigs']) == 1) {
        if ($_SESSION['YSF2DMRConfigs']['Enabled']['Enabled'] == 1) outputPill("YSF2DMR", isProcessRunning("YSF2DMR") ? "active" : "error");
        if ($_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'] == 1) outputPill("YSF2NXDN", isProcessRunning("YSF2NXDN") ? "active" : "error");
        if ($_SESSION['YSF2P25Configs']['Enabled']['Enabled'] == 1) outputPill("YSF2P25", isProcessRunning("YSF2P25") ? "active" : "error");
    }

    // APRS
    if (isPaused("APRS")) { outputPill("APRS Net", "paused"); }
    elseif (getServiceEnabled('/etc/aprsgateway') == 1) {
        outputPill("APRS Net", isProcessRunning("APRSGateway") && (isAPRSISGatewayConnected() == 1) ? "active" : "error");
    } else { outputPill("APRS Net", "inactive"); }
    ?>
</div>

<br />

<?php
$testMMDVModeDSTAR = getConfigItem("D-Star", "Enable", $_SESSION['MMDVMHostConfigs']);
if ( $testMMDVModeDSTAR == 1 || isPaused("D-Star") ) {
    $linkedTo = getActualLink($reverseLogLinesMMDVM, "D-Star");
?>
    <div class="sidebar-section-title"><?php echo __( 'D-Star Status' ); ?></div>
    <div class="sidebar-status-grid">
        <div class="status-pill active" style="grid-column: span 2;"><span>RPT1</span><span class="pill-value"><?php echo($_SESSION['ircDDBConfigs']['repeaterCall1'] ."&nbsp ".$_SESSION['ircDDBConfigs']['repeaterBand1']); ?></span></div>
        <div class="status-pill active" style="grid-column: span 2;"><span>RPT2</span><span class="pill-value"><?php echo($_SESSION['ircDDBConfigs']['repeaterCall1'] ."&nbsp G"); ?></span></div>
    </div>
    
    <div class="sidebar-section-title related"><?php echo __( 'D-Star Network' ); ?></div>
    <div class="sidebar-status-grid">
        <?php
        if (isPaused("D-Star")) {
            echo "<div class='status-pill paused' style='grid-column: span 2;'><span>D-Star</span><span class='pill-value'>Mode Paused</span><i class='fa fa-pause-circle'></i></div>";
        } else {
            echo "<div class='status-pill active' style='grid-column: span 2;' title=\"$linkedTo\"><span>Link</span><span class='pill-value'>$linkedTo</span></div>";
        }
        
        if ($_SESSION['ircDDBConfigs']['aprsEnabled'] == 1) {
            $aprsAddr = substr($_SESSION['ircDDBConfigs']['aprsAddress'], 0, 20);
            $aprsDisplay = ($aprsAddr == '127.0.0.1') ? "Gateway" : $aprsAddr;
            echo "<div class='status-pill active' style='grid-column: span 2;'><span>APRS</span><span class='pill-value'>$aprsDisplay</span></div>";
        }
        
        if ($_SESSION['ircDDBConfigs']['ircddbEnabled'] == 1) {
            if (isProcessRunning("ircddbgatewayd")) {
                $ircHost = substr($_SESSION['ircDDBConfigs']['ircddbHostname'], 0 ,20);
                echo "<div class='status-pill active' style='grid-column: span 2;'><span>ircDDB</span><span class='pill-value'>$ircHost</span></div>";
            } else {
                echo "<div class='status-pill inactive' style='grid-column: span 2;'><span>ircDDB</span><span class='pill-value'>Stopped</span><i class='fa fa-times'></i></div>";
            }
        }
        ?>
    </div>
    <br />
<?php } ?>

<?php
$testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);
if ( $testMMDVModeDMR == 1 || isPaused("DMR") ) {
    if (isPaused("DMR")) {
        $dmrMasterHost = "Mode Paused";
        $dmrMasterHostTooltip = $dmrMasterHost;
    } else {
        $dmrMasterFile = fopen("/usr/local/etc/DMR_Hosts.txt", "r");
        $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
        $dmrMasterPort = getConfigItem("DMR Network", "Port", $_SESSION['MMDVMHostConfigs']);

        if ($dmrMasterHost == '127.0.0.1') {
            if (isset($_SESSION['DMRGatewayConfigs']['XLX Network 1']['Address'])) {
                $xlxMasterHost1 = $_SESSION['DMRGatewayConfigs']['XLX Network 1']['Address'];
            }
            else {
                $xlxMasterHost1 = "";
            }
            while (!feof($dmrMasterFile)) {
                $dmrMasterLine = fgets($dmrMasterFile);
                $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
                if ((count($dmrMasterHostF) >= 2) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
                    if ((strpos($dmrMasterHostF[0], 'XLX_') === 0) && ($xlxMasterHost1 == $dmrMasterHostF[2])) {
                        $xlxMasterHost1 = str_replace('_', ' ', $dmrMasterHostF[0]);
                    }
                }
            }
            $xlxMasterHost1Tooltip = $xlxMasterHost1;
            if (strlen($xlxMasterHost1) > 25) {
                $xlxMasterHost1 = substr($xlxMasterHost1, 0, 20) . '...';
            }
        }
        else {
            while (!feof($dmrMasterFile)) {
                $dmrMasterLine = fgets($dmrMasterFile);
                $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
                if ((count($dmrMasterHostF) >= 4) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
                    if (($dmrMasterHost == $dmrMasterHostF[2]) && ($dmrMasterPort == $dmrMasterHostF[4])) {
                        $dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]);
                    }
                }
            }
            $dmrMasterHostTooltip = $dmrMasterHost;
            if (strlen($dmrMasterHost) > 25) {
                $dmrMasterHost = substr($dmrMasterHost, 0, 20) . '...';
            }
        }
        fclose($dmrMasterFile);
    }
?>
    <div class="sidebar-section-title"><?php echo __( 'DMR Status' );?></div>
    <div class="sidebar-status-grid">
        <?php if (getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == 1) outputPill("TS1", "active", "fa fa-check", "On"); ?>
        <?php if (getConfigItem("DMR Network", "Slot2", $_SESSION['MMDVMHostConfigs']) == 1) outputPill("TS2", "active", "fa fa-check", "On"); ?>
        
        <?php if(isWPSDrepeater() == 1) { // repeater-only
            if (getConfigItem("DMR", "Beacons", $_SESSION['MMDVMHostConfigs']) == 1 && getConfigItem("DMR", "BeaconInterval", $_SESSION['MMDVMHostConfigs']) != null) {
                outputPill("Beacon", "active", "fa fa-clock-o", "Timed");
            } elseif  (getConfigItem("DMR", "Beacons", $_SESSION['MMDVMHostConfigs']) == 1 && getConfigItem("DMR", "BeaconInterval", $_SESSION['MMDVMHostConfigs']) == null) {
                outputPill("Beacon", "active", "fa fa-globe", "Net");
            } else {
                outputPill("Beacon", "inactive", "fa fa-times", "Off");
            }
        } ?>
        
        <div class="status-pill active"><span>ID</span><span class="pill-value"><?php echo getConfigItem("General", "Id", $_SESSION['MMDVMHostConfigs']); ?></span></div>
        <div class="status-pill active"><span>CC</span><span class="pill-value"><?php echo getConfigItem("DMR", "ColorCode", $_SESSION['MMDVMHostConfigs']); ?></span></div>
    </div>

    <div class="sidebar-section-title related"><?php echo ($numDMRmasters <= 1) ? __( 'DMR Master' ) : "DMR Masters"; ?></div>
    <div class="sidebar-status-grid">
        <?php
            if (getEnabled("DMR Network", $_SESSION['MMDVMHostConfigs']) == 1) {
                if ($dmrMasterHost == '127.0.0.1') {
                    if (isProcessRunning("DMRGateway")) {
                        foreach ($_SESSION['DMRNetStatusAliases'] as $statusName => $sectionName) {
                            if (!isset($_SESSION['DMRGatewayConfigs'][$sectionName])) continue;
                            $sectionVars = $_SESSION['DMRGatewayConfigs'][$sectionName];
                            if ($sectionVars['Enabled'] == 1) {
                                $name = str_replace("_", " ", $sectionVars['Name']);
                                if (strlen($name) > 25) $name = substr($name, 0, 20) . '..';
                                
                                $style = GetActiveConnectionStyle($remoteDMRgwResults, $statusName);
                                echo "<div class='status-pill $style' style='grid-column: span 2;' title=\"$name\"><span>$name</span><i class='fa fa-link'></i></div>";
                            }
                        }
                        
                        if ( !isset($_SESSION['DMRGatewayConfigs']['XLX Network 1']['Enabled']) && isset($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled']) && $_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'] == 1) {
                             $xlxMasterHostLinkState = "";
                             if (file_exists("/var/log/pi-star/DMRGateway-".gmdate("Y-m-d").".log")) {
                                $xlxMasterHostLinkState = exec('grep \'XLX, Linking\|XLX, Unlinking\|XLX, Logged\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d").'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\'');
                                if(empty($xlxMasterHostLinkState)) {
                                    $xlxMasterHostLinkState = exec('grep \'XLX, Linking\|XLX, Unlinking\|XLX, Logged\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d", time() - 86340).'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\'');
                                }
                            } else {
                                $xlxMasterHostLinkState = exec('grep \'XLX, Linking\|XLX, Unlinking\|XLX, Logged\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d", time() - 86340).'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\'');
                            }
                            if ($xlxMasterHostLinkState != "") {
                                if ( strpos($xlxMasterHostLinkState, 'Linking') !== false ) {
                                    $xlxMasterHost1 = str_replace('Linking ', '', $xlxMasterHostLinkState);
                                    $xlxMasterHost1 = str_replace(" ", " Module ", $xlxMasterHost1); 
                                }
                                else if ( strpos($xlxMasterHostLinkState, 'Unlinking') !== false ) {
                                    $xlxMasterHost1 = "XLX Not Linked";
                                }
                                else if ( strpos($xlxMasterHostLinkState, 'Logged') !== false ) {
                                    $xlxMasterHost1 = "XLX Not Linked";
                                }
                            }
                            else {
                                $xlxMasterHost1 = "".$xlxMasterHost1." ".$_SESSION['DMRGatewayConfigs']['XLX Network']['Module']."";
                            }
                            $style = GetActiveConnectionStyle($remoteDMRgwResults, "xlx");
                            echo "<div class='status-pill $style' style='grid-column: span 2;'><span>$xlxMasterHost1</span><i class='fa fa-link'></i></div>";
                        }
                    } else {
                        outputPill("DMRGateway", "inactive", "fa fa-times", "Stopped");
                    }
                } else {
                    $style = GetActiveConnectionStyle($remoteDMRgwResults, "dmr");
                    echo "<div class='status-pill $style' style='grid-column: span 2;' title=\"$dmrMasterHostTooltip\"><span>$dmrMasterHost</span><i class='fa fa-link'></i></div>";
                }
            } else {
                outputPill("DMR Network", "inactive", "fa fa-ban", "Disabled");
            }
        ?>
    </div>
    <br />
<?php } ?>

<?php
$testMMDVModeYSF = getConfigItem("System Fusion Network", "Enable", $_SESSION['MMDVMHostConfigs']);
if ( isset($_SESSION['DMR2YSFConfigs']['Enabled']['Enabled']) ) {
    $testDMR2YSF = $_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'];
}
if ( $testMMDVModeYSF == 1 || isPaused("YSF") || (isset($testDMR2YSF) && $testDMR2YSF == 1) ) { 
    if (isPaused("YSF")) {
        $ysfLinkedTo = "Mode Paused";
        $ysfLinkStateTooltip = $ysfLinkedTo;
    } else {
        $ysfLinkedTo = getActualLink($reverseLogLinesYSFGateway, "YSF");
    }
    if ($ysfLinkedTo == 'Not Linked' || $ysfLinkedTo == 'Service Not Started') {
        $ysfLinkedToTxt = $ysfLinkedTo;
        $ysfLinkState = '';
        $ysfLinkStateTooltip = $ysfLinkedTo;
    } else {
        $ysfHostFile = fopen("/usr/local/etc/YSFHosts.txt", "r");
        $ysfLinkedToTxt = "null";
        while (!feof($ysfHostFile)) {
            $ysfHostFileLine = fgets($ysfHostFile);
            $ysfRoomTxtLine = preg_split('/;/', $ysfHostFileLine);
            if (empty($ysfRoomTxtLine[0]) || empty($ysfRoomTxtLine[1])) continue;
            if (($ysfRoomTxtLine[0] == $ysfLinkedTo) || ($ysfRoomTxtLine[1] == $ysfLinkedTo)) {
                $ysfRoomNo = "YSF".$ysfRoomTxtLine[0];
                $ysfLinkedToTxt = $ysfRoomTxtLine[1];
                break;
            }
        }
        fclose($ysfHostFile);
        if ($_SESSION['YSFGatewayConfigs']['FCS Network']['Enable'] == 1) {
            $fcsHostFile = fopen("/usr/local/etc/FCSHosts.txt", "r");
            while (!feof($fcsHostFile)) {
                $ysfHostFileLine = fgets($fcsHostFile);
                $ysfRoomTxtLine = preg_split('/;/', $ysfHostFileLine);
                if (empty($ysfRoomTxtLine[0]) || empty($ysfRoomTxtLine[1])) continue;
                if (($ysfRoomTxtLine[0] == $ysfLinkedTo) || ($ysfRoomTxtLine[1] == $ysfLinkedTo)) {
                    $ysfLinkedToTxt = $ysfRoomTxtLine[1];
                    $ysfRoomNo = $ysfRoomTxtLine[0];
                    break;
                }
            }
            fclose($fcsHostFile);
        }
        if ($_SESSION['YSFGatewayConfigs']['FCS Network']['Enable'] != 1) {
            $ysfLinkedToTxt = $ysfLinkedTo;
            $ysfLinkState = ' [Linked]';
        } else {
            if ($ysfLinkedToTxt != "null") {
                $ysfLinkState = ' [In Room]';
            } else {
                $ysfLinkedToTxt = $ysfLinkedTo;
                $ysfLinkState = ' [Linked]';
            }
        }
        $ysfLinkedToTxt = str_replace('_', ' ', $ysfLinkedToTxt);
    }

    if (empty($ysfRoomNo) || ($ysfRoomNo == "null")) {
        $ysfTableData = $ysfLinkedToTxt;
    } else {
        $ysfTableData = $ysfLinkedToTxt;
    }
    if (strlen($ysfLinkedToTxt) > 25) {
        $ysfLinkedToTxt = substr($ysfLinkedToTxt, 0, 20) . '...';
    }
?>
    <div class="sidebar-section-title"><?php echo __( 'YSF Status' ) . $ysfLinkState; ?></div>
    <div class="sidebar-status-grid">
    <?php
        if (isPaused("YSF")) {
            outputPill("Status", "paused", "fa fa-pause", "Paused");
        } elseif (isProcessRunning("YSFGateway")) {
            echo "<div class='status-pill active' style='grid-column: span 2;' title=\"$ysfLinkedToTooltip\"><span>Link</span><div class='pill-data'><span class='pill-value'>$ysfTableData</span><i class='fa fa-link'></i></div></div>";
        } else {
            outputPill("Status", "inactive", "fa fa-times", "Stopped");
        }
    ?>
    </div>
    <br />
<?php } ?>

<?php if (getServiceEnabled('/etc/dgidgateway') == 1 ) { ?>
    <div class="sidebar-section-title related">DG-ID Gateway</div>
    <div class="sidebar-status-grid">
    <?php
        if (isPaused("YSF")) {
            outputPill("DG-ID", "paused", "fa fa-pause", "Paused");
        } elseif (isProcessRunning("DGIdGateway")) {
            $dgidLink = str_replace("<br />", " ", getDGIdLinks());
            echo "<div class='status-pill active' style='grid-column: span 2;' title=\"$dgidLink\"><span>Link</span><div class='pill-data'><span class='pill-value'>$dgidLink</span><i class='fa fa-link'></i></div></div>";
        } else {
            outputPill("DG-ID", "inactive", "fa fa-times", "Stopped");
        }
    ?>
    </div>
    <br />
<?php } ?>

<?php
$testYSF2DMR = 0;
if ( isset($_SESSION['YSF2DMRConfigs']['Enabled']['Enabled']) ) {
    $testYSF2DMR = $_SESSION['YSF2DMRConfigs']['Enabled']['Enabled'];
}
if ($testYSF2DMR == 1) { 
    $dmrMasterFile = fopen("/usr/local/etc/DMR_Hosts.txt", "r");
    $dmrMasterHost = $_SESSION['YSF2DMRConfigs']['DMR Network']['Address'];
    while (!feof($dmrMasterFile)) {
        $dmrMasterLine = fgets($dmrMasterFile);
        $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
        if ((count($dmrMasterHostF) >= 2) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
            if ($dmrMasterHost == $dmrMasterHostF[2]) {
                $dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]);
            }
        }
    }
    if (strlen($dmrMasterHost) > 25) {
        $dmrMasterHostDisp = substr($dmrMasterHost, 0, 20) . '..';
    } else {
        $dmrMasterHostDisp = $dmrMasterHost;
    }
    fclose($dmrMasterFile);
?>
    <div class="sidebar-section-title">YSF2DMR</div>
    <div class="sidebar-status-grid">
        <div class="status-pill active"><span>DMR ID</span><div class="pill-data"><span class="pill-value"><?php echo $_SESSION['YSF2DMRConfigs']['DMR Network']['Id']; ?></span></div></div>
        <div class="status-pill active" title="<?php echo $dmrMasterHost; ?>"><span>Master</span><div class="pill-data"><span class="pill-value"><?php echo $dmrMasterHostDisp; ?></span></div></div>
    </div>
    <br />
<?php } ?>

<?php
$testMMDVModeP25 = getConfigItem("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
$testYSF2P25 = 0;
if ( isset($_SESSION['YSF2P25Configs']['Enabled']['Enabled']) ) { $testYSF2P25 = $_SESSION['YSF2P25Configs']['Enabled']['Enabled']; }
if ( $testMMDVModeP25 == 1 || $testYSF2P25 || isPaused("P25") ) { 
?>
    <div class="sidebar-section-title"><?php echo __( 'P25 Status' ); ?></div>
    <div class="sidebar-status-grid">
        <?php
        if (getConfigItem("P25", "NAC", $_SESSION['MMDVMHostConfigs'])) {
            echo "<div class='status-pill active'><span>NAC</span><div class='pill-data'><span class='pill-value'>".getConfigItem("P25", "NAC", $_SESSION['MMDVMHostConfigs'])."</span></div></div>";
        }
        
        if (isPaused("P25")) {
            outputPill("P25 Net", "paused", "fa fa-pause", "Paused");
        } else {
            $P25tg = str_replace("TG", "", getActualLink($logLinesP25Gateway, "P25"));
            if (strpos($P25tg, 'Not Linked') || strpos($P25tg, 'Service Not Started')) {
                outputPill("P25 Net", "inactive", "fa fa-unlink", $P25tg);
            } else {
                if (empty($P25tg)) {
                    outputPill("P25 Net", "inactive", "fa fa-unlink", "Unlinked");
                } else {
                    if (file_exists("/etc/.TGNAMES")) {
                        $P25_target = preg_replace('#\((.*?)\)#', "", tgLookup("P25", $P25tg));
                        echo "<div class='status-pill active' title='TG $P25tg'><span>Link</span><div class='pill-data'><span class='pill-value'>$P25_target</span><i class='fa fa-link'></i></div></div>";
                    } else {
                        echo "<div class='status-pill active'><span>Link</span><div class='pill-data'><span class='pill-value'>TG $P25tg</span><i class='fa fa-link'></i></div></div>";
                    }
                }
            }
        }
        ?>
    </div>
    <br />
<?php } ?>

<?php
$testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
$testYSF2NXDN = 0;
$testDMR2NXDN = 0;
if ( isset($_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled']) && $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'] == 1) { $testYSF2NXDN = 1; }
if ( isset($_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled']) && $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'] == 1) { $testDMR2NXDN = 1; }

if ( $testMMDVModeNXDN == 1 || $testYSF2NXDN == 1 || $testDMR2NXDN == 1 || isPaused("NXDN") ) {
    if (getConfigItem("NXDN", "RAN", $_SESSION['MMDVMHostConfigs'])) {
?>
    <div class="sidebar-section-title"><?php echo __( 'NXDN Status' ); ?></div>
    <div class="sidebar-status-grid">
        <div class="status-pill active"><span>RAN</span><div class="pill-data"><span class="pill-value"><?php echo getConfigItem("NXDN", "RAN", $_SESSION['MMDVMHostConfigs']); ?></span></div></div>
        <?php
        if (isPaused("NXDN")) {
            outputPill("Net", "paused", "fa fa-pause", "Paused");
        } else {
            $NXDNtg = str_replace("TG", "", getActualLink($logLinesNXDNGateway, "NXDN"));
            if (strpos($NXDNtg, 'Not Linked') || strpos($NXDNtg, 'Service Not Started')) {
                outputPill("Net", "inactive", "fa fa-unlink", $NXDNtg);
            } else {
                if (empty($NXDNtg)) {
                    outputPill("Net", "inactive", "fa fa-unlink", "Unlinked");
                } else {
                    if (file_exists("/etc/.TGNAMES")) {
                        $NXDN_target = preg_replace('#\((.*?)\)#', "", tgLookup("NXDN", $NXDNtg));
                        echo "<div class='status-pill active' title='TG $NXDNtg'><span>Link</span><div class='pill-data'><span class='pill-value'>$NXDN_target</span><i class='fa fa-link'></i></div></div>";
                    } else {
                        echo "<div class='status-pill active'><span>Link</span><div class='pill-data'><span class='pill-value'>TG $NXDNtg</span><i class='fa fa-link'></i></div></div>";
                    }
                }
            }
        }
        ?>
    </div>
    <br />
<?php
    }
}
?>

<?php
$testMMDVModePOCSAG = getConfigItem("POCSAG Network", "Enable", $_SESSION['MMDVMHostConfigs']);
if ( $testMMDVModePOCSAG == 1 || isPaused("POCSAG")) {
?>
    <div class="sidebar-section-title">POCSAG Status</div>
    <div class="sidebar-status-grid">
        <div class="status-pill active stacked" style="grid-column: span 2;">
            <span>Freq</span>
            <div class="pill-data">
                <span class="pill-value"><?php echo getMHZ(getConfigItem("POCSAG", "Frequency", $_SESSION['MMDVMHostConfigs'])); ?></span>
            </div>
        </div>

        <?php
        if (isPaused("POCSAG")) {
            echo "<div class='status-pill paused' style='grid-column: span 2;'><span>DAPNET</span><div class='pill-data'><span class='pill-value'>Paused</span><i class='fa fa-pause-circle'></i></div></div>";
        } else {
            if (isset($_SESSION['DAPNETGatewayConfigs']['DAPNET']['Address'])) {
                $dapnetGatewayRemoteAddr = $_SESSION['DAPNETGatewayConfigs']['DAPNET']['Address'];
                $dapnetDisp = (strlen($dapnetGatewayRemoteAddr) > 25) ? substr($dapnetGatewayRemoteAddr, 0, 20).'..' : $dapnetGatewayRemoteAddr;
            } else {
                $dapnetDisp = "Unknown";
            }
            
            if (isProcessRunning("DAPNETGateway")) {
                // Server (Stacked)
                echo "<div class='status-pill active stacked' style='grid-column: span 2;' title='$dapnetGatewayRemoteAddr'><span>Server</span><div class='pill-data'><span class='pill-value'>$dapnetDisp</span><i class='fa fa-server'></i></div></div>";
            } else {
                echo "<div class='status-pill inactive' style='grid-column: span 2;'><span>Server</span><div class='pill-data'><span class='pill-value'>Stopped</span><i class='fa fa-times'></i></div></div>";
            }
        }
        ?>
    </div>
    <br />
<?php } ?>

<?php
$testAPRSdmr = $_SESSION['DMRGatewayConfigs']['APRS']['Enable'];
$testAPRSysf = $_SESSION['YSFGatewayConfigs']['APRS']['Enable'];
$testAPRSnxdn = $_SESSION['NXDNGatewayConfigs']['APRS']['Enable'];
$testAPRSdgid = $_SESSION['DGIdGatewayConfigs']['APRS']['Enable'];
$testAPRSircddb = $_SESSION['ircDDBConfigs']['aprsEnabled'];

if (getServiceEnabled('/etc/aprsgateway') == 1 || isPaused("APRS"))  {
?>
    <div class="sidebar-section-title">APRS Gateway</div>
    <div class="sidebar-status-grid">
    <?php
    if (!isProcessRunning("APRSGateway")) {
        outputPill("Service", "inactive", "fa fa-times", "Stopped");
    } else {
        // Pool
        $pool = substr($_SESSION['APRSGatewayConfigs']['APRS-IS']['Server'], 0, 20);
        echo "<div class='status-pill active' style='grid-column: span 2;' title='Pool: ".$_SESSION['APRSGatewayConfigs']['APRS-IS']['Server']."'><span>Pool</span><div class='pill-data'><span class='pill-value'>$pool</span><i class='fa fa-server'></i></div></div>";
        
        // Server Status
        $aprsServer = getAPRSISserver();
        if(strpos($aprsServer, 'Not Conn') !== false) {
            outputPill("Link", "inactive", "fa fa-unlink", "Unlinked");
        } else {
            // Strip HTML links if getAPRSISserver returns them, for cleaner pill display
            $aprsServerClean = strip_tags($aprsServer);
            echo "<div class='status-pill active' style='grid-column: span 2;'><span>Linked</span><div class='pill-data'><span class='pill-value'>$aprsServerClean</span><i class='fa fa-check-circle'></i></div></div>";
        }
    }
    ?>
    </div>

    <div class="sidebar-section-title related">APRS Modes</div>
    <div class="sidebar-status-grid">
    <?php
        if ($testAPRSdmr == 0 && $testAPRSircddb == 0 && $testAPRSysf == 0 && $testAPRSdgid == 0 && $testAPRSnxdn == 0) {
            echo "<div class='status-pill inactive' style='grid-column: span 2;'><a href='/admin/configure.php#APRSgw' style='color:inherit;'>No Modes Selected</a></div>";
        } else {
            // Display ALL modes with correct status (Active/Inactive)
            outputPill("DMR",    ($testAPRSdmr == 1)    ? "active" : "inactive");
            outputPill("ircDDB", ($testAPRSircddb == 1) ? "active" : "inactive");
            outputPill("YSF",    ($testAPRSysf == 1)    ? "active" : "inactive");
            outputPill("DGId",   ($testAPRSdgid == 1)   ? "active" : "inactive");
            outputPill("NXDN",   ($testAPRSnxdn == 1)   ? "active" : "inactive");
        }
    ?>
    </div>
    <br />
<?php } ?>
