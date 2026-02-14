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

// Import Theme Variables
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php'; 

// ONLY RUN IF IN ADMIN CONTEXT
if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
?>

<style>
    /* Main Layout */
    .dstar-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .dstar-card {
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
    #dstar-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    #dstar-nav-placeholder input[type="button"],
    #dstar-nav-placeholder button {
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
    #dstar-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    /* Header */
    .dstar-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        text-align: center;
    }

    /* Form Body */
    .dstar-body {
        padding: 25px;
    }

    /* Form Groups */
    .dstar-form-group {
        margin-bottom: 20px;
        text-align: left;
    }
    .dstar-label {
        display: block;
        margin-bottom: 8px;
        color: <?php echo $textContent; ?>;
        font-size: 0.85em;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.8;
    }

    /* Styled Selects & Inputs */
    .dstar-select, .dstar-input {
        width: 100%;
        padding: 12px;
        background-color: <?php echo $tableRowEvenBg; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 4px;
        color: <?php echo $textContent; ?>;
        font-size: 1.1rem;
        font-family: 'Inconsolata', monospace;
    }
    
    /* Reflector Input Group (Flex) */
    .dstar-input-row {
        display: flex;
        gap: 10px;
    }
    .dstar-input-main { flex: 3; }
    .dstar-input-suffix { flex: 1; min-width: 80px; }

    /* Segmented Toggle */
    .dstar-toggle-group {
        display: flex;
        background: <?php echo $tableRowEvenBg; ?>;
        border-radius: 4px;
        padding: 3px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .dstar-toggle-option { flex: 1; text-align: center; position: relative; }
    .dstar-toggle-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .dstar-toggle-option label {
        display: block; padding: 10px; border-radius: 3px; cursor: pointer;
        color: <?php echo $textContent; ?>; font-weight: 600; font-size: 0.95rem; opacity: 0.6;
        transition: all 0.2s;
        margin: 0;
    }
    .dstar-toggle-option input[type="radio"]:checked + label {
        background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>;
        opacity: 1;
    }

    /* Action Button */
    .dstar-btn {
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
    .dstar-btn:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?>;
        transform: translateY(-2px);
    }

    /* Alerts */
    .dstar-alert {
        padding: 20px;
        border-radius: 4px;
        margin: 20px;
        text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        background-color: <?php echo $backgroundModeCellActiveColor; ?>;
        color: <?php echo $textModeCellActiveColor; ?>;
    }
    .dstar-alert-error {
        background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
        color: <?php echo $textModeCellInactiveColor; ?>;
    }

    /* --- SELECT2 DARK THEME OVERRIDES --- */
    /* Container (The input box) */
    .dstar-card .select2-container .select2-selection--single {
        background-color: <?php echo $tableRowEvenBg; ?> !important;
        border: 1px solid <?php echo $tableBorderColor; ?> !important;
        height: 45px !important;
        padding: 8px 0 !important;
        border-radius: 4px !important;
    }
    .dstar-card .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: <?php echo $textContent; ?> !important;
        line-height: 28px !important;
        background-color: transparent !important;
        font-family: 'Inconsolata', monospace !important;
        font-size: 1.1rem !important;
        padding-left: 10px !important;
    }
    .dstar-card .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px !important; right: 5px !important;
    }
    .dstar-card .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: <?php echo $textContent; ?> transparent transparent transparent !important;
    }

    /* Dropdown (Global) */
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
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('dstar-nav-placeholder');
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    legacyButtons.forEach(btn => {
        if (!btn.closest('.dstar-card')) {
            navPlaceholder.appendChild(btn);
        }
    });
    if (navPlaceholder.children.length === 0) { navPlaceholder.style.display = 'none'; }
});
</script>

<?php
    if (!empty($_POST) && isset($_POST["dstrMgrSubmit"])) {
        // --- POST PROCESSING & FEEDBACK VIEW ---
        
        // Input Validation Logic
        if (preg_match('/[^A-Z]/',$_POST["Link"])) { unset ($_POST["Link"]); }
        if ($_POST["Link"] == "LINK") {
            if (preg_match('/[^A-Za-z0-9 ]/',$_POST["RefName"])) { unset ($_POST["RefName"]); }
            if (preg_match('/[^A-Z]/',$_POST["Letter"])) { unset ($_POST["Letter"]); }
            if (preg_match('/[^A-Z0-9 ]/',$_POST["Module"])) { unset ($_POST["Module"]); }
        }
        if ($_POST["Link"] == "UNLINK") {
            if (preg_match('/[^A-Z0-9 ]/',$_POST["Module"])) { unset ($_POST["Module"]); }
        }
        
        ?>
        <div class="dstar-wrapper">
            <div class="dstar-card">
                <div id="dstar-nav-placeholder"></div>
                <div class="dstar-header">D-Star Link Manager</div>
                <div class="dstar-body">
        <?php

        if (empty($_POST["RefName"]) || empty($_POST["Letter"]) || empty($_POST["Module"])) {
            echo '<div class="dstar-alert dstar-alert-error"><strong>Error:</strong> Invalid Input. Please try again.<br>Page reloading...</div>';
        } else {
            // Processing
            if (strlen($_POST["RefName"]) != 7) {
                $targetRef = str_pad($_POST["RefName"], 7, " ");
            } else {
                $targetRef = $_POST["RefName"];
            }
            $targetRef = $targetRef.$_POST["Letter"];
            $targetRef = strtoupper($targetRef);
            $module = $_POST["Module"];
            
            if (strlen($module) != 8) {
                $moduleFixedCs = strlen($module) - 1;
                $moduleFixedBand = substr($module, -1);
                $moduleFixedCallPad = str_pad(substr($module, 0, $moduleFixedCs), 7);
                $module = $moduleFixedCallPad.$moduleFixedBand;
            }
            
            $unlinkCommand = "sudo remotecontrold \"".$module."\" unlink";
            $linkCommand = "sudo remotecontrold \"".$module."\" link never \"".$targetRef."\"";
            
            // Execute & Output
            if ($module != $targetRef && $_POST["Link"] == "LINK") {
                echo '<div class="dstar-alert"><strong>Command Sent</strong><br>';
                echo system($linkCommand);
                echo '<br><br>Page reloading...</div>';
            }
            if ($module == $targetRef && $_POST["Link"] == "LINK") {
                echo '<div class="dstar-alert dstar-alert-error"><strong>Error:</strong> Cannot link to myself.<br>Aborting link request!<br><br>Page reloading...</div>';
            }
            if ($_POST["Link"] == "UNLINK") {
                echo '<div class="dstar-alert"><strong>Command Sent</strong><br>';
                echo system($unlinkCommand);
                echo '<br><br>Page reloading...</div>';
            }
        }
        
        echo '</div></div></div>';
        unset($_POST);
        echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';

    } else {
        // --- MAIN FORM VIEW ---
?>
        <div class="dstar-wrapper">
            <div class="dstar-card">
                <div id="dstar-nav-placeholder"></div>

                <div class="dstar-header"><?php echo __( 'D-Star Link Manager' );?></div>

                <div class="dstar-body">
                    <form action="/admin/?func=ds_man" method="post">
                        
                        <div class="dstar-form-group">
                            <label class="dstar-label">Radio Module</label>
                            <select name="Module" class="dstar-select">
                                <?php
                                $ci = 0;
                                for($i = 1;$i < 5; $i++) {
                                    $param="repeaterBand" . $i;
                                    if((isset($_SESSION['ircDDBConfigs'][$param])) && strlen($_SESSION['ircDDBConfigs'][$param]) == 1) {
                                        $ci++;
                                        if($ci > 1) { $ci = 0; }
                                        $module = $_SESSION['ircDDBConfigs'][$param];
                                        $rcall = sprintf("%-7.7s%-1.1s",$MYCALL,$module);
                                        $param="repeaterCall" . $i;
                                        if(isset($_SESSION['ircDDBConfigs'][$param])) {
                                            $rptrcall=sprintf("%-7.7s%-1.1s",$_SESSION['ircDDBConfigs'][$param],$module);
                                        } else {
                                            $rptrcall = $rcall;
                                        }
                                        print "<option>$rptrcall</option>\n";
                                    }
                                } ?>
                            </select>
                        </div>

                        <div class="dstar-form-group">
                            <label class="dstar-label">Target Reflector</label>
                            <div class="dstar-input-row">
                                <div class="dstar-input-main">
                                    <select name="RefName" class="RefName dstar-select"
                                            onchange="if (this.options[this.selectedIndex].value == 'customOption') {
                                                  toggleField(this,this.nextSibling);
                                                  this.selectedIndex='0';
                                                  } " style="width:100%;">
                                        <?php
                                        $dcsFile = fopen("/usr/local/etc/DCS_Hosts.txt", "r");
                                        $dplusFile = fopen("/usr/local/etc/DPlus_Hosts.txt", "r");
                                        $dextraFile = fopen("/usr/local/etc/DExtra_Hosts.txt", "r");
                                        
                                        echo "    <option value=\"".substr($_SESSION['ircDDBConfigs']['reflector1'], 0, 6)."\" selected=\"selected\">".substr($_SESSION['ircDDBConfigs']['reflector1'], 0, 6)."</option>\n";
                        
                                        while (!feof($dcsFile)) {
                                            $dcsLine = fgets($dcsFile);
                                            if (strpos($dcsLine, 'DCS') !== FALSE && strpos($dcsLine, '#') === FALSE) {
                                                echo "	<option value=\"".substr($dcsLine, 0, 6)."\">".substr($dcsLine, 0, 6)."</option>\n";
                                            }
                                            if (strpos($dcsLine, 'XLX') !== FALSE && strpos($dcsLine, '#') === FALSE) {
                                                echo "	<option value=\"".substr($dcsLine, 0, 6)."\">".substr($dcsLine, 0, 6)."</option>\n";
                                            }
                                        }
                                        fclose($dcsFile);
                                        
                                        while (!feof($dplusFile)) {
                                            $dplusLine = fgets($dplusFile);
                                            if (strpos($dplusLine, 'REF') !== FALSE && strpos($dplusLine, '#') === FALSE) {
                                                echo "	<option value=\"".substr($dplusLine, 0, 6)."\">".substr($dplusLine, 0, 6)."</option>\n";
                                            }
                                            if (strpos($dplusLine, 'XRF') !== FALSE && strpos($dplusLine, '#') === FALSE) {
                                                echo "	<option value=\"".substr($dplusLine, 0, 6)."\">".substr($dplusLine, 0, 6)."</option>\n";
                                            }
                                        }
                                        fclose($dplusFile);
                                        
                                        while (!feof($dextraFile)) {
                                            $dextraLine = fgets($dextraFile);
                                            if (strpos($dextraLine, 'XRF') !== FALSE && strpos($dextraLine, '#') === FALSE)
                                                echo "	<option value=\"".substr($dextraLine, 0, 6)."\">".substr($dextraLine, 0, 6)."</option>\n";
                                        }
                                        fclose($dextraFile);
                                        ?>
                                    </select>
                                    <input name="RefName" class="dstar-input" style="display:none;" disabled="disabled" type="text" size="7" maxlength="7" onblur="if(this.value==''){toggleField(this,this.previousSibling);}" placeholder="REF001" />
                                </div>
                                <div class="dstar-input-suffix">
                                    <select name="Letter" class="ModSel dstar-select" style="width:100%;">
                                        <?php echo "  <option value=\"".substr($_SESSION['ircDDBConfigs']['reflector1'], 7)."\" selected=\"selected\">".substr($_SESSION['ircDDBConfigs']['reflector1'], 7)."</option>\n"; ?>
                                        <?php
                                        foreach (range('A', 'Z') as $char) {
                                            echo "<option>$char</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="dstar-form-group">
                            <label class="dstar-label">Action</label>
                            <div class="dstar-toggle-group">
                                <div class="dstar-toggle-option">
                                    <input type="radio" id="link" name="Link" value="LINK">
                                    <label for="link">Link</label>
                                </div>
                                <div class="dstar-toggle-option">
                                    <input type="radio" id="unlink" name="Link" value="UNLINK" checked="checked">
                                    <label for="unlink">Unlink</label>
                                </div>
                            </div>
                        </div>

                        <input type="submit" class="dstar-btn" name="dstrMgrSubmit" value="Execute Action" />
                    </form>
                </div>
            </div>
        </div>
<?php
    } // End Else (Main View)
} // End Admin Check
?>
