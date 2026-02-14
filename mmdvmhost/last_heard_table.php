<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';         // Version Lib
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code

if (isset($_SESSION['CSSConfigs']['ExtraSettings']['LastHeardRows']) && $_SESSION['WPSDrelease']['WPSD']['ProcNum'] >= 4) {
    $lastHeardRows = $_SESSION['CSSConfigs']['ExtraSettings']['LastHeardRows'];
    if ($lastHeardRows > 100) {  
	$lastHeardRows = "100";  // need an internal limit
    }
} else {
    $lastHeardRows = "40";
}
if (isset($_SESSION['CSSConfigs']['Background'])) {
    $backgroundModeCellActiveColor = $_SESSION['CSSConfigs']['Background']['ModeCellActiveColor'];
    $backgroundModeCellPausedColor = $_SESSION['CSSConfigs']['Background']['ModeCellPausedColor'];
    $backgroundModeCellInactiveColor = $_SESSION['CSSConfigs']['Background']['ModeCellInactiveColor'];
}

if (isset($_SESSION['WPSDdashConfig']['WPSD']['CallLookupProvider'])) {
    $callsignLookupSvc = $_SESSION['WPSDdashConfig']['WPSD']['CallLookupProvider'];
} else {
    $callsignLookupSvc = "QRZ";
}
if (($callsignLookupSvc != "RadioID") && ($callsignLookupSvc != "QRZ")) {
    $callsignLookupSvc = "QRZ";
}
$idLookupUrl = "https://database.radioid.net/database/view?id=";
if ($callsignLookupSvc == "RadioID") {
    $callsignLookupUrl = "https://database.radioid.net/database/view?callsign=";
}
if ($callsignLookupSvc == "QRZ") {
    $callsignLookupUrl = "https://www.qrz.com/db/";
}

// geoLookup/flags
if (!class_exists('xGeoLookup')) require_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.GeoLookup.php');
$Flags = new xGeoLookup();
$Flags->SetFlagFile("/usr/local/etc/countries.json");
$Flags->LoadFlags();

// for name column
$testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);
?>

<div class="table-header-bar">
  <div class="table-title"><?php echo __( 'Gateway Activity' );?></div>
  <div class="table-actions">

    <input type="hidden" name="filter-activity" value="OFF" />
    <div id="lhAc" style="display:flex; align-items:center; gap:5px;">
        <span title="Hide Kerchunks">Hide Kerchunks:</span>
        <input id="toggle-filter-activity" class="toggle toggle-round-flat" type="checkbox" name="display-lastcaller" value="ON" <?php if ( file_exists( '/etc/.FILTERACTIVITY' ) ) { echo 'checked="checked"'; }; ?> aria-checked="true" aria-label="Filter Out Kerchunks (< 1s)" onchange="setFilterActivity(this)" /><label for="toggle-filter-activity" ></label>
        <?php if ( file_exists( '/etc/.FILTERACTIVITY' ) ) : ?>
          <div class="filter-activity-max-wrap">
            <input onChange='setFilterActivityMax(this)' onfocus = 'clearInterval(reloadDynDataId)' class='filter-activity-max' style="width:40px;" type='number' step='0.5' min='0.5' name='filter-activity-max' value='<?php echo file_get_contents( '/etc/.FILTERACTIVITY' ); ?>' />
            <span class="ms">s</span>
          </div>
        <?php endif; ?>
    </div>

    <input type="hidden" name="display-lastcaller" value="OFF" />
    <?php if(isset($_SESSION['WPSDrelease']['WPSD']['ProcNum']) && ($_SESSION['WPSDrelease']['WPSD']['ProcNum'] >= 4)) { ?>
    <div id="lhCN" style="display:flex; align-items:center; gap:5px;">
        <span title="Display Caller Details">Caller Details:</span>
        <input id="toggle-display-lastcaller" class="toggle toggle-round-flat" type="checkbox" name="display-lastcaller" value="ON" <?php if(file_exists('/etc/.CALLERDETAILS')) { echo 'checked="checked"';}?> aria-checked="true" aria-label="Display Caller Details" onchange="setLastCaller(this)" /><label for="toggle-display-lastcaller" ></label>
    </div>
    <?php } ?>

    <?php if(isset($_SESSION['WPSDrelease']['WPSD']['ProcNum']) && ($_SESSION['WPSDrelease']['WPSD']['ProcNum'] >= 4)) { ?>
        <?php if (getEnabled("DMR", $_SESSION['MMDVMHostConfigs']) == 1 || getEnabled("NXDN", $_SESSION['MMDVMHostConfigs']) == 1 || getEnabled("P25", $_SESSION['MMDVMHostConfigs']) == 1 || getServiceEnabled('/etc/ysf2dmr') == 1 || getServiceEnabled('/etc/ysf2p25') == 1 || getServiceEnabled('/etc/ysf2nxdn') == 1) { ?>
        <input type="hidden" name="lh-tgnames" value="OFF" />
        <div id="lhTGN" style="display:flex; align-items:center; gap:5px;">
            <span title="Display Talkgroup Names">Display TG Names:</span>
            <input id="toggle-lh-tgnames" class="toggle toggle-round-flat" type="checkbox" name="lh-tgnames" value="ON" <?php if(file_exists('/etc/.TGNAMES')) { echo 'checked="checked"';}?> aria-checked="true" aria-label="Show Talkgroup Names" onchange="setLHTGnames(this)" /><label for="toggle-lh-tgnames" ></label>
        </div>
        <?php } ?>
    <?php } ?>

  </div>
</div>

<div class="modern-table-container">
  <table class="modern-table">
    <thead>
    <tr>
      <th width="15%"><a class="tooltip" href="#"><?php echo __( 'Time' );?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
      <th width="12%"><a class="tooltip" href="#"><?php echo __( 'Callsign' );?><span><b>Callsign</b></span></a></th>
      <th width="5%" class="text-center"><a class="tooltip" href="#">Country<span><b>Country</b></span></a></th>
      <th width="10%"><a class="tooltip" href="#"><?php echo __( 'Mode' );?><span><b>Transmitted Mode</b></span></a></th>
      <th width="30%"><a class="tooltip" href="#"><?php echo __( 'Target' );?><span><b>Target, D-Star Reflector, DMR Talk Group etc</b></span></a></th>
      <th width="10%" class="text-center"><a class="tooltip" href="#"><?php echo __( 'Src' );?><span><b>Received from source</b></span></a></th>
      <th width="8%" class="text-center"><a class="tooltip" href="#"><?php echo __( 'Dur' );?>(s)<span><b>Duration in Seconds</b></span></a></th>
      <th class="noMob text-center" width="10%"><a class="tooltip" href="#"><?php echo __( 'Loss' );?><span><b>Packet Loss</b></span></a></th>
    </tr>
    </thead>
    <tbody>
<?php
$i = 0;
for ($i = 0;  ($i <= $lastHeardRows - 1); $i++) {
	if (isset($lastHeard[$i])) {
		$listElem = $lastHeard[$i];
		if ( $listElem[2] ) {
                $utc_time = $listElem[0];
                $utc_tz =  new DateTimeZone('UTC');
                $local_tz = new DateTimeZone(date_default_timezone_get ());
                $dt = new DateTime($utc_time, $utc_tz);
                $dt->setTimeZone($local_tz);
                if (constant("TIME_FORMAT") == "24") {
                    $local_time = $dt->format('H:i:s M j');
                } else {
                    $local_time = $dt->format('h:i:s A M j');
                }
		// YSF & D-Star sometimes has malformed calls with bad spaces and freeform text...address these
		if (preg_match('/[\s-]/', $listElem[2])) { // handle and display calls with certain suffixes:
			$listElem[2] = preg_replace('!\s+!', ',', $listElem[2]);
			$listElem[2] = preg_replace('/-/', ',', $listElem[2]);
		    } else { // all other modes with dash and/or single space
			$listElem[2] = preg_replace('/[\s+-]/', ',', $listElem[2]);
		    }
		    $callBase = explode(",", $listElem[2]);
		    $callPre = $callBase[0];
		    if (empty($callBase[1])) { // handler for suffix specified, but has space or is empty (e.g. clueless YSF users)
			$callSuff = ""; // kill invalid suffix
		    } else {
			$callSuff = "-$callBase[1]"; // "CALL-SUFF" format
		    }
		} else { // no suffix
		    $callPre = $listElem[2];
		    $callSuff = "";
		}
		// init geo/flag class
		list ($Flag, $Name) = $Flags->GetFlag($listElem[2]);
		if (is_numeric($listElem[2]) !== FALSE || !preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $listElem[2])) {
		    $flContent = " ";
		} elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/images/flags/".$Flag.".png")) {
		    $flContent = "<a class='tooltip' href=\"$callsignLookupUrl"."$callPre\" target=\"_blank\"><div style='padding: 0 12px;'><img src='/images/flags/$Flag.png?version=$versionCmd' alt='' style='height:18px;' /></div><span>$Name</span></a>";
		} else {
		    $flContent = " ";
		}
		echo"<tr>";
		echo"<td title='Row #".($i+1)."'>$local_time</td>";
		if (is_numeric($listElem[2])) {
		    if ($listElem[2] > 9999) {
			echo "<td class='divTableCellMono'><a href=\"".$idLookupUrl.$listElem[2]."\" target=\"_blank\">$listElem[2]</a></td><td class='text-center'>$flContent</td>";
		    } else {
			echo "<td class='divTableCellMono'>$callPre$callSuff</td><td class='text-center'>$flContent</td>";
		    }
		} elseif (strpos($listElem[2], "openSPOT") !== FALSE) {
		    echo "<td class='divTableCellMono'>$callPre$callSuff</td><td>&nbsp</td>";
		} elseif (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $listElem[2])) {
		    echo "<td class='divTableCellMono'>$callPre$callSuff</td><td class='text-center'>$flContent</td>";
		} else {
		    if ( $listElem[3] && $listElem[3] != '    ' ) {
			echo "<td class='divTableCellMono'><a href=\"$callsignLookupUrl"."$listElem[2]\" target=\"_blank\">$listElem[2]</a>/$listElem[3]</td><td class='text-center'>$flContent</td>";
		    } else {
			echo "<td class='divTableCellMono'><a href=\"$callsignLookupUrl"."$callPre\" target=\"_blank\">$callPre</a>$callSuff</td><td class='text-center'>$flContent</td></td>";
		    }
		}

		echo "<td>".str_replace('Slot ', 'TS', $listElem[1])."</td>";

		if (file_exists("/etc/.TGNAMES")) {
		    $mode = $listElem[1];

		    // Handle "via" and "at"
		    if (strpos($listElem[4], "via ")) {
			$listElem[4] = preg_replace("/via (.*)/", "<span class='noMob'> $1</span>", $listElem[4]);
		    }
		    if (strpos($listElem[4], "at ")) {
			$listElem[4] = preg_replace("/at (.*)/", "<span class='noMob'>at $1</span>", $listElem[4]);
		    }

		    // Pad if the target is too short
		    if (strlen($listElem[4]) == 1) { 
			$listElem[4] = str_pad($listElem[4], 8, " ", STR_PAD_LEFT); 
		    }

		    // Set target based on specific condition
		    if (substr($listElem[4], 0, 6) === 'CQCQCQ') {
			$target = $listElem[4];
		    } else {
			$target = trim($listElem[4]);  // Trim to avoid extra spaces
		    }

		    // Check if it's a private call or TG
		    if (strpos($mode, "DMR") !== false && strpos($target, "TG") === false) {
			$target = "Private Call to $target";  // Private call detected
		    } else {
			$target = preg_replace('/TG /', '', $target);  // Clean up "TG" from the target
			$target = tgLookup($mode, $target);  // Perform TG lookup
		    }

		    echo "<td>$target</td>";
		} else {
		    // Handle "via" and "at"
		    if (strpos($listElem[4], "via ")) {
		        $listElem[4] = preg_replace("/via (.*)/", "<span class='noMob'> $1</span>", $listElem[4]);
		    }
		    if (strpos($listElem[4], "at ")) {
		        $listElem[4] = preg_replace("/at (.*)/", "<span class='noMob'>at $1</span>", $listElem[4]);
		    }

		    // Set target based on specific condition
		    if (substr($listElem[4], 0, 6) === 'CQCQCQ') {
		        echo "<td>$listElem[4]</td>";
		    } else {
		        $listElem[4] = trim($listElem[4]);  // Trim to avoid extra spaces

		        // Check if it's a private call or TG
		        if (strpos($listElem[1], "DMR") !== false && strpos($listElem[4], "TG") === false) {
		            $listElem[4] = "Private Call to $listElem[4]";  // Private call detected
		        }

		        echo "<td>$listElem[4]</td>";
		    }
		}

		if ($listElem[5] == "RF") {
		    echo "<td class='text-center'><span style='color:$backgroundModeCellInactiveColor;font-weight:bold;'>RF</span></td>";
		} else {
		    echo "<td class='text-center'>$listElem[5]</td>";
		}
		if ($listElem[6] == null && (file_exists("/etc/.CALLERDETAILS")))  {
		    echo "<td colspan =\"2\" class='activity-duration text-center' style=\"background:#d11141;color:#fff;\">TX</td>";
		} else if ($listElem[6] == null) {
		    // Live duration
		    $utc_time = $listElem[0];
		    $utc_tz =  new DateTimeZone('UTC');
		    $now = new DateTime("now", $utc_tz);
		    $dt = new DateTime($utc_time, $utc_tz);
		    $duration = $now->getTimestamp() - $dt->getTimestamp();
		    $duration_string = $duration<999 ? round($duration) . "+" : "&infin;";
		    echo "<td colspan=\"2\" class='activity-duration text-center' style=\"background:#d11141;color:#fff;\">TX " . $duration_string . " sec</td>";
		} else if ($listElem[6] == "DMR Data") {
			echo "<td class='noMob text-center' colspan =\"3\" style=\"background:#00718F;color:#fff;\">DMR Data</td>";
		} else if ($listElem[6] == "POCSAG") {
			echo "<td class='noMob text-center' colspan=\"3\" style=\"background:#00718F;color:#fff;\">POCSAG Data</td>";
		} else {
		    echo "<td class='activity-duration text-center'>$listElem[6]</td>";

		    // Color the Loss Field
		    if (floatval($listElem[7]) < 1) { echo "<td class='noMob text-center'>$listElem[7]</td>"; }
		    elseif (floatval($listElem[7]) == 1) { echo "<td class='noMob text-center'><span style='color:$backgroundModeCellActiveColor;font-weight:bold'>$listElem[7]</span></td>"; }
		    elseif (floatval($listElem[7]) > 1 && floatval($listElem[7]) <= 3) { echo "<td class='noMob text-center'><span style='color:$backgroundModeCellPausedColor;font-weight:bold'>$listElem[7]</span></td>"; }
		    else { echo "<td class='noMob text-center'><span style='color:$backgroundModeCellInactiveColor;font-weight:bold;'>$listElem[7]</span></td>"; }
		}
		echo"</tr>\n";
		if (!empty($listElem[10] && file_exists("/etc/.SHOWDMRTA")) && (!file_exists('/etc/.CALLERDETAILS'))) {
		    echo "<tr>";
		    echo "<td style='background:$backgroundContent;'></td>";
		    echo "<td colspan='8' style=\"text-align:left;background:#0000ff;color:#fff;\">&#8593; $listElem[10]</td>";
		    echo "</tr>";
		} elseif (!empty($listElem[10] && file_exists("/etc/.SHOWDMRTA")) && (file_exists('/etc/.CALLERDETAILS'))) {
		    echo "<tr>";
		    echo "<td style='background:$backgroundContent;'></td>";
		    echo "<td colspan='9' style=\"text-align:left;background:#0000ff;color:#fff;\">&#8593; $listElem[10]</td>";
		    echo "</tr>";
		}
	    }
	}
?>
    </tbody>
  </table>
</div>
<script>clear_activity();</script>
