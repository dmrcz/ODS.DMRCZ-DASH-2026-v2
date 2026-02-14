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

// Check if P25 is Enabled
$testMMDVModeP25 = getConfigItem("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
$testYSF2P25 = isset($_SESSION['YSF2P25Configs']['Enabled']['Enabled']) ? $_SESSION['YSF2P25Configs']['Enabled']['Enabled'] : 0;

if ( $testMMDVModeP25 == 1 || $testYSF2P25 == 1 ) {
    // Check that the remote is enabled
    if (isset($_SESSION['P25GatewayConfigs']['Remote Commands']['Enable']) && (isset($_SESSION['P25GatewayConfigs']['Remote Commands']['Port'])) && ($_SESSION['P25GatewayConfigs']['Remote Commands']['Enable'] == 1)) {
        $remotePort = $_SESSION['P25GatewayConfigs']['Remote Commands']['Port'];
?>

<style>
    /* Modern P25 Manager CSS */
    .p25-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .p25-card {
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 8px;
        padding: 0;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        color: <?php echo $textContent; ?>;
        overflow: visible;
        position: relative;
        text-align: left;
    }

    /* Navigation Toolbar */
    #p25-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    #p25-nav-placeholder input[type="button"],
    #p25-nav-placeholder button {
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
    #p25-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    .p25-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        text-align: center;
    }

    .p25-body { padding: 25px; }
    .p25-form-group { margin-bottom: 20px; }
    .p25-label {
        display: block; margin-bottom: 8px; color: <?php echo $textContent; ?>;
        font-size: 0.85em; font-weight: 700; text-transform: uppercase; opacity: 0.8;
    }

    /* Status Box */
    .p25-status-box {
        width: 100%; padding: 12px; background-color: <?php echo $tableRowEvenBg; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>; border-radius: 4px;
        color: <?php echo $textContent; ?>; font-family: 'Inconsolata', monospace;
        font-weight: bold; font-size: 1.1rem; box-sizing: border-box;
        min-height: 45px; display: flex; align-items: center;
    }

    /* Segmented Toggle */
    .p25-toggle-group {
        display: flex; background: <?php echo $tableRowEvenBg; ?>;
        border-radius: 4px; padding: 3px; border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .p25-toggle-option { flex: 1; text-align: center; position: relative; }
    .p25-toggle-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .p25-toggle-option label {
        display: block; padding: 10px; border-radius: 3px; cursor: pointer;
        color: <?php echo $textContent; ?>; font-weight: 600; font-size: 0.95rem; opacity: 0.6;
        transition: all 0.2s; margin: 0;
    }
    .p25-toggle-option input[type="radio"]:checked + label {
        background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>;
        opacity: 1;
    }

    /* Button */
    .p25-btn {
        width: 100%; padding: 15px; background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>; border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 6px; font-size: 1.1rem; font-weight: 700; cursor: pointer;
        text-transform: uppercase; transition: all 0.2s;
    }
    .p25-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?>;
        transform: translateY(-2px);
    }

    /* Alerts */
    .p25-alert {
        padding: 20px; border-radius: 4px; margin: 20px; text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .p25-alert-success { background-color: <?php echo $backgroundModeCellActiveColor; ?>; color: <?php echo $textModeCellActiveColor; ?>; }
    .p25-alert-error   { background-color: <?php echo $backgroundModeCellInactiveColor; ?>; color: <?php echo $textModeCellInactiveColor; ?>; }

    .p25-help-text {
        font-size: 0.85em; text-align: center; margin-top: 20px;
        border-top: 1px solid <?php echo $tableBorderColor; ?>; padding-top: 15px;
    }
    .p25-help-text a { color: <?php echo $textLinks; ?>; text-decoration: underline; }

    /* --- GLOBAL SELECT2 OVERRIDES --- */
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
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: <?php echo $backgroundNavbar; ?> !important;
        color: <?php echo $textNavbar; ?> !important;
    }
    .select2-search__field {
        background-color: <?php echo $backgroundContent; ?> !important;
        color: <?php echo $textContent; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        font-family: 'Inconsolata', monospace !important;
    }
    /* Container Overrides */
    .p25-card .select2-container .select2-selection--single {
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        height: 45px !important;
        padding: 8px 0 !important;
        border-radius: 4px !important;
    }
    .p25-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: <?php echo $textContent; ?> !important;
        line-height: 28px !important;
        background-color: transparent !important;
        font-family: 'Inconsolata', monospace !important;
        font-size: 1.1rem !important;
        padding-left: 10px !important;
    }
    .p25-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important; right: 5px !important;
    }
    .p25-card .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: <?php echo $textContent; ?> transparent transparent transparent !important;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('p25-nav-placeholder');
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    legacyButtons.forEach(btn => {
        if (!btn.closest('.p25-card')) {
            navPlaceholder.appendChild(btn);
        }
    });
    if (navPlaceholder.children.length === 0) { navPlaceholder.style.display = 'none'; }
});
</script>

<?php
    // --- POST HANDLING ---
    if (!empty($_POST) && isset($_POST["p25MgrSubmit"])) {
        ?>
        <div class="p25-wrapper">
            <div class="p25-card">
                <div id="p25-nav-placeholder"></div>
                <div class="p25-header">P25 Link Manager</div>
                <div class="p25-body">
        <?php
        
        if (preg_match('/[^A-Za-z0-9]/',$_POST['p25LinkHost'])) { unset($_POST['p25LinkHost']); }
        
        $remoteCommand = null;
        $errorMsg = null;

        if ($_POST["Link"] == "LINK") {
            if (empty($_POST['p25LinkHost'])) {
                $errorMsg = "No target specified. Please try again.";
            } elseif ($_POST['p25LinkHost'] == "none") {
                $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup unlink";
            } else {
                $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup ".$_POST['p25LinkHost'];
            }
        } elseif ($_POST["Link"] == "UNLINK") {
            $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup unlink";
        } else {
            $errorMsg = "Invalid Command.";
        }

        if ($errorMsg) {
            echo '<div class="p25-alert p25-alert-error"><strong>Error:</strong> '.$errorMsg.'<br>Page reloading...</div>';
        } elseif ($remoteCommand) {
            echo '<div class="p25-alert p25-alert-success"><strong>Command Sent</strong><br>';
            echo exec($remoteCommand);
            echo '<br><br>Page reloading...</div>';
        }

        echo '</div></div></div>';
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';

    } else {
        // --- MAIN VIEW ---
?>
    <div class="p25-wrapper">
        <div class="p25-card">
            <div id="p25-nav-placeholder"></div>
            <div class="p25-header">P25 Link Manager</div>
            <div class="p25-body">
                <form action="//<?php echo htmlentities($_SERVER['HTTP_HOST']).htmlentities($_SERVER['PHP_SELF']); ?>?func=p25_man" method="post">
                    
                    <div class="p25-form-group">
                        <label class="p25-label">Select Reflector</label>
                        <select name="p25LinkHost" class="p25LinkHost" style="width:100%;">
                            <?php
                            $testP25Host = "none";
                            if (isset($_SESSION['P25GatewayConfigs']['Network']['Startup'])) {
                                $testP25Host = $_SESSION['P25GatewayConfigs']['Network']['Startup'];
                            } elseif (isset($_SESSION['P25GatewayConfigs']['Network']['Static'])) {
                                $testP25Host = $_SESSION['P25GatewayConfigs']['Network']['Static'];
                            }
                            
                            echo '<option value="none" ' . (($testP25Host == "") ? 'selected' : '') . '>None</option>'."\n";
                            echo '<option value="10" ' . (($testP25Host == "10") ? 'selected' : '') . '>10 - Parrot</option>'."\n";

                            $p25Hosts = fopen("/usr/local/etc/P25Hosts.txt", "r");
                            while (!feof($p25Hosts)) {
                                $p25HostsLine = fgets($p25Hosts);
                                $p25Host = preg_split('/\s+/', $p25HostsLine);
                                if ((strpos($p25Host[0], '#') === FALSE ) && ($p25Host[0] != '')) {
                                    $selected = ($testP25Host == $p25Host[0]) ? 'selected="selected"' : '';
                                    echo "<option value=\"$p25Host[0]\" $selected>$p25Host[0] - $p25Host[1]</option>\n";
                                }
                            }
                            fclose($p25Hosts);
                            
                            if (file_exists('/usr/local/etc/P25HostsLocal.txt')) {
                                $p25Hosts2 = fopen("/usr/local/etc/P25HostsLocal.txt", "r");
                                while (!feof($p25Hosts2)) {
                                    $p25HostsLine2 = fgets($p25Hosts2);
                                    $p25Host2 = preg_split('/\s+/', $p25HostsLine2);
                                    if ((strpos($p25Host2[0], '#') === FALSE ) && ($p25Host2[0] != '')) {
                                        $selected = ($testP25Host == $p25Host2[0]) ? 'selected="selected"' : '';
                                        echo "<option value=\"$p25Host2[0]\" $selected>$p25Host2[0] - $p25Host2[1]</option>\n";
                                    }
                                }
                                fclose($p25Hosts2);
                            }
                            ?>
                        </select>
                    </div>

                    <?php
                    $target = getActualLink($logLinesP25Gateway, "P25");
                    $target = str_replace("TG", "", $target);
                    if (strpos($target, "Not") === false) { 
                        $target_lookup = exec("grep -w \"$target\" /usr/local/etc/TGList_P25.txt | awk -F';' '{print $2}'");
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

                    <div class="p25-form-group">
                        <label class="p25-label">Current Link</label>
                        <div class="p25-status-box">
                            <span class="CheckLink"><?php echo $target; ?></span>
                        </div>
                    </div>

                    <div class="p25-form-group">
                        <label class="p25-label">Action</label>
                        <div class="p25-toggle-group">
                            <div class="p25-toggle-option">
                                <input type="radio" id="link" name="Link" value="LINK">
                                <label for="link">Link</label>
                            </div>
                            <div class="p25-toggle-option">
                                <input type="radio" id="unlink" name="Link" value="UNLINK" checked="checked">
                                <label for="unlink">Unlink</label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="func" value="p25_man" />
                    <input type="submit" class="p25-btn" name="p25MgrSubmit" value="Execute Action" />
                </form>

                <div class="p25-help-text">
                    <a href="https://w0chp.radio/p25-reflectors/" target="_blank">List of P25 Reflectors</a>
                </div>
            </div>
        </div>
    </div>
<?php
    }
    }
}
?>
