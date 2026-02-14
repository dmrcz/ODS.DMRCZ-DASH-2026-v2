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
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php';           // Theme Variables

if (exec('grep "CCS link" '.$linkLogPath.' | wc -l') >= 1) {
?>
    <style>
        .ccs-container {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: <?php echo $backgroundContent; ?>;
            border: 1px solid <?php echo $tableBorderColor; ?>;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
            margin-top: 15px;
            color: <?php echo $textContent; ?>;
        }
        
        .ccs-header-title {
            background-color: <?php echo $backgroundBanners; ?>;
            color: <?php echo $textBanners; ?>;
            padding: 10px 15px;
            font-weight: 700;
            font-size: 1rem;
            border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        }

        .ccs-list-header {
            display: flex;
            background-color: <?php echo $tableRowOddBg; ?>;
            padding: 8px 15px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
            opacity: 0.9;
        }

        .ccs-row {
            display: flex;
            padding: 10px 15px;
            border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
            align-items: center;
            transition: background-color 0.1s;
        }
        .ccs-row:hover { background-color: rgba(255,255,255,0.03); }
        .ccs-row:last-child { border-bottom: none; }

        /* Columns */
        .ccs-c-rpt   { flex: 2; font-weight: bold; }
        .ccs-c-link  { flex: 2; font-weight: bold; color: #2ecc71; }
        .ccs-c-proto { flex: 1.5; }
        .ccs-c-dir   { flex: 1.5; }
        
        .ccs-c-time  { flex: 3; text-align: right; font-size: 0.9rem; }
        /* Apply monospace ONLY to data rows */
        .ccs-row .ccs-c-time { font-family: 'Inconsolata', monospace; }
    </style>

    <div class="ccs-container">
        <div class="ccs-header-title">Active CCS Connections</div>
        
        <div class="ccs-list-header">
            <div class="ccs-c-rpt">Repeater</div>
            <div class="ccs-c-link">Linked To</div>
            <div class="ccs-c-proto">Protocol</div>
            <div class="ccs-c-dir">Direction</div>
            <div class="ccs-c-time">Last Change (<?php echo date('T')?>)</div>
        </div>

        <?php
        $ci = 0;
        if ($linkLog = fopen($linkLogPath,'r')) {
            $i = 0;
            while ($linkLine = fgets($linkLog)) {
                // 2013-02-27 19:49:27: CCS link - Rptr: DB0LJ  B Remote: DL5DI    Dir: Incoming
                if(preg_match_all('/^(.{19}).*(C[A-Za-z]*).*Rptr: (.{8}).*Remote: (.{8}).*Dir: (.{8})$/',$linkLine,$linx) > 0) {
                    $utc_time = $linx[1][0];
                    $utc_tz =  new DateTimeZone('UTC');
                    $local_tz = new DateTimeZone(date_default_timezone_get ());
                    $dt = new DateTime($utc_time, $utc_tz);
                    $dt->setTimeZone($local_tz);
                    
                    if (constant("TIME_FORMAT") == "24") {
                        $local_time = $dt->format('H:i:s M j');
                    } else {
                        $local_time = $dt->format('h:i:s A M j');
                    }
                    
                    $linkDate = $local_time;
                    $linkType = $linx[2][0];
                    $linkRptr = $linx[3][0];
                    $linkRem = $linx[4][0];
                    $linkDir = $linx[5][0];
                    $ci++;
                    if($ci > 1) { $ci = 0; }
                    ?>
                    <div class="ccs-row">
                        <div class="ccs-c-rpt"><?php echo $linkRptr; ?></div>
                        <div class="ccs-c-link"><?php echo $linkRem; ?></div>
                        <div class="ccs-c-proto">CCS</div>
                        <div class="ccs-c-dir"><?php echo $linkDir; ?></div>
                        <div class="ccs-c-time"><?php echo $linkDate; ?></div>
                    </div>
                    <?php
                }
            }
            fclose($linkLog);
        }
        ?>
    </div>
<?php
}

$stn_is_set = 0;
for($i = 1;$i < 6; $i++) {
    $param="starNetCallsign" . $i;
    if(isset($_SESSION['ircDDBConfigs'][$param]) && !empty($_SESSION['ircDDBConfigs'][$param])) {
    $stn_is_set = 1;
    break;
    }
}
if($stn_is_set > 0) {
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/active_starnet_groups.php';
}
?>
