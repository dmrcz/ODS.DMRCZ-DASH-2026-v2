<?php
if (file_exists('/etc/.CALLERDETAILS')) {
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';         // Version Lib
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

    // geoLookup/flags
    if (!class_exists('xGeoLookup')) require_once($_SERVER['DOCUMENT_ROOT'].'/classes/class.GeoLookup.php');
    $Flags = new xGeoLookup();
    $Flags->SetFlagFile("/usr/local/etc/countries.json");
    $Flags->LoadFlags();

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

// get the data from the MMDVMHost logs
$i = 0;
for ($i = 0;  ($i <= 0); $i++) { //Last 20  calls
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
	    // YSF & D-Star sometimes has malformed calls with a space and freeform text...address these
            if (preg_match('/ /', $listElem[2])) {
                $listElem[2] = preg_replace('/ .*$/', "", $listElem[2]);
            }
            if (is_numeric($listElem[2]) !== FALSE) {
		if ($listElem[2] > 9999) {
                    $callsign = "<a href=\"".$idLookupUrl.$listElem[2]."\" target=\"_blank\">$listElem[2]</a>";
		} else {
                    $callsign = $listElem[2];
		}
	    } elseif (strpos($listElem[2], "openSPOT") !== FALSE) {
		$callsign = $listElem[2];
            } elseif (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $listElem[2])) {
                $callsign = $listElem[2];
            } else {
                if (strpos($listElem[2],"-") > 0) {
                    $listElem[2] = substr($listElem[2], 0, strpos($listElem[2],"-"));
                }
		if ( $listElem[3] && $listElem[3] != '    ' ) {
		    $callsign = "<a href=\"".$callsignLookupUrl.$listElem[2]."\" target=\"_blank\">$listElem[2]</a>/$listElem[3]";
		} else {
		    $callsign = "<a href=\"".$callsignLookupUrl.$listElem[2]."\" target=\"_blank\">$listElem[2]</a>";
		}
            }

	    $source = $listElem[5];

	    if ($listElem[6] == null) {
		// Live duration
		$utc_time = $listElem[0];
		$utc_tz =  new DateTimeZone('UTC');
		$now = new DateTime("now", $utc_tz);
		$dt = new DateTime($utc_time, $utc_tz);
		$duration = $now->getTimestamp() - $dt->getTimestamp();
		$duration_string = $duration<999 ? round($duration) . "+" : "&infin;";
		$duration = "<td class='text-center' style=\"background:#d11141;color:#fff;font-size:1.3em;\">TX " . $duration_string . " sec</td>";
		// dynamic TX <title>
		echo "<script>if(typeof window.original_title === 'undefined'){window.original_title = jQuery('title').text();}</script>";
		echo $_SESSION['MYCALL'] != $listElem[2] ? "<script>jQuery('title').text('>$listElem[2]<');</script>" : "<script>jQuery('title').text('TX');</script>";
		echo "<script>jQuery('link[rel=\"shortcut icon\"').attr('href', '/images/favicon_active.ico?version=$versionCmd');</script>";
	    } else if ($listElem[6] == "DMR Data") {
		$duration =  "<td class='text-center' style=\"background:#00718F;color:#ff;font-size:1.3em;\">DMR Data</td>";
	    } else if ($listElem[6] == "POCSAG") {
		$duration =  "<td class='text-center' style=\"background:#00718F;color:#fff;font-size:1.3em;\">POCSAG</td>";
	    } else {
		$utc_time = $listElem[0];
		$utc_tz =  new DateTimeZone('UTC');
		$now = new DateTime("now", $utc_tz);
 		$dt = new DateTime($utc_time, $utc_tz);
		$TA = timeago( $dt->getTimestamp(), $now->getTimestamp() );
		$duration = "<td class='text-center' style='font-size:1.3em;'>$listElem[6]s <span class='noMob'>($TA)</span></td>";
		// dynamic <title> reset
		echo "<script>if(typeof window.original_title !== 'undefined'){jQuery('title').text(window.original_title)}</script>";
		echo "<script>jQuery('link[rel=\"shortcut icon\"').attr('href', '/images/favicon.ico?version=$versionCmd');</script>";
	    }

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

	    $mode = str_replace("Slot ", "TS", $mode);

	    if (!is_numeric($listElem[2])) {
        	$searchCall = $listElem[2];
        	$callMatch = array();
		if ($mode == "NXDN") {
		    $handle = @fopen("/usr/local/etc/NXDN.csv", "r");
		} else { # all other modes
		    $handle = @fopen("/usr/local/etc/stripped.csv", "r");
		}
        	if ($handle)
        	{
                    while (!feof($handle))
                    {
                	$buffer = fgets($handle);
                	if (strpos($buffer, $searchCall) !== FALSE)
                	{
			    $csvBuffer = explode(",", $buffer);
			    if(strpos($searchCall, $csvBuffer[1]) !== FALSE)
				$callMatch[] = $buffer;
			}
		    }
		    fclose($handle);
		}
		$callMatch = explode(",", $callMatch[0]);
		$name    = sentence_cap(" ", "$callMatch[2]");
		$city    = ucwords_ext( $callMatch[4] );
		$state   = ucwords_ext( $callMatch[5] );
		$country = ucwords_ext( $callMatch[6] );
		if(strpos($country, "United States") !== false) {
		   $country = str_replace("United States", "USA", $country);
		}
		if (strlen($country) > 150) {
		    $country = substr($country, 0, 120) . '...';
		}
		if (empty($callMatch[0])) {
		    $name = getName($listElem[2]);
	    	    // init geo/flag class for country name as fallback
	    	    list ($Flag, $Name) = $Flags->GetFlag($listElem[2]);
		    $country = $Name;
		}
	    }

	    $target = $listElem[4];
	    if (file_exists("/etc/.TGNAMES")) {

		// Handle "via" and "at"
		if (strpos($target, "via ")) {
		    $target = preg_replace("/via (.*)/", "<span class='noMob'> $1</span>", $target);
		}
		if (strpos($target, "at ")) {
		    $target = preg_replace("/at (.*)/", "<span class='noMob'>at $1</span>", $target);
		}

		// Pad if the target is too short
		if (strlen($target) == 1) {
		    $target = str_pad($target, 8, " ", STR_PAD_LEFT);
		}

		// Set target based on specific condition
		if (substr($target, 0, 6) === 'CQCQCQ') {
		    $target = $target;
		} else {
		    $target = trim($target);  // Trim to avoid extra spaces
		}

		// Check if it's a private call or TG
		if (strpos($mode, "DMR") !== false && strpos($target, "TG") === false) {
		    $target = "Private Call to $target";  // Private call detected
		} else {
		    $target = preg_replace('/TG /', '', $target);  // Clean up "TG" from the target
		    $target = tgLookup($mode, $target);  // Perform TG lookup
		}
            } else {
		// Handle "via" and "at"
		if (strpos($target, "via ")) {
		    $target = preg_replace("/via (.*)/", "<span class='noMob'> $1</span>", $target);
		}
		if (strpos($target, "at ")) {
		    $target = preg_replace("/at (.*)/", "<span class='noMob'>at $1</span>", $target);
		}

		// Set target based on specific condition
		if (substr($target, 0, 6) === 'CQCQCQ') {
		    $target = $target;
		} else {
		    $target = trim($target);  // Trim to avoid extra spaces

		    // Check if it's a private call or TG
		    if (strpos($mode, "DMR") !== false && strpos($target, "TG") === false) {
			$target = "Private Call to $target";  // Private call detected
		    }
		}
	    }

	    $modeArray = array('DMR', 'NXDN', 'P25');
	    if (strpos($mode, $modeArray[0]) !== false) {
		$target = "$target";
	    }

	    if($listElem[2] == "4000" || $listElem[2] == "9990" || $listElem[2] == "DAPNET") {
		$name = "";
		$city = "";
		$state = "";
		$country = "---";
		if ($listElem[2] == "DAPNET") {
		    $target = "---";
		}
		$duration = "<td class='text-center' style='font-size:1.3em;'>---</td>";
	    }
	    // init geo/flag class
	    list ($Flag, $Name) = $Flags->GetFlag($listElem[2]);
	    if (is_numeric($listElem[2]) !== FALSE || !preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $listElem[2])) {
 		$flContent = "---";
	    } elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/images/flags/".$Flag.".png")) {
		$flContent = "<a class='tooltip' href=\"http://www.qrz.com/db/$listElem[2]\" target=\"_blank\"><img src='/images/flags/$Flag.png?version=$versionCmd' alt='' style='height:25px;' /><span>$Name</span></a>";
	    } else {
		$flContent = "---";
	    }

?>
<div class="table-header-bar">
  <div class="table-title">Current / Last Caller Details</div>
</div>

<div class="modern-table-container">
  <table class="modern-table" style="word-wrap: break-word; white-space:normal;">
    <thead>
    <tr>
      <th><a class="tooltip" href="#"><?php echo __( 'Callsign' );?><span><b>Callsign</b></span></a></th>
      <th width="50px" class="text-center">Country</th>
      <?php if ($name != $searchCall) { ?>
      <th>Name</th>
      <?php } ?>
      <th class='noMob'>Location</th>
      <th><a class="tooltip" href="#"><?php echo __( 'Mode' );?><span><b>Transmitted Mode</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo __( 'Target' );?><span><b>Target, D-Star Reflector, DMR Talk Group etc</b></span></a></th>
      <th class="text-center"><a class="tooltip" href="#"><?php echo __( 'Src' );?><span><b>Received from source</b></span></a></th>
      <th class="text-center"><a class="tooltip" href="#"><?php echo __( 'Dur' );?>(s)<span><b>Duration in Seconds</b></span></a></th>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="padding:3px 20px 5px 20px;" class='divTableCellMono'><strong style="font-size:1.5em;"><?php echo $callsign ?? ' '; ?></strong></td>
      <td class="text-center"><?php echo $flContent; ?></td>
      <?php if ($name != $searchCall) { ?>
      <td style="font-size:1.3em;"><?php echo $name; ?></td>
      <?php } ?>
      <td class='noMob' style="font-size:1.3em;"><?php
		if (!empty($city)) {
			echo $city .", ";
		}
		if (!empty($state)) {
			echo $state . ", ";
		} if (!empty($country)) {
			echo $country;
		} ?></td>
      <td style="font-size:1.3em;"><?php echo $mode ?? ' '; ?></td>
      <td style="font-size:1.3em;"><?php echo $target ?? ' '; ?></td>
      <?php
	if ($listElem[5] == "RF") {
		echo "<td class='text-center' style='font-size:1.3em;'><span style='color:$backgroundModeCellInactiveColor;font-weight:bold;'>RF</span></td>";
	} else {
    		echo" <td class='text-center' style='font-size:1.3em;'>".$source ?? ' '."</td>";
	}
        echo $duration;
    ?>
     </tr>
<?php
	    }
	}
    }
?>
    </tbody>
  </table>
</div>
<?php
}
?>
