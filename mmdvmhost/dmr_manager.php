<?php
if ($_SERVER["PHP_SELF"] != "/admin/index.php") return;  // Stop this working outside of the admin page

if (isset($_COOKIE['PHPSESSID']))
{
    session_id($_COOKIE['PHPSESSID']); 
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION) || (count($_SESSION, COUNT_RECURSIVE) < 10)) {
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

// Import Theme Variables
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php'; 
?>

<style>
    /* Main Layout */
    .dmr-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .dmr-card {
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 8px;
        padding: 0;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        color: <?php echo $textContent; ?>;
        overflow: visible; /* Important for Select2 Dropdown */
        position: relative;
    }

    /* Navigation Toolbar (Legacy Buttons) */
    #dmr-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    #dmr-nav-placeholder input[type="button"],
    #dmr-nav-placeholder button {
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
    #dmr-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    /* Header */
    .dmr-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        text-align: center;
    }
    .dmr-subheader {
        padding: 15px 20px;
        background-color: <?php echo $tableRowEvenBg; ?>;
        font-weight: 600;
        font-size: 1.1rem;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        border-top: 1px solid <?php echo $tableBorderColor; ?>;
    }

    /* Body */
    .dmr-body {
        padding: 0;
    }

    /* Network Row */
    .dmr-net-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        transition: background-color 0.2s;
    }
    .dmr-net-row:last-child { border-bottom: none; }
    .dmr-net-row:hover { background-color: rgba(255,255,255,0.02); }
    .dmr-net-name {
        font-size: 1.1rem;
        font-weight: 600;
    }

    /* Modern Toggle Switch */
    .dmr-switch {
        position: relative;
        display: inline-block;
        width: 40px;       /* Reduced */
        height: 20px;      /* Reduced */
    }
    .dmr-switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }
    .dmr-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
        transition: .4s;
        border-radius: 20px; /* Adjusted */
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .dmr-slider:before {
        position: absolute;
        content: "";
        height: 14px;      /* Reduced */
        width: 14px;       /* Reduced */
        left: 3px;         /* Adjusted */
        bottom: 2px;       /* Adjusted */
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .dmr-slider {
        background-color: #2ecc71;
        border-color: #27ae60;
    }
    input:checked + .dmr-slider:before {
        transform: translateX(20px); /* Adjusted */
    }

    /* XLX Form Section */
    .xlx-container {
        padding: 25px;
    }
    .xlx-form-group {
        margin-bottom: 20px;
        text-align: left;
    }
    .xlx-label {
        display: block;
        margin-bottom: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.8;
    }
    .xlx-select {
        width: 100%;
        padding: 12px;
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 4px;
        color: <?php echo $textContent; ?> !important;
        font-size: 1rem;
        font-family: 'Inconsolata', monospace;
    }
    .xlx-status-box {
        background-color: rgba(0,0,0,0.2);
        border: 1px solid <?php echo $tableBorderColor; ?>;
        padding: 15px;
        border-radius: 4px;
        text-align: center;
        margin-bottom: 20px;
    }
    .xlx-status-text {
        font-family: 'Inconsolata', monospace;
        font-weight: bold;
        font-size: 1.1rem;
    }

    /* Action Button */
    .dmr-btn {
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
    .dmr-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?>;
        transform: translateY(-2px);
    }

    /* Alerts */
    .dmr-alert {
        padding: 20px;
        border-radius: 4px;
        margin: 20px;
        text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .dmr-alert-success {
        background-color: <?php echo $backgroundModeCellActiveColor; ?>;
        color: <?php echo $textModeCellActiveColor; ?>;
        border-color: <?php echo $textModeCellActiveColor; ?>;
    }

    .dmr-help-text {
        padding: 0 25px 25px 25px;
        font-size: 0.85em;
        line-height: 1.5;
        text-align: center;
        border-top: 1px solid <?php echo $tableBorderColor; ?>;
        padding-top: 15px;
        margin-top: 0;
    }
    .dmr-help-text a { color: <?php echo $textLinks; ?>; text-decoration: underline; }

    /* --- SELECT2 DARK THEME OVERRIDES --- */
    .dmr-card .select2-container .select2-selection--single {
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        height: 45px !important;
        padding: 8px 0 !important;
        border-radius: 4px !important;
    }
    .dmr-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: <?php echo $textContent; ?> !important;
        line-height: 28px !important;
        background-color: transparent !important;
        font-family: 'Inconsolata', monospace !important;
        font-size: 1.1rem !important;
        padding-left: 10px !important;
    }
    .dmr-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important; right: 5px !important;
    }
    .dmr-card .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: <?php echo $textContent; ?> transparent transparent transparent !important;
    }
    .select2-dropdown {
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        color: <?php echo $textContent; ?> !important;
        z-index: 9999;
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
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('dmr-nav-placeholder');
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    
    legacyButtons.forEach(btn => {
        if (!btn.closest('.dmr-card')) {
            navPlaceholder.appendChild(btn);
        }
    });

    if (navPlaceholder.children.length === 0) {
        navPlaceholder.style.display = 'none';
    }
});
</script>

<div class="dmr-wrapper">
    <div class="dmr-card">
        <div id="dmr-nav-placeholder"></div>

        <div class="dmr-header">DMR Network Manager</div>

        <div class="dmr-body">
            <?php
                $remoteDMRgwResults = isset($_SESSION['remoteDMRgwResults'])? $_SESSION['remoteDMRgwResults']: [];
                $dmrNets = [];
                foreach ($_SESSION['DMRNetStatusAliases'] as $netId => $sectionName) {
                    if ($_SESSION['DMRGatewayConfigs'][$sectionName]['Enabled'] != "1") continue;
                    $dmrNets[] = [
                        'input-id' => 'toggle-' . $netId,
                        'id'       => $netId,
                        'enabled'  => !isset($remoteDMRgwResults[$netId]) || $remoteDMRgwResults[$netId] == 'conn',
                        'name'     => str_replace('_', ' ', $_SESSION['DMRGatewayConfigs'][$sectionName]['Name']),
                    ];
                }

                if ($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'] == "1") {
                    $dmrNets[] = [
                        'input-id' => 'toggle-xlx',
                        'id'       => 'xlx',
                        'enabled'  => !isset($remoteDMRgwResults['xlx']) || $remoteDMRgwResults['xlx'] == 'conn',
                        'name'     => 'XLX-' . $_SESSION['DMRGatewayConfigs']['XLX Network']['Startup'],
                    ];
                }

                if (count($dmrNets) > 1) {
            ?>
            
            <div id="dmrNetManList">
                <?php foreach ($dmrNets as $net) { ?>
                    <div class="dmr-net-row">
                        <div class="dmr-net-name"><?php echo $net['name']; ?></div>
                        <label class="dmr-switch">
                            <input class="dmrnetman-switch" type="checkbox" data-net-id="<?php echo $net['id']; ?>" <?php echo $net['enabled']? 'checked': ''; ?>>
                            <span class="dmr-slider"></span>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <div class="dmr-help-text" style="margin-top:15px; border-top:none;">
                Instantly disable / enable DMR Networks.<br />
                <em>Note: networks will be re-enabled upon reboots, updates and maintenance.</em>
            </div>

            <script type="text/javascript">
            $(function() {
                // Selector updated to match new structure
                $('.dmrnetman-switch').change(function() {
                    url = "/admin/system_api.php?action=dmrnet_set_status&dmrNet=" + $(this).data('net-id') +
                        "&netState=" + ($(this).prop('checked')? 'enable': 'disable');

                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function (response) {
                            // console.log('Success:', response);
                        },
                        error: function (xhr, status, error) {
                            // console.error('Error:', status, error);
                        }
                    });
                });
            });
            </script>
            <?php } ?>


            <?php
                // XLX Logic
                if ( !isset($_SESSION['DMRGatewayConfigs']['XLX Network 1']['Enabled']) && isset($_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled']) && $_SESSION['DMRGatewayConfigs']['XLX Network']['Enabled'] == 1) {
                    
                    if (!empty($_POST) && isset($_POST["xlxMgrSubmit"])) {
                        // POST Handling Logic (Hidden from view, logic only)
                        $xlxLinkHost = $_POST['dmrMasterHost3Startup'];
                        $startupModule = $_POST['dmrMasterHost3StartupModule'];
                        $xlxLinkToHost = "";
                        $remoteCommand = null;

                        if ($xlxLinkHost == "None") { 
                            $remoteCommand = 'sudo sed -i "/Module=/c\\Module=@" /etc/dmrgateway ; sudo systemctl restart dmrgateway.service';
                            $xlxLinkToHost = "Unlinking";
                        } elseif ($xlxLinkHost != "None") {
                            $remoteCommand = 'sudo sed -i "/Module=/c\\Module='.$startupModule.'" /etc/dmrgateway ; sudo sed -i "/Startup=/c\\Startup='.$xlxLinkHost.'" /etc/dmrgateway ; sudo systemctl restart dmrgateway.service';
                            $xlxLinkToHost = "Link set to XLX-".$xlxLinkHost.", Module ".$startupModule."";
                        }
                        
                        // Output Feedback
                        if ($remoteCommand) {
                            echo '<div class="dmr-alert dmr-alert-success"><strong>Success:</strong> '.$xlxLinkToHost.'<br>Re-Initializing... Page reloading...</div>';
                            exec($remoteCommand);
                            unset($_POST);
                            echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
                        } else {
                            echo '<div class="dmr-alert" style="background:red; color:white;"><strong>Error:</strong> Invalid Input. Page reloading...</div>';
                            unset($_POST);
                            echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
                        }

                    } else {
                        // Display XLX Form
            ?>
                <div class="dmr-subheader">XLX Link Manager</div>
                
                <div class="xlx-container">
                    <form action="" method="post">
                        
                        <div class="xlx-form-group">
                            <label class="xlx-label">Select Reflector</label>
                            <select name="dmrMasterHost3Startup" class="dmrMasterHost3Startup xlx-select" style="width:100%;">
                                <?php
                                $configdmrgateway = $_SESSION['DMRGatewayConfigs'];
                                $dmrMasterFile3 = fopen("/usr/local/etc/DMR_Hosts.txt", "r");
                                if (isset($configdmrgateway['XLX Network']['Startup'])) { $testMMDVMdmrMaster3 = $configdmrgateway['XLX Network']['Startup']; }
                                echo '<option value="None" ' . (!isset($configdmrgateway['XLX Network']['Startup']) ? 'selected="selected"' : '') . '>None (Unlink)</option>'."\n";
                                
                                while (!feof($dmrMasterFile3)) {
                                        $dmrMasterLine3 = fgets($dmrMasterFile3);
                                        $dmrMasterHost3 = preg_split('/\s+/', $dmrMasterLine3);
                                        if ((strpos($dmrMasterHost3[0], '#') === FALSE ) && (substr($dmrMasterHost3[0], 0, 3) == "XLX") && ($dmrMasterHost3[0] != '')) {
                                                if ($testMMDVMdmrMaster3 == $dmrMasterHost3[2]) { echo "      <option value=\"$dmrMasterHost3[2],$dmrMasterHost3[3],$dmrMasterHost3[4],$dmrMasterHost3[0]\" selected=\"selected\">$dmrMasterHost3[0]</option>\n"; }
                                                if ('XLX_'.$testMMDVMdmrMaster3 == $dmrMasterHost3[0]) { echo "      <option value=\"".str_replace('XLX_', '', $dmrMasterHost3[0])."\" selected=\"selected\">$dmrMasterHost3[0]</option>\n"; }
                                                else { echo "      <option value=\"".str_replace('XLX_', '', $dmrMasterHost3[0])."\">$dmrMasterHost3[0]</option>\n"; }
                                        }
                                }
                                fclose($dmrMasterFile3);
                                ?>
                            </select>
                        </div>

                        <?php if (isset($configdmrgateway['XLX Network']['TG'])) { ?>
                        <div class="xlx-form-group">
                            <label class="xlx-label">Module</label>
                            <select name="dmrMasterHost3StartupModule" class="ModSel xlx-select" style="width:100%;">
                                <?php
                                if ((isset($configdmrgateway['XLX Network']['Module'])) && ($configdmrgateway['XLX Network']['Module'] != "@")) {
                                    echo '<option value="'.$configdmrgateway['XLX Network']['Module'].'" selected="selected">'.$configdmrgateway['XLX Network']['Module'].'</option>'."\n";
                                }
                                foreach (range('A', 'Z') as $char) {
                                    echo '<option value="'.$char.'">'.$char.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <?php } ?>

                        <div class="xlx-form-group">
                            <label class="xlx-label">Current Status</label>
                            <?php
                            if(getDMRnetStatus("xlx") == "disabled") {
                                $target = "<span style='color:orange'>User Disabled</span>";
                            } else {
                                $target = exec('cd /var/log/pi-star; /usr/local/bin/RemoteCommand ' .$_SESSION['DMRGatewayConfigs']['Remote Control']['Port']. ' hosts | sed "s/ /\n/g" | egrep -oh "XLX(.*)" | sed "s/\"//g" | sed "s/_/ Module /g"'); 
                            }
                            ?>
                            <div class="xlx-status-box">
                                <span class="xlx-status-text CheckLink">
                                    <?php echo !empty($target) ? $target : "Unlinked"; ?>
                                </span>
                            </div>
                        </div>

                        <input type="hidden" name="Link" value="LINK" />
                        <input type="submit" name="xlxMgrSubmit" class="dmr-btn" value="Request Change" />
                    </form>

                    <div class="dmr-help-text" style="margin-top:20px; padding-bottom:0; border:0;">
                        Instantly change XLX reflectors and modules.<br/>
                        <b><a href="https://w0chp.radio/xlx-reflectors/" target="_blank">List of XLX Reflectors</a></b>
                    </div>
                </div>

                <script>
                $(document).ready(function(){
                    setInterval(function(){
                        $(".CheckLink").load(window.location.href + " .CheckLink" );
                    }, 3000);
                });
                </script>
            <?php } 
            } ?>
        </div>
    </div>
</div>
