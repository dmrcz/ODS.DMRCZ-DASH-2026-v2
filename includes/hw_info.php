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

if (isset($_SESSION['CSSConfigs']['Text']['TextColor'])) {
    $textContent = $_SESSION['CSSConfigs']['Text']['TextColor'];
}

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
                 'os' => preg_replace('/\(|\)/','"', trim( exec( 'lsb_release -sd' ) )),
                 'os_ver' => trim( exec( 'cat /etc/debian_version' ) ),
    );
}

$system = system_information();

function formatSize( $bytes ) {
    $types = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
    for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
}

$diskUsed = @exec("df --block-size=1 / | tail -1 | awk {'print $3'}");
$diskTotal = @exec("df --block-size=1 / | tail -1 | awk {'print $2'}");
$diskPercent = sprintf('%.2f',($diskUsed / $diskTotal) * 100);
$rootfs_free = $diskTotal - $diskUsed;
$rootfs_stats = formatSize($diskUsed). " / " .formatSize($diskTotal);
$rootfsTip = "<strong>Used:</strong> $diskPercent%<br><strong>Free:</strong> ".formatSize($rootfs_free);

if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
    $cpuTempCRaw = exec('cat /sys/class/thermal/thermal_zone0/temp');
    if ($cpuTempCRaw > 1000) { $cpuTempC = sprintf('%.0f',round($cpuTempCRaw / 1000, 1)); } else { $cpuTempC = sprintf('%.0f',round($cpuTempCRaw, 1)); }
    $cpuTempF = sprintf('%.0f',round(+$cpuTempC * 9 / 5 + 32, 1));
    if ($cpuTempC <= 69) { $cpuTempHTML = $cpuTempF."&deg;F / ".$cpuTempC."&deg;C"; }
    if ($cpuTempC >= 70) { $cpuTempHTML = "<span style=\"color:#fa0;\">".$cpuTempF."&deg;F / ".$cpuTempC."&deg;C</span>"; }
    if ($cpuTempC >= 80) { $cpuTempHTML = "<span style=\"color:#f00;font-weight:bold;\">".$cpuTempF."&deg;F / ".$cpuTempC."&deg;C</span>"; }
}

$loads = sys_getloadavg();
$core_nums = trim(shell_exec("grep -c '^processor' /proc/cpuinfo"));
$load = number_format(round($loads[0]/($core_nums + 1)*100, 2));

$sysRamUsed = $system['mem_info']['MemTotal'] - $system['mem_info']['MemFree'] - $system['mem_info']['Buffers'] - $system['mem_info']['Cached'];
$sysRamPercent = sprintf('%.2f',($sysRamUsed / $system['mem_info']['MemTotal']) * 100); 
$ramDeetz = formatSize($sysRamUsed). " / ".formatSize($system['mem_info']['MemTotal']);
$ramTip = "<strong>Used:</strong> $sysRamPercent%<br><strong>Free:</strong> ".formatSize($system['mem_info']['MemTotal'] - $sysRamUsed);

$iface = $_SESSION['WPSDrelease']['WPSD']['iface'];
$VNStatGetData = exec("vnstat -i $iface | grep today | sed 's/today//g' | awk '{print $1\" \"$2\" \"$4\" \"$5\" \"$7\" \"$8\" \"$10\" \"$11}'");
if (empty($VNStatGetData) == false) {
    $Data = explode(" ", $VNStatGetData);
    $NetworkTraffic = "$Data[0]$Data[1] <i class='fa fa-arrow-down'></i> | $Data[2]$Data[3] <i class='fa fa-arrow-up'></i>";
    $NetTrafficTotal = "$Data[4] $Data[5] combined<br />";
    $NetTrafficAvg = "$Data[6] $Data[7] avg. rate<br />";
} else {
    $NetworkTraffic = "N/A";
    $NetTrafficTotal = "Collecting data...";
    $NetTrafficAvg = "<br>";
}
?>
<div id="hwInfoTable" class="dashboard-header-stats">
    
    <div class="stat-card">
        <span class="stat-label"><?php echo __( 'CPU Load' );?></span>
        <div class="stat-value">
            <a class="tooltip" href="#"><?php echo $load; ?>%<span><strong>Hardware:</strong> <?php echo $_SESSION['WPSDrelease']['WPSD']['Hardware'];?><br /><strong>Platform:</strong> <?php echo $_SESSION['WPSDrelease']['WPSD']['Platform'];?><br /><strong><?php echo 'OS:</strong> ' . $system['os'] . " (release ver. " . $system['os_ver']; ?>)<br /><strong>Linux Kernel:</strong> <?php echo php_uname('r');?><br /><strong>Uptime:</strong> <?php  echo(str_replace("up", "", exec('uptime -p')));?></span></a>
        </div>
    </div>

    <?php if (file_exists('/sys/class/thermal/thermal_zone0/temp')) { ?>
    <div class="stat-card">
        <span class="stat-label"><?php echo __( 'CPU Temp' );?></span>
        <div class="stat-value">
            <a class="tooltip" href="#"><?php echo $cpuTempHTML; ?><span><strong>CPU Temperature</strong></span></a>
        </div>
    </div>
    <?php } ?>

    <div class="stat-card">
        <span class="stat-label">RAM Usage</span>
        <div class="stat-value">
            <a class="tooltip" href="#"><?php echo $ramDeetz; ?><span><?php echo $ramTip; ?></span></a>
        </div>
    </div>

    <div class="stat-card">
        <span class="stat-label">Disk Usage</span>
        <div class="stat-value">
            <a class="tooltip" href="#"><?php echo $rootfs_stats;?><span><?php echo $rootfsTip; ?></span></a>
        </div>
    </div>

    <div class="stat-card">
        <span class="stat-label">Net Traffic</span>
        <div class="stat-value">
            <a class="tooltip" href="#"><?php echo $NetworkTraffic;?><span><strong>Total Network Traffic</strong><br /><?php echo "$NetTrafficTotal $NetTrafficAvg"; ?>(Interface: <?php echo($iface); ?>)</span></a>
        </div>
    </div>

</div>
