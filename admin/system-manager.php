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

// Import Theme Variables
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php'; 

$fw_enable    = "sudo /usr/local/sbin/wpsd-system-manager -efw";
$fw_disable   = "sudo /usr/local/sbin/wpsd-system-manager -dfw";

$rfc_enable   = "sudo /usr/local/sbin/wpsd-system-manager -erf";
$rfc_disable  = "sudo /usr/local/sbin/wpsd-system-manager -drf";

$wds_enable   = "sudo /usr/local/sbin/wpsd-system-manager -esw";
$wds_disable  = "sudo /usr/local/sbin/wpsd-system-manager -dsw";
?>

<style>
    /* Main Layout */
    .admin-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .admin-card {
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
    #admin-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    /* Style for the Moved Buttons */
    #admin-nav-placeholder input[type="button"],
    #admin-nav-placeholder button {
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
    #admin-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    /* Cohesion Fixes for Legacy Elements */
    .contentwide, .content { text-align: center; }

    /* Header */
    .admin-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    }

    /* Body & Forms */
    .admin-body {
        padding: 25px;
    }

    /* Toggle Switch */
    .admin-toggle-group {
        display: flex;
        background: <?php echo $tableRowEvenBg; ?>;
        border-radius: 4px;
        padding: 4px;
        margin-bottom: 20px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .admin-toggle-option {
        flex: 1;
        text-align: center;
        position: relative;
    }
    .admin-toggle-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .admin-toggle-option label {
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
    .admin-toggle-option input[type="radio"]:checked + label {
        background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>;
        opacity: 1;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Inputs */
    .admin-select-wrapper { margin-bottom: 25px; text-align: left; }
    .admin-label {
        display: block;
        margin-bottom: 8px;
        color: <?php echo $textContent; ?>;
        font-size: 0.85em;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.8;
    }
    .admin-select {
        width: 100%;
        padding: 12px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 4px;
        font-size: 1.1rem;
        color: <?php echo $textContent; ?> !important;
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        font-family: 'Source Sans Pro', sans-serif !important;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='<?php echo urlencode($textContent); ?>' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
        cursor: pointer;
    }

    /* Main Action Button */
    .admin-btn {
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
    .admin-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?>;
        transform: translateY(-2px);
    }

    /* Alerts */
    .admin-alert {
        padding: 20px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .admin-alert-error {
        background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
        color: <?php echo $textModeCellInactiveColor; ?>;
        border-color: <?php echo $textModeCellInactiveColor; ?>;
    }
    .admin-alert-success {
        background-color: <?php echo $backgroundModeCellActiveColor; ?>;
        color: <?php echo $textModeCellActiveColor; ?>;
        border-color: <?php echo $textModeCellActiveColor; ?>;
    }

    .admin-help-text {
        margin-top: 25px;
        font-size: 0.85em;
        opacity: 0.7;
        line-height: 1.5;
        border-top: 1px solid <?php echo $tableBorderColor; ?>;
        padding-top: 15px;
        text-align: left;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('admin-nav-placeholder');
    // Select all inputs of type button that are likely the legacy nav
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    
    legacyButtons.forEach(btn => {
        // Exclude our own internal buttons (if any match)
        if (!btn.closest('.admin-card')) {
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
// take action based on form submission
if (!empty($_POST["submit_service"]) && empty($_POST["service_sel"])) { //handler for nothing selected
    ?>
    <div class="admin-wrapper">
        <div class="admin-card">
            <div id="admin-nav-placeholder"></div>
            <div class="admin-header">System Manager</div>
            <div class="admin-body">
                <div class="admin-alert admin-alert-error">
                    <strong>Error:</strong> No Service Selected.<br />Page Reloading...
                </div>
            </div>
        </div>
    </div>
    <?php
    unset($_POST);
    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';

} elseif (!empty($_POST['submit_service']) && escapeshellcmd($_POST['service_action'] == "Disable")) {
    $mode = escapeshellcmd($_POST['service_sel']); 
    if ($mode == "RF Remote Control" && (getPSRState() == 0) || $mode == "Firewall" && (getFWstate() == 0) || $mode == "WPSD Services Watchdog" && (getPSWState() == 0)) { //check if already disabled
        ?>
        <div class="admin-wrapper">
            <div class="admin-card">
                <div id="admin-nav-placeholder"></div>
                <div class="admin-header">System Manager</div>
                <div class="admin-body">
                    <div class="admin-alert admin-alert-error">
                        <strong><?php echo $mode; ?></strong> is already disabled!<br />Did you mean to "enable" it?<br />Page Reloading...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    } else { 
        if ($mode == "Firewall") {
            system($fw_disable);
        } elseif ($mode == "RF Remote Control") {
            system($rfc_disable);
        } elseif ($mode == "WPSD Services Watchdog") {
            system($wds_disable);
        } else {
            die;
        }
        ?>
        <div class="admin-wrapper">
            <div class="admin-card">
                <div id="admin-nav-placeholder"></div>
                <div class="admin-header">System Manager</div>
                <div class="admin-body">
                    <div class="admin-alert admin-alert-success">
                        <strong>Disabled:</strong> <?php echo $mode; ?><br />Page Reloading...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    }

} elseif (!empty($_POST['submit_service']) && escapeshellcmd($_POST['service_action'] == "Enable")) {
    $mode = escapeshellcmd($_POST['service_sel']);
    if ($mode == "RF Remote Control" && (getPSRState() == 1) || $mode == "Firewall" && (getFWstate() == 1) || $mode == "WPSD Services Watchdog" && (getPSWState() == 1)) {
        ?>
        <div class="admin-wrapper">
            <div class="admin-card">
                <div id="admin-nav-placeholder"></div>
                <div class="admin-header">System Manager</div>
                <div class="admin-body">
                    <div class="admin-alert admin-alert-error">
                        <strong><?php echo $mode; ?></strong> is already enabled!<br />Did you mean to "disable" it?<br />Page Reloading...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    } else { 
        if ($mode == "Firewall") {
            system($fw_enable);
        } elseif ($mode == "RF Remote Control") {
            system($rfc_enable);
        } elseif ($mode == "WPSD Services Watchdog") {
            system($wds_enable);
        } else {
            die;
        }
        ?>
        <div class="admin-wrapper">
            <div class="admin-card">
                <div id="admin-nav-placeholder"></div>
                <div class="admin-header">System Manager</div>
                <div class="admin-body">
                    <div class="admin-alert admin-alert-success">
                        <strong>Enabled:</strong> <?php echo $mode; ?><br />Page Reloading...
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
    <div class="admin-wrapper">
        <div class="admin-card">
            
            <div id="admin-nav-placeholder"></div>

            <div class="admin-header">System Manager</div>
            
            <div class="admin-body">
                <form id="system-action-form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?func=sys_man" method="post">
                    
                    <div class="admin-select-wrapper" style="margin-bottom: 10px;">
                        <label class="admin-label">Desired Action</label>
                    </div>
                    <div class="admin-toggle-group">
                        <div class="admin-toggle-option">
                            <input name="service_action" id="en-dis-0" value="Disable" type="radio" checked="checked">
                            <label for="en-dis-0">Disable</label>
                        </div>
                        <div class="admin-toggle-option">
                            <input name="service_action" id="en-dis-1" value="Enable" type="radio">
                            <label for="en-dis-1">Enable</label>
                        </div>
                    </div>

                    <div class="admin-select-wrapper">
                        <label class="admin-label" for="service_sel">Select Service</label>
                        <select name="service_sel" id="service_sel" class="admin-select">
                            <option value="" disabled="disabled" selected="selected">Choose a service...</option>
                            <option value="Firewall">Firewall <?php echo (getFWstate() == '0') ? '(Disabled)' : ''; ?></option>
                            <option value="RF Remote Control">RF Remote Control <?php echo (getPSRState() == '0') ? '(Disabled)' : ''; ?></option>
                            <option value="WPSD Services Watchdog">WPSD Services Watchdog <?php echo (getPSWState() == '0') ? '(Disabled)' : ''; ?></option>
                        </select>
                    </div>

                    <input type="hidden" name="func" value="sys_man">
                    <input type="submit" class="admin-btn" name="submit_service" value="Execute Action" title="Submit">
                </form>

                <div class="admin-help-text">
                    <p>Instantly disable or enable system services. This function is intended for advanced users.</p>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>
