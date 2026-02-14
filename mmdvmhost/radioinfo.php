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

// Pre-process Radio Status to determine styles and content
$statusText = "IDLE";
$cardStyle = "";  // Default: Use theme background
$valueStyle = ""; // Default: Use theme text color

if (isset($lastHeard[0])) {
    $isTXing = false;
    // Go through the whole LH array, backward, looking for transmission.
    for (end($lastHeard); (($currentKey = key($lastHeard)) !== null); prev($lastHeard)) {                                                                         
            $listElem = current($lastHeard);
            if ($listElem[2] && ($listElem[6] == null) && ($listElem[5] !== 'RF')) {                                                                              
                $isTXing = true;
                $txMode = preg_split('#\s+#', $listElem[1])[0];
                $statusText = "TX $txMode";
                // TX Style - Red Background, White Text
                $cardStyle = "background-color: #d11141;";
                $valueStyle = "color: white;";
                break;
            }
    }
    if ($isTXing == false) {
            $listElem = $lastHeard[0];
            $actMode = getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']);
        if ($actMode === 'idle') {
            if (isProcessRunning("MMDVMHost")) {
                $statusText = "IDLE";
                // Idle uses defaults
            } else { 
                $statusText = "OFFLINE";
                // Offline Style - Default Background, Red Text
                $valueStyle = "color: #e74c3c;";
            }
        }
        else if ($actMode === NULL) {
            if (isProcessRunning("MMDVMHost")) {
                $statusText = "IDLE";
            } else {
                $statusText = "OFFLINE";
                $valueStyle = "color: #e74c3c;";
            }
        }
        else if ($listElem[2] && $listElem[6] == null) {
            $statusText = "RX " . $actMode;
            // RX Style - Green Background, White Text
            $cardStyle = "background-color: #2ecc71;";
            $valueStyle = "color: white;";
        }
        else {
            $statusText = "Listen " . $actMode;
            // Listen Style - Yellow/Orange Background, Black Text
            $cardStyle = "background-color: #ffc425;";
            $valueStyle = "color: black;";
        }   
    }   
}   
else {
    $statusText = "IDLE";
}
?>

<div class="dashboard-header-stats">
    
    <div class="stat-card" style="<?php echo $cardStyle; ?>">
        <span class="stat-label"><?php _e( 'Radio Status' ); ?></span>
        <div class="stat-value" style="<?php echo $valueStyle; ?>">
            <?php echo $statusText; ?>
        </div>
    </div>

    <?php if ((isDVmegaCast() == 1) && (($_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasths") || ($_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasthd"))) { ?>
        <div class="stat-card">
            <span class="stat-label">Hotspot Freq</span>
            <div class="stat-value"><?php echo getMHZ(getConfigItem("Info", "RXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></div>
        </div>
    <?php } else { ?>
      <?php if(getConfigItem("General", "Duplex", $_SESSION['MMDVMHostConfigs']) == "1") { ?>
        <div class="stat-card">
            <span class="stat-label">TX Freq</span>
            <div class="stat-value"><?php echo getMHZ(getConfigItem("Info", "TXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></div>
        </div>
        <div class="stat-card">
            <span class="stat-label">RX Freq</span>
            <div class="stat-value"><?php echo getMHZ(getConfigItem("Info", "RXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></div>
        </div>
      <?php } else { ?>
        <div class="stat-card">
            <span class="stat-label">Frequency</span>
            <div class="stat-value"><?php echo getMHZ(getConfigItem("Info", "RXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></div>
        </div>
      <?php } } ?>

      <?php if ((isDVmegaCast() == 1) && $_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasths" || $_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasthd") { ?>
        <div class="stat-card">
            <span class="stat-label">Cast Mode</span>
            <div class="stat-value">Hotspot: Simplex</div>
        </div>
      <?php } ?>
      
      <?php if (isDVmegaCast() == 0) { ?>
      <div class="stat-card">
          <span class="stat-label">Mode</span>
          <div class="stat-value"><?php if(getConfigItem("General", "Duplex", $_SESSION['MMDVMHostConfigs']) == "1") { echo "Duplex"; } else { echo "Simplex"; } ?></div>
      </div>
      <?php } ?>

      <?php if (isDVmegaCast() == 0 && strpos($_SESSION['ModemConfigs']['Modem']['Hardware'], 'dvmpi') === false) { ?> 
      <div class="stat-card">
          <span class="stat-label">TCXO</span>
          <div class="stat-value"><?php echo $_SESSION['DvModemTCXOFreq']; ?></div>
      </div>
      <?php } ?>

      <div class="stat-card">
          <span class="stat-label">Firmware</span>
          <div class="stat-value"><?php echo $_SESSION['DvModemFWVersion']; ?></div>
      </div>

</div>
