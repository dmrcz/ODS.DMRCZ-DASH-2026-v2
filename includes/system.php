<?php
if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

// Helper to render modern pills with 3-state logic
function renderSystemPill($label, $isRunning, $isEnabled = false, $customTitle = '') {
    // Logic:
    // 1. Running -> ACTIVE (Green)
    // 2. Not Running AND Enabled -> ERROR (Red)
    // 3. Not Running AND Not Enabled -> INACTIVE/PAUSED (Grey/Yellow)

    $icon = 'fa-circle-o';
    $class = 'inactive';
    $val = 'Inactive';

    if ($isRunning) {
        $class = 'active';
        $icon = 'fa-check-circle';
        $val = 'Running';
    } elseif ($isEnabled) {
        // It SHOULD be running, but isn't
        $class = 'error';
        $icon = 'fa-exclamation-triangle';
        $val = 'Stopped';
    } else {
        // It is correctly disabled / optional
        $class = 'paused';
        $icon = 'fa-pause-circle';
        $val = 'Disabled';
    }

    if ($customTitle) {
        $val = $customTitle;
    }

    echo "<div class='status-pill $class'>";
    echo "<span>$label</span>";
    echo "<div class='pill-data'>";
    echo "<span class='pill-value'>$val</span>";
    echo "<i class='fa $icon'></i>";
    echo "</div></div>";
}

// Config Helper to check if a mode/net is explicitly enabled
function isConfigEnabled($section, $key, $configs) {
    $val = getConfigItem($section, $key, $configs);
    return ($val == 1 || $val == "1" || $val == "true" || $val == "True");
}

// Helper for separate config files (like Cross-modes)
function isServiceConfigEnabled($configArray) {
    if (isset($configArray['Enabled']['Enabled'])) {
        return ($configArray['Enabled']['Enabled'] == 1);
    }
    return false;
}
?>

<h3 style="text-align:left;font-weight:bold;margin:10px 0 10px 0;"><?php echo __( 'Service &amp; Process Status' );?></h3>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 8px;">

  <?php
  // 1. FIREWALL
  // If FW is off (0), we set isEnabled to false so it renders as Paused (Yellow/Grey) instead of Error (Red)
  $fwState = (getFWstate() == '1');
  renderSystemPill("Firewall", $fwState, $fwState, $fwState ? "Enabled" : "Disabled");

  // 2. CORE MMDVM
  renderSystemPill("MMDVMHost", isProcessRunning('MMDVMHost'), true);

  // 3. MAIN GATEWAYS
  // DMR
  $dmrEnabled = isConfigEnabled("DMR Network", "Enable", $_SESSION['MMDVMHostConfigs']);
  renderSystemPill("DMRGateway", isProcessRunning('DMRGateway'), $dmrEnabled);

  // D-Star (ircDDB)
  $dstarEnabled = isConfigEnabled("D-Star Network", "Enable", $_SESSION['MMDVMHostConfigs']);
  renderSystemPill("ircDDBGateway", isProcessRunning('ircddbgatewayd'), $dstarEnabled);

  // YSF
  $ysfEnabled = isConfigEnabled("System Fusion Network", "Enable", $_SESSION['MMDVMHostConfigs']);
  renderSystemPill("YSFGateway", isProcessRunning('YSFGateway'), $ysfEnabled);

  // P25
  $p25Enabled = isConfigEnabled("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
  renderSystemPill("P25Gateway", isProcessRunning('P25Gateway'), $p25Enabled);

  // NXDN
  $nxdnEnabled = isConfigEnabled("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
  renderSystemPill("NXDNGateway", isProcessRunning('NXDNGateway'), $nxdnEnabled);

  // POCSAG / DAPNET
  $pocsagEnabled = isConfigEnabled("POCSAG Network", "Enable", $_SESSION['MMDVMHostConfigs']);
  renderSystemPill("DAPNETGateway", isProcessRunning('DAPNETGateway'), $pocsagEnabled);

  // 4. CROSS MODES
  // Check specific cross-mode config files to see if enabled

  $ysf2dmrEnabled = isServiceConfigEnabled($_SESSION['YSF2DMRConfigs']);
  renderSystemPill("YSF2DMR", isProcessRunning('YSF2DMR'), $ysf2dmrEnabled);

  $ysf2p25Enabled = isServiceConfigEnabled($_SESSION['YSF2P25Configs']);
  renderSystemPill("YSF2P25", isProcessRunning('YSF2P25'), $ysf2p25Enabled);

  $ysf2nxdnEnabled = isServiceConfigEnabled($_SESSION['YSF2NXDNConfigs']);
  renderSystemPill("YSF2NXDN", isProcessRunning('YSF2NXDN'), $ysf2nxdnEnabled);

  $dmr2ysfEnabled = isServiceConfigEnabled($_SESSION['DMR2YSFConfigs']);
  renderSystemPill("DMR2YSF", isProcessRunning('DMR2YSF'), $dmr2ysfEnabled);

  $dmr2nxdnEnabled = isServiceConfigEnabled($_SESSION['DMR2NXDNConfigs']);
  renderSystemPill("DMR2NXDN", isProcessRunning('DMR2NXDN'), $dmr2nxdnEnabled);

  // 5. TOOLS & UTILITIES

  // APRS Gateway
  $aprsEnabled = getServiceEnabled('/etc/aprsgateway');
  renderSystemPill("APRSGateway", isProcessRunning('APRSGateway'), $aprsEnabled);

  // RF Remote Control
  $rfRemoteEnabled = (getPSRState() == '1');
  renderSystemPill("RF Remote Control", isProcessRunning('/usr/local/sbin/pistar-remote', true), $rfRemoteEnabled);

  // DG-ID Gateway
  $dgidEnabled = getServiceEnabled('/etc/dgidgateway');
  renderSystemPill("DGIdGateway", isProcessRunning('DGIdGateway'), $dgidEnabled);

  // Starnet Server (D-Star Optional)
  // We treat this as optional (isEnabled=false).
  // It will show Green if Running, Grey if Stopped. Never Red.
  renderSystemPill("Starnet Server", isProcessRunning('starnetserverd'), false);

  // TimeServer (D-Star Optional)
  renderSystemPill("TimeServer (D-Star)", isProcessRunning('timeserverd'), false);

  // Parrots
  renderSystemPill("YSFParrot", isProcessRunning('YSFParrot'), $ysfEnabled);
  renderSystemPill("P25Parrot", isProcessRunning('P25Parrot'), $p25Enabled);
  renderSystemPill("NXDNParrot", isProcessRunning('NXDNParrot'), $nxdnEnabled);

  // 6. SYSTEM SERVICES

  // WPSD Nightly Tasks
  renderSystemPill("Nightly Tasks", isSystemdServiceRunning("wpsd-nightly-tasks.timer"), true, "Service Active");

  // WPSD Maintenance
  renderSystemPill("Maint. Service", isSystemdServiceRunning('wpsd-running-tasks.timer'), true, "Service Active");

  // Hostfile Updates
  renderSystemPill("Hostfile Updates", isSystemdServiceRunning("wpsd-hostfile-update.timer"), true, "Service Active");

  // Network Metrics
  renderSystemPill("Net Metrics (vnstat)", isProcessRunning('usr/sbin/vnstatd', true), true);

  // Watchdog
  $watchdogRunning = isProcessRunning('/usr/local/sbin/pistar-watchdog', true);
  renderSystemPill("Services Watchdog", $watchdogRunning, false, $watchdogRunning ? "Running" : "Disabled");

  // Auto AP
  renderSystemPill("Auto AP", autoAPenabled(), false, autoAPenabled() ? "Enabled" : "Disabled");

  // UPnP
  renderSystemPill("UPnP", UPnPenabled(), false, UPnPenabled() ? "Enabled" : "Disabled");

  // Time Sync
  if (isSystemdServiceRunning('chrony.service') == "active") {
    renderSystemPill("Chrony Service", isSystemdServiceRunning('chrony.service'), true);
  } else {
    renderSystemPill("TimeSync Service", isSystemdServiceRunning('systemd-timesyncd.service'), true);
  }

  // Display Drivers (Vendor specific)
  if (isDVmegaCast() == 0) {
      // Optional driver, set isEnabled=false so it's Grey if off.
      renderSystemPill("NextionDriver", isProcessRunning('NextionDriver'), false);
  } else {
      renderSystemPill("Cast UDP Service", isProcessRunning('castudp'), true);
  }
  ?>

</div>
<br />
