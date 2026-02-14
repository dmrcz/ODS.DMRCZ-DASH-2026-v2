<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    unset($_SESSION['CSSConfigs']);
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';

// Defaults
$backgroundContent = "#1a1a1a";
$textContent = "inherit";
$textContent = "#ffffff";
$tableBorderColor = "#333";
$backgroundServiceCellActiveColor = "#27ae60";
$backgroundServiceCellInactiveColor = "#8C0C26";
$tableRowOddBg = "#333";
$tableRowEvenBg = "#444";
$textLinks = "#2196F3";

if (isset($_SESSION['CSSConfigs'])) {
    if (isset($_SESSION['CSSConfigs']['Background']['ContentColor'])) $backgroundContent = $_SESSION['CSSConfigs']['Background']['ContentColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['ServiceCellActiveColor'])) $backgroundServiceCellActiveColor = $_SESSION['CSSConfigs']['Background']['ServiceCellActiveColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['ServiceCellInactiveColor'])) $backgroundServiceCellInactiveColor = $_SESSION['CSSConfigs']['Background']['ServiceCellInactiveColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['TableRowBgOddColor'])) $tableRowOddBg = $_SESSION['CSSConfigs']['Background']['TableRowBgOddColor'];
    if (isset($_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'])) $tableRowEvenBg = $_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['TextColor'])) $textContent = $_SESSION['CSSConfigs']['Text']['TextColor'];
    if (isset($_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'])) $tableBorderColor = $_SESSION['CSSConfigs']['ExtraSettings']['TableBorderColor'];
    if (isset($_SESSION['CSSConfigs']['Text']['TextLinkColor'])) $textLinks = $_SESSION['CSSConfigs']['Text']['TextLinkColor'];
}

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/profile_manager.php") {
    // Sanity Check Passed.
    header('Cache-Control: no-cache');

    $profile_dir = "/etc/WPSD_config_mgr";
    $current_profile_raw = trim(file_get_contents('/etc/.WPSD_config'));
    $saved = date("M d Y @ h:i A", filemtime("$profile_dir" . "/". "$current_profile_raw"));
    $current_profile_friendly = str_replace("_", " ", $current_profile_raw);
    
    // Determine Current Profile Status for Display
    if (file_exists('/etc/.WPSD_config') && count(glob("$profile_dir/*")) > 0) {
        if (is_dir("$profile_dir" . "/" ."$current_profile_raw") != false ) {
             // Use theme variable for active color
             $activeColor = isset($textModeCellActiveColor) ? $textModeCellActiveColor : '#2ecc71';
             $current_profile_display = "<h2 style='margin:0; color:".$activeColor."; font-size: 1.5rem;'>".trim(file_get_contents('/etc/.WPSD_config'))."</h2><div class='profile-meta'>Saved: ".$saved."</div>";
             $no_raw_profile = false;
        } else {
            $no_raw_profile = true;
            $current_profile_display = "<div class='profile-alert profile-alert-error'>Current Profile Deleted!<br>Please switch to a saved profile or save a new one.</div>";
        }
    } else {
        $no_raw_profile = true;
        $current_profile_display = "<div class='profile-meta'>No saved profiles yet.</div>";
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta name="language" content="English" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="pragma" content="no-cache" />
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
        <meta http-equiv="Expires" content="0" />
        <title>WPSD <?php echo __( 'Dashboard' )."";?> - Profile Manager</title>
        <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
        <?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
        <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
        <script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
        
        <style>
            /* Modern Profile Manager CSS */
            .profile-wrapper {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 20px;
                padding-top: 20px;
                max-width: 1200px;
                margin: 0 auto;
            }
            
            .profile-card {
                background-color: <?php echo $backgroundContent; ?>;
                border: 1px solid <?php echo $tableBorderColor; ?>;
                border-radius: 8px;
                padding: 0;
                flex: 1;
                min-width: 300px;
                max-width: 500px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.25);
                color: <?php echo $textContent; ?>;
                overflow: hidden;
                position: relative;
                display: flex;
                flex-direction: column;
            }

            .profile-header {
                padding: 15px 20px;
                font-weight: 700;
                font-size: 1.2rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                border-bottom: 1px solid <?php echo $tableBorderColor; ?>;
                text-align: center;
                background-color: <?php echo $backgroundBanners; ?>;
                color: <?php echo $textBanners; ?>;
                font-family: 'Source Sans Pro', sans-serif;
            }
            
            .profile-body {
                padding: 25px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                text-align: center;
            }

            /* Inputs and Selects - Force Monospace for data consistency */
            .profile-select, .profile-input {
                width: 100%;
                padding: 12px;
                border: 1px solid <?php echo $tableBorderColor; ?>;
                border-radius: 4px;
                color: <?php echo $textContent; ?> !important;
                background-color: <?php echo $tableRowEvenBg; ?> !important;
                font-family: 'Inconsolata', monospace !important;
                font-size: 1.1rem;
                margin-bottom: 15px;
                box-sizing: border-box;
                appearance: none;
                -webkit-appearance: none;
            }

            /* Buttons */
            .profile-btn {
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
                -webkit-appearance: none;
                font-family: 'Source Sans Pro', sans-serif;
            }
            .profile-btn:hover {
                background-color: <?php echo $backgroundNavbarHover; ?>;
                color: <?php echo $textNavbarHover; ?>;
                transform: translateY(-2px);
            }
            
            .profile-btn-delete {
                background-color: #c0392b !important;
                color: #ffffff !important;
                border-color: #a93226 !important;
            }
            .profile-btn-delete:hover {
                background-color: #e74c3c !important;
            }

            .profile-meta {
                margin-top: 5px;
                font-size: 0.9em;
                opacity: 0.7;
                font-family: 'Source Sans Pro', sans-serif;
            }
            
            .profile-helper {
                margin-top: 15px;
                font-size: 0.85em;
                opacity: 0.6;
                border-top: 1px solid <?php echo $tableBorderColor; ?>;
                padding-top: 10px;
                line-height: 1.4;
                font-family: 'Source Sans Pro', sans-serif;
            }

            /* Alerts */
            .profile-alert {
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 20px;
                text-align: center;
                border: 1px solid <?php echo $tableBorderColor; ?>;
                font-family: 'Source Sans Pro', sans-serif;
            }
            .profile-alert-success { background-color: <?php echo $backgroundServiceCellActiveColor; ?>; color: <?php echo $textContent; ?>; }
            .profile-alert-error   { background-color: <?php echo $backgroundServiceCellInactiveColor; ?>; color: <?php echo $textContent; ?>; }

            /* Mobile Visibility & Back Button */
            .mobile-back-btn { display: none; }

            @media screen and (max-width: 768px) {
                .desktop-only { display: none !important; }
                .noMob { display: none !important; }
                .profile-wrapper { flex-direction: column; align-items: center; padding: 10px; }
                .profile-card { width: 100%; max-width: 100%; }
                
                /* Back Button */
                .mobile-back-btn { 
                    display: block; width: 100%; margin-bottom: 15px;
                }
                .mobile-back-btn a {
                    display: block; width: 100%; padding: 12px; text-align: center;
                    background-color: <?php echo $backgroundNavbar; ?>;
                    color: <?php echo $textNavbar; ?>;
                    border: 1px solid <?php echo $tableBorderColor; ?>;
                    border-radius: 4px; text-decoration: none; font-weight: bold; box-sizing: border-box;
                    font-family: 'Source Sans Pro', sans-serif;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="SmallHeader shLeft noMob">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
                <div class="SmallHeader shRight noMob">
                    <div id="CheckUpdate"><?php include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php'; ?></div><br />
                </div>
                <h1>WPSD <?php echo __(( 'Dashboard' )); ?> - Profile Manager</h1>
                <div class="navbar">
                    <script type= "text/javascript">
                    window.time_format = '<?php echo constant("TIME_FORMAT"); ?>';
                    function reloadDateTime(){
                      $( '#timer' ).html( _getDatetime( window.time_format ) );
                        setTimeout(reloadDateTime,1000);
                      }
                      reloadDateTime();
                    </script>
                    <div class="headerClock"><span id="timer"></span></div>
                    <a class="menuconfig noMob" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
                    <a class="menuadmin noMob" href="/admin/"><?php echo __( 'Admin' );?></a>
                    <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
                </div>
            </div>

            <div class="profile-wrapper">
                
                <div class="mobile-back-btn">
                    <a href="/"><i class="fa fa-chevron-left"></i> Back to Dashboard</a>
                </div>

                <?php if (!empty($_POST)) { ?>
                    <div style="width: 100%; max-width: 600px; margin-bottom: 20px;">
                    <?php
                    // --- SAVE LOGIC ---
                    if ( escapeshellcmd($_POST["save_current_config"]) || escapeshellcmd($_POST['current_profile'] )) {
                        $desc = isset($_POST["save_current_config"]) ? $_POST['config_desc'] : $_POST['current_profile'];
                        
                        if ($desc == "") {
                            echo '<div class="profile-alert profile-alert-error"><i class="fa fa-times-circle"></i> You need to provide a Profile Description!<br>Page reloading...</div>';
                            echo '<script>setTimeout("location.href = \''.$_SERVER["PHP_SELF"].'\'", 3000);</script>';
                        } else if (!preg_match('/^[a-zA-Z0-9\s]+$/', $desc)) {
                            echo '<div class="profile-alert profile-alert-error"><i class="fa fa-ban"></i> Non-Alpha-Numeric characters are not permitted.<br>Page reloading...</div>';
                            echo '<script>setTimeout("location.href = \''.$_SERVER["PHP_SELF"].'\'", 3000);</script>';
                        } else {
                            $desc = str_replace(' ', '_', $desc);
                            $desc_friendly = str_replace("_", " ", $desc);
                            exec("sudo mkdir -p /etc/WPSD_config_mgr/$desc > /dev/null");
                            $profileDir = "/etc/WPSD_config_mgr/$desc";
                            exec("sudo rm -rf $profileDir > /dev/null");
                            exec("sudo mkdir $profileDir > /dev/null");
                            
                            // Copy Configs
                            $configs_to_copy = array(
                                '/etc/dhcpcd.conf', '/etc/wpa_supplicant/wpa_supplicant.conf', '/etc/wpsd-upnp-rules',
                                '/etc/WPSD-Dashboard-Config.ini', '/etc/hostapd/hostapd.conf', '/etc/*css.ini',
                                '/etc/aprsgateway', '/etc/ircddbgateway', '/etc/mmdvmhost', '/etc/dapnetgateway',
                                '/etc/p25gateway', '/etc/ysfgateway', '/etc/dmr2nxdn', '/etc/dmr2ysf', '/etc/nxdn2dmr',
                                '/etc/ysf2dmr', '/etc/dgidgateway', '/etc/nxdngateway', '/etc/ysf2nxdn', '/etc/ysf2p25',
                                '/etc/dmrgateway', '/etc/starnetserver', '/etc/timeserver', '/etc/dstar-radio.*',
                                '/etc/pistar-remote', '/etc/hosts', '/etc/hostname', '/etc/bmapi.key', 
                                '/etc/wpsd-bm-config.json', '/etc/dapnetapi.key', '/etc/default/gpsd', 
                                '/etc/*_paused', '/etc/.CALLERDETAILS', '/etc/.TGNAMES', '/usr/local/etc/RSSI.dat'
                            );
                            
                            if (exec('cat /etc/dhcpcd.conf | grep "static ip_address" | grep -v "#"')) {
                                exec("sudo cp /etc/dhcpcd.conf $profileDir > /dev/null");
                            }
                            
                            foreach($configs_to_copy as $file) {
                                exec("sudo cp $file $profileDir > /dev/null 2>&1");
                            }
                            
                            exec("sudo sh -c 'cp -a /root/*Hosts.txt' $profileDir > /dev/null");
                            
                            if (isDVmegaCast() == 1) {
                                exec("sudo mkdir -p $profileDir/cast-settings > /dev/null");
                                exec("sudo sh -c 'cp -a \"/usr/local/cast/etc/\"* \"$profileDir/cast-settings/\"' > /dev/null");
                            }
                            exec("sudo sh -c \"echo $desc > /etc/.WPSD_config\"");
                            
                            echo '<div class="profile-alert profile-alert-success"><i class="fa fa-check-square"></i> Saved Profile: <strong>'.$desc_friendly.'</strong><br>Page reloading...</div>';
                            echo '<script>setTimeout("location.href = \''.$_SERVER["PHP_SELF"].'\'", 3000);</script>';
                        }
                    } 
                    // --- RESTORE LOGIC ---
                    else if ( escapeshellcmd($_POST["restore_config"]) ) {
                        if (empty($_POST['configs'])) {
                             echo '<div class="profile-alert profile-alert-error">No profile selected!</div>';
                             echo '<script>setTimeout("location.href = \''.$_SERVER["PHP_SELF"].'\'", 3000);</script>';
                        } else {
                            $resto = escapeshellarg($_POST['configs']);
                            $resto_friendly = str_replace("_", " ", $resto);
                            $profileDir = "/etc/WPSD_config_mgr/$resto";
                            
                            // Restore Ops
                            exec("sudo sh -c 'mv $profileDir/*.php /var/www/dashboard/config/' > /dev/null 2>&1");
                            exec("sudo sh -c 'cp -a $profileDir/*Hosts.txt /root/' > /dev/null");
                            
                            if (isDVmegaCast() == 1) {
                                exec("sudo mkdir -p /usr/local/cast/etc  > /dev/null");
                                exec("sudo sh -c 'cp -a $profileDir/cast-settings/* /usr/local/cast/etc/' > /dev/null");
                                exec('sudo chmod 775 /usr/local/cast/etc ; sudo chown -R www-data:pi-star /usr/local/cast/etc ; sudo chmod 664 /usr/local/cast/etc/*');
                                exec('sudo /usr/local/cast/sbin/RSET.sh  > /dev/null 2>&1 &');
                                exec('sudo /usr/local/cast/bin/cast-reset ; sleep 2 > /dev/null 2>/dev/null');
                            }
                            
                            exec("sudo sh -c 'rm -rf $profileDir/*Hosts.txt' > /dev/null");
                            exec("sudo sh -c 'mv $profileDir/pistar-css.ini /etc/wpsd-css.ini' > /dev/null 2>&1");
                            exec("sudo sh -c 'cp -a $profileDir/* /etc/' > /dev/null");
                            exec("sudo sh -c 'cp -a $profileDir/.CALLERDETAILS /etc/' > /dev/null");
                            exec("sudo sh -c 'cp -a $profileDir/.TGNAMES /etc/' > /dev/null");
                            exec("sudo chown www-data:www-data /var/www/dashboard/ > /dev/null");
                            exec("sudo sh -c 'cp -a /root/*Hosts.txt $profileDir' > /dev/null");
                            exec("sudo sh -c \"echo ".$_POST['configs']." > /etc/.WPSD_config\"");
                            exec("sudo wpsd-services restart > /dev/null &");
                            
                            echo '<div class="profile-alert profile-alert-success"><i class="fa fa-check-square"></i> Switched to Profile: <strong>'.$resto_friendly.'</strong><br>Services restarting...<br><br>Loading Dashboard...</div>';
                            echo '<script>setTimeout("location.href = \'/\'", 5000);</script>';
                        }
                    }
                    // --- DELETE LOGIC ---
                    else if ( escapeshellcmd($_POST["remove_config"]) ) {
                        if (empty($_POST['delete_configs'])) {
                            echo '<div class="profile-alert profile-alert-error">No profile selected for deletion!</div>';
                            echo '<script>setTimeout("location.href = \''.$_SERVER["PHP_SELF"].'\'", 3000);</script>';
                        } else {
                            $del = escapeshellarg($_POST['delete_configs']);
                            $del_friendly = str_replace("_", " ", $del);
                            exec("sudo rm -rf /etc/WPSD_config_mgr/$del > /dev/null");
                            echo '<div class="profile-alert profile-alert-success"><i class="fa fa-check-square"></i> Deleted Profile: <strong>'.$del_friendly.'</strong><br>Page reloading...</div>';
                            echo '<script>setTimeout("location.href = \''.$_SERVER["PHP_SELF"].'\'", 3000);</script>';
                        }
                    }
                    ?>
                    </div>
                <?php } else { 
                    // PAUSED CHECK
                    $is_paused = glob('/etc/*_paused');
                    if (!empty($is_paused)) {
                        echo '<div class="profile-alert profile-alert-error"><h3><i class="fa fa-pause-circle"></i> Modes Paused</h3>One or more modes are paused. You must Resume them in the <a href="/admin/?func=mode_man">Instant Mode Manager</a> before managing profiles.</div>';
                    } else {
                ?>
                
                <div class="profile-card">
                    <div class="profile-header">Switch Profile</div>
                    <div class="profile-body">
                        <?php if (count(glob("$profile_dir/*")) == 0) { ?>
                            <p>No saved profiles found.</p>
                        <?php } else { ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <select name="configs" class="profile-select">
                                    <option value="" disabled selected>Select Profile...</option>
                                    <?php
                                    foreach ( glob("$profile_dir/*") as $dir ) {
                                        $profile_file = str_replace("$profile_dir/", "", $dir);
                                        $profile_file_friendly = str_replace("_", " ", $profile_file);
                                        echo "<option value='$profile_file'>$profile_file_friendly</option>\n";
                                    }
                                    ?>
                                </select>
                                <input type="submit" name="restore_config" value="Switch to Profile" class="profile-btn">
                            </form>
                            <div class="profile-helper"><i class='fa fa-info-circle'></i> Instantly switch to a saved profile.</div>
                        <?php } ?>
                    </div>
                </div>

                <div class="profile-card desktop-only">
                    <div class="profile-header">Current Profile</div>
                    <div class="profile-body">
                        <?php echo str_replace("_", " ", $current_profile_display); ?>
                        
                        <?php if (!$no_raw_profile) { ?>
                            <hr style="width:100%; border:0; border-top:1px solid rgba(255,255,255,0.1); margin:15px 0;">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="current_profile" value="<?php echo $current_profile_friendly; ?>">
                                <button type="submit" name="running_config" class="profile-btn">Quick Save Over Current</button>
                            </form>
                            <div class="profile-helper"><i class="fa fa-info-circle"></i> Updates the current profile with current settings.</div>
                        <?php } ?>
                    </div>
                </div>

                <div class="profile-card desktop-only">
                    <div class="profile-header">Save New Profile</div>
                    <div class="profile-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="text" placeholder="Enter Description..." name="config_desc" class="profile-input" maxlength="255">
                            <input type="submit" name="save_current_config" value="Save New Profile" class="profile-btn">
                        </form>
                        <div class="profile-helper"><i class='fa fa-save'></i> Save current settings as a new profile.<br>(Spaces permitted)</div>
                    </div>
                </div>

                <div class="profile-card desktop-only">
                    <div class="profile-header">Delete Profile</div>
                    <div class="profile-body">
                        <?php if (count(glob("$profile_dir/*")) == 0) { ?>
                            <p>No profiles to delete.</p>
                        <?php } else { ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="del_configs">
                                <select name="delete_configs" class="profile-select">
                                    <option value="" disabled selected>Select Profile to Delete...</option>
                                    <?php
                                    foreach ( glob("$profile_dir/*") as $dir ) {
                                        $profile_file = str_replace("$profile_dir/", "", $dir);
                                        $profile_file_friendly = str_replace("_", " ", $profile_file);
                                        echo "<option value='$profile_file'>$profile_file_friendly</option>\n";
                                    }
                                    ?>
                                </select>
                                <input type="submit" name="remove_config" value="Delete Profile" class="profile-btn profile-btn-delete" onclick="return confirm('Are you sure you want to delete this profile?');">
                            </form>
                        <?php } ?>
                    </div>
                </div>
                
                <?php } // End paused check ?>
                <?php } // End !POST ?>
            </div>
            
            <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
        </div>
    </body>
</html>
<?php
}
?>
