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

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php';           // Theme Variables

// Check if YSF is Enabled
if (isset($_SESSION['YSFGatewayConfigs']['YSF Network']['Enable']) == 1) {
    // Check that the remote is enabled
    if (isset($_SESSION['YSFGatewayConfigs']['Remote Commands']['Enable']) && (isset($_SESSION['YSFGatewayConfigs']['Remote Commands']['Port'])) && ($_SESSION['YSFGatewayConfigs']['Remote Commands']['Enable'] == 1)) {
        $remotePort = $_SESSION['YSFGatewayConfigs']['Remote Commands']['Port'];
?>

<style>
    /* Main Layout */
    .ysf-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .ysf-card {
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 8px;
        padding: 0;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        color: <?php echo $textContent; ?>;
        overflow: visible; /* Must be visible for dropdowns */
        position: relative;
        text-align: left;
    }

    /* Navigation Toolbar */
    #ysf-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    #ysf-nav-placeholder input[type="button"],
    #ysf-nav-placeholder button {
        background-color: rgba(255,255,255,0.1);
        color: <?php echo $textBanners; ?> !important;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s;
        margin: 0 !important;
        box-shadow: none;
    }
    #ysf-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    /* Header */
    .ysf-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        text-align: center;
    }

    /* Body */
    .ysf-body {
        padding: 25px;
    }

    .ysf-form-group {
        margin-bottom: 20px;
    }
    .ysf-label {
        display: block;
        margin-bottom: 8px;
        color: <?php echo $textContent; ?>;
        font-size: 0.85em;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.8;
    }

    /* Status Box */
    .ysf-status-box {
        width: 100%;
        padding: 12px;
        background-color: <?php echo $tableRowEvenBg; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 4px;
        color: <?php echo $textContent; ?>;
        font-family: 'Inconsolata', monospace;
        font-weight: bold;
        font-size: 1.1rem;
        box-sizing: border-box;
        min-height: 45px;
        display: flex;
        align-items: center;
    }

    /* Segmented Toggle */
    .ysf-toggle-group {
        display: flex;
        background: <?php echo $tableRowEvenBg; ?>;
        border-radius: 4px;
        padding: 3px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .ysf-toggle-option { flex: 1; text-align: center; position: relative; }
    .ysf-toggle-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .ysf-toggle-option label {
        display: block; padding: 10px; border-radius: 3px; cursor: pointer;
        color: <?php echo $textContent; ?>; font-weight: 600; font-size: 0.95rem; opacity: 0.6;
        transition: all 0.2s;
        margin: 0;
    }
    .ysf-toggle-option input[type="radio"]:checked + label {
        background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>;
        opacity: 1;
    }

    /* Button */
    .ysf-btn {
        width: 100%;
        padding: 15px;
        background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 6px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.2s;
    }
    .ysf-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?>;
        transform: translateY(-2px);
    }

    /* Alerts */
    .ysf-alert {
        padding: 20px;
        border-radius: 4px;
        margin: 20px;
        text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .ysf-alert-success { background-color: <?php echo $backgroundModeCellActiveColor; ?>; color: <?php echo $textModeCellActiveColor; ?>; }
    .ysf-alert-error   { background-color: <?php echo $backgroundModeCellInactiveColor; ?>; color: <?php echo $textModeCellInactiveColor; ?>; }

    .ysf-help-text {
        font-size: 0.85em;
        text-align: center;
        margin-top: 20px;
        border-top: 1px solid <?php echo $tableBorderColor; ?>;
        padding-top: 15px;
    }
    .ysf-help-text a { color: <?php echo $textLinks; ?>; text-decoration: underline; }

    /* --- SELECT2 GLOBAL OVERRIDES --- */
    /* Forces the dropdown (which is attached to body) to use Dark Theme */
    .select2-container .select2-selection--single {
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        height: 45px !important;
        padding: 8px 0 !important;
        border-radius: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: <?php echo $textContent; ?> !important;
        line-height: 28px !important;
        background-color: transparent !important;
        font-family: 'Inconsolata', monospace !important;
        font-size: 1.1rem !important;
        padding-left: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important;
        right: 5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: <?php echo $textContent; ?> transparent transparent transparent !important;
    }
    
    /* The Dropdown Menu Itself */
    .select2-dropdown {
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        color: <?php echo $textContent; ?> !important;
        z-index: 9999 !important;
    }
    .select2-container--default .select2-results__option {
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        color: <?php echo $textContent; ?> !important;
        font-family: 'Inconsolata', monospace !important;
        padding: 8px 10px !important;
    }
    /* Hover/Selected State */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: <?php echo $backgroundNavbar; ?> !important;
        color: <?php echo $textNavbar; ?> !important;
    }
    /* Search Box inside Dropdown */
    .select2-search__field {
        background-color: <?php echo $backgroundContent; ?> !important;
        color: <?php echo $textContent; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        font-family: 'Inconsolata', monospace !important;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('ysf-nav-placeholder');
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    legacyButtons.forEach(btn => {
        if (!btn.closest('.ysf-card')) {
            navPlaceholder.appendChild(btn);
        }
    });
    if (navPlaceholder.children.length === 0) { navPlaceholder.style.display = 'none'; }
});
</script>

<?php
    // --- POST HANDLING ---
    if (!empty($_POST) && isset($_POST["ysfMgrSubmit"])) {
        ?>
        <div class="ysf-wrapper">
            <div class="ysf-card">
                <div id="ysf-nav-placeholder"></div>
                <div class="ysf-header">YSF Link Manager</div>
                <div class="ysf-body">
        <?php
        
        // Validation
        if (preg_match('/[^A-Za-z0-9]/',$_POST['ysfLinkHost'])) { unset($_POST['ysfLinkHost']); }
        
        $remoteCommand = null;
        $errorMsg = null;

        if ($_POST["Link"] == "LINK") {
            if (empty($_POST['ysfLinkHost'])) {
                $errorMsg = "No target specified. Please try again.";
            } elseif ($_POST['ysfLinkHost'] == "none") {
                $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." UnLink";
                if (isset($_SESSION['DMR2YSFConfigs']['Enabled']['Enabled']) == 1) {
                    exec("sudo sed -i '/DefaultDstTG=/c\\DefaultDstTG=9' /etc/dmr2ysf ; sudo systemctl restart dmr2ysf.service");
                }
            } else {
                $ysfLinkHost = $_POST['ysfLinkHost'];
                $ysfType = substr($ysfLinkHost, 0, 3);
                $ysfID = substr($ysfLinkHost, 3);
                $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." Link".$ysfType." ".$ysfID."";
                if (isset($_SESSION['DMR2YSFConfigs']['Enabled']['Enabled']) == 1) {
                    exec("sudo sed -i '/DefaultDstTG=/c\\DefaultDstTG=$ysfID' /etc/dmr2ysf ; sudo systemctl restart dmr2ysf.service");
                }
            }
        } elseif ($_POST["Link"] == "UNLINK") {
            $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." UnLink";
            if (isset($_SESSION['DMR2YSFConfigs']['Enabled']['Enabled']) == 1) {
                exec("sudo sed -i '/DefaultDstTG=/c\\DefaultDstTG=9' /etc/dmr2ysf ; sudo systemctl restart dmr2ysf.service");
            }
        } else {
            $errorMsg = "Invalid Command (Neither Link nor Unlink).";
        }

        // Output Result
        if ($errorMsg) {
            echo '<div class="ysf-alert ysf-alert-error"><strong>Error:</strong> '.$errorMsg.'<br>Page reloading...</div>';
        } elseif ($remoteCommand) {
            echo '<div class="ysf-alert ysf-alert-success"><strong>Command Sent</strong><br>';
            echo exec($remoteCommand);
            echo '<br><br>Page reloading...</div>';
        }

        echo '</div></div></div>';
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';

    } else {
        // --- MAIN VIEW ---
?>
    <div class="ysf-wrapper">
        <div class="ysf-card">
            <div id="ysf-nav-placeholder"></div>

            <div class="ysf-header">YSF Link Manager</div>

            <div class="ysf-body">
                <form action="//<?php echo htmlentities($_SERVER['HTTP_HOST']).htmlentities($_SERVER['PHP_SELF']); ?>?func=ysf_man" method="post">
                    
                    <div class="ysf-form-group">
                        <label class="ysf-label">Select Reflector</label>
                        <select name="ysfLinkHost" class="ysfLinkHost" style="width:100%;">
                            <?php
                            $testYSFHost = isset($_SESSION['YSFGatewayConfigs']['Network']['Startup']) ? $_SESSION['YSFGatewayConfigs']['Network']['Startup'] : "none";
                            echo '<option value="none" ' . ($testYSFHost == "none" ? 'selected' : '') . '>None</option>'."\n";
                            
                            // Standard Special Links
                            $specialLinks = [
                                "ZZ Parrot" => ["YSF00001", "YSF00001 - Parrot"],
                                "YSF2DMR"   => ["YSF00002", "YSF00002 - Link YSF2DMR"],
                                "YSF2NXDN"  => ["YSF00003", "YSF00003 - Link YSF2NXDN"],
                                "YSF2P25"   => ["YSF00004", "YSF00004 - Link YSF2P25"]
                            ];

                            foreach ($specialLinks as $key => $val) {
                                $selected = ($testYSFHost == $key) ? 'selected' : '';
                                echo "<option value=\"$val[0]\" $selected>$val[1]</option>\n";
                            }

                            // YSF Hosts
                            if (file_exists("/usr/local/etc/YSFHosts.txt")) {
                                $ysfHosts = fopen("/usr/local/etc/YSFHosts.txt", "r");
                                while (!feof($ysfHosts)) {
                                    $ysfHostsLine = fgets($ysfHosts);
                                    $ysfHost = preg_split('/;/', $ysfHostsLine);
                                    if ((strpos($ysfHost[0], '#') === FALSE ) && ($ysfHost[0] != '')) {
                                        $desc = (strlen($ysfHost[1]) >= 30) ? substr($ysfHost[1], 0, 27)."..." : $ysfHost[1];
                                        $selected = ($testYSFHost == $ysfHost[1]) ? 'selected="selected"' : '';
                                        echo "<option value=\"YSF$ysfHost[0]\" $selected>YSF$ysfHost[0] - ".htmlspecialchars($desc)." - ".htmlspecialchars($ysfHost[2])."</option>\n";
                                    }
                                }
                                fclose($ysfHosts);
                            }

                            // FCS Hosts
                            if ($_SESSION['YSFGatewayConfigs']['FCS Network']['Enable'] == 1) {
                                if (file_exists("/usr/local/etc/FCSHosts.txt")) {
                                    $fcsHosts = fopen("/usr/local/etc/FCSHosts.txt", "r");
                                    while (!feof($fcsHosts)) {
                                        $ysfHostsLine = fgets($fcsHosts);
                                        $ysfHost = preg_split('/;/', $ysfHostsLine);
                                        if (substr($ysfHost[0], 0, 3) == "FCS") {
                                            $desc = (strlen($ysfHost[1]) >= 30) ? substr($ysfHost[1], 0, 27)."..." : $ysfHost[1];
                                            $selected = ($testYSFHost == $ysfHost[0]) ? 'selected="selected"' : '';
                                            echo "<option value=\"$ysfHost[0]\" $selected>$ysfHost[0] - ".htmlspecialchars($desc)."</option>\n";
                                        }
                                    }
                                    fclose($fcsHosts);
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <?php
                    $ysfLinkedTo = getActualLink($reverseLogLinesYSFGateway, "YSF");
                    $ysfLinkState = '';
                    $ysfRoomNo = '';
                    
                    if ($ysfLinkedTo == 'Not Linked' || $ysfLinkedTo == 'Service Not Started') {
                        $ysfLinkedToTxt = 'Not Linked';
                    } else {
                        // Lookup YSF Name
                        $ysfLinkedToTxt = "null";
                        if (file_exists("/usr/local/etc/YSFHosts.txt")) {
                            $ysfHostFile = fopen("/usr/local/etc/YSFHosts.txt", "r");
                            while (!feof($ysfHostFile)) {
                                $ysfHostFileLine = fgets($ysfHostFile);
                                $ysfRoomTxtLine = preg_split('/;/', $ysfHostFileLine);
                                if (!empty($ysfRoomTxtLine[0]) && !empty($ysfRoomTxtLine[1])) {
                                    if (($ysfRoomTxtLine[0] == $ysfLinkedTo) || ($ysfRoomTxtLine[1] == $ysfLinkedTo)) {
                                        $ysfRoomNo = "YSF".$ysfRoomTxtLine[0];
                                        $ysfLinkedToTxt = $ysfRoomTxtLine[1];
                                        break;
                                    }
                                }
                            }
                            fclose($ysfHostFile);
                        }

                        // Lookup FCS Name
                        if ($_SESSION['YSFGatewayConfigs']['FCS Network']['Enable'] == 1) {
                            if (file_exists("/usr/local/etc/FCSHosts.txt")) {
                                $fcsHostFile = fopen("/usr/local/etc/FCSHosts.txt", "r");
                                while (!feof($fcsHostFile)) {
                                    $ysfHostFileLine = fgets($fcsHostFile);
                                    $ysfRoomTxtLine = preg_split('/;/', $ysfHostFileLine);
                                    if (!empty($ysfRoomTxtLine[0]) && !empty($ysfRoomTxtLine[1])) {
                                        if (($ysfRoomTxtLine[0] == $ysfLinkedTo) || ($ysfRoomTxtLine[1] == $ysfLinkedTo)) {
                                            $ysfLinkedToTxt = $ysfRoomTxtLine[1];
                                            $ysfRoomNo = $ysfRoomTxtLine[0];
                                            break;
                                        }
                                    }
                                }
                                fclose($fcsHostFile);
                            }
                        }

                        if ($_SESSION['YSFGatewayConfigs']['FCS Network']['Enable'] != 1) {
                            $ysfLinkedToTxt = $ysfLinkedTo;
                            $ysfLinkState = ' [Linked]';
                        } else {
                            if ($ysfLinkedToTxt != "null") {
                                $ysfLinkState = ' [In Room]';
                            } else {
                                $ysfLinkedToTxt = $ysfLinkedTo;
                                $ysfLinkState = ' [Linked]';
                            }
                        }
                        $ysfLinkedToTxt = str_replace('_', ' ', $ysfLinkedToTxt);
                    }

                    $ysfTableData = (empty($ysfRoomNo) || ($ysfRoomNo == "null")) ? "$ysfLinkState $ysfLinkedToTxt" : "$ysfLinkState $ysfLinkedToTxt ($ysfRoomNo)";
                    ?>
                    
                    <script>
                        $(document).ready(function(){
                            setInterval(function(){
                                $(".CheckLink").load(window.location.href + " .CheckLink" );
                            }, 3000);
                        });
                    </script>

                    <div class="ysf-form-group">
                        <label class="ysf-label">Current Link</label>
                        <div class="ysf-status-box">
                            <span class="CheckLink"><?php echo $ysfTableData; ?></span>
                        </div>
                    </div>

                    <div class="ysf-form-group">
                        <label class="ysf-label">Action</label>
                        <div class="ysf-toggle-group">
                            <div class="ysf-toggle-option">
                                <input type="radio" id="link" name="Link" value="LINK">
                                <label for="link">Link</label>
                            </div>
                            <div class="ysf-toggle-option">
                                <input type="radio" id="unlink" name="Link" value="UNLINK" checked="checked">
                                <label for="unlink">Unlink</label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="func" value="ysf_man" />
                    <input type="submit" class="ysf-btn" name="ysfMgrSubmit" value="Execute Action" />
                </form>

                <div class="ysf-help-text">
                    <a href="https://w0chp.radio/ysf-reflectors/" target="_blank">List of YSF Reflectors</a> &bull; <a href="https://w0chp.radio/fcs-reflectors/" target="_blank">List of FCS Reflectors</a>
                </div>
            </div>
        </div>
    </div>
<?php
    }
    }
}
?>
