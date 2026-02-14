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

// Load the language support
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';

// Force the Locale to the stock locale just while we run the update
setlocale(LC_ALL, "LC_CTYPE=en_GB.UTF-8;LC_NUMERIC=C;LC_TIME=C;LC_COLLATE=C;LC_MONETARY=C;LC_MESSAGES=C;LC_PAPER=C;LC_NAME=C;LC_ADDRESS=C;LC_TELEPHONE=C;LC_MEASUREMENT=C;LC_IDENTIFICATION=C");

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/advanced/modem_fw_upgrade.php") {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['modem'])) {
	    $selectedOption = $_POST['modem'];
	}
    }

    if (!isset($_GET['ajax'])) {
	system('sudo NP=1 /usr/local/sbin/wpsd-modemupgrade ' . escapeshellarg($selectedOption) . ' > /dev/null 2>&1 &');
	$_SESSION['modemupgrade-isrunning'] = 1;
    }

    // passed sanity chk.
    header('Cache-Control: no-cache');

    if (!isset($_GET['ajax'])) {
	if (file_exists('/var/log/pi-star/WPSD-modemflash.log')) {
	    $_SESSION['update_offset'] = filesize('/var/log/pi-star/WPSD-modemflash.log');
	}
	else {
	    $_SESSION['update_offset'] = 0;
	}
    }

    if (isset($_GET['ajax'])) {
	if (!file_exists('/var/log/pi-star/WPSD-modemflash.log')) {
	    exit();
	}

	if (($handle = fopen('/var/log/pi-star/WPSD-modemflash.log', 'rb')) != false) {
	    if (isset($_SESSION['update_offset'])) {
		fseek($handle, 0, SEEK_END);
		if ($_SESSION['update_offset'] > ftell($handle)) { //log rotated/truncated
		    $_SESSION['update_offset'] = 0; //continue at beginning of the new log
		}

		$data = stream_get_contents($handle, -1, $_SESSION['update_offset']);

		$upgradeIsRunning = shell_exec('ps ax | grep "/usr/local/sbin/wpsd-modemupgrade" | grep -v grep') != null ? "YES" : "NO";
		$oldOffset = $_SESSION['update_offset'];

		$_SESSION['update_offset'] += strlen($data);
		echo "<pre>$data</pre>";

		// we reached the end of the cmd
		if (($oldOffset == $_SESSION['update_offset']) && (isset($_SESSION['modemupgrade-isrunning']) && ($_SESSION['modemupgrade-isrunning'] == 1)) && ($upgradeIsRunning == "NO"))
		{
		    unset($_SESSION['modemupgrade-isrunning']);
		    echo "<pre>
			</pre>";
		}
	    }
	    else {
		fseek($handle, 0, SEEK_END);
		$_SESSION['update_offset'] = ftell($handle);
	    }
	}
	exit();
    }

   if (file_exists('/usr/local/bin/firmware/versions.txt')) {
       $versionData = getNoSectionsConfigContent('/usr/local/bin/firmware/versions.txt');
   }
   if (isset($versionData['mmdvm_hs'])) {
       $mmdvm_hs_version = $versionData['mmdvm_hs'];
       $dvmega_fw_version = $versionData['dvmega'];
       $rpt_version = $versionData['rpt'];
       $fw_ver_msg = "Latest firmware version(s): MMDVM Hotspots: <b><code>v".$mmdvm_hs_version. "</code></b> &bull; DVMega: <b><code>v".$dvmega_fw_version."</code></b> &bull; MMDVM Repeaters: <b><code>v".$rpt_version."</code></b>";
   } else {
       $fw_ver_msg = "Unkown (failed to retrieve firmware version).";
   }

?>

  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html lang="en">
  <head>
    <meta name="language" content="English" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
    <meta http-equiv="Expires" content="0" />
    <title>WPSD <?php echo __( 'Dashboard' )." - Modem Firmware Upgrade";?></title>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
    <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/jquery-timing.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript">
    function disableSubmitButtons() {
            var inputs = document.getElementsByTagName('input');
            for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].type === 'button') {
                            inputs[i].disabled = true;
		            inputs[i].value = 'Please Wait...';
                    }
            }
    }

    function submitform() {
	disableSubmitButtons();
	document.getElementById("up_fw").submit();
    }

    $(function() {
      $.repeat(1000, function() {
        $.get('/admin/advanced/modem_fw_upgrade.php?ajax', function(data) {
          if (data.length < 1) return;
          var objDiv = document.getElementById("tail");
          var isScrolledToBottom = objDiv.scrollHeight - objDiv.clientHeight <= objDiv.scrollTop + 1;
          $('#tail').append(data);
          if (isScrolledToBottom)
            objDiv.scrollTop = objDiv.scrollHeight;
        });
      });
    });
    </script>
  </head>
  <body>
  <div class="container">
  <?php include './header-menu.inc'; ?>
  <div class="contentwide">
  <table width="100%">
  <?php if (empty($_POST['modem'])) { ?>
  <tr><td>
	      <div class="divTable" class="left">
		<div class="divTableBody">
		  <div class="divTableRow">
		    <div class="divTableCellSans">
		      <br />
		      <h2 class="center larger ConfSec">Modem Firmware Upgrade Utility</h2>
		      <div class="larger">
		        <p>This tool will attempt to upgrade your selected modem to the latest version available firmware version:<br />
		          <?php echo $fw_ver_msg; ?>
		        <p>When ready, select your modem type below and click, "Upgrade Modem". Do not interrupt the process or navigate away from the page while the process is running.</p>
		        <p><strong><i class="fa fa-exclamation-circle"></i> Please understand what you are doing, as well as the risks associated with flashing your modem.</strong></p>
		        <p><em>(NOTES: The WPSD project does not develop firmware, nor do we support firmware nor modems. Not all modem types are available in this tool.)</em></p>
		      </div>
		    </div>
	      </div>
	    </div>
	  </div>
  </td></tr>
  <tr><td>
<?php

    $friendlyNames = [
      'hs_hat' => 'MMDVM_HS_Hat (14.7456MHz TCXO) GPIO',
      'hs_hat-12mhz' => 'MMDVM_HS_Hat (12.2880MHz TCXO) GPIO',
      'hs_dual_hat' => 'MMDVM_HS_Dual_Hat (14.7456MHz TCXO) GPIO',
      'hs_dual_hat-12mhz' => 'MMDVM_HS_Dual_Hat (12.2880MHz TCXO) GPIO',
      'zum_rpi' => 'ZUMSpot RPi boards/hotspots GPIO',
      'zum_rpi-duplex' => 'ZUMSpot RPi duplex GPIO board/hotspots',
      'zum_usb' => 'ZUMspot USB stick',
      'zum_libre' => 'ZUMspot Libre Kit or generic MMDVM_HS board',
      'skybridge' => 'SkyBridge hotspots (14.7456MHz TCXO) GPIO',
      'euronode' => 'DVMega-EuroNode hotspots (14.7456MHz TCXO) GPIO',
      'nanodv_npi' => 'NANO_DV NPi GPIO by BG4TGO',
      'nanodv_usb' => 'NANO_DV USB by BG4TG',
      'hs_hat_ambe' => 'HS_HAT_AMBE (14.7456MHz TCXO) GPIO',
      'hs_hat_lonestar-usb' => 'LoneStar LS MMDVM USB (14.7456MHz TCXO) USB',
      'hs_hat_generic' => 'MMDVM_HS_GENERIC (14.7456MHz TCXO) GPIO',
      'hs_hat_generic_duplex' => 'MMDVM_HS_GENERIC_DUPLEX (14.7456MHz TCXO) GPIO',
      'hs_hat_generic_duplex-usb' => 'MMDVM_HS_GENERIC_DUPLEX (14.7456MHz TCXO) USB',
      'hs_hat_nano_hotspot' => 'Nano_hotSPOT by BI7JTA (14.7456MHz TCXO) GPIO',
      'dvmega_gpio' => 'DV-Mega Raspberry Pi Hat (Single or Dual Band) GPIO',
      'dvmega_usb_uno' => 'DV-Mega Arduino Uno Shield USB (ttyUSB0)',
      'dvmega_usb_mega' => 'DV-Mega Arduino Mega Shield USB (ttyUSB0)',
      'rpt_nucleo-64' => 'Repeater - Nucleo64 F446RE GPIO',
      'rpt_nucleo-144' => 'Repeater - Nucleo144 F767ZI GPIO',
      'rpt_mmdvm_hat-v0.2' => 'Repeater - MMDVM_RPT_Hat v0.2 GPIO',
      'rpt_mmdvm_hat-v0.3' => 'Repeater - MMDVM_RPT_Hat v0.3 GPIO',
      'rpt_zum-v0.9' => 'Repeater - ZUM Radio MMDVM for Pi v0.9 GPIO',
      'rpt_zum-v1.0-F4' => 'Repeater - ZUM Radio MMDVM for Pi v1.0 GPIO (F4 chip)',
      'rpt_zum-v1.0-F7' => 'Repeater - ZUM Radio MMDVM for Pi v1.0 GPIO (F7 chip)',
      'rpt_builder_v3' => 'Repeater - Repeater Builder STM32_DVM v3 GPIO',
      'rpt_builder_v4' => 'Repeater - Repeater Builder STM32_DVM v4 GPIO',
      'rpt_builder_v5' => 'Repeater - Repeater Builder STM32_DVM v5 GPIO',
      'rpt_builder_mtr2k_v3-F446' => 'Repeater - Repeater Builder STM32_DVM_MTR2K v3 GPIO (F446 chip)',
      'rpt_builder_mtr2k_v3-F405' => 'Repeater - Repeater Builder STM32_DVM_MTR2K v3 GPIO (F405 chip)',
    ];

    $output = shell_exec('sudo /usr/local/sbin/wpsd-modemupgrade list');

    if ($output !== null) {
        // Split the output into an array of options
        $options = explode("\n", trim($output));

        // Create the select element
        echo '<p><form method="post" id="up_fw">';
        echo '<label for="modem">Select Modem:</label>';
        echo '<select id="modem" name="modem">';
	echo '<option value="" disabled selected>Please choose device type...</option>';
	// Output each option with user-friendly names
	foreach ($options as $option) {
	    $friendlyName = isset($friendlyNames[$option]) ? $friendlyNames[$option] : $option;
	    echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($friendlyName) . '</option>';
 	}
        echo '</select>';
        echo '<input type="button" value="Upgrade Modem" onclick="submitform()">';
        echo '</form></p>';
    } else {
        echo '<p>Error executing the command.</p>';
    }
?>
  </form>
  </td></tr>
  </table>
  </div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
  </div>
  </body>
  <?php } else { ?>

  <tr><td><b>Modem Flash/Upgrade Output:</b></td></tr>
  <tr><td align="left"><div id="tail"><h3>Starting Modem Firmware Upgrade...</h3></div></td></tr>
  </table>
  </div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
  </div>
  </body>
  </html>
<?php }
}