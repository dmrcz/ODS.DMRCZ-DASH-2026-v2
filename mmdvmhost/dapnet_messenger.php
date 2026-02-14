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

// Most of the work here contributed by geeks4hire (Ben Horan)

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php';           // Theme Variables

?>

<style>
    /* Modern DAPNET Messenger CSS */
    .dapnet-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .dapnet-card {
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 8px;
        padding: 0;
        width: 100%;
        max-width: 800px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        color: <?php echo $textContent; ?>;
        overflow: hidden;
        position: relative;
    }

    /* Navigation Toolbar */
    #dapnet-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    #dapnet-nav-placeholder input[type="button"],
    #dapnet-nav-placeholder button {
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
    #dapnet-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    /* Header */
    .dapnet-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        text-align: center;
    }

    /* Body & Layout */
    .dapnet-body {
        padding: 25px;
    }
    .dapnet-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }
    @media (max-width: 700px) {
        .dapnet-grid { grid-template-columns: 1fr; }
    }

    .dapnet-form-group {
        margin-bottom: 20px;
    }
    .dapnet-label {
        display: block;
        margin-bottom: 8px;
        color: <?php echo $textContent; ?>;
        font-size: 0.9em;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.8;
    }
    
    .dapnet-input, .dapnet-textarea {
        width: 100%;
        padding: 12px;
        background-color: <?php echo $tableRowEvenBg; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 4px;
        color: <?php echo $textContent; ?>;
        font-family: 'Inconsolata', monospace;
        font-size: 1rem;
    }
    .dapnet-textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* Action Button */
    .dapnet-btn {
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
        margin-top: 10px;
    }
    .dapnet-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?>;
        transform: translateY(-2px);
    }

    /* Alerts */
    .dapnet-alert {
        padding: 20px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .dapnet-alert-error {
        background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
        color: <?php echo $textModeCellInactiveColor; ?>;
    }
    .dapnet-alert-success {
        background-color: <?php echo $backgroundModeCellActiveColor; ?>;
        color: <?php echo $textModeCellActiveColor; ?>;
    }
    .dapnet-alert a { color: inherit; text-decoration: underline; font-weight: bold; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('dapnet-nav-placeholder');
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    legacyButtons.forEach(btn => {
        if (!btn.closest('.dapnet-card')) {
            navPlaceholder.appendChild(btn);
        }
    });
    if (navPlaceholder.children.length === 0) { navPlaceholder.style.display = 'none'; }
});
</script>

<?php
if (isset($_SESSION['DAPNETAPIKeyConfigs']['DAPNETAPI']['USER']) && (empty($_SESSION['DAPNETAPIKeyConfigs']['DAPNETAPI']['USER']) != TRUE)) {
    // Max length for the textarea
    $maxlength = (5 * (80 - (strlen($_SESSION['DAPNETAPIKeyConfigs']['DAPNETAPI']['USER']) + 2 /* 'CALLSIGN: ' prefix */ + 4 /* 'x/n ' count */)));
    $dapnetTrxAreas = (isset($_SESSION['DAPNETAPIKeyConfigs']['DAPNETAPI']['TRXAREA']) && (! empty($_SESSION['DAPNETAPIKeyConfigs']['DAPNETAPI']['TRXAREA'])) ? $_SESSION['DAPNETAPIKeyConfigs']['DAPNETAPI']['TRXAREA'] : "");

    // Data has been posted for this page (POST)
    if ((empty($_POST) != TRUE) && (isset($_POST['dapSubmit']) && (empty($_POST['dapSubmit']) != TRUE)) &&
        (isset($_POST['dapToCallsign']) && (empty($_POST['dapToCallsign']) != TRUE)) && (isset($_POST['dapMsgContent']) && (empty($_POST['dapMsgContent']) != TRUE))) {
	
        // A little bit of cleaning
        $dapnetTo = preg_replace('/[^,:space:[:alnum:]]/', "", trim($_POST['dapToCallsign']));
        while (preg_match('/,,/', $dapnetTo)) { $dapnetTo = preg_replace('/,,/', ",", $dapnetTo); }
        $dapnetTo = rtrim($dapnetTo, ",");
	
        $filteredChars = array('\''=>'\\\'', '"'=>'\\\\\\"');
        $dapnetContent = strtr(str_replace(array("\r\n", "\n", "\r"), "", iconv('UTF-8','ASCII//TRANSLIT', $_POST['dapMsgContent'])), $filteredChars);
	
        // TRX AREA
        $dapnetTrx=((isset($_POST['dapToTrxArea']) && (empty($_POST['dapToTrxArea']) != TRUE)) ? preg_replace('/[^,:space:[:alnum:]-]/', "", trim(strtolower(trim($_POST['dapToTrxArea'])))) : "");
        while (preg_match('/,,/', $dapnetTrx)) { $dapnetTrx = preg_replace('/,,/', ",", $dapnetTrx); }
        while (preg_match('/--/', $dapnetTrx)) { $dapnetTrx = preg_replace('/--/', "-", $dapnetTrx); }
        $dapnetTrx = rtrim($dapnetTrx, ",");
	
        if (strlen($dapnetTrx) > 0 && (strcmp($dapnetTrx, $_SESSION['DAPNETAPIKeyConfigs']['DAPNETAPI']['TRXAREA']) != 0)) {
            $dapnetTrx = 'DAPNET_TRXAREA='.escapeshellarg($dapnetTrx);
        } else {
            $dapnetTrx = "";
        }
	
        // Build command line
        $dapnetCmd = 'sudo '.$dapnetTrx.' /usr/local/sbin/wpsd-dapnetapi '.escapeshellarg($dapnetTo).' '.escapeshellarg($dapnetContent).' nohost 2>&1';
        
        unset($dummy);
        $resultapi = exec($dapnetCmd, $dummy, $retValue);
        
        // Output Feedback
        ?>
        <div class="dapnet-wrapper">
            <div class="dapnet-card">
                <div id="dapnet-nav-placeholder"></div>
                <div class="dapnet-header">DAPNET Messenger</div>
                <div class="dapnet-body">
                    <div class="dapnet-alert dapnet-alert-success">
                        <strong>Command Output:</strong><br>
                        <?php print $resultapi; ?><br><br>
                        Page reloading...
                    </div>
                </div>
            </div>
        </div>
        <?php
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
	
    } else {
        // Main Form
        ?>
        <div class="dapnet-wrapper">
            <div class="dapnet-card">
                <div id="dapnet-nav-placeholder"></div>
                <div class="dapnet-header">DAPNET Messenger</div>
                <div class="dapnet-body">
                    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?func=pocsag_man" method="post">
                        <div class="dapnet-grid">
                            <div>
                                <div class="dapnet-form-group">
                                    <label class="dapnet-label">To Callsign(s)</label>
                                    <input type="text" name="dapToCallsign" class="dapnet-input" maxlength="70" placeholder="Callsign1, Callsign2..." required />
                                </div>
                                <div class="dapnet-form-group">
                                    <label class="dapnet-label">T/RX Group(s)</label>
                                    <input type="text" name="dapToTrxArea" class="dapnet-input" maxlength="50" value="<?php echo $dapnetTrxAreas; ?>" placeholder="Transmitter Groups..." />
                                </div>
                            </div>
                            
                            <div>
                                <div class="dapnet-form-group">
                                    <label class="dapnet-label">Message (Max <?php echo $maxlength; ?> chars)</label>
                                    <textarea name="dapMsgContent" class="dapnet-textarea" maxlength="<?php echo $maxlength; ?>" required></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <input type="submit" value="Send Message" name="dapSubmit" class="dapnet-btn" />
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    // Disabled State
    ?>
    <div class="dapnet-wrapper">
        <div class="dapnet-card">
            <div id="dapnet-nav-placeholder"></div>
            <div class="dapnet-header">DAPNET Messenger</div>
            <div class="dapnet-body">
                <div class="dapnet-alert dapnet-alert-error">
                    <strong>Feature Disabled</strong><br>
                    DAPNET API configuration is incomplete.<br>
                    Setup your <a href='/admin/advanced/edit_dapnetapi.php'>DAPNET API information</a> to use this feature.
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
