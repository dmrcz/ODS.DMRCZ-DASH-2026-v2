<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] != "/admin/live_log.php") die();

$curDateStr = gmdate('Y-m-d');
$logFiles = [
    "MMDVMHost"     => "/var/log/pi-star/MMDVM-{$curDateStr}.log",
    "DMRGateway"    => "/var/log/pi-star/DMRGateway-{$curDateStr}.log",
    "YSFGateway"    => "/var/log/pi-star/YSFGateway-{$curDateStr}.log",
    "DGIdGateway"   => "/var/log/pi-star/DGIdGateway-{$curDateStr}.log",
    "ircDDBGateway" => "/var/log/pi-star/ircDDBGateway-{$curDateStr}.log",
    "P25Gateway"    => "/var/log/pi-star/P25Gateway-{$curDateStr}.log",
    "NXDNGateway"   => "/var/log/pi-star/NXDNGateway-{$curDateStr}.log",
    "DAPNETGateway" => "/var/log/pi-star/DAPNETGateway-{$curDateStr}.log",
    "DMR2NXDN"      => "/var/log/pi-star/DMR2NXDN-{$curDateStr}.log",
    "DMR2YSF"       => "/var/log/pi-star/DMR2YSF-{$curDateStr}.log",
    "YSF2DMR"       => "/var/log/pi-star/YSF2DMR-{$curDateStr}.log",
    "YSF2NXDN"      => "/var/log/pi-star/YSF2NXDN-{$curDateStr}.log",
    "YSF2P25"       => "/var/log/pi-star/YSF2P25-{$curDateStr}.log",
    "APRSGateway"   => "/var/log/pi-star/APRSGateway-{$curDateStr}.log",
];

// Sanity Check Passed.
header('Cache-Control: no-cache');

$log = $_GET['log'] ?? '';
$logfile = $logFiles[$log] ?? null;

if (isset($_GET['download'])) {
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="WPSD_' . basename($logfile) . '";');
    header('Content-Length: ' . filesize($logfile));
    header('Accept-Ranges: bytes');

    set_time_limit(0);
    $file = @fopen($logfile, "rb");

    fpassthru($file);
    exit();
}

if (!isset($_GET['ajax'])) {
    unset($_SESSION['logviewer'][$log]);
} else {
    // ajax
    if (empty($logfile) || !file_exists($logfile)) exit();

    $handle = fopen($logfile, 'rb');
    if (!$handle) exit();

    fseek($handle, 0, SEEK_END);
    $logLen = ftell($handle);

    $output = "";

    $curOffset = 0;
    if (!isset($_SESSION['logviewer'][$log])) {  // first ajax load
        // truncate to only last N Kb if more:
        $maxInitialLogSize = 32 * 1024;
        if ($logLen - $curOffset > $maxInitialLogSize) {
            $output = "<i>&gt;&gt;&gt; FILE TOO LARGE, TRUNCATED &lt;&lt;&lt;</i><br /> ... ";
            $curOffset = $logLen - $maxInitialLogSize;
        }
    } else {
        $curOffset = $_SESSION['logviewer'][$log]['offset'];

        //log rotated/truncated => continue at beginning of the new log:
        if ($curOffset > $logLen) $curOffset = 0;
    }

    $_SESSION['logviewer'][$log]['offset'] = $logLen;  // save new offset

    $data = stream_get_contents($handle, null, $curOffset);
    $data = wordwrap($data, 200, "\n");
    echo $output . nl2br(htmlentities($data));

    fclose($handle);
    exit();
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
    <title>WPSD <?php echo __( 'Dashboard' )." - ".__( 'Log Viewer' );?></title>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/jquery-timing.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
<?php if (!empty($log)) {?>
    <script type="text/javascript">
    $(function() {
        var placeholderVisible = true;

        $.repeat(1000, function() {
            $.get('/admin/live_log.php?log=<?php echo "$log";?>&ajax', function(data) {
                if (data.length < 1) return;

                var objDiv = document.getElementById("tail");
                var isScrolledToBottom = objDiv.scrollHeight - objDiv.clientHeight <= objDiv.scrollTop + 1;

                if (placeholderVisible) {
                    $('#tail').empty(); // Remove placeholder text
                    placeholderVisible = false;
                }

                $('#tail').append(data);

                if (isScrolledToBottom)
                    objDiv.scrollTop = objDiv.scrollHeight;
            });
        });
    });
    </script>
<?php }?>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
        <?php if ($_SESSION['CURRENT_PROFILE']) { ?><div class="SmallHeader shLeft noMob"> | <?php echo __( 'Current Profile' ).": ";?> <?php echo $_SESSION['CURRENT_PROFILE']; ?></div><?php } ?>
        <div class="SmallHeader shRight">
          <div id="CheckUpdate">
            <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php'; ?>
          </div>
          <br />
        </div>
        <h1>WPSD <?php echo __(( 'Dashboard' )) . " - ".__( 'Log Viewer' );?></h1>
        <p>
          <div class="navbar">
            <script type= "text/javascript">
              window.time_format = '<?php echo constant("TIME_FORMAT"); ?>';
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
            <a class="menubackup" href="/admin/config_backup.php"><?php echo __( 'Backup/Restore' );?></a>
            <a class="menupower" href="/admin/power.php"><?php echo __( 'Power' );?></a>
            <a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
            <?php if (file_exists("/etc/dstar-radio.mmdvmhost")) { ?>
            <a class="menulive" href="/live/">Live Caller</a>
            <?php } ?>
            <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
          </div>
        </p>
      </div>
      <div class="contentwide">
        <table width="100%">
          <tr>
            <th>
            <?php
                echo __( 'Log Viewer' );
                if (!empty($log)) echo " - $log";
            ?>
            </th>
          </tr>
          <tr>
            <td>
              <form method="get">
              <b>Select a log:</b>
                <select name="log" value="log">
                  <?php foreach ($logFiles as $logName => $logFilename) { ?>
                    <option name="<?=$logName?>" <?php if ($log == $logName) { echo "selected='selected'"; } ?>><?=$logName?></option>
                  <?php } ?>
                </select>
                <input type="submit" name="" value="Live View" />
                <input type="submit" name="download" value="Download" />
                &nbsp;&bull;&nbsp;
                <button class="button" onclick="location.href='/admin/download_all_logs.php'; return false;" style="margin:2px 5px;">Download All Logs</button>
              </form>
            </td>
          </tr>
        </table>
        <?php if (!empty($log)) { ?>
          <div id="tail">
              <?php
                  if (!file_exists($logfile)) {
                      echo "<p><b>File `$logfile` not found!</b></p>";
                  } else {
                      echo "<i>Loading, please wait...</i><br />";
                  }
              ?>
          </div>
        <?php } ?>
      </div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
    </div>
  </body>
</html>
