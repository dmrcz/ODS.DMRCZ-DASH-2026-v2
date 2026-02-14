<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    unset($_SESSION['WPSDrelease']); // ensures bin. version #'s are refreshed

    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
require_once($_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php');

// Defaults
$backgroundModeCellActiveColor = "#27ae60";
$backgroundModeCellDisabledColor = "#ccc";

if (isset($_SESSION['CSSConfigs'])) {
    if (isset($_SESSION['CSSConfigs']['Background']['ModeCellActiveColor'])) $backgroundModeCellActiveColor = $_SESSION['CSSConfigs']['Background']['ModeCellActiveColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['ModeCellDisabledColor'])) $backgroundModeCellDisabledColor = $_SESSION['CSSConfigs']['Background']['ModeCellDisabledColor'];
}

$instanceUUID = $_SESSION['WPSDrelease']['WPSD']['UUID'];

$DASHBOARD_DIR = "/var/www/dashboard";
$SBIN_DIR = "/usr/local/sbin";
$BIN_DIR = "/usr/local/bin";
$CAST_DIR = "/opt/cast";

function displayRepoStatus($dir) {
    $repo = trim(shell_exec("git --work-tree={$dir} --git-dir={$dir}/.git config --get remote.origin.url"));
    $ver_cmd = trim(shell_exec("git --work-tree={$dir} --git-dir={$dir}/.git rev-parse HEAD"));
    $ver_cmd = substr($ver_cmd, 0, 10); // Get first 10 characters of hash

    echo "Ver.# {$ver_cmd}\n";
}

function getMacAddresses() {
    $interfaces = [];

    $output = shell_exec('ip link');
    preg_match_all('/^\d+:\s+(\w+):.*?\n\s+link\/ether\s+([0-9a-f:]+)/im', $output, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $interfaces[] = ['interface' => $match[1], 'mac' => $match[2]];
    }

    return $interfaces;
}
$interfaces = getMacAddresses();

function system_information() {
    @list($system, $host, $kernel) = preg_split('/[\s,]+/', php_uname('a'), 5);
    $meminfo = false;
    if (@is_readable('/proc/meminfo')) {
        $data = explode("\n", file_get_contents("/proc/meminfo"));
        $meminfo = array();
        foreach ($data as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $val) = explode(":", $line);
                $meminfo[$key] = 1024 * floatval( trim( str_replace( ' kB', '', $val ) ) );
            }
        }
    }
    return array('date' => date('Y-m-d H:i:s T'),
                 'mem_info' => $meminfo,
                 'partitions' => disk_list(),
		 'os' => preg_replace('/\(|\)/','"', trim( exec( 'lsb_release -sd' ) )),
    );
}

function disk_list() {
    $partitions = array();
    // Fetch partition information from df command
    // I would have used disk_free_space() and disk_total_space() here but
    // there appears to be no way to get a list of partitions in PHP?
    $output = array();
    @exec('df --block-size=1', $output);
    foreach($output as $line) {
        $columns = array();
        foreach(explode(' ', $line) as $column) {
            $column = trim($column);
            if($column != '') $columns[] = $column;
        }

        // Only process 6 column rows
        // (This has the bonus of ignoring the first row which is 7)
        if(count($columns) == 6) {
            $partition = $columns[5];
            $partitions[$partition]['Temporary']['bool'] = in_array($columns[0], array('tmpfs', 'devtmpfs'));
            $partitions[$partition]['Partition']['text'] = $partition;
            $partitions[$partition]['FileSystem']['text'] = $columns[0];
            if(is_numeric($columns[1]) && is_numeric($columns[2]) && is_numeric($columns[3])) {
                $partitions[$partition]['Size']['value'] = $columns[1];
                $partitions[$partition]['Free']['value'] = $columns[3];
                $partitions[$partition]['Used']['value'] = $columns[2];
            }
            else {
                // Fallback if we don't get numerical values
                $partitions[$partition]['Size']['text'] = $columns[1];
                $partitions[$partition]['Used']['text'] = $columns[2];
                $partitions[$partition]['Free']['text'] = $columns[3];
            }
        }
    }
    return $partitions;
}

function formatSize( $bytes ) {
    $types = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
    for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
}

function timesyncdProc() {
    $cmd = exec('systemctl status systemd-timesyncd.service | grep -o running');
    if (strpos($cmd, "running") !== false) {
	return 1;
    } else {
	return 0;
    }
}

function chronyProc() {
    $cmd = exec('systemctl status chrony.service | grep -o running');
    if (strpos($cmd, "running") !== false) {
	return 1;
    } else {
	return 0;
    }
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
	<title>WPSD - Hardware/Software Details</title>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
	<script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
	<script type="text/javascript" src="/js/jquery-timing.min.js?version=<?php echo $versionCmd; ?>"></script>
	<script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
	<style>
         .progress {
             background-color: <?php echo $backgroundModeCellDisabledColor; ?>;
             border-radius: 8px;
             padding: 3px;
             margin-bottom: 8px;
             box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
             height: 26px;
             overflow: hidden;
         }
         .progress-info .bar, .progress .bar-info {
             background-color: <?php echo $backgroundModeCellActiveColor; ?>;
             background-image: linear-gradient(180deg, rgba(255,255,255,0.15), rgba(255,255,255,0));
             border-radius: 6px;
             color: #fff;
             float: left;
             font-size: 18px;
             font-weight: 600;
             height: 100%;
             line-height: 26px;
             text-align: center;
             text-shadow: 0 1px 1px rgba(0,0,0,0.2);
             transition: width 0.6s ease;
             white-space: nowrap;
         }
	</style>
	<script type="text/javascript">
	 function refreshTable () {
	     $("#infotable").load(" #infotable > *");
	 }
	 var timer = setInterval(function(){refreshTable()}, 10000);

	 function refreshTS () {
	     $("#synctable").load(" #synctable > *");
	 }
	 var timer = setInterval(function(){refreshTS()}, 10000);
	 window.time_format = '<?php echo constant("TIME_FORMAT"); ?>';
	</script>
    </head>
    <body>
	<div class="container">
	    <div class="header">
		<div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
		<?php if ($_SESSION['CURRENT_PROFILE']) { ?><div class="SmallHeader shLeft noMob"> | <?php echo __( 'Current Profile' ).": ";?> <?php echo $_SESSION['CURRENT_PROFILE']; ?></div><?php } ?>
                <div class="SmallHeader shRight">
                <div id="CheckUpdate">
                <?php
                    include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php';
                ?>
                </div><br />
                </div>
		<h1>WPSD Hardware/Software Details</h1>
		<p>
		    <div class="navbar">
              <script type= "text/javascript">
                function reloadDateTime(){
                  $( '#timer' ).html( _getDatetime( window.time_format ) );
                  setTimeout(reloadDateTime,1000);
                }
              reloadDateTime();
              </script>
              <div class="headerClock">
                <span id="timer"></span>
            </div>
			<a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
			<a class="menuupdate" href="/admin/update.php"><?php echo __( 'WPSD Update' );?></a>
			<a class="menupower" href="/admin/power.php"><?php echo __( 'Power' );?></a>
			<a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
			<a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
		    </div>
		</p>
	    </div>
	    <div class="contentwide">

            <?php
                echo '<script type="text/javascript">'."\n";
                echo 'function reloadSysInfo(){'."\n";
                echo '  $("#sysInfo").load("/includes/system.php",function(){ setTimeout(reloadSysInfo,5000) });'."\n";
                echo '}'."\n";
                echo 'setTimeout(reloadSysInfo,5000);'."\n";
                echo '</script>'."\n";
                echo '<div id="sysInfo">'."\n";
                include $_SERVER['DOCUMENT_ROOT'].'/includes/system.php';
                echo '</div>'."\n";
            ?>

		<h3 class='larger' style="text-align:left;font-weight:bold;margin:5px 0 2px 0;">System Status</h3>
		<table id="infotable" width="100%" border="0">
          <tr>
            <th class='larger' align='left'>WPSD System Components</th>
            <th class='larger' align='left'>Version</th>
          </tr>
		  <tr>
		    <td align='left' class='sans'>WPSD Dashboard Web Software</td>
		    <td align='left'><?php displayRepoStatus('/var/www/dashboard'); ?></td>
		  </tr>
		  <tr>
		    <td align='left' class='sans'>WPSD Support Utilites and Programs</td>
		    <td align='left'><?php displayRepoStatus('/usr/local/sbin'); ?></td>
		  </tr>
		  <tr>
		    <td align='left' class='sans'>WPSD Digital Voice and Related Binaries</td>
		    <td align='left'><?php displayRepoStatus('/usr/local/bin'); ?></td>
		  </tr>
		  <?php if (isDVmegaCast() == 1) { ?>
		  <tr>
		    <td align='left' class='sans'>WPSD DVMega CAST Software</td>
		    <td align='left'><?php displayRepoStatus('/opt/cast'); ?></td>
		  </tr>
		  <?php } ?>
        	  <tr>
          	    <th class='larger' align='left'>Network Interface(s)</th>
           	    <th class='larger' align='left'>MAC Address</th>
        	  </tr>
        	  <?php foreach ($interfaces as $interface): ?>
            	  <tr>
                    <td align='left' class='sans'><?php echo htmlspecialchars($interface['interface']); ?></td>
                    <td align='left'><?php echo htmlspecialchars($interface['mac']); ?></td>
            	  </tr>
        	  <?php endforeach; ?>

		    <?php
		    // OS Information
		    $system = system_information();
		    echo "<tr><th class='larger' align='left' class='sans'>Host System</th><th class='larger' align='left'>Details</th></tr>";
		    echo "<tr><td align='left' class='sans'>Operating System</td><td align='left'>{$system['os']}, release ver. $osVer</td></tr>";
		    echo "<tr><td align='left' class='sans''>Kernel</td><td align='left'>".php_uname('r')."</td></tr>";
		    echo "<tr><td align='left' class='sans'>Hardware &amp; Platform</td><td align='left'>".$_SESSION['WPSDrelease']['WPSD']['Hardware']."<br />".$_SESSION['WPSDrelease']['WPSD']['Platform']."</td></tr>";
		    echo "<tr><td align='left' class='sans'>Hardware UUID</td><td align='left'>$instanceUUID</td></tr>";

		    // Ram information
		    if ($system['mem_info']) {
			echo "  <tr><th class='larger' align='left'>Memory</th><th class='larger' align='left'>Stats</th></tr>\n";
			$sysRamUsed = $system['mem_info']['MemTotal'] - $system['mem_info']['MemFree'] - $system['mem_info']['Buffers'] - $system['mem_info']['Cached'];
			$sysRamPercent = sprintf('%.2f',($sysRamUsed / $system['mem_info']['MemTotal']) * 100);
			echo "  <tr><td class='sans' align=\"left\">RAM</td><td align=\"left\"><div class='progress progress-info' style='margin-bottom: 0;'><div class='bar' style='width: ".$sysRamPercent."%;'>Used&nbsp;".$sysRamPercent."%</div></div>";
			echo "  <b>Total:</b> ".formatSize($system['mem_info']['MemTotal'])."<b> Used:</b> ".formatSize($sysRamUsed)."<b> Free:</b> ".formatSize($system['mem_info']['MemTotal'] - $sysRamUsed)."</td></tr>\n";
		    }
		    // Filesystem Information
		    if (count($system['partitions']) > 0) {
			echo "  <tr><th class='larger' align='left'>Filesystem Mountpoints</th><th class='larger' align='left'>Stats</th></tr>\n";
			foreach($system['partitions'] as $fs) {
			    if ($fs['Used']['value'] > 0 && $fs['FileSystem']['text']!= "none" && $fs['FileSystem']['text']!= "udev") {
				$diskFree = $fs['Free']['value'];
				$diskTotal = $fs['Size']['value'];
				$diskUsed = $fs['Used']['value'];
				$diskPercent = sprintf('%.2f',($diskUsed / $diskTotal) * 100);

				echo "  <tr><td align=\"left\" class='sans'>".$fs['Partition']['text']."</td><td align=\"left\"><div class='progress progress-info' style='margin-bottom: 0;'><div class='bar' style='width: ".$diskPercent."%;'>Used&nbsp;".$diskPercent."%</div></div>";
				echo "  <b>Total:</b> ".formatSize($diskTotal)."<b> Used:</b> ".formatSize($diskUsed)."<b> Free:</b> ".formatSize($diskFree)."</td></tr>\n";
			    }
			}
		    }
		    // Binary Information
		    echo "  <tr><th class='larger' align='left'>WPSD Software Binaries</th><th class='larger' align='left'>Version</th></tr>\n";
		    if (is_executable('/usr/local/bin/MMDVMHost')) {
			$MMDVMHost_Ver = exec('/usr/local/bin/MMDVMHost -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>MMDVMHost</td><td align=\"left\">".$MMDVMHost_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DMRGateway')) {
			$DMRGateway_Ver = exec('/usr/local/bin/DMRGateway -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>DMRGateway</td><td align=\"left\">".$DMRGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DMR2YSF')) {
			$DMR2YSF_Ver = exec('/usr/local/bin/DMR2YSF -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>DMR2YSF</td><td align=\"left\">".$DMR2YSF_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DMR2NXDN')) {
			$DMR2NXDN_Ver = exec('/usr/local/bin/DMR2NXDN -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>DMR2NXDN</td><td align=\"left\">".$DMR2NXDN_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSFGateway')) {
			$YSFGateway_Ver = exec('/usr/local/bin/YSFGateway -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>YSFGateway</td><td align=\"left\">".$YSFGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSFParrot')) {
			$YSFParrot_Ver = exec('/usr/local/bin/YSFParrot -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>YSFParrot</td><td align=\"left\">".$YSFParrot_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DGIdGateway')) {
			$DGIdGateway_Ver = exec('/usr/local/bin/DGIdGateway -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>DGIdGateway</td><td align=\"left\">".$DGIdGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/ircddbgatewayd')) {
			$ircDDBGateway_Ver = $_SESSION['WPSDrelease']['WPSD']['ircddbgateway'];
			echo "  <tr><td align='left' class='sans'>ircDDBGateway</td><td align=\"left\">".$ircDDBGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSF2DMR')) {
			$YSF2DMR_Ver = exec('/usr/local/bin/YSF2DMR -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>YSF2DMR</td><td align=\"left\">".$YSF2DMR_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSF2P25')) {
			$YSF2P25_Ver = exec('/usr/local/bin/YSF2P25 -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>YSF2P25</td><td align=\"left\">".$YSF2P25_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/YSF2NXDN')) {
			$YSF2NXDN_Ver = exec('/usr/local/bin/YSF2NXDN -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>YSF2NXDN</td><td align=\"left\">".$YSF2NXDN_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/P25Gateway')) {
			$P25Gateway_Ver = exec('/usr/local/bin/P25Gateway -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>P25Gateway</td><td align=\"left\">".$P25Gateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/P25Parrot')) {
			$P25Parrot_Ver = exec('/usr/local/bin/P25Parrot -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>P25Parrot</td><td align=\"left\">".$P25Parrot_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/NXDNGateway')) {
			$NXDNGateway_Ver = exec('/usr/local/bin/NXDNGateway -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>NXDNGateway</td><td align=\"left\">".$NXDNGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/DAPNETGateway')) {
			$DAPNETGateway_Ver = exec('/usr/local/bin/DAPNETGateway -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>DAPNETGateway</td><td align=\"left\">".$DAPNETGateway_Ver."</td></tr>\n";
		    }
		    if (is_executable('/usr/local/bin/APRSGateway')) {
			$APRSGateway_Ver = exec('/usr/local/bin/APRSGateway -v | cut -d\' \' -f 3-');
			echo "  <tr><td align='left' class='sans'>APRSGateway</td><td align=\"left\">".$APRSGateway_Ver."</td></tr>\n";
		    }
		    if (isDVmegaCast() == 0) {
			if (is_executable('/usr/local/bin/NextionDriver')) {
			    $NEXTIONDRIVER_Ver = exec('/usr/local/bin/NextionDriver -V | head -n 2 | cut -d\' \' -f 3');
			    echo "  <tr><td align='left' class='sans'>NextionDriver</td><td align=\"left\">".$NEXTIONDRIVER_Ver."</td></tr>\n";
			}
		    } else {
			if (is_executable('/usr/local/cast/bin/castudp')) {
			    echo "  <tr><td align='left' class='sans'>DVMega Cast UDP Service</td><td align=\"left\">DVMega</td></tr>\n";
			}
		    }
?>
		</table>
		<br />
	        <h3 class='larger' style="text-align:left;font-weight:bold;margin:5px 0 2px 0;">Time Synchronization Status</h3>
		<table id="synctable" width="100%" border="0">
<?php
		    // time sync status
		    echo "<tr>";
		    echo "<td align='left' colspan='2'>";
		    echo "<pre>";
		    system("timedatectl | sed -e 's/^[ \t]*/  /' | sed '/RTC/d'");
		    echo "</pre>";
		    if (timesyncdProc() == "1") {
					echo "<pre>";
			    system("timedatectl timesync-status | sed -e 's/^[ \t]*/  /'");
			    echo "</pre>";
			    echo "</td>";
		    } else if (chronyProc() == "1") {
					echo "<pre>";
			    system("chronyc -N tracking | sed -e 's/^[ \t]*/  /' | sed -E 's/\s+:/:/'");
			    echo "<br>";
			    system("chronyc -N activity | tail -n +2 | grep -v '^0' | sed -e 's/^[ \t]*/  /'");
					echo "<br>";
			    system("chronyc -N sourcestats | sed -e 's/^[ \t]*/  /'");
			    echo "</pre>";
			    echo "</td>";
		    } else {
			    echo "<td align='left' class='inactive-service-cell' colspan='2'>TimeSync/Chrony Deamon not running!</td>";
		    }
		    echo "</tr>";
		    ?>
		</table>
	    </div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
	</div>
    </body>
</html>
