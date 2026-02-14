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

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php';

// Check if NXDN is Enabled
$testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
$testDMR2NXDN = isset($_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled']) ? $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'] : 0;
$testYSF2NXDN = isset($_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled']) ? $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'] : 0;

if ( $testMMDVModeNXDN == 1 || $testDMR2NXDN == 1 || $testYSF2NXDN == 1 ) {
    
    if (isset($_SESSION['NXDNGatewayConfigs']['Remote Commands']['Enable']) && (isset($_SESSION['NXDNGatewayConfigs']['Remote Commands']['Port'])) && ($_SESSION['NXDNGatewayConfigs']['Remote Commands']['Enable'] == 1)) {
        $remotePort = $_SESSION['NXDNGatewayConfigs']['Remote Commands']['Port'];
?>

<style>
    /* Modern NXDN Manager CSS */
    .nxdn-wrapper {
        display: flex; justify-content: center; align-items: flex-start; padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .nxdn-card {
        background-color: <?php echo $backgroundContent; ?>; border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 8px; padding: 0; width: 100%; max-width: 600px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25); color: <?php echo $textContent; ?>;
        overflow: visible; position: relative; text-align: left;
    }
    /* Toolbar */
    #nxdn-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>; padding: 10px; border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex; flex-wrap: wrap; justify-content: center; gap: 5px;
    }
    #nxdn-nav-placeholder input[type="button"] {
        background-color: rgba(255,255,255,0.1); color: <?php echo $textBanners; ?> !important;
        border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; padding: 6px 12px;
        font-size: 0.8rem; font-weight: 600; text-transform: uppercase; cursor: pointer;
        transition: all 0.2s; margin: 0 !important; box-shadow: none;
    }
    #nxdn-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>; color: <?php echo $textNavbarHover; ?> !important; border-color: <?php echo $textNavbarHover; ?>;
    }
    /* Header */
    .nxdn-header {
        padding: 20px; font-weight: 700; font-size: 1.4rem; text-transform: uppercase;
        letter-spacing: 1px; border-bottom: 1px solid <?php echo $tableBorderColor; ?>; text-align: center;
    }
    .nxdn-body { padding: 25px; }
    .nxdn-form-group { margin-bottom: 20px; }
    .nxdn-label {
        display: block; margin-bottom: 8px; color: <?php echo $textContent; ?>;
        font-size: 0.85em; font-weight: 700; text-transform: uppercase; opacity: 0.8;
    }
    /* Status Box */
    .nxdn-status-box {
        width: 100%; padding: 12px; background-color: <?php echo $tableRowEvenBg; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>; border-radius: 4px;
        color: <?php echo $textContent; ?>; font-family: 'Inconsolata', monospace;
        font-weight: bold; font-size: 1.1rem; box-sizing: border-box; min-height: 45px;
        display: flex; align-items: center;
    }
    /* Toggle */
    .nxdn-toggle-group {
        display: flex; background: <?php echo $tableRowEvenBg; ?>; border-radius: 4px; padding: 3px; border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .nxdn-toggle-option { flex: 1; text-align: center; position: relative; }
    .nxdn-toggle-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .nxdn-toggle-option label {
        display: block; padding: 10px; border-radius: 3px; cursor: pointer; color: <?php echo $textContent; ?>;
        font-weight: 600; font-size: 0.95rem; opacity: 0.6; transition: all 0.2s; margin: 0;
    }
    .nxdn-toggle-option input[type="radio"]:checked + label {
        background-color: <?php echo $backgroundNavbar; ?>; color: <?php echo $textNavbar; ?>; opacity: 1;
    }
    /* Button */
    .nxdn-btn {
        width: 100%; padding: 15px; background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>; border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 6px; font-size: 1.1rem; font-weight: 700; cursor: pointer;
        text-transform: uppercase; transition: all 0.2s;
    }
    .nxdn-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>; color: <?php echo $textNavbarHover; ?>; transform: translateY(-2px);
    }
    /* Alert */
    .nxdn-alert {
        padding: 20px; border-radius: 4px; margin: 20px; text-align: center; border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .nxdn-alert-success { background-color: <?php echo $backgroundModeCellActiveColor; ?>; color: <?php echo $textModeCellActiveColor; ?>; }
    .nxdn-alert-error { background-color: <?php echo $backgroundModeCellInactiveColor; ?>; color: <?php echo $textModeCellInactiveColor; ?>; }
    .nxdn-help-text {
        font-size: 0.85em; text-align: center; margin-top: 20px;
        border-top: 1px solid <?php echo $tableBorderColor; ?>; padding-top: 15px;
    }
    .nxdn-help-text a { color: <?php echo $textLinks; ?>; text-decoration: underline; }

    /* Select2 Overrides */
    .select2-dropdown { background-color: <?php echo $tableRowEvenBg; ?> !important; border: 1px solid <?php echo $tableBorderColor; ?> !important; color: <?php echo $textContent; ?> !important; z-index: 9999 !important; }
    .select2-container--default .select2-results__option { background-color: <?php echo $tableRowEvenBg; ?> !important; color: <?php echo $textContent; ?> !important; font-family: 'Inconsolata', monospace !important; padding: 8px 10px !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: <?php echo $backgroundNavbar; ?> !important; color: <?php echo $textNavbar; ?> !important; }
    .select2-search__field { background-color: <?php echo $backgroundContent; ?> !important; color: <?php echo $textContent; ?> !important; border: 1px solid <?php echo $tableBorderColor; ?> !important; font-family: 'Inconsolata', monospace !important; }
    .nxdn-card .select2-container .select2-selection--single { background-color: <?php echo $tableRowEvenBg; ?> !important; border: 1px solid <?php echo $tableBorderColor; ?> !important; height: 45px !important; padding: 8px 0 !important; border-radius: 4px !important; }
    .nxdn-card .select2-container--default .select2-selection--single .select2-selection__rendered { color: <?php echo $textContent; ?> !important; line-height: 28px !important; background-color: transparent !important; font-family: 'Inconsolata', monospace !important; font-size: 1.1rem !important; padding-left: 10px !important; }
    .nxdn-card .select2-container--default .select2-selection--single .select2-selection__arrow { height: 43px !important; right: 5px !important; }
    .nxdn-card .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: <?php echo $textContent; ?> transparent transparent transparent !important; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('nxdn-nav-placeholder');
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    legacyButtons.forEach(btn => { if (!btn.closest('.nxdn-card')) { navPlaceholder.appendChild(btn); } });
    if (navPlaceholder.children.length === 0) { navPlaceholder.style.display = 'none'; }
});
</script>

<?php
    if (!empty($_POST) && isset($_POST["nxdnMgrSubmit"])) {
        ?>
        <div class="nxdn-wrapper">
            <div class="nxdn-card">
                <div id="nxdn-nav-placeholder"></div>
                <div class="nxdn-header">NXDN Link Manager</div>
                <div class="nxdn-body">
        <?php
        
        if (preg_match('/[^A-Za-z0-9]/',$_POST['nxdnLinkHost'])) { unset ($_POST['nxdnLinkHost']); }
        $remoteCommand = null;
        $errorMsg = null;

        if ($_POST["Link"] == "LINK") {
            if (empty($_POST['nxdnLinkHost'])) {
                $errorMsg = "No target specified. Please try again.";
            } elseif ($_POST['nxdnLinkHost'] == "none") {
                $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup unlink";
            } else {
                $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup ".$_POST['nxdnLinkHost'];
            }
        } elseif ($_POST["Link"] == "UNLINK") {
            $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup unlink";
        } else {
            $errorMsg = "Invalid Command.";
        }

        if ($errorMsg) {
            echo '<div class="nxdn-alert nxdn-alert-error"><strong>Error:</strong> '.$errorMsg.'<br>Page reloading...</div>';
        } elseif ($remoteCommand) {
            echo '<div class="nxdn-alert nxdn-alert-success"><strong>Command Sent</strong><br>'.exec($remoteCommand).'<br><br>Page reloading...</div>';
        }

        echo '</div></div></div>';
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';

    } else {
?>
    <div class="nxdn-wrapper">
        <div class="nxdn-card">
            <div id="nxdn-nav-placeholder"></div>
            <div class="nxdn-header">NXDN Link Manager</div>
            <div class="nxdn-body">
                <form action="//<?php echo htmlentities($_SERVER['HTTP_HOST']).htmlentities($_SERVER['PHP_SELF']); ?>?func=nxdn_man" method="post">
                    
                    <div class="nxdn-form-group">
                        <label class="nxdn-label">Select Reflector</label>
                        <select name="nxdnLinkHost" class="nxdnLinkHost" style="width:100%;">
                            <?php
                            $nxdnHosts = fopen("/usr/local/etc/NXDNHosts.txt", "r");
                            $testNXDNHost = "none";
                            if (isset($_SESSION['NXDNGatewayConfigs']['Network']['Startup'])) {
                                $testNXDNHost = $_SESSION['NXDNGatewayConfigs']['Network']['Startup'];
                            } elseif (isset($_SESSION['NXDNGatewayConfigs']['Network']['Static'])) {
                                $testNXDNHost = $_SESSION['NXDNGatewayConfigs']['Network']['Static'];
                            }
                            
                            echo '<option value="none" ' . (($testNXDNHost == "") ? 'selected' : '') . '>None</option>'."\n";
                            echo '<option value="10" ' . (($testNXDNHost == "10") ? 'selected' : '') . '>10 - Parrot</option>'."\n";

                            while (!feof($nxdnHosts)) {
                                $nxdnHostsLine = fgets($nxdnHosts);
                                $nxdnHost = preg_split('/\s+/', $nxdnHostsLine);
                                if ((strpos($nxdnHost[0], '#') === FALSE ) && ($nxdnHost[0] != '')) {
                                    $selected = ($testNXDNHost == $nxdnHost[0]) ? 'selected="selected"' : '';
                                    echo "<option value=\"$nxdnHost[0]\" $selected>$nxdnHost[0] - $nxdnHost[1]</option>\n";
                                }
                            }
                            fclose($nxdnHosts);
                            
                            if (file_exists('/usr/local/etc/NXDNHostsLocal.txt')) {
                                $nxdnHosts2 = fopen("/usr/local/etc/NXDNHostsLocal.txt", "r");
                                while (!feof($nxdnHosts2)) {
                                    $nxdnHostsLine2 = fgets($nxdnHosts2);
                                    $nxdnHost2 = preg_split('/\s+/', $nxdnHostsLine2);
                                    if ((strpos($nxdnHost2[0], '#') === FALSE ) && ($nxdnHost2[0] != '')) {
                                        $selected = ($testNXDNHost == $nxdnHost2[0]) ? 'selected="selected"' : '';
                                        echo "<option value=\"$nxdnHost2[0]\" $selected>$nxdnHost2[0] - $nxdnHost2[1]</option>\n";
                                    }
                                }
                                fclose($nxdnHosts2);
                            }
                            ?>
                        </select>
                    </div>

                    <?php
                    $target = getActualLink($logLinesNXDNGateway, "NXDN");
                    $target = str_replace("TG", "", $target);
                    if (strpos($target, "Not") === false) { 
                        $target_lookup = exec("grep -w \"$target\" /usr/local/etc/TGList_NXDN.txt | awk -F';' '{print $2}'");
                        if (!empty($target_lookup)) {
                            $target = "TG $target: $target_lookup";
                        }
                    } else {
                        $target = "Not Linked";
                    }
                    ?>
                    
                    <script>
                        $(document).ready(function(){
                            setInterval(function(){
                                $(".CheckLink").load(window.location.href + " .CheckLink" );
                            }, 3000);
                        });
                    </script>

                    <div class="nxdn-form-group">
                        <label class="nxdn-label">Current Link</label>
                        <div class="nxdn-status-box">
                            <span class="CheckLink"><?php echo $target; ?></span>
                        </div>
                    </div>

                    <div class="nxdn-form-group">
                        <label class="nxdn-label">Action</label>
                        <div class="nxdn-toggle-group">
                            <div class="nxdn-toggle-option">
                                <input type="radio" id="link" name="Link" value="LINK">
                                <label for="link">Link</label>
                            </div>
                            <div class="nxdn-toggle-option">
                                <input type="radio" id="unlink" name="Link" value="UNLINK" checked="checked">
                                <label for="unlink">Unlink</label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="func" value="nxdn_man" />
                    <input type="submit" class="nxdn-btn" name="nxdnMgrSubmit" value="Execute Action" />
                </form>

                <div class="nxdn-help-text">
                    <a href="https://w0chp.radio/nxdn-reflectors/" target="_blank">List of NXDN Reflectors</a>
                </div>
            </div>
        </div>
    </div>
<?php
    }
    }
}
?>
