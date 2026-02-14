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

require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

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

if ($_SERVER["PHP_SELF"] == "/admin/power.php") {
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
    <meta name="language" content="English" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
    <meta http-equiv="Expires" content="0" />
    <title>WPSD <?php echo __( 'Dashboard' )." - ".__( 'Power' );?></title>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
    <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
    
    <style>
        .power-container {
            max-width: 800px;
            margin: 30px auto;
            background: <?php echo $tableRowOddBg; ?>;
            border: 1px solid <?php echo $tableBorderColor; ?>;
            color: <?php echo $textContent; ?>;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            padding: 40px;
            text-align: center;
        }

        .power-icon-large {
            font-size: 60px;
            color: <?php echo $textContent; ?>;
            margin-bottom: 20px;
        }

        .power-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            color: <?php echo $textContent; ?>;
        }

        .power-actions {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .power-btn {
            background: transparent;
            border: 2px solid <?php echo $tableBorderColor; ?>;
            border-radius: 10px;
            padding: 20px;
            width: 200px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: <?php echo $textContent; ?>;
        }

        .power-btn:hover {
            background: <?php echo $tableRowEvenBg; ?>;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-color: <?php echo $textContent; ?>;
            color: inherit;
        }

        .power-btn i {
            font-size: 48px;
            margin-bottom: 15px;
            color: <?php echo $textLinks; ?>;
        }
        
        .power-btn.shutdown:hover {
            border-color: <?php echo $backgroundServiceCellInactiveColor; ?>;
            color: inherit;
        }
        .power-btn.shutdown i {
            color: <?php echo $backgroundServiceCellInactiveColor; ?>;
        }

        .power-btn span {
            font-size: 18px;
            font-weight: 600;
        }

        #status-overlay {
            display: none;
        }
        
        .progress-wrapper {
            background-color: <?php echo $tableRowOddBg; ?>; 
            border-radius: 20px;
            height: 30px;
            width: 100%;
            margin-bottom: 20px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.3);
        }
        
        .progress-bar {
            background-color: #2196F3;
            height: 100%;
            width: 100%;
            border-radius: 20px;
            background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
            background-size: 1rem 1rem;
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            0% { background-position: 1rem 0; }
            100% { background-position: 0 0; }
        }

        .status-text {
            font-size: 16px;
            color: <?php echo $textContent; ?>;
            margin-bottom: 20px;
            opacity: 0.8;
        }
    </style>

    <script type="text/javascript">
      $.ajaxSetup({ cache: false });
      window.time_format = '<?php echo constant("TIME_FORMAT"); ?>';

      function triggerReboot() {
          $('.power-actions').hide();
          $('#status-overlay').fadeIn();
          
          $('#status-title').text('System Rebooting');
          $('#status-message').text('The system is restarting. Please wait...');
      }
    </script>
</head>
<body>
    <div class="container">
    <div class="header">
        <div class="SmallHeader shLeft noMob">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
        <?php if ($_SESSION['CURRENT_PROFILE']) { ?><div class="SmallHeader shLeft noMob"> | <?php echo __( 'Current Profile' ).": ";?> <?php echo $_SESSION['CURRENT_PROFILE']; ?></div><?php } ?>
        <div class="SmallHeader shRight noMob">
            <div id="CheckUpdate">
            <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php'; ?>
            </div><br />
        </div>
        <h1>WPSD <?php echo __(( 'Dashboard' )) . " - ".__( 'Power' );?></h1>
        <div class="navbar">
            <script type= "text/javascript">
            function reloadDateTime(){
                $( '#timer' ).html( _getDatetime( window.time_format ) );
                setTimeout(reloadDateTime,1000);
            }
            reloadDateTime();
            </script>
            <div class="headerClock"><span id="timer"></span></div>
            <a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
            <a class="menubackup noMob" href="/admin/config_backup.php"><?php echo __( 'Backup/Restore' );?></a>
            <a class="menuupdate noMob" href="/admin/update.php"><?php echo __( 'WPSD Update' );?></a>
            <a class="menuadmin noMob" href="/admin/"><?php echo __( 'Admin' );?></a>
            <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
        </div>
    </div>

    <div class="contentwide">
        
        <div class="power-container">
            
            <?php 
            if (!empty($_POST) && isset($_POST["action"])) {
                $action = escapeshellcmd($_POST["action"]);
                
                if ($action === "reboot") {
                    exec("sudo sync && sleep 2 && sudo reboot > /dev/null 2>&1 &");
                    ?>
                    <div class="power-icon-large"><i class="fa fa-refresh fa-spin"></i></div>
                    <div class="power-title">System Rebooting</div>
                    <div class="status-text" id="reboot-status">Waiting for system to go offline...</div>
                    
                    <div class="progress-wrapper">
                        <div class="progress-bar"></div>
                    </div>
                    
                    <script>
                        var offline = false;
                        function check_internet_connection(){
                            $.ajax({
                                type: "GET",
                                url: "/api/",
                                timeout: 2000,
                                success: function(data, status, xhr) {
                                    if (offline) {
                                        // We were offline, now we are back!
                                        $("#reboot-status").text("System is online. Redirecting...");
                                        $(".fa-refresh").removeClass("fa-spin");
                                        setTimeout(() => { location.href = "/"; }, 1500);
                                    }
                                },
                                error: function(output) {
                                    // Connection failed = System is offline (Rebooting)
                                    if (!offline) {
                                        offline = true;
                                        $("#reboot-status").text("System is restarting. Please wait...");
                                    }
                                }
                            });
                        }
                        setInterval(check_internet_connection, 1500);
                    </script>
                    <?php
                }
                
                else if ($action === "shutdown") {
                    exec("sudo sync && sleep 3 && sudo shutdown -h now > /dev/null 2>&1 &");
                    ?>
                    <div class="power-icon-large" style="color: <?php echo $backgroundServiceCellInactiveColor; ?>"><i class="fa fa-power-off"></i></div>
                    <div class="power-title">Shutting Down</div>
                    <div class="status-text">
                        System is halting. Please wait at least 30 seconds before removing power.
                    </div>
                    <div class="progress-wrapper">
                        <div class="progress-bar" style="background-color: <?php echo $backgroundServiceCellInactiveColor; ?>; width: 100%;"></div>
                    </div>
                    <?php
                }
                
                unset($_POST);
                
            } else { 
            ?>
                <div class="power-icon-large"><i class="fa fa-bolt"></i></div>
                <div class="power-title"><?php echo __( 'Power Control' );?></div>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="power-actions">
                        
                        <button type="submit" name="action" value="reboot" class="power-btn" onclick="return confirm('Are you sure you want to reboot the system?');">
                            <i class="fa fa-refresh"></i>
                            <span>Reboot</span>
                        </button>
                        
                        <button type="submit" name="action" value="shutdown" class="power-btn shutdown" onclick="return confirm('Are you sure you want to SHUT DOWN the system?');">
                            <i class="fa fa-power-off"></i>
                            <span>Shutdown</span>
                        </button>

                    </div>
                </form>
            <?php } ?>

        </div>

    </div>
    
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
    </div>
</body>
</html>
<?php
}
?>
