<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code

$localTXList = $lastHeard;

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

?>
<div class="table-header-bar">
  <div class="table-title"><?php echo __( 'Local RF Activity' );?></div>
</div>

<div class="modern-table-container">
  <table class="modern-table">
    <thead>
    <tr>
      <th width="15%"><a class="tooltip" href="#"><?php echo __( 'Time' );?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
      <th width="12%"><a class="tooltip" href="#"><?php echo __( 'Callsign' );?><span><b>Callsign</b></span></a></th>
      <th width="10%"><a class="tooltip" href="#"><?php echo __( 'Mode' );?><span><b>Transmitted Mode</b></span></a></th>
      <th width="30%"><a class="tooltip" href="#"><?php echo __( 'Target' );?><span><b>Target, D-Star Reflector, DMR Talk Group etc</b></span></a></th>
      <th width="8%" class="text-center"><a class="tooltip" href="#"><?php echo __( 'Dur' );?>(s)<span><b>Duration in Seconds</b></span></a></th>
      <th width="8%" class="text-center" style="min-width:5ch"><a class="tooltip" href="#"><?php echo __( 'BER' );?><span><b>Bit Error Rate</b></span></a></th>
      <?php if ($_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicast") { ?>
      <th class="noMob text-center" style="min-width:8ch"><a class="tooltip" href="#">RSSI<span><b>Received Signal Strength Indication</b></span></a></th>
      <?php } ?>
    </tr>
    </thead>
    <tbody>
<?php
$counter = 0;
$i = 0;
$TXListLim = count($localTXList);
for ($i = 0; $i < $TXListLim; $i++) {
    $listElem = $localTXList[$i];
    if ($listElem[5] == "RF" && ($listElem[1] == "D-Star" || startsWith($listElem[1], "DMR") || $listElem[1] == "YSF" || $listElem[1]== "P25" || $listElem[1]== "NXDN" )) {
	if ($counter <= 19) {
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
	    if (preg_match('/[\s-]/', $listElem[2])) { // handle and display calls with certain suffixes:	
		} else { // all other modes with dash and/or single space
		    $listElem[2] = preg_replace('/[\s+-]/', ' ', $listElem[2]);
		}
	    }

	    echo"<tr>";
	    echo"<td>$local_time</td>";

	    if (is_numeric($listElem[2]) !== FALSE) {
		if ($listElem[2] > 9999) {
		    echo "<td class='divTableCellMono'><a href=\"".$idLookupUrl.$listElem[2]."\" target=\"_blank\">$listElem[2]</a></td>";
		} else {
		    echo "<td class='divTableCellMono'>$listElem[2]</td>";
		}
	    } elseif (strpos($listElem[2], "openSPOT") !== FALSE) {
		echo "<td class='divTableCellMono'>$listElem[2]</td>";
	    } elseif (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $listElem[2])) {
		echo "<td class='divTableCellMono'>$listElem[2]</td>";
	    } else {
		if (strpos($listElem[2],"-") > 0) {
		    $listElem[2] = substr($listElem[2], 0, strpos($listElem[2],"-"));
		}
		if ($listElem[3] && $listElem[3] != '    ' ) {
		    echo "<td class='divTableCellMono'><a href=\"".$callsignLookupUrl.$listElem[2]."\" target=\"_blank\">$listElem[2]</a>/$listElem[3]</td>";
		} else {
		    echo "<td class='divTableCellMono'><a href=\"".$callsignLookupUrl.$listElem[2]."\" target=\"_blank\">$listElem[2]</a></td>";
		}
	    }

	    echo "<td>".str_replace('Slot ', 'TS', $listElem[1])."</td>";
	    if (file_exists("/etc/.TGNAMES")) {
		if ($listElem[8] == null) {
		    $ber = "&nbsp;";
		} else {
		    $mode = $listElem[8];
		}
		if ($listElem[1] == null) {
		    $ber = "&nbsp;";
		} else {
		    $mode = $listElem[1];
		}
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

	    if ($listElem[6] == null && (file_exists("/etc/.CALLERDETAILS"))) {
		echo "<td colspan =\"3\" style=\"background:#d11141;color:#fff;\">TX</td>";
	    } else if ($listElem[6] == null) {
		// Live duration
		$utc_time = $listElem[0];
		$utc_tz =  new DateTimeZone('UTC');
		$now = new DateTime("now", $utc_tz);
		$dt = new DateTime($utc_time, $utc_tz);
		$duration = $now->getTimestamp() - $dt->getTimestamp();
		$duration_string = $duration<999 ? round($duration) . "+" : "&infin;";
		echo "<td colspan =\"3\" class='text-center' style=\"background:#d11141;color:#fff;\">TX " . $duration_string . " sec</td>";
	    } else if ($listElem[6] == "DMR Data") {
		echo "<td colspan =\"3\" class='tex-center' style=\"background:#00718F;color:#fff;\">DMR Data</td>";
	    } else {
		$utc_time = $listElem[0];
		$utc_tz =  new DateTimeZone('UTC');
		$now = new DateTime("now", $utc_tz);
 		$dt = new DateTime($utc_time, $utc_tz);
		$TA = timeago( $dt->getTimestamp(), $now->getTimestamp() );
		$duration = "<td class='text-center'>$listElem[6]s <span class='noMob'>($TA)</span></td>";
		echo "$duration"; //duration

		if ($listElem[6] >= 10) { // BER is useless < 10 sec. TX
		    // Color the BER Field
		    if (floatval($listElem[8]) == 0) {
			echo "<td class='text-center'>$listElem[8]</td>";
		    } elseif (floatval($listElem[8]) >= 0.0 && floatval($listElem[8]) <= 1.9) {
			echo "<td class='text-center'><span style='color:$backgroundModeCellActiveColor;font-weight:bold'>$listElem[8]</span></td>";
		    } elseif (floatval($listElem[8]) >= 2.0 && floatval($listElem[8]) <= 4.9) {
			echo "<td class='text-center'><span style='color:$backgroundModeCellPausedColor;font-weight:bold'>$listElem[8]</span></td>";
		    } else {
			echo "<td class='text-center'><span style='color:$backgroundModeCellInactiveColor;font-weight:bold;'>$listElem[8]</span></td>";
		    }
		} else { 
		    echo "<td class='text-center'>---</td>";
		}

		if ($_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicast") { // Begin DVMega Cast Logic
		    echo "<td class='noMob text-center'>$listElem[9]</td>"; //rssi
		}
	    }
	    echo"</tr>\n";
	    $counter++;
	}
    }
?>
    </tbody>
  </table>
</div>
