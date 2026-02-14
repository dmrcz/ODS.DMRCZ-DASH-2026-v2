<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';	  // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';	// MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	// Translation Code
    checkSessionValidity();
}

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';	  // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';	// MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	// Translation Code

// Import Theme Variables
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php'; 

$mode_cmd = '/usr/local/sbin/wpsd-mode-manager';

$mmdvmConfigFile = '/etc/mmdvmhost';
$configmmdvm = parse_ini_file($mmdvmConfigFile, true);
$aprsConfigFile = '/etc/aprsgateway';
$configaprsgw = parse_ini_file($aprsConfigFile, true);

$is_paused = glob('/etc/*_paused');
$repl_str = array('/\/etc\//', '/_paused/');
$paused_modes = preg_replace($repl_str, '', $is_paused);

function manageGateways($action,$service) {
    $service = strtolower($service);

    if ($service == "pocsag") {
	$service = "dapnet";
    }
    if ($service == "dstar" || $service == "d-star") {
	$service = "ircddb";
    }

    exec("sudo systemctl $action $service"."gateway.timer");
    exec("sudo systemctl $action $service"."gateway.service");

    $is_paused = glob('/etc/*_paused');
    if (empty($is_paused) == TRUE) {
	exec("sudo systemctl $action pistar-watchdog.timer");
	exec("sudo systemctl $action pistar-watchdog.service");
    }
}

$DSTAR  = ($configmmdvm['D-Star']['Enable']);
$DMR    = ($configmmdvm['DMR']['Enable']);
$YSF    = ($configmmdvm['System Fusion']['Enable']);
$P25    = ($configmmdvm['P25']['Enable']);
$NXDN   = ($configmmdvm['NXDN']['Enable']);
$AX25   = ($configmmdvm['AX.25']['Enable']);
$POCSAG = ($configmmdvm['POCSAG']['Enable']);
$APRS   = ($configaprsgw['Enabled']['Enabled']);

?>

<style>
    /* Main Layout */
    .imm-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .imm-card {
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 8px;
        padding: 0;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        text-align: center;
        color: <?php echo $textContent; ?>;
        overflow: hidden;
        position: relative;
    }

    /* Navigation Toolbar (Where we move the old buttons) */
    #imm-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    /* Style for the Moved Buttons */
    #imm-nav-placeholder input[type="button"],
    #imm-nav-placeholder button {
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
        margin: 0 !important; /* Reset legacy margins */
        box-shadow: none;
    }
    #imm-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    /* Header */
    .imm-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    }

    /* Body & Forms */
    .imm-body {
        padding: 25px;
    }

    /* Toggle Switch */
    .imm-toggle-group {
        display: flex;
        background: <?php echo $tableRowEvenBg; ?>;
        border-radius: 4px;
        padding: 4px;
        margin-bottom: 20px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .imm-toggle-option {
        flex: 1;
        text-align: center;
        position: relative;
    }
    .imm-toggle-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .imm-toggle-option label {
        display: block;
        padding: 12px;
        border-radius: 3px;
        cursor: pointer;
        color: <?php echo $textContent; ?>;
        font-weight: 600;
        margin: 0;
        font-size: 1rem;
        opacity: 0.6;
        transition: all 0.2s;
    }
    .imm-toggle-option input[type="radio"]:checked + label {
        background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>;
        opacity: 1;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Inputs */
    .imm-select-wrapper { margin-bottom: 25px; text-align: left; }
    .imm-label {
        display: block;
        margin-bottom: 8px;
        color: <?php echo $textContent; ?>;
        font-size: 0.85em;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.8;
    }
    .imm-select {
        width: 100%;
        padding: 12px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        color: <?php echo $textContent; ?> !important;
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        font-family: 'Source Sans Pro', sans-serif !important;
        border-radius: 4px;
        font-size: 1.1rem;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='<?php echo urlencode($textContent); ?>' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
        cursor: pointer;
    }

    /* Main Action Button */
    .imm-btn {
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
    .imm-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?>;
        transform: translateY(-2px);
    }

    /* Alerts */
    .imm-alert {
        padding: 20px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .imm-alert-error {
        background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
        color: <?php echo $textModeCellInactiveColor; ?>;
        border-color: <?php echo $textModeCellInactiveColor; ?>;
    }
    .imm-alert-success {
        background-color: <?php echo $backgroundModeCellActiveColor; ?>;
        color: <?php echo $textModeCellActiveColor; ?>;
        border-color: <?php echo $textModeCellActiveColor; ?>;
    }

    /* Status Bar */
    .imm-status-bar {
        background-color: <?php echo $backgroundModeCellPausedColor; ?>;
        color: <?php echo $textModeCellActiveColor; ?>;
        padding: 15px 20px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .imm-resume-btn {
        padding: 8px 16px;
        font-weight: bold;
        text-transform: uppercase;
        background: rgba(0,0,0,0.25);
        color: inherit;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 4px;
        cursor: pointer;
    }
    .imm-resume-btn:hover { background: rgba(0,0,0,0.4); }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('imm-nav-placeholder');
    
    // Select all inputs of type button that are likely the legacy nav
    // We look for them in the parent container
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    
    legacyButtons.forEach(btn => {
        // Exclude our own internal buttons
        if (!btn.closest('.imm-card')) {
            navPlaceholder.appendChild(btn);
        }
    });

    // If no buttons found to move, hide the placeholder
    if (navPlaceholder.children.length === 0) {
        navPlaceholder.style.display = 'none';
    }
});
</script>

<?php

if (!empty($_POST["submit_mode"]) && empty($_POST["mode_sel"])) {
    ?>
    <div class="imm-wrapper">
        <div class="imm-card">
            <div id="imm-nav-placeholder"></div>
            <div class="imm-header">Instant Mode Manager</div>
            <div class="imm-body">
                <div class="imm-alert imm-alert-error">
                    <strong>Error:</strong> No Mode Selected.<br />Page Reloading...
                </div>
            </div>
        </div>
    </div>
    <?php
    unset($_POST);
    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';

} elseif (!empty($_POST['submit_mode']) && escapeshellcmd($_POST['mode_action'] == "Pause")) {
    $mode = escapeshellcmd($_POST['mode_sel']); 
    if (isPaused($mode)) { 
        ?>
        <div class="imm-wrapper">
            <div class="imm-card">
                 <div id="imm-nav-placeholder"></div>
                <div class="imm-header">Instant Mode Manager</div>
                <div class="imm-body">
                    <div class="imm-alert imm-alert-error">
                        <strong><?php echo $mode; ?></strong> is already paused.<br />Page Reloading...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    } else { 
        manageGateways("stop", $mode);
        exec("sudo $mode_cmd $mode Disable"); 
        ?>
        <div class="imm-wrapper">
            <div class="imm-card">
                 <div id="imm-nav-placeholder"></div>
                <div class="imm-header">Instant Mode Manager</div>
                <div class="imm-body">
                    <div class="imm-alert imm-alert-success">
                        <strong>Paused:</strong> <?php echo $mode; ?><br />Services are stopping...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    }

} elseif (!empty($_POST['submit_mode']) && escapeshellcmd($_POST['mode_action'] == "Resume")) {
    $mode = escapeshellcmd($_POST['mode_sel']); 
    if (!isPaused($mode)) { 
        ?>
        <div class="imm-wrapper">
            <div class="imm-card">
                 <div id="imm-nav-placeholder"></div>
                <div class="imm-header">Instant Mode Manager</div>
                <div class="imm-body">
                    <div class="imm-alert imm-alert-error">
                        <strong><?php echo $mode; ?></strong> is already running.<br />Page Reloading...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    } else { 
        manageGateways("start", $mode);
        exec("sudo $mode_cmd $mode Enable"); 
        ?>
        <div class="imm-wrapper">
            <div class="imm-card">
                 <div id="imm-nav-placeholder"></div>
                <div class="imm-header">Instant Mode Manager</div>
                <div class="imm-body">
                    <div class="imm-alert imm-alert-success">
                        <strong>Resumed:</strong> <?php echo $mode; ?><br />Services are starting...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    }
} else {
    //
    // MAIN VIEW
    //
?>
    <div class="imm-wrapper">
        <div class="imm-card">
            
            <div id="imm-nav-placeholder"></div>

            <div class="imm-header">Instant Mode Manager</div>
            
            <?php if(isset($_GET['all_resumed'])) { ?>
                <div class="imm-alert imm-alert-success" style="margin: 0; border:0; border-radius:0; border-bottom: 1px solid <?php echo $tableBorderColor; ?>;">
                    <strong>Success:</strong> All Modes Resumed!
                </div>
            <?php } ?>

            <?php if (!empty($is_paused)) { ?>
                <div class="imm-status-bar">
                    <div class="imm-status-info" style="text-align:left;">
                        <h4 style="margin:0; font-size:0.9rem; text-transform:uppercase;">System Status: Paused</h4>
                        <p style="margin:0; font-family:'Inconsolata',monospace; font-weight:bold;"><?php echo implode(', ', $paused_modes); ?></p>
                    </div>
                    <form method="post" action="/admin/.resume_all_modes.php?imm" style="margin:0;">
                        <input type="hidden" name="paused_modes" value="<?php echo implode(',', $paused_modes); ?>">
                        <input type="submit" name="unpause_modes" value="Resume All" class="imm-resume-btn">
                    </form>
                </div>
            <?php } ?>

            <div class="imm-body">
                <form id="action-form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?func=mode_man" method="post">
                    
                    <div class="imm-select-wrapper" style="margin-bottom: 10px;">
                        <label class="imm-label">Desired Action</label>
                    </div>
                    <div class="imm-toggle-group">
                        <div class="imm-toggle-option">
                            <input name="mode_action" id="pause-res-0" value="Pause" type="radio" checked="checked">
                            <label for="pause-res-0">Pause</label>
                        </div>
                        <div class="imm-toggle-option">
                            <input name="mode_action" id="pause-res-1" value="Resume" type="radio">
                            <label for="pause-res-1">Resume</label>
                        </div>
                    </div>

                    <div class="imm-select-wrapper">
                        <label class="imm-label" for="mode_sel">Select Mode</label>
                        <select name="mode_sel" id="mode_sel" class="imm-select">
                            <option value="" disabled="disabled" selected="selected">Choose a mode...</option>
                            <option value="DMR" <?php echo (($DMR == '0' && !isPaused("DMR")) ? 'disabled="disabled"' : ''); ?>>DMR <?php echo ($DMR == '0') ? '(Disabled)' : ''; ?></option>
                            <option value="YSF" <?php echo (($YSF == '0' && !isPaused("YSF")) ? 'disabled="disabled"' : ''); ?>>YSF <?php echo ($YSF == '0') ? '(Disabled)' : ''; ?></option>
                            <option value="D-Star" <?php echo (($DSTAR == '0' && !isPaused("D-Star")) ? 'disabled="disabled"' : ''); ?>>D-Star <?php echo ($DSTAR == '0') ? '(Disabled)' : ''; ?></option>
                            <?php if (isDVmegaCast() != 1) { ?>
                                <option value="P25" <?php echo (($P25 == '0' && !isPaused("P25")) ? 'disabled="disabled"' : ''); ?>>P25 <?php echo ($P25 == '0') ? '(Disabled)' : ''; ?></option>
                                <option value="NXDN" <?php echo (($NXDN == '0' && !isPaused("NXDN")) ? 'disabled="disabled"' : ''); ?>>NXDN <?php echo ($NXDN == '0') ? '(Disabled)' : ''; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <input type="hidden" name="func" value="mode_man">
                    <input type="submit" class="imm-btn" name="submit_mode" value="Execute Action" title="Submit">
                </form>
            </div>
        </div>
    </div>
<?php
}
?>
