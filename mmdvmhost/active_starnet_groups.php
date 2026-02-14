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

include_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php';           // Theme Variables

?>

<style>
    /* Modern Starnet Groups CSS */
    .stn-container {
        font-family: 'Source Sans Pro', sans-serif;
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 15px;
        margin-top: 15px;
        color: <?php echo $textContent; ?>;
    }
    
    .stn-header-title {
        background-color: <?php echo $backgroundBanners; ?>;
        color: <?php echo $textBanners; ?>;
        padding: 10px 15px;
        font-weight: 700;
        font-size: 1rem;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    }

    .stn-list-header {
        display: flex;
        background-color: <?php echo $tableRowOddBg; ?>;
        padding: 8px 15px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        opacity: 0.9;
    }

    .stn-row {
        display: flex;
        padding: 10px 15px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        align-items: center;
        transition: background-color 0.1s;
    }
    .stn-row:hover { background-color: rgba(255,255,255,0.03); }
    .stn-row:last-child { border-bottom: none; }

    /* Columns Config Table */
    .stn-c-call   { flex: 1.5; font-weight: bold; }
    .stn-c-logoff { flex: 1.5; }
    .stn-c-info   { flex: 4; }
    .stn-c-utot   { flex: 1; text-align: center; }
    .stn-c-gtot   { flex: 1; text-align: center; }

    /* Columns Members Table */
    .stn-m-time   { flex: 2; }
    .stn-m-group  { flex: 1.5; text-align: center; }
    .stn-m-user   { flex: 1.5; text-align: center; font-weight: bold; }
    .stn-m-fill   { flex: 4; } /* Filler to match width feel */

    /* Data Monospace Logic - Applied ONLY to row data, not headers */
    .stn-row .stn-c-call,
    .stn-row .stn-c-logoff,
    .stn-row .stn-c-utot, 
    .stn-row .stn-c-gtot,
    .stn-row .stn-m-time,
    .stn-row .stn-m-group,
    .stn-row .stn-m-user {
        font-family: 'Inconsolata', monospace;
    }
    
    .stn-row .stn-m-user a { color: <?php echo $textLinks; ?>; text-decoration: none; }
    .stn-row .stn-m-user a:hover { text-decoration: underline; }
</style>

<div class="stn-container">
    <div class="stn-header-title"><?php echo __( 'Active Starnet Groups' );?></div>
    
    <div class="stn-list-header">
        <div class="stn-c-call"><?php echo __( 'Callsign' );?></div>
        <div class="stn-c-logoff"><?php echo __( 'LogOff' );?></div>
        <div class="stn-c-info"><?php echo __( 'Information' );?></div>
        <div class="stn-c-utot"><?php echo __( 'UTOT' );?></div>
        <div class="stn-c-gtot"><?php echo __( 'GTOT' );?></div>
    </div>

    <?php
    $ci = 0;
    $stngrp = array();
    for($i = 1; $i < 6; $i++) {
        $param = "starNetCallsign" . $i;
        exec('echo T:\"'.$_SESSION['ircDDBConfigs'][$param].'\" >> /tmp/trace.txt');
        
        if(isset($_SESSION['ircDDBConfigs'][$param]) && !empty($_SESSION['ircDDBConfigs'][$param])) {
            $gname = $_SESSION['ircDDBConfigs'][$param];
            $stngrp[$gname] = $i;
            $ci++;
            if($ci > 1) { $ci = 0; }
            
            // Prepare Data
            $d_call = str_replace(' ', '&nbsp;', substr($gname, 0, 8));
            
            $paramLog = "starNetLogoff" . $i;
            $d_logoff = isset($_SESSION['ircDDBConfigs'][$paramLog]) ? str_replace(' ', '&nbsp;', substr($_SESSION['ircDDBConfigs'][$paramLog],0,8)) : "&nbsp;";
            
            $paramInfo = "starNetInfo" . $i;
            $d_info = isset($_SESSION['ircDDBConfigs'][$paramInfo]) ? $_SESSION['ircDDBConfigs'][$paramInfo] : "&nbsp;";
            
            $paramUT = "starNetUserTimeout" . $i;
            $d_utot = isset($_SESSION['ircDDBConfigs'][$paramUT]) ? $_SESSION['ircDDBConfigs'][$paramUT] : "&nbsp;";
            
            $paramGT = "starNetGroupTimeout" . $i;
            $d_gtot = isset($_SESSION['ircDDBConfigs'][$paramGT]) ? $_SESSION['ircDDBConfigs'][$paramGT] : "&nbsp;";
            ?>
            <div class="stn-row">
                <div class="stn-c-call"><?php echo $d_call; ?></div>
                <div class="stn-c-logoff"><?php echo $d_logoff; ?></div>
                <div class="stn-c-info"><?php echo $d_info; ?></div>
                <div class="stn-c-utot"><?php echo $d_utot; ?></div>
                <div class="stn-c-gtot"><?php echo $d_gtot; ?></div>
            </div>
            <?php
        }
    }
    ?>
</div>

<?php
// MEMBER LIST LOGIC
$groupsx = array();
if ($starLog = fopen($starLogPath,'r')) {
    while($logLine = fgets($starLog)) {
        preg_match_all('/^(.{19}).*(Adding|Removing) (.{8}).*StarNet group (.{8}).*$/',$logLine,$matches);
        if (isset($matches[4][0])) {
            $groupz = substr($matches[4][0],0,8);
            $member = substr($matches[3][0],0,8);
            $action = substr($matches[2][0],0,8);
            $date = $matches[1][0];
            if (isset($stngrp[$groupz])) {
                $guid = $stngrp[$groupz];
                if ($action == 'Adding') {
                    $groupsx[$guid][$groupz][$member] = $date;
                }
                elseif ($action == 'Removing'){
                    unset($groupsx[$guid][$groupz][$member]);
                }
            }
        }
    }
    fclose($starLog);
}

// Clean empty arrays
$groupsx = array_map('array_filter', $groupsx);

$active = 0;
for ($i = 1; $i < 6; $i++) {
    if (isset($groupsx[$i])) {
        $active = $active + count($groupsx[$i]);
    }
}

if ($active >= 1) {
?>
    <div class="stn-container">
        <div class="stn-header-title"><?php echo __( 'Active Starnet Group Members' );?></div>
        
        <div class="stn-list-header">
            <div class="stn-m-time"><?php echo __( 'Time' );?> (<?php echo date('T'); ?>)</div>
            <div class="stn-m-group"><?php echo __( 'Group' );?></div>
            <div class="stn-m-user"><?php echo __( 'Callsign' );?></div>
            <div class="stn-m-fill"></div>
        </div>

        <?php
        $ci = 0;
        for($i = 1; $i < 6; $i++) {
            if(isset($groupsx[$i])) {
                $glist = $groupsx[$i];
                foreach ($glist as $gcall => $ulist) {
                    foreach ($ulist as $ucall => $ulogin) {
                        $ci++;
                        if($ci > 1) { $ci = 0; }
                        
                        $ulogin = date("d-M-Y H:i:s", strtotime(substr($ulogin,0,19)));
                        $utc_time = $ulogin;
                        $utc_tz =  new DateTimeZone('UTC');
                        $local_tz = new DateTimeZone(date_default_timezone_get ());
                        $dt = new DateTime($utc_time, $utc_tz);
                        $dt->setTimeZone($local_tz);
                        
                        if (constant("TIME_FORMAT") == "24") {
                            $local_time = $dt->format('H:i:s M j');
                        } else {
                            $local_time = $dt->format('h:i:s A M j');
                        }
                        
                        $groupz = str_replace(' ', '&nbsp;', substr($gcall,0,8));
                        $ucall_raw = str_replace(' ', '', substr($ucall,0,8));
                        $ucall_link = '<a href="http://www.qrz.com/db/'.$ucall_raw.'" target="_blank" alt="Lookup Callsign">'.$ucall_raw.'</a>';
                        ?>
                        <div class="stn-row">
                            <div class="stn-m-time"><?php echo $local_time; ?></div>
                            <div class="stn-m-group"><?php echo $groupz; ?></div>
                            <div class="stn-m-user"><?php echo $ucall_link; ?></div>
                            <div class="stn-m-fill"></div>
                        </div>
                        <?php
                    }
                }
            }
        }
        ?>
    </div>
<?php
}
?>
