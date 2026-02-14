<?php

define('MMDVM_FUNC_DEFS_ONLY', true);  // do not autoexecute logs reading code in mmdvmhost/functions.php

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

include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.BMApi.php';

// Import Theme Variables
include_once $_SERVER['DOCUMENT_ROOT'].'/css/css-base.php'; 

$processUrl = '/admin/bm-manager.php';
$returnUrl = '/admin/?func=bm_man';

$bmApi = BMApi::getInstance();

//==== process actions ====
if (isset($_POST['static-tg-add'])) {
    if ($bmApi->getStatus() == BMApi::STATUS_OK) {
        $slot = $_POST['TS'];

        // get list of groups (any separator allowed)
        preg_match_all("/(\d+)/s", $_POST['TG'], $tgmatches);
        $tgs = $tgmatches[1];

        foreach ($tgs as $tg)
            $bmApi->addFavTG($tg, $slot);

        $bmApi->getFavTGs(); //update from profile if added directly from BM
        $bmApi->saveConfig();
    }

    redirectByHeader($returnUrl);
}

if (isset($_GET['droptg'])) {
    if ($bmApi->getStatus() == BMApi::STATUS_OK) {
        $bmApi->delFavTG($_GET['droptg'], $_GET['slot']);
        $bmApi->saveConfig();
    }

    redirectByHeader($returnUrl);
}

if (isset($_GET['masstg'])) {
    switch ($_GET['masstg']) {
        case 'enable':
            $bmApi->linkAllStatic();
            break;

        case 'disable':
            $bmApi->dropAllStatic();
            break;

        case 'drop':
            $bmApi->dropAllStatic(/*forever=*/true);
            break;
    }

    redirectByHeader($returnUrl);
}
//==========================
?>

<style>
    /* Main Layout */
    .bm-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 20px;
        font-family: 'Source Sans Pro', sans-serif;
    }
    .bm-card {
        background-color: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 8px;
        padding: 0;
        width: 100%;
        max-width: 850px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        color: <?php echo $textContent; ?>;
        overflow: hidden;
        position: relative;
    }

    /* Navigation Toolbar (Legacy Buttons) */
    #bm-nav-placeholder {
        background-color: <?php echo $backgroundBanners; ?>;
        padding: 10px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    #bm-nav-placeholder input[type="button"],
    #bm-nav-placeholder button {
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
    #bm-nav-placeholder input[type="button"]:hover {
        background-color: <?php echo $backgroundNavbarHover; ?>;
        color: <?php echo $textNavbarHover; ?> !important;
        border-color: <?php echo $textNavbarHover; ?>;
    }

    /* Header & Info */
    .bm-header {
        padding: 20px;
        font-weight: 700;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        text-align: center;
    }
    .bm-info-bar {
        background-color: rgba(0,0,0,0.2);
        padding: 10px;
        text-align: center;
        font-size: 0.9rem;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .bm-info-bar a { color: <?php echo $textLinks; ?>; text-decoration: underline; }

    /* Section Headers */
    .bm-section-title {
        padding: 15px 25px;
        background-color: <?php echo $tableRowEvenBg; ?>;
        font-weight: 600;
        font-size: 1.1rem;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        border-top: 1px solid <?php echo $tableBorderColor; ?>;
        margin: 0;
        text-align: left;
    }

    /* Lists (Static & Dynamic Rows) */
    .bm-list-container {
        padding: 0;
        width: 100%;
    }
    .bm-list-header {
        display: flex;
        padding: 10px 25px;
        background-color: rgba(0,0,0,0.1);
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        opacity: 0.8;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .bm-list-row {
        display: flex;
        align-items: center;
        padding: 12px 25px;
        border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
        transition: background-color 0.1s;
    }
    .bm-list-row:last-child { border-bottom: none; }
    .bm-list-row:hover { background-color: rgba(255,255,255,0.03); }

    /* Columns Alignment */
    .bm-col-tg { 
        flex: 2; 
        font-weight: 600; 
        font-size: 1.05rem; 
        text-align: left; 
    }
    .bm-col-name { 
        flex: 3; 
        opacity: 0.8; 
        font-size: 0.95rem; 
        text-align: left; 
    }
    
    /* TS Column */
    .bm-col-ts { 
        flex: 1; 
        text-align: center; 
        font-size: 0.9rem;
    }
    /* Only use Inconsolata for data rows, inherit for header */
    .bm-list-row .bm-col-ts {
        font-family: 'Inconsolata', monospace; 
    }

    .bm-col-action { 
        flex: 1.5; 
        text-align: right; 
        display: flex; 
        justify-content: flex-end; 
        gap: 15px; 
        align-items: center;
    }

    /* Timeout Column */
    .bm-col-timeout { 
        flex: 2; 
        text-align: left; 
        font-size: 0.9rem; 
    }
    /* Only use Inconsolata for data rows, inherit for header */
    .bm-list-row .bm-col-timeout {
        font-family: 'Inconsolata', monospace; 
    }

    /* Tweak TS badges */
    .bm-ts-badge {
        display: inline-block;
        background: rgba(0,0,0,0.2); 
        border-radius: 4px; 
        padding: 2px 6px;
    }

    /* Switches */
    .bm-switch {
        position: relative;
        display: inline-block;
        width: 40px;       /* Reduced */
        height: 20px;      /* Reduced */
        margin-right: 10px;
    }
    .bm-switch input { opacity: 0; width: 0; height: 0; }
    .bm-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
        transition: .4s;
        border-radius: 20px; /* Adjusted */
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .bm-slider:before {
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
    input:checked + .bm-slider { background-color: #2ecc71; border-color: #27ae60; }
    input:checked + .bm-slider:before { transform: translateX(20px); } /* Adjusted */
    /* Add Form Area */
    .bm-add-form {
        padding: 25px;
        background-color: rgba(0,0,0,0.1);
        display: flex;
        flex-wrap: wrap;
        gap: 25px;
        align-items: flex-start;
        border-top: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .bm-input-area { flex: 2; min-width: 300px; }
    .bm-ts-area { flex: 1; min-width: 140px; }
    .bm-submit-area { flex: 1; min-width: 200px; display: flex; flex-direction: column; gap: 10px; }

    .bm-textarea {
        width: 100%;
        padding: 12px;
        background-color: <?php echo $tableRowEvenBg; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        border-radius: 4px;
        color: <?php echo $textContent; ?>;
        font-family: 'Inconsolata', monospace;
        resize: vertical;
        min-height: 80px;
    }

    /* Segmented TS Toggle */
    .bm-toggle-group {
        display: flex;
        background: <?php echo $tableRowEvenBg; ?>;
        border-radius: 4px;
        padding: 3px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .bm-toggle-option { flex: 1; text-align: center; position: relative; }
    .bm-toggle-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .bm-toggle-option label {
        display: block; padding: 8px; border-radius: 3px; cursor: pointer;
        color: <?php echo $textContent; ?>; font-weight: 600; font-size: 0.9rem; opacity: 0.6;
        transition: all 0.2s;
    }
    .bm-toggle-option input[type="radio"]:checked + label {
        background-color: <?php echo $backgroundNavbar; ?>;
        color: <?php echo $textNavbar; ?>;
        opacity: 1;
    }
    .bm-toggle-option input[type="radio"]:disabled + label {
        opacity: 0.2; cursor: not-allowed; text-decoration: line-through;
    }

    /* Buttons */
    .bm-btn {
        width: 100%;
        padding: 10px;
        background-color: #27ae60;
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        transition: 0.2s;
    }
    .bm-btn:hover { background-color: #219150; }

    .bm-btn-drop {
        background-color: #c0392b;
        color: #ffffff !important; /* Force white text */
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .bm-btn-drop:hover { background-color: #a93226; color: #ffffff !important; }

    /* Mass Mgmt Toolbar */
    .bm-mass-mgmt {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .bm-btn-mass {
        background: rgba(255,255,255,0.1);
        color: <?php echo $textContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 0.7rem;
        text-transform: uppercase;
        text-decoration: none;
        font-weight: 600;
        flex-grow: 1;
        text-align: center;
    }
    .bm-btn-mass:hover { background: rgba(255,255,255,0.2); }

    /* Drop QSO Bar */
    .bm-drop-bar {
        padding: 15px;
        background-color: rgba(0,0,0,0.1);
        border-top: 1px solid <?php echo $tableBorderColor; ?>;
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    /* Alerts */
    .bm-alert {
        padding: 20px;
        border-radius: 4px;
        margin: 20px;
        text-align: center;
        border: 1px solid <?php echo $tableBorderColor; ?>;
    }
    .bm-alert-error {
        background-color: <?php echo $backgroundModeCellInactiveColor; ?>;
        color: <?php echo $textModeCellInactiveColor; ?>;
    }

    .bm-hint {
        padding: 15px;
        font-size: 0.85rem;
        opacity: 0.7;
        text-align: center;
        background-color: rgba(0,0,0,0.05);
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const navPlaceholder = document.getElementById('bm-nav-placeholder');
    const legacyButtons = document.querySelectorAll('body input[type="button"]');
    legacyButtons.forEach(btn => {
        // Only move buttons that aren't inside our new card (like the drop buttons)
        if (!btn.closest('.bm-card')) {
            navPlaceholder.appendChild(btn);
        }
    });
    if (navPlaceholder.children.length === 0) { navPlaceholder.style.display = 'none'; }
});
</script>


<?php
if ($bmApi->getStatus() != BMApi::STATUS_OK) {
    $helpBMHtml = "<a href=\"https://news.brandmeister.network/introducing-user-api-keys/\" target=\"new\" alt=\"BM API Keys\">BM API Key Announcement</a>; then <a href=\"/admin/advanced/fulledit_bmapikey.php\">Enter your API Key</a>.";

    $status2error = [
        BMApi::STATUS_BADCONFIG  => "Notice! Bad BrandMeister network configuration.",
        BMApi::STATUS_BMDISABLED => "Notice! BrandMeister network disabled.",
        BMApi::STATUS_NOKEY      => "Notice! No BrandMeister API key defined. $helpBMHtml",
        BMApi::STATUS_LEGACYKEY  => "Notice! Legacy Brandmeister API Key detected. $helpBMHtml",
    ];

    $errorMessage = $status2error[$bmApi->getStatus()] ?? "BM Error";
?>
    <div class="bm-wrapper">
        <div class="bm-card">
            <div id="bm-nav-placeholder"></div>
            <div class="bm-header">BrandMeister Manager</div>
            <div class="bm-alert bm-alert-error">
                <strong><?=$errorMessage?></strong>
            </div>
        </div>
    </div>
<?php
} else { // bm status ok
    $duplexMode = getConfigItem("General", "Duplex", $_SESSION['MMDVMHostConfigs']) == "1";
    $ts1Enabled = getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == "1";
    $ts2Enabled = getConfigItem("DMR Network", "Slot2", $_SESSION['MMDVMHostConfigs']) == "1";
    $ts1Selected = $ts1Enabled;
    $ts2Selected = !$ts1Enabled && $ts2Enabled;
    $profile = $bmApi->getProfile();
?>
    <script type="text/javascript" src="/js/bm-manager.js"></script>

    <div class="bm-wrapper">
        <div class="bm-card">
            <div id="bm-nav-placeholder"></div>
            
            <div class="bm-header">BrandMeister Manager</div>

            <div class="bm-info-bar">
                 ID: <a href="https://brandmeister.network/?page=hotspot&amp;id=<?=$bmApi->dmrID?>" target="_blank"><strong><?=$bmApi->dmrID?></strong></a>
                 &nbsp;&bull;&nbsp; Connected To: <strong><?=$bmApi->dmrNetName?></strong>
                 &nbsp;&bull;&nbsp; <a href="https://w0chp.radio/brandmeister-talkgroups/" target="_blank">Full Talkgroup List</a>
            </div>

            <h3 class="bm-section-title">Static Talkgroups</h3>
            
            <form id="bmm-tg-static-form" action="<?=$processUrl?>" method="POST">
                <div class="bm-list-container">
                    <div class="bm-list-header">
                        <div class="bm-col-tg">Talkgroup</div>
                        <div class="bm-col-ts">Slot</div>
                        <div class="bm-col-name">Name</div>
                        <div class="bm-col-action">Actions</div>
                    </div>

                    <?php
                    foreach ($bmApi->getFavTGs() as $tg => $favTGData) {
                        $dropUrl = htmlspecialchars("{$processUrl}?droptg={$tg}&slot={$favTGData['slot']}");
                        $switchId = "toggle-tg{$tg}";
                        $displaySlot = $favTGData['slot'] == 0? 2: $favTGData['slot'];
                    ?>
                    <div class="bm-list-row">
                        <div class="bm-col-tg">TG <?=$tg?></div>
                        <div class="bm-col-ts"><span class="bm-ts-badge">TS<?=$displaySlot?></span></div>
                        <div class="bm-col-name"><?=$bmApi->resolveGroupName($tg)?></div>
                        <div class="bm-col-action">
                             <label class="bm-switch">
                                <input type="checkbox" id="<?=$switchId?>" name="<?=$switchId?>" value="ON"
                                    <?php echo $favTGData['linked']? 'checked="checked"': ''?>
                                    data-tg="<?=$tg?>" data-slot="<?=$favTGData['slot']?>"
                                    class="bmm-tg-switch">
                                <span class="bm-slider"></span>
                            </label>
                            <a class="bm-btn-drop clickloader" href="<?=$dropUrl?>" title="Delete Static TG">Delete</a>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div class="bm-add-form">
                    <div class="bm-input-area">
                        <textarea id="add-bm-tg-list" class="bm-textarea" name="TG" placeholder="Enter Talkgroups (One per line)"></textarea>
                    </div>
                    
                    <div class="bm-ts-area">
                        <div class="bm-toggle-group">
                        <?php if ($duplexMode) { ?>
                            <div class="bm-toggle-option">
                                <input type="radio" id="ts1" name="TS" <?php if (!$ts1Enabled) echo 'disabled="disabled"'?> <?php if ($ts1Selected) echo 'checked="checked"'?> value="1">
                                <label for="ts1">TS1</label>
                            </div>
                            <div class="bm-toggle-option">
                                <input type="radio" id="ts2" name="TS" <?php if (!$ts2Enabled) echo 'disabled="disabled"'?> <?php if ($ts2Selected) echo 'checked="checked"'?> value="2">
                                <label for="ts2">TS2</label>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="TS" value="0">
                            <div class="bm-toggle-option">
                                <input type="radio" disabled>
                                <label style="opacity:0.3">TS1</label>
                            </div>
                            <div class="bm-toggle-option">
                                <input type="radio" checked>
                                <label>TS2</label>
                            </div>
                        <?php } ?>
                        </div>
                    </div>

                    <div class="bm-submit-area">
                        <input type="submit" class="bm-btn clickloader" name="static-tg-add" value="Add &amp; Link">
                        <div class="bm-mass-mgmt">
                            <a class="bm-btn-mass clickloader" href="<?=$processUrl?>?masstg=enable" title="Enable All">Enable All</a>
                            <a class="bm-btn-mass clickloader" href="<?=$processUrl?>?masstg=disable" title="Disable All">Disable All</a>
                            <a class="bm-btn-mass clickloader" href="<?=$processUrl?>?masstg=drop" onclick="return confirm('Do you really want to drop all talkgroups?')" title="Delete All" style="border-color:#c0392b; color:#e74c3c;">Delete All</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="bm-hint">
                <b>Hint:</b> You can add multiple talkgroups at once. Added talkgroups are linked to BrandMeister. Disabling a talkgroup unlinks it, but keeps it in your list.
            </div>


            <h3 class="bm-section-title">Dynamic Talkgroups</h3>

            <div class="dynamic-tgs" data-update-url="<?=$processUrl?>" data-update-period="15000">
                <div class="bm-list-container">
                    <div class="bm-list-header">
                        <div class="bm-col-tg">Talkgroup</div>
                        <div class="bm-col-ts">Slot</div>
                        <div class="bm-col-name">Name</div>
                        <div class="bm-col-timeout">Timeout</div>
                    </div>

                    <?php
                    $localTimeFormat = constant("TIME_FORMAT") == "24"? 'H:i:s': 'h:i:s A';
                    $now = new DateTime();
                    $dynTGs = $bmApi->getDynamicTGs();

                    if (count($dynTGs) == 0) {
                        echo '<div class="bm-list-row" style="justify-content:center; padding:20px; opacity:0.6;">No Dynamic Talkgroups Linked</div>';
                    } else {
                        foreach ($dynTGs as $dynTGData) {
                            $displaySlot = $dynTGData['slot'] == 0? 2: $dynTGData['slot'];
                            $then = new DateTime("@" . $dynTGData['timeout']);
                            $minRemaining = $then->diff($now)->format('%i:%S');
                            $expirationInfo = date($localTimeFormat, substr($dynTGData['timeout'], 0, 10)) . 
                                " (<span class=\"auto-calculate-remaining\" data-exptime=\"{$dynTGData['timeout']}\">{$minRemaining}</span>)";
                    ?>
                        <div class="bm-list-row">
                            <div class="bm-col-tg">TG <?=$dynTGData['talkgroup']?></div>
                            <div class="bm-col-ts"><span class="bm-ts-badge">TS<?=$displaySlot?></span></div>
                            <div class="bm-col-name"><?=$bmApi->resolveGroupName($dynTGData['talkgroup'])?></div>
                            <div class="bm-col-timeout"><?=$expirationInfo?></div>
                        </div>
                    <?php 
                        }
                    } 
                    ?>
                </div>

                <div class="bm-drop-bar">
                    <?php if ($duplexMode) { ?>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <strong>TS1:</strong>
                            <input class="bm-btn-mass clickbtn" data-linkto="/admin/system_api.php?action=bm_manager&cmd=drop_qso&slot=1" type="button" value="Drop QSO">
                            <input class="bm-btn-mass clickbtn" data-linkto="/admin/system_api.php?action=bm_manager&cmd=drop_dynamic&slot=1" type="button" value="Drop All Dynamic">
                        </div>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <strong>TS2:</strong>
                            <input class="bm-btn-mass clickbtn" data-linkto="/admin/system_api.php?action=bm_manager&cmd=drop_qso&slot=2" type="button" value="Drop QSO">
                            <input class="bm-btn-mass clickbtn" data-linkto="/admin/system_api.php?action=bm_manager&cmd=drop_dynamic&slot=2" type="button" value="Drop All Dynamic">
                        </div>
                    <?php } else { ?>
                        <input class="bm-btn-mass clickbtn" data-linkto="/admin/system_api.php?action=bm_manager&cmd=drop_qso&slot=0" type="button" value="Drop QSO">
                        <input class="bm-btn-mass clickbtn" data-linkto="/admin/system_api.php?action=bm_manager&cmd=drop_dynamic&slot=0" type="button" value="Drop All Dynamic">
                    <?php } ?>
                </div>
                </div>
            </div>
    </div>
<?php
}
?>
