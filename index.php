<?php
session_set_cookie_params(0, "/");
session_name("WPSD_Session");
session_id('wpsdsession');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/class-wpsd-functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';

$MYCALL = strtoupper($callsign);
$_SESSION['MYCALL'] = $MYCALL;

// Clear session data (page {re}load);
$keepSessions = ['MYCALL'];
foreach ($_SESSION as $key => $value) {
    if (!in_array($key, $keepSessions)) {
        unset($_SESSION[$key]);
    }
}

checkSessionValidity();

if (isset($_SESSION['CSSConfigs']['Text'])) {
    $textSections = $_SESSION['CSSConfigs']['Text']['TextSectionColor'];
}
if(empty($_GET['func'])) {
    $_GET['func'] = "main";
}
if(empty($_POST['func'])) {
    $_POST['func'] = "main";
}

// --- THEME & COLOR LOGIC (Defaults) ---
$backgroundContent = "#1a1a1a";
$backgroundBanners = "#333";
$backgroundNavbar = "#444";
$textNavbar = "#fff";
$backgroundNavbarHover = "#666";
$textNavbarHover = "#fff";
$tableRowOddBg = "#333";

if (isset($_SESSION['CSSConfigs'])) {
    if (isset($_SESSION['CSSConfigs']['Text']['TextSectionColor'])) $textSections = $_SESSION['CSSConfigs']['Text']['TextSectionColor'];
    // Setup Variables for Unconfigured Cards
    if (isset($_SESSION['CSSConfigs']['Background']['ContentColor'])) $backgroundContent = $_SESSION['CSSConfigs']['Background']['ContentColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['BannersColor'])) $backgroundBanners = $_SESSION['CSSConfigs']['Background']['BannersColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['BannersTextColor'])) $textBanners = $_SESSION['CSSConfigs']['Text']['BannersTextColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['TableRowBgOddColor'])) $tableRowOddBg = $_SESSION['CSSConfigs']['Background']['TableRowBgOddColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['TextColor'])) $textContent = $_SESSION['CSSConfigs']['Text']['TextColor'];
    if (isset($_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'])) $tableBorderColor = $_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['NavbarColor'])) $backgroundNavbar = $_SESSION['CSSConfigs']['Background']['NavbarColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['NavbarTextColor'])) $textNavbar = $_SESSION['CSSConfigs']['Text']['NavbarTextColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['NavbarHoverColor'])) $backgroundNavbarHover = $_SESSION['CSSConfigs']['Background']['NavbarHoverColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['NavbarHoverTextColor'])) $textNavbarHover = $_SESSION['CSSConfigs']['Text']['NavbarHoverTextColor'];
}

if (empty($_GET['func'])) {
    $_GET['func'] = "main";
}
if (empty($_POST['func'])) {
    $_POST['func'] = "main";
}

// Vendor-specific: ZUMradio image init
$iniFile = '/etc/dstar-radio.mmdvmhost';
$section = 'ZUM';
$key = 'NewInstall';
$expectedValue = '1';
$iniData = parse_ini_file($iniFile, true);
$isNewZumInstall = isset($iniData[$section][$key]) && $iniData[$section][$key] === $expectedValue;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="language" content="English" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="pragma" content="no-cache" />
    <link rel="shortcut icon" href="/images/favicon.ico?version=<?php echo $versionCmd; ?>" type="image/x-icon" />
    <title><?php echo "$MYCALL" . " - " . __('Dashboard'); ?></title>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css?version=<?php echo $versionCmd; ?>" />
    <?php include_once "config/browserdetect.php"; ?>
    <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            cache: false
        });
        window.time_format = '<?php echo constant("TIME_FORMAT"); ?>';
    </script>
    <link href="/js/select2/css/select2.min.css?version=<?php echo $versionCmd; ?>" rel="stylesheet" />
    <script src="/js/select2/js/select2.full.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script src="/js/select2/js/select2-searchInputPlaceholder.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ysfLinkHost').select2({
                searchInputPlaceholder: 'Search...'
            });
            $('.p25LinkHost').select2({
                searchInputPlaceholder: 'Search...'
            });
            $('.nxdnLinkHost').select2({
                searchInputPlaceholder: 'Search...'
            });
            $(".RefName").select2({
                tags: true,
                width: '125px',
                dropdownAutoWidth: false,
                createTag: function(params) {
                    return {
                        id: params.term,
                        text: params.term,
                        newOption: true
                    }
                },
                templateResult: function(data) {
                    var $result = $("<span></span>");
                    $result.text(data.text);
                    if (data.newOption) {
                        $result.append(" <em>(Search existing, or enter and save custom reflector value.)</em>");
                    }
                    return $result;
                }
            });
            $('.dmrMasterHost3').select2();
            $('.dmrMasterHost3Startup').select2({
                searchInputPlaceholder: 'Search...',
                width: '125px'
            });
            $('.ModSel').select2();
        });

        $(document).ready(function() {
            // System Info Toggle
            $('.menuhwinfo').click(function() {
                $(".hw_toggle").slideToggle(function() {
                    localStorage.setItem('hwinfo_visible', $(this).is(":visible"));
                })
            });
            $('.hw_toggle').toggle(localStorage.getItem('hwinfo_visible') === 'true');

            // Sidebar Toggle Logic
            function updateSidebarState(collapsed) {
                var nav = $('.nav');
                var content = $('.content');
                var icon = $('#sidebarToggleIcon');

                if (collapsed) {
                    nav.addClass('collapsed');
                    content.css('margin-left', '40px'); // 30px nav + 10px gap
                    icon.removeClass('fa-caret-left').addClass('fa-caret-right');
                } else {
                    nav.removeClass('collapsed');
                    content.css('margin-left', '250px');
                    icon.removeClass('fa-caret-right').addClass('fa-caret-left');
                }
            }

            // Click on the toggle button OR the collapsed navbar itself
            $('.sidebar-toggle, .nav.collapsed').click(function(e) {
                if ($(this).hasClass('nav') && !$(this).hasClass('collapsed')) {
                    return;
                }
                e.preventDefault();
                var nav = $('.nav');
                var isCollapsed = nav.hasClass('collapsed');
                updateSidebarState(!isCollapsed);
                localStorage.setItem('sidebar_collapsed', !isCollapsed);
            });

            $('#repeaterInfo').hover(function() {
                $('.sidebar-toggle').css('opacity', '0.8');
            }, function() {
                $('.sidebar-toggle').css('opacity', '0');
            });

            $('.sidebar-toggle').hover(function() {
                $(this).css('opacity', '1');
            })

            if (localStorage.getItem('sidebar_collapsed') === 'true') {
                updateSidebarState(true);
            } else {
                updateSidebarState(false);
            }
        });

        function clear_activity() {
            if ('true' === localStorage.getItem('filter_activity') || jQuery('.filter-activity-max-wrap').length > 0) {
                max = localStorage.getItem('filter_activity_max') || 1;
                jQuery('.filter-activity-max').attr('value', max);
                jQuery('.activity-duration').each(function(i, el) {
                    duration = parseFloat(jQuery(this).text());
                    if (duration < max) {
                        jQuery(this).closest('tr').hide();
                    } else {
                        jQuery(this).closest('tr').addClass('good-activity');
                    }
                });

                jQuery('.good-activity').each(function(i, el) {
                    if (i % 2 === 0) {
                        jQuery(el).addClass('even');
                    } else {
                        jQuery(el).addClass('odd');
                    }
                });
            }
        };

        function setFilterActivity(obj) {
            localStorage.setItem('filter_activity', obj.checked);
            $.ajax({
                type: "POST",
                url: '/mmdvmhost/filteractivity_ajax.php',
                data: {
                    action: obj.checked
                },
            });
        }

        function setFilterActivityMax(obj) {
            max = obj.value || 1;
            localStorage.setItem('filter_activity_max', obj.value);
            reloadDynDataId = setInterval(reloadDynData, reloadDynDataInterval);
        }

        function reloadUpdateCheck() {
            $("#CheckUpdate").load("/includes/checkupdates.php", function() {
                setTimeout(reloadUpdateCheck, 10000)
            });
        }
        setTimeout(reloadUpdateCheck, 10000);

        function reloadMessageCheck() {
            $("#CheckMessage").load("/includes/messages.php", function() {
                setTimeout(reloadMessageCheck, 300000)
            });
        }
        setTimeout(reloadMessageCheck, 300000);

        function reloadDateTime() {
            $('#DateTime').html(_getDatetime(window.time_format));
            setTimeout(reloadDateTime, 1000);
        }
        reloadDateTime();
    </script>
    <script>
        function executeBackgroundTasks() {
            $.ajax({
                url: '/includes/execute-background-tasks.php',
                success: function(data) {
                    console.log('Background tasks executed successfully.');
                },
                error: function() {
                    console.log('Error executing background tasks.');
                }
            });
        }

        $(document).ready(function() {
            setInterval(function() {
                executeBackgroundTasks();
            }, 300000); // 5 mins
        });
    </script>
    <?php if ('/index.php' === $_SERVER["PHP_SELF"]) : ?>
        <script>
            document.addEventListener('keydown', function(event) {
                if (event.key === 'S' || event.keyCode === 83) {
                    window.location.href = '/mmdvmhost/export-lh.php';
                }
            });
        </script>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="SmallHeader shLeft noMob">
                <a style="border-bottom: 1px dotted;" class="tooltip" href="#">
                    <?php echo __('Hostname') . ": "; ?>
                    <span><strong>System IP Address<br /></strong><?php echo str_replace(',', ',<br />', exec("hostname -I | awk '{print $1}'")); ?> </span>
                    <?php echo exec('cat /etc/hostname'); ?>
                </a>
            </div>
            <?php if ($_SESSION['CURRENT_PROFILE']) { ?>
                <div class="SmallHeader shLeft noMob"> | <?php echo __('Current Profile') . ": "; ?> <?php echo $_SESSION['CURRENT_PROFILE']; ?></div>
            <?php } ?>
            <div class="SmallHeader shRight noMob">
                <div id="CheckUpdate">
                    <?php include 'includes/checkupdates.php'; ?>
                </div><br />
            </div>

            <h1>WPSD <?php echo __('Dashboard for') . " <code style='font-weight:550;'>" . $_SESSION['MYCALL'] . "</code>"; ?></h1>
            <div id="CheckMessage">
                <?php include 'includes/messages.php'; ?>
            </div>

            <div class="navbar">
                <div class="headerClock">
                    <span id="DateTime"></span>
                </div>
                <?php
                if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
                    echo ' <a class="menuconfig" href="/admin/configure.php">' . __('Configuration') . '</a>';
                    if ($osVer >= 12 && (isDVmegaCast() == 0) ) {
                        echo ' <a class="menuwifi" href="/admin/wifi-manager.php">WiFi</a>' . "\n";
                    }
                    echo ' <a class="menuupdate noMob" href="/admin/update.php">' . __('WPSD Update') . '</a>' . "\n";
                    echo ' <a class="menuadvanced noMob" href="/admin/advanced/">Advanced</a>' . "\n";
                    echo ' <a class="menupower" href="/admin/power.php">' . __('Power') . '</a>' . "\n";
                    echo ' <a class="menulogs noMob" href="/admin/live_log.php">' . __('Log Viewer') . '</a>' . "\n";
                    echo ' <a class="menudashboard" href="/">' . __('Dashboard') . '</a>' . "\n";
                }
                if ($_SERVER["PHP_SELF"] !== "/admin/index.php") {
                    echo '<a class="menuadmin noMob" href="/admin/">' . __('Admin') . '</a>' . "\n";
                    echo '<a class="menuhwinfo noMob" href="#">SysInfo</a>';
                    echo '<a class="menulive" href="/live/">Live Caller</a>';
		    echo ' <a class="menuupdate noMob" href="/admin/advanced/services.php?action=updatehostsfiles">'.__( 'Aktualizace ODS' ).'</a>'."\n";
                    if (isDVmegaCast() == 1) {
                        echo '<a class="menucastmemory noMob" href="/admin/cast/memory-list/">Cast Memory</a>';
                    }
                    echo ' <a class="menuappearance noMob" href="/admin/appearance.php">Appearance</a>' . "\n";
                    echo '<a class="menuprofile" href="/admin/profile_manager.php">Profiles</a>' . "\n";
                }
                ?>
            </div>
        </div>

        <?php
        // Output default features only if configured
        if (($_SERVER["PHP_SELF"] == "/index.php" || $_SERVER["PHP_SELF"] == "/admin/index.php") && file_exists('/etc/dstar-radio.mmdvmhost')) {
            echo '<div class="contentwide">' . "\n";
            echo '<script type="text/javascript">' . "\n";
            echo 'function reloadHwInfo(){' . "\n";
            echo '  $("#hwInfo").load("/includes/hw_info.php",function(){ setTimeout(reloadHwInfo, 30000) });' . "\n";
            echo '}' . "\n";
            echo 'setTimeout(reloadHwInfo, 30000);' . "\n";
            echo '</script>' . "\n";
            echo '<script type="text/javascript">' . "\n";
            echo 'function reloadRadioInfo(){' . "\n";
            echo '  $("#radioInfo").load("/mmdvmhost/radioinfo.php",function(){ setTimeout(reloadRadioInfo, 1000) });' . "\n";
            echo '}' . "\n";
            echo 'setTimeout(reloadRadioInfo, 1000);' . "\n";
            echo '</script>' . "\n";
            echo "<div id='hw_info' class='hw_toggle'>\n";
            echo '<div id="hwInfo">' . "\n";
            include 'includes/hw_info.php';
            echo '</div>' . "\n";
            echo '</div>' . "\n";
            echo '<div id="radioInfo">' . "\n";
            include 'mmdvmhost/radioinfo.php';
            echo '</div>' . "\n";
            echo '</div>' . "\n";
        }

        // Configuration Check Logic
        if ($isNewZumInstall) {
            echo '<div class="contentwide">' . "\n";
            echo "<h1>New ZUMspot Installation...</h1>\n";
            echo "<p>You will be redirected to the configuration page in 10 seconds to setup your ZUMspot...</p>\n";
            echo '<script type="text/javascript">setTimeout(function() { window.location="/admin/configure.php";},10000);</script>' . "\n";
        } else if (file_exists('/etc/dstar-radio.mmdvmhost')) {
            echo '<div class="nav">' . "\n";
            echo '  <div class="sidebar-toggle noMob" title="Toggle Sidebar"><i id="sidebarToggleIcon" class="fa fa-caret-left"></i></div>' . "\n";
            echo '<script type="text/javascript">' . "\n";
            echo 'function reloadRepeaterInfo(){' . "\n";
            echo '  $("#repeaterInfo").load("/mmdvmhost/repeaterinfo.php",function(){ setTimeout(reloadRepeaterInfo,5000) });' . "\n";
            echo '}' . "\n";
            echo 'setTimeout(reloadRepeaterInfo,5000);' . "\n";
            echo '</script>' . "\n";
            echo '<div id="repeaterInfo">' . "\n";
            include 'mmdvmhost/repeaterinfo.php';
            echo '</div>' . "\n";
            echo '</div>' . "\n";

            echo '<div class="content">' . "\n";

            // Network Checks
            $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
            if ($dmrMasterHost == '127.0.0.1') {
                $dmrMasterHost = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'];
                $bmEnabled = ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'] != "0" ? true : false);
            } elseif (preg_match("/brandmeister.network/", $dmrMasterHost)) {
                $bmEnabled = true;
            }

            // Check if master is a BrandMeister Master
            if (($dmrMasterFile = fopen("/usr/local/etc/DMR_Hosts.txt", "r")) != FALSE) {
                while (!feof($dmrMasterFile)) {
                    $dmrMasterLine = fgets($dmrMasterFile);
                    $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
                    if ((strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
                        if ($dmrMasterHost == $dmrMasterHostF[2]) {
                            $dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]);
                        }
                    }
                }
                fclose($dmrMasterFile);
            }

            // TGIF Check
            if ($testMMDVModeDMR == 1) {
                $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
                if ($dmrMasterHost == '127.0.0.1') {
                    for ($i = 1; $i <= 5; $i++) {
                        if (isset($_SESSION['DMRGatewayConfigs']["DMR Network $i"]['Address'])) {
                            if (($_SESSION['DMRGatewayConfigs']["DMR Network $i"]['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']["DMR Network $i"]['Enabled'])) {
                                $tgifEnabled = true;
                            }
                        }
                    }
                } elseif ($dmrMasterHost == 'tgif.network') {
                    $tgifEnabled = true;
                }
            }

            $testMMDVModeDSTARnet = getConfigItem("D-Star", "Enable", $_SESSION['MMDVMHostConfigs']);
            if ($testMMDVModeDSTARnet == 1) {
                if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "ds_man" || $_GET["func"] == "ds_man") {
                    echo '<script type="text/javascript">' . "\n";
                    echo 'function reloadrefLinks(){' . "\n";
                    echo '  $("#refLinks").load("/mmdvmhost/dstar_reflector_links.php",function(){ setTimeout(reloadrefLinks,5000) });' . "\n";
                    echo '}' . "\n";
                    echo 'setTimeout(reloadrefLinks,5000);' . "\n";
                    echo '</script>' . "\n";
                    echo '<div id="refLinks">' . "\n";
                    include 'mmdvmhost/dstar_reflector_links.php';
                    echo '</div>' . "\n";
                    include 'mmdvmhost/dstar_link_manager.php';
                    echo '<script type="text/javascript">' . "\n";
                    echo 'function reloadccsConnections(){' . "\n";
                    echo '  $("#ccsConnects").load("/mmdvmhost/dstar_ccs_connections.php",function(){ setTimeout(reloadccsConnections,15000) });' . "\n";
                    echo '}' . "\n";
                    echo 'setTimeout(reloadccsConnections,15000);' . "\n";
                    echo '</script>' . "\n";
                    echo '<div id="ccsConnects">' . "\n";
                    include 'mmdvmhost/dstar_ccs_connections.php';
                    echo '</div>' . "\n";
                }
            }

            if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "mode_man" || $_GET["func"] == "mode_man") {
                include "admin/instant-mode-manager.php";
            }

            if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "sys_man" || $_GET["func"] == "sys_man") {
                include "admin/system-manager.php";
            }

            if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "bm_man" || $_GET["func"] == "bm_man") {
                include "admin/bm-manager.php";
            }

            if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "tgif_man" || $_GET["func"] == "tgif_man") {
                include 'mmdvmhost/tgif_manager.php';
            }

            $testMMDVModeYSF = getConfigItem("System Fusion", "Enable", $_SESSION['MMDVMHostConfigs']);
            $testDMR2YSF = $_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'];
            if ($testMMDVModeYSF == 1 || $testDMR2YSF == 1) {
                if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "ysf_man" || $_GET["func"] == "ysf_man") {
                    include 'mmdvmhost/ysf_manager.php';
                }
            }

            $testMMDVModeP25net = getConfigItem("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
            $testYSF2P25 = $_SESSION['YSF2P25Configs']['Enabled']['Enabled'];
            if ($testMMDVModeP25net == 1 || $testYSF2P25 == 1) {
                if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "p25_man" || $_GET["func"] == "p25_man") {
                    include 'mmdvmhost/p25_manager.php';
                }
            }

            $testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
            $testDMR2NXDN = $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'];
            $testYSF2NXDN = $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'];
            if ($testMMDVModeNXDN == 1 || $testDMR2NXDN == 1 || $testYSF2NXDN == 1) {
                if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "nxdn_man" || $_GET["func"] == "nxdn_man") {
                    include 'mmdvmhost/nxdn_manager.php';
                }
            }

            $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
            if ($dmrMasterHost == '127.0.0.1') {
                if ($testMMDVModeDMR == 1) {
                    if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "dmr_man" || $_GET["func"] == "dmr_man") {
                        include 'mmdvmhost/dmr_manager.php';
                    }
                }
            }

            $testMMDVModePOCSAG = getConfigItem("POCSAG", "Enable", $_SESSION['MMDVMHostConfigs']);
            if ($testMMDVModePOCSAG == 1) {
                if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "pocsag_man" || $_GET["func"] == "pocsag_man") {
                    echo '<div id="dapnetMsgr">' . "\n";
                    include 'mmdvmhost/dapnet_messenger.php';
                    echo '</div>' . "\n";
                }
            }

            // Admin Selection Form
            if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
                if ($_GET['func'] != 'main') {
                    echo '<br />';
                }
                echo '<h3 class="larger" style="text-align:left;font-weight:bold;margin:-5px 0 2px 0;">Select an Admin Section/Page:</h3><br />' . "\n";
                echo '<form method="get" id="admin_sel" name="admin_sel" action="/admin/" style="padding-bottom:10px;">' . "\n";
                echo '      <div class="mode_flex">' . "\n";
                echo '        <div class="mode_flex row">' . "\n";
                echo '          <div class="mode_flex column">' . "\n";
                echo '            <button form="admin_sel" type="submit" value="main" name="func"><span>Admin Main Page</span></button>' . "\n";
                echo '          </div><div class="mode_flex column">' . "\n";
                $testMMDVModeDSTARnet = getConfigItem("D-Star", "Enable", $_SESSION['MMDVMHostConfigs']);
                if ($testMMDVModeDSTARnet == 1 && !isPaused("D-Star")) {
                    echo '		<button form="admin_sel" type="submit" value="ds_man" name="func"><span>D-Star Manager</span></button>' . "\n";
                } else {
                    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="ds_man" name="func"><span>D-Star Manager</span></button>' . "\n";
                }
                echo '          </div><div class="mode_flex column">' . "\n";
                $testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);
                if ($bmEnabled == true && $testMMDVModeDMR == 1) {
                    echo '		<button form="admin_sel" type="submit" value="bm_man" name="func"><span>BrandMeister Manager</span></button>' . "\n";
                } else {
                    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="bm_man" name="func"><span>BrandMeister Manager</span></button>' . "\n";
                }
                echo '          </div><div class="mode_flex column">' . "\n";
                if (isset($tgifEnabled) && $tgifEnabled == 1 && $testMMDVModeDMR == 1) {
                    echo '		<button form="admin_sel" type="submit" value="tgif_man" name="func"><span>TGIF Manager</span></button>' . "\n";
                } else {
                    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="tgif_man" name="func"><span>TGIF Manager</span></button>' . "\n";
                }
                echo '          </div><div class="mode_flex column">' . "\n";
                $testMMDVModeYSF = getConfigItem("System Fusion", "Enable", $_SESSION['MMDVMHostConfigs']);
                $testDMR2YSF = $_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'];
                if ($testMMDVModeYSF == 1 || $testDMR2YSF == 1) {
                    echo '		<button form="admin_sel" type="submit" value="ysf_man" name="func"><span>YSF Manager</span></button>' . "\n";
                } else {
                    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="ysf_man" name="func"><span>YSF Manager</span></button>' . "\n";
                }
                echo '          </div><div class="mode_flex column">' . "\n";
                $testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);
                if ($testMMDVModeDMR == 1) {
                    echo '          <button form="admin_sel" type="submit" value="dmr_man" name="func"><span>DMR Network Manager</span></button>' . "\n";
                } else {
                    echo '          <button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="dmr_man" name="func"><span>DMR Network Manager</span></button>' . "\n";
                }
                echo '      </div></div>' . "\n";
                echo '        <div class="mode_flex row">' . "\n";
                if (isDVmegaCast() != 1) {
                    echo '          <div class="mode_flex column">' . "\n";
                    $testMMDVModeP25 = getConfigItem("P25", "Enable", $_SESSION['MMDVMHostConfigs']);
                    $testYSF2P25 = $_SESSION['YSF2P25Configs']['Enabled']['Enabled'];
                    if ($testMMDVModeP25 == 1 || $testYSF2P25 == 1) {
                        echo '		<button form="admin_sel" type="submit" value="p25_man" name="func"><span>P25 Manager</span></button>' . "\n";
                    } else {
                        echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="p25_man" name="func"><span>P25 Manager</span></button>' . "\n";
                    }
                    echo '          </div><div class="mode_flex column">' . "\n";
                    $testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
                    $testDMR2NXDN = $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'];
                    $testYSF2NXDN = $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'];
                    if (($testMMDVModeNXDN == 1 || $testDMR2NXDN == 1 || $testYSF2NXDN == 1) && !isPaused("NXDN")) {
                        echo '		<button form="admin_sel" type="submit" value="nxdn_man" name="func"><span>NXDN Manager</span></button>' . "\n";
                    } else {
                        echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="nxdn_man" name="func"><span>NXDN Manager</span></button>' . "\n";
                    }
                    echo '          </div><div class="mode_flex column">' . "\n";
                    $testMMDVModePOCSAG = getConfigItem("POCSAG", "Enable", $_SESSION['MMDVMHostConfigs']);
                    if ($testMMDVModePOCSAG == 1) {
                        echo '		<button form="admin_sel" type="submit" value="pocsag_man" name="func"><span>POCSAG Manager</span></button>' . "\n";
                    } else {
                        echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="pocsag_man" name="func"><span>POCSAG Manager</span></button>' . "\n";
                    }
                    echo '          </div><div class="mode_flex column">' . "\n";
                } else {
                    echo '          <div class="mode_flex column">' . "\n";
                }
                echo '            <button form="admin_sel" type="submit" value="mode_man" name="func"><span>Instant Mode Manager</span></button>' . "\n";
                echo '          </div><div class="mode_flex column">' . "\n";
                echo '		<button form="admin_sel" type="submit" value="sys_man" name="func"><span>System Manager</span></button>' . "\n";
                echo '          </div>' . "\n";
                echo '      </div>' . "\n" . '</div>' . "\n";
                echo '      <div><br /><b>Note:</b> Modes/networks/services not <a href="/admin/configure.php" style="text-decoration:underline;color:inherit;">globally configured/enabled</a>, or that are paused, are not selectable here until they are enabled or <a href="./?func=mode_man" style="text-decoration:underline;color:inherit;">resumed from pause</a>.</div>' . "\n";
                echo ' </form>' . "\n";
                if ($_GET['func'] != "main" && $_GET['func'] != "pocsag_man") {
                    echo "</div>\n";
                }
            }

            if ($_SERVER["PHP_SELF"] == "/index.php" || $_SERVER["PHP_SELF"] == "/admin/index.php") {
                echo '<script type="text/javascript">' . "\n";
                echo 'function setLastCaller(obj) {' . "\n";
                echo '    if (obj.checked) {' . "\n";
                echo "        $.ajax({
				success: function(data) { 
     				    $('#lcmsg').html(data).fadeIn('slow');
				    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>For optimal performance, the number of Last Heard rows will be decreased while Caller Details function is enabled.</div>\").fadeIn('slow')
     				    $('#lcmsg').delay(4000).fadeOut('slow');
				},
                	        type: \"POST\",
  	          	        url: '/mmdvmhost/callerdetails_ajax.php',
                	        data:{action:'enable'},
         	             });";
                echo '    }' . "\n";
                echo '    else {' . "\n";
                echo "        $.ajax({
				success: function(data) { 
     				    $('#lcmsg').html(data).fadeIn('slow');
				    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>Caller Details function disabled. Increasing Last Heard table rows to user preference (if set) or default (40).</div>\").fadeIn('slow')
     				    $('#lcmsg').delay(4000).fadeOut('slow');
				},
	                        type: \"POST\",
	                        url: '/mmdvmhost/callerdetails_ajax.php',
	                        data:{action:'disable'},
	                      });";
                echo '    }' . "\n";
                echo '}' . "\n";
                echo '</script>' . "\n";
                echo '<div id="lcmsg" style="background:#d6d6d6;color:black; margin:0 0 10px 0;"></div>' . "\n";

                if ($_SERVER["PHP_SELF"] !== "/admin/index.php") {
                    echo '<script>
		      async function fetchData(url, targetElement) {
		        try {
		          const response = await fetch(url);
		          const data = await response.text();
		          $(targetElement).html(data);
		        } catch (error) {
		          console.error(`Error fetching data from ${url}:`, error);
		        }
		      }
                      function reloadDynData() {
                        fetchData("/mmdvmhost/last_heard_table.php", "#lastHeard");
                        fetchData("/mmdvmhost/local_tx_table.php", "#localTxs");';
                    if (isset($_SESSION['WPSDrelease']['WPSD']['ProcNum']) && ($_SESSION['WPSDrelease']['WPSD']['ProcNum'] >= 4)) {
                        echo 'fetchData("/mmdvmhost/caller_details_table.php", "#liveCallerDeets");';
                    }
                    echo '
                      }';
                    if (isset($_SESSION['WPSDrelease']['WPSD']['ProcNum']) && ($_SESSION['WPSDrelease']['WPSD']['ProcNum'] >= 4)) {
                        echo "reloadDynDataInterval = 1500;";
                    } else {
                        echo "reloadDynDataInterval = 2500;";
                    }
                    echo "reloadDynDataId = setInterval(reloadDynData, reloadDynDataInterval);";

                    echo '
                    </script>';
                }

                echo '<script>' . "\n";
                echo 'function setLHTGnames(obj) {' . "\n";
                echo '    if (obj.checked) {' . "\n";
                echo "        $.ajax({
                                success: function(data) { 
                                    $('#lcmsg').html(data).fadeIn('slow');
                                    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>Talkgroup Names display enabled: Please wait until data populated. For optimal performance, the number of Last Heard rows will be decreased while TG Names function is enabled.</div>\").fadeIn('slow')
                                    $('#lcmsg').delay(4000).fadeOut('slow');
                                },
                	        type: \"POST\",
  	          	        url: '/mmdvmhost/tgnames_ajax.php',
                	        data:{action:'enable'},
         	             });";
                echo '    }' . "\n";
                echo '    else {' . "\n";
                echo "        $.ajax({
                                success: function(data) { 
                                    $('#lcmsg').html(data).fadeIn('slow');
                                    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>Talkgroup Names display disabled: Please wait until data is cleared. Increasing Last Heard table rows to user preference (if set) or default (40).</div>\").fadeIn('slow')
                                    $('#lcmsg').delay(4000).fadeOut('slow');
                                },
	                        type: \"POST\",
	                        url: '/mmdvmhost/tgnames_ajax.php',
	                        data:{action:'disable'},
	                      });";
                echo '    }' . "\n";
                echo '}' . "\n";
                echo '</script>' . "\n";
            }

            if ($_SERVER["PHP_SELF"] !== "/admin/index.php") {
                echo '<div id="liveCallerDeets">' . "\n";
                echo '</div>' . "\n";

                if (!file_exists('/etc/.CALLERDETAILS')) {
                    echo '<div id="lastHeard" style="margin-top:-20px;">' . "\n";
                } else {
                    echo '<div id="lastHeard">' . "\n";
                }
                echo '</div>' . "\n";

                echo '<div id="localTxs" style="margin-top: 20px;">' . "\n";
                echo '</div>' . "\n";
            }

            if ($testMMDVModePOCSAG == 1) {
                if (($_SERVER["PHP_SELF"] == "/index.php" || $_POST["func"] == "pocsag_man" || $_GET["func"] == "pocsag_man")) {
                    $myOrigin = ($_SERVER["PHP_SELF"] == "/admin/index.php" ? "admin" : "other");

                    echo '<script type="text/javascript">' . "\n";
                    echo 'var pagesto;' . "\n";
                    echo 'function setPagesAutorefresh(obj) {' . "\n";
                    echo '        pagesto = setTimeout(reloadPages, 10000, "?origin=' . $myOrigin . '");' . "\n";
                    echo '}' . "\n";
                    echo 'function reloadPages(OptStr){' . "\n";
                    echo '    $("#Pages").load("/mmdvmhost/pocsag_table.php"+OptStr, function(){ pagesto = setTimeout(reloadPages, 10000, "?origin=' . $myOrigin . '") });' . "\n";
                    echo '}' . "\n";
                    echo 'pagesto = setTimeout(reloadPages, 10000, "?origin=' . $myOrigin . '");' . "\n";
                    echo '</script>' . "\n";
                    echo "\n" . '<div id="Pages">' . "\n";
                    include 'mmdvmhost/pocsag_table.php';
                    echo '</div>' . "\n";
                }
            }
        } else {
            echo '<style>
            .setup-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; padding: 20px; font-family: "Source Sans Pro", sans-serif; }
            .setup-card { 
                background: ' . $backgroundContent . '; 
                border: 1px solid ' . $tableBorderColor . '; 
                border-radius: 8px; width: 300px; padding: 0; overflow: hidden; text-align: center; 
                box-shadow: 0 4px 10px rgba(0,0,0,0.3); display: flex; flex-direction: column; 
                color: ' . $textContent . ';
            }
            .setup-header { 
                background: ' . $backgroundBanners . '; 
                padding: 15px; font-weight: bold; font-size: 1.2em; 
                border-bottom: 1px solid ' . $tableBorderColor . '; 
                color: ' . $textBanners . '; 
            }
            .setup-body { 
                padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; 
                background: ' . $backgroundContent . '; 
            }
            .setup-btn { 
                background: ' . $backgroundNavbar . '; 
                color: ' . $textNavbar . ' !important; 
                padding: 12px; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 15px; display: block; 
                border: 1px solid ' . $tableBorderColor . '; 
                transition: background 0.2s; 
            }
            .setup-btn:hover { 
                background: ' . $backgroundNavbarHover . '; 
                color: ' . $textNavbarHover . ' !important;
            }
            .setup-icon { 
                font-size: 4em; margin-bottom: 15px; 
                color: ' . $textContent . '; opacity: 0.7;
            }
            .setup-desc { 
                color: ' . $textContent . '; font-size: 0.95em; line-height: 1.4; opacity: 0.8;
            }
        </style>';

            echo '<div class="contentwide" style="min-height: 400px; padding-top:20px;">' . "\n";
            echo "<h1>Welcome to WPSD</h1>\n";
            echo "<p>WPSD is currently unconfigured. Please select an option below to get started:</p>\n";

            echo '<div class="setup-container">';

            // Card 1: WiFi
            echo '<div class="setup-card">';
            echo '<div class="setup-header">Configure WiFi</div>';
            echo '<div class="setup-body">';
            echo '<i class="fa fa-wifi setup-icon"></i>';
            echo '<span class="setup-desc">Configure your wireless connection.</span>';
            echo '<a href="/admin/wifi-manager.php" class="setup-btn">WiFi Manager</a>';
            echo '</div></div>';

            // Card 2: Configuration
            echo '<div class="setup-card">';
            echo '<div class="setup-header">Configure Software</div>';
            echo '<div class="setup-body">';
            echo '<i class="fa fa-cogs setup-icon"></i>';
            echo '<span class="setup-desc">Setup callsign, frequency, radio and all other settings.</span>';
            echo '<a href="/admin/configure.php" class="setup-btn">WPSD Configuration</a>';
            echo '</div></div>';

            echo '</div>'; // End setup-container
            echo '</div>' . "\n";
        }
        ?>
    </div>
    <?php
    include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/includes/execute-background-tasks.php';
    echo wpsd()->user_js();
    ?>
    <script>
        executeBackgroundTasks();
        reloadDateTime();
        if (typeof reloadDynData === 'function') reloadDynData();
    </script>
    <?php
    if ($_SESSION['WPSDdashConfig']['WPSD']['PhoneticCallsigns'] == "1") {
        echo '<script src="/js/phonetic-callsigns.js"></script>';
    }
    ?>
    </div>
</body>
</html>
