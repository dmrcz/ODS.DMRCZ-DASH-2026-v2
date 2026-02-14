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
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php';           // Theme Variables

?>

<style>
    /* Modern D-Star Links Table CSS */
    .dsl-container {
        font-family: 'Source Sans Pro', sans-serif;
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 15px;
        color: <?php echo $textContent; ?>;
    }
    
    .dsl-header-title {
        background-color: <?php echo $backgroundBanners; ?>;
        color: <?php echo $textBanners; ?>;
        padding: 10px 15px;
        font-weight: 700;
        font-size: 1rem;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    }

    .dsl-list-wrap {
        overflow-x: auto; /* Allow scroll on small screens */
    }

    .dsl-list-header {
        display: flex;
        min-width: 800px; /* Ensure alignment */
        background-color: <?php echo $tableRowOddBg; ?>;
        padding: 8px 10px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        opacity: 0.9;
    }

    .dsl-row {
        display: flex;
        min-width: 800px;
        padding: 8px 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        align-items: center;
        transition: background-color 0.1s;
    }
    .dsl-row:hover { background-color: rgba(255,255,255,0.03); }
    .dsl-row:last-child { border-bottom: none; }

    /* Columns */
    .dsl-col { padding: 0 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
    .dsl-c-radio   { flex: 0 0 10%; font-weight: bold; }
    /* Apply monospace ONLY to data rows */
    .dsl-row .dsl-c-radio { font-family: 'Inconsolata', monospace; }

    .dsl-c-def     { flex: 0 0 12%; }
    .dsl-c-auto    { flex: 0 0 8%; text-align: center; }
    .dsl-c-timer   { flex: 0 0 10%; text-align: center; }
    .dsl-c-status  { flex: 0 0 8%; text-align: center; }
    .dsl-c-linked  { flex: 0 0 12%; font-weight: bold; }
    .dsl-c-mode    { flex: 0 0 10%; }
    .dsl-c-dir     { flex: 0 0 10%; }
    
    .dsl-c-time    { flex: 1; text-align: right; font-size: 0.9rem; }
    /* Apply monospace ONLY to data rows */
    .dsl-row .dsl-c-time { font-family: 'Inconsolata', monospace; }

    /* Status Icons */
    .dsl-icon-yes { color: #2ecc71; font-size: 1.1em; }
    .dsl-icon-no  { color: #e74c3c; font-size: 1.1em; opacity: 0.5; }
    .dsl-icon-link { color: #2ecc71; animation: pulse 2s infinite; }
    
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
</style>

<div class="dsl-container">
    <div class="dsl-header-title"><?php echo __( 'D-Star Link Information' );?></div>
    
    <div class="dsl-list-wrap">
        <div class="dsl-list-header">
            <div class="dsl-col dsl-c-radio">Radio</div>
            <div class="dsl-col dsl-c-def">Default</div>
            <div class="dsl-col dsl-c-auto">Auto</div>
            <div class="dsl-col dsl-c-timer">Timer</div>
            <div class="dsl-col dsl-c-status">Status</div>
            <div class="dsl-col dsl-c-linked">Linked To</div>
            <div class="dsl-col dsl-c-mode">Mode</div>
            <div class="dsl-col dsl-c-dir">Dir</div>
            <div class="dsl-col dsl-c-time">Last Change (<?php echo date('T')?>)</div>
        </div>

        <?php
        $tot = array(0=>"Never",1=>"Fixed",2=>"5min",3=>"10min",4=>"15min",5=>"20min",6=>"25min",7=>"30min",8=>"60min",9=>"90min",10=>"120min",11=>"180min",12=>"&nbsp;");
        $ci = 0;
        $tr = 0; // Row Tracker for active links

        for($i = 1; $i < 5; $i++) {
            $param="repeaterBand" . $i;
            if((isset($_SESSION['ircDDBConfigs'][$param])) && strlen($_SESSION['ircDDBConfigs'][$param]) == 1) {
                $ci++;
                if($ci > 1) { $ci = 0; }
                
                $tr = 1; // Assume not linked initially
                $module = $_SESSION['ircDDBConfigs'][$param];
                $rcall = sprintf("%-7.7s%-1.1s", $_SESSION['MYCALL'], $module);
                $param = "repeaterCall" . $i;
                
                if(isset($_SESSION['ircDDBConfigs'][$param])) {
                    $rptrcall = sprintf("%-7.7s%-1.1s",$_SESSION['ircDDBConfigs'][$param],$module);
                } else {
                    $rptrcall = $rcall;
                }

                // Prepare Row Data
                $radioDisplay = str_replace(' ', '&nbsp;', substr($rptrcall,0,8));
                
                $paramRef = "reflector" . $i;
                $defaultRef = isset($_SESSION['ircDDBConfigs'][$paramRef]) ? str_replace(' ', '&nbsp;', substr($_SESSION['ircDDBConfigs'][$paramRef],0,8)) : "&nbsp;";
                
                $paramStart = "atStartup" . $i;
                $autoLinkIcon = ($_SESSION['ircDDBConfigs'][$paramStart] == 1) ? '<i class="fa fa-check-circle dsl-icon-yes" title="Yes"></i>' : '<i class="fa fa-times-circle dsl-icon-no" title="No"></i>';
                
                $paramRec = "reconnect" . $i;
                $t = isset($_SESSION['ircDDBConfigs'][$paramRec]) ? $_SESSION['ircDDBConfigs'][$paramRec] : 0;
                if($t > 12) { $t = 12; }
                $timerDisplay = $tot[$t];

                // Check Active Links
                $linkFound = false;
                if (file_exists($linkLogPath) && (($linkLog = fopen($linkLogPath,'r')))) {
                    while ($linkLine = fgets($linkLog)) {
                        if(preg_match_all('/^(.{19}).*(D[A-Za-z]*).*Type: ([A-Za-z]*).*Rptr: (.{8}).*Refl: (.{8}).*Dir: Outgoing$/',$linkLine,$linx) > 0) {
                            $linkRptr = $linx[4][0];
                            if($linkRptr == $rptrcall) {
                                $linkFound = true;
                                $linkRefl = $linx[5][0];
                                $protocol = $linx[2][0];
                                $linkDate = date("d-M-Y H:i:s", strtotime(substr($linx[1][0],0,19)));
                                
                                // Time conversion
                                $utc_tz = new DateTimeZone('UTC');
                                $local_tz = new DateTimeZone(date_default_timezone_get());
                                $dt = new DateTime($linkDate, $utc_tz);
                                $dt->setTimeZone($local_tz);
                                $local_time = (constant("TIME_FORMAT") == "24") ? $dt->format('H:i:s M j') : $dt->format('h:i:s A M j');

                                // Render ACTIVE Link Row
                                ?>
                                <div class="dsl-row">
                                    <div class="dsl-col dsl-c-radio"><?php echo $radioDisplay; ?></div>
                                    <div class="dsl-col dsl-c-def"><?php echo $defaultRef; ?></div>
                                    <div class="dsl-col dsl-c-auto"><?php echo $autoLinkIcon; ?></div>
                                    <div class="dsl-col dsl-c-timer"><?php echo $timerDisplay; ?></div>
                                    <div class="dsl-col dsl-c-status"><i class="fa fa-link dsl-icon-link" title="Up"></i></div>
                                    <div class="dsl-col dsl-c-linked"><?php echo str_replace(' ', '&nbsp;', substr($linkRefl,0,8)); ?></div>
                                    <div class="dsl-col dsl-c-mode"><?php echo $protocol; ?></div>
                                    <div class="dsl-col dsl-c-dir">Outgoing</div>
                                    <div class="dsl-col dsl-c-time"><?php echo $local_time; ?></div>
                                </div>
                                <?php
                                $tr = 0; // Mark as found
                            }
                        }
                    }
                    fclose($linkLog);
                }

                // If no active link found for this module, print "Not Linked" row
                if ($tr == 1) {
                    ?>
                    <div class="dsl-row">
                        <div class="dsl-col dsl-c-radio"><?php echo $radioDisplay; ?></div>
                        <div class="dsl-col dsl-c-def"><?php echo $defaultRef; ?></div>
                        <div class="dsl-col dsl-c-auto"><?php echo $autoLinkIcon; ?></div>
                        <div class="dsl-col dsl-c-timer"><?php echo $timerDisplay; ?></div>
                        <div class="dsl-col dsl-c-status"><i class="fa fa-unlink dsl-icon-no" title="Down"></i></div>
                        <div class="dsl-col dsl-c-linked" style="opacity:0.5;">Not Linked</div>
                        <div class="dsl-col dsl-c-mode">--</div>
                        <div class="dsl-col dsl-c-dir">--</div>
                        <div class="dsl-col dsl-c-time">--</div>
                    </div>
                    <?php
                }

                // Incoming Links Logic (Dongle/User links)
                if (file_exists($linkLogPath) && ($linkLog = fopen($linkLogPath,'r'))) {
                    while ($linkLine = fgets($linkLog)) {
                        if(preg_match_all('/^(.{19}).*(D[A-Za-z]*).*Type: ([A-Za-z]*).*User: (.[^\s]+).*Dir: Incoming$/',$linkLine,$linx) > 0) {
                            $linkRptr = $linx[4][0]; 
                        }
                        
                        // Re-implementing the specific incoming checks from original file
                        // Case 1: Repeater-to-Repeater Incoming
                        if(preg_match_all('/^(.{19}).*(D[A-Za-z]*).*Type: ([A-Za-z]*).*Rptr: (.{8}).*Refl: (.{8}).*Dir: Incoming$/',$linkLine,$linx) > 0) {
                            $linkRptr = $linx[4][0];
                            if($linkRptr == $rptrcall) {
                                $linkRefl = $linx[5][0];
                                $protocol = $linx[2][0];
                                $linkDate = date("d-M-Y H:i:s", strtotime(substr($linx[1][0],0,19)));
                                
                                $utc_tz = new DateTimeZone('UTC');
                                $local_tz = new DateTimeZone(date_default_timezone_get());
                                $dt = new DateTime($linkDate, $utc_tz);
                                $dt->setTimeZone($local_tz);
                                $local_time = (constant("TIME_FORMAT") == "24") ? $dt->format('H:i:s M j') : $dt->format('h:i:s A M j');

                                ?>
                                <div class="dsl-row">
                                    <div class="dsl-col dsl-c-radio"><?php echo $radioDisplay; ?></div>
                                    <div class="dsl-col dsl-c-def"></div>
                                    <div class="dsl-col dsl-c-auto"></div>
                                    <div class="dsl-col dsl-c-timer"></div>
                                    <div class="dsl-col dsl-c-status"><i class="fa fa-link dsl-icon-link"></i></div>
                                    <div class="dsl-col dsl-c-linked"><?php echo str_replace(' ', '&nbsp;', substr($linkRefl,0,8)); ?></div>
                                    <div class="dsl-col dsl-c-mode"><?php echo $protocol; ?></div>
                                    <div class="dsl-col dsl-c-dir">Incoming</div>
                                    <div class="dsl-col dsl-c-time"><?php echo $local_time; ?></div>
                                </div>
                                <?php
                            }
                        }

                        // Case 2: Dongle User Incoming
                        if(preg_match_all('/^(.{19}).*(D[A-Za-z]*).*Type: ([A-Za-z]*).*User: (.[^\s]+).*Dir: Incoming$/',$linkLine,$linx) > 0) {
                             // Logic preserved from original (no display for this case implemented in original loop logic)
                        }
                    }
                    fclose($linkLog);
                }
            }
        }
        ?>
    </div>
</div>
