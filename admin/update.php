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

// Force the Locale to the stock locale just while we run the update
setlocale(LC_ALL, "LC_CTYPE=en_GB.UTF-8;LC_NUMERIC=C;LC_TIME=C;LC_COLLATE=C;LC_MONETARY=C;LC_MESSAGES=C;LC_PAPER=C;LC_NAME=C;LC_ADDRESS=C;LC_TELEPHONE=C;LC_MEASUREMENT=C;LC_IDENTIFICATION=C");

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
if ($_SERVER["PHP_SELF"] == "/admin/update.php") {

  if (!isset($_GET['ajax'])) {
    if (!file_exists('/var/log/pi-star')) {
      system('sudo mkdir -p /var/log/pi-star/');
      system('sudo chmod 775 /var/log/pi-star/');
      system('sudo chown root:mmdvm /var/log/pi-star/');
    }
    system('sudo touch /var/log/pi-star/WPSD-update.log > /dev/null 2>&1 &');
    system('sudo echo "" > /var/log/pi-star/WPSD-update.log > /dev/null 2>&1 &');
    system('sudo /usr/local/sbin/wpsd-update > /dev/null 2>&1 &');
  }

  // Sanity Check Passed.
  header('Cache-Control: no-cache');

  if (!isset($_GET['ajax'])) {
    if (file_exists('/var/log/pi-star/WPSD-update.log')) {
      $_SESSION['update_offset'] = filesize('/var/log/pi-star/WPSD-update.log');
    } else {
      $_SESSION['update_offset'] = 0;
    }
  }

  if (isset($_GET['ajax'])) {
    if (!file_exists('/var/log/pi-star/WPSD-update.log')) {
      exit();
    }

    $handle = fopen('/var/log/pi-star/WPSD-update.log', 'rb');
    if (isset($_SESSION['update_offset'])) {
      fseek($handle, 0, SEEK_END);
      if ($_SESSION['update_offset'] > ftell($handle)) //log rotated/truncated
        $_SESSION['update_offset'] = 0; //continue at beginning of the new log
      $data = stream_get_contents($handle, -1, $_SESSION['update_offset']);
      $_SESSION['update_offset'] += strlen($data);
      echo nl2br($data);
    }
    else {
      fseek($handle, 0, SEEK_END);
      $_SESSION['update_offset'] = ftell($handle);
    }
    exit(); // Stop here if it's an AJAX call
  }

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
    <title>WPSD <?php echo __( 'Dashboard' )." - ".__( 'WPSD Update' );?></title>
    <?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/jquery-timing.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>

    <style>
      .update-container {
        max-width: 900px;
        margin: 20px auto;
        
        background: <?php echo $backgroundContent; ?>;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        color: <?php echo $textContent; ?>;
        
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        padding: 40px;
        text-align: center;
      }

      .update-icon {
        font-size: 60px;
        color: #2196F3;
        margin-bottom: 20px;
        transition: color 0.5s ease;
      }
      
      .update-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
        color: <?php echo $textContent; ?>;
      }
      
      .update-status {
        font-size: 16px;
        color: <?php echo $textContent; ?>;
        opacity: 0.8;
        margin-bottom: 30px;
        min-height: 24px;
        font-weight: 500;
      }
      
      .progress-wrapper {
        background-color: <?php echo $tableRowOddBg; ?>; 
        border-radius: 20px;
        height: 30px;
        width: 100%;
        margin-bottom: 30px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.3);
      }
      
      .progress-bar {
        background-color: #2196F3;
        height: 100%;
        width: 5%; 
        border-radius: 20px;
        transition: width 0.8s ease-in-out;
        background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
        background-size: 1rem 1rem;
        animation: progress-bar-stripes 1s linear infinite;
        
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: 16px;
        text-shadow: 0 1px 4px rgba(0,0,0,0.9);
      }
      
      @keyframes progress-bar-stripes {
        0% { background-position: 1rem 0; }
        100% { background-position: 0 0; }
      }
      
      .update-complete .progress-bar {
        background-color: <?php echo $backgroundServiceCellActiveColor; ?>;
        animation: none;
        background-image: none;
      }
      .update-complete .update-icon {
        color: <?php echo $backgroundServiceCellActiveColor; ?>;
      }

      .update-failed .progress-bar {
        background-color: <?php echo $backgroundServiceCellInactiveColor; ?>;
        animation: none;
        background-image: none;
      }
      .update-failed .update-icon {
        color: <?php echo $backgroundServiceCellInactiveColor; ?>;
      }

      .btn-details {
        background: transparent;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        padding: 8px 16px;
        border-radius: 4px;
        color: <?php echo $textContent; ?>;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        font-weight: 600;
      }
      .btn-details:hover {
        background: <?php echo $tableRowEvenBg; ?>;
        color: <?php echo $textContent; ?>;
      }

      #tail-wrapper {
        display: none;
        margin-top: 20px;
        text-align: left;
      }
      
      #tail {
        background: #000;
        color: #4DEEEA;
        padding: 15px;
        border-radius: 4px;
        border: 1px solid <?php echo $tableBorderColor; ?>;
        height: 300px;
        overflow-y: scroll;
        font-size: 16px;
        line-height: 1.4;
        scrollbar-width: auto;
      }

      #tail::-webkit-scrollbar {
        width: 12px;
        display: block;
      }
      #tail::-webkit-scrollbar-track {
        background: #000;
      }
      #tail::-webkit-scrollbar-thumb {
        background-color: #555;
        border-radius: 6px;
        border: 3px solid #000;
      }
      #tail::-webkit-scrollbar-thumb:hover {
        background-color: #888;
      }
    </style>

    <script type="text/javascript">
      window.time_format = '<?php echo constant("TIME_FORMAT"); ?>';
      $(function() {
        var updateCompleted = false;
        var currentProgress = 5; 
        
        $.repeat(1000, function() {
          $.get('/admin/update.php?ajax', function(data) {
            if (data.length < 1) return;
            
            var objDiv = document.getElementById("tail");
            var isScrolledToBottom = objDiv.scrollHeight - objDiv.clientHeight <= objDiv.scrollTop + 1;

            $('#tail').append(data);

            var lowerData = data.toLowerCase();
            var statusText = "";
            var newProgress = 0;

            // ---------------------------------------------------------
            // 1. PRIORITY CHECK: FAILURE
            // ---------------------------------------------------------
            if (lowerData.includes("cannot connect to the wpsd update system")) {
                statusText = "Error: Cannot connect to Update System.";
                updateCompleted = true; // Lock the status
                
                // Immediate Failure UI Update
                $('#ui-title').text('Update Failed');
                $('#ui-status').text('Update Server connectivity check failed. Please try again later.');
                $('.update-container').addClass('update-failed');
                $('.update-icon').html('<i class="fa fa-times-circle"></i>');
                $('.progress-bar').css('width', '100%'); 
                $('#progress-percent').text('Error');
            }

            // ---------------------------------------------------------
            // 2. Normal Progress Steps
            // NOTE: We use independent IFs here (no 'else') so that if 
            // the log buffer contains multiple steps at once (e.g. Connectivity OK + Utilities),
            // the code falls through and updates to the latest step immediately.
            // ---------------------------------------------------------
            else if (!updateCompleted) {
                
                if (lowerData.includes("checking connectivity to the wpsd update system")) {
                    statusText = "Verifying Connectivity to the WPSD Update Server...";
                    newProgress = 10;
                }
                
                if (lowerData.includes("updating wpsd utilities and support programs")) {
                    statusText = "Updating WPSD System Utilities...";
                    newProgress = 20;
                }
                
                if (lowerData.includes("new version of the updater available")) {
                    statusText = "New version of the Updater available. Performing self-update...";
                    newProgress = 30;
                }
                
                if (lowerData.includes("updating wpsd web dashboard software")) {
                    statusText = "Updating Dashboard Interface...";
                    newProgress = 50;
                }
                
                if (lowerData.includes("updating hostfiles")) {
                    statusText = "Updating ID Databases & Hostfiles...";
                    newProgress = 65;
                }
                
                if (lowerData.includes("updating wpsd digital voice-related binaries")) {
                    statusText = "Updating Digital Voice Binaries...";
                    newProgress = 80;
                }
                
                if (lowerData.includes("updating wpsd dvmega cast software")) {
                    statusText = "Updating DVMega Software...";
                    newProgress = 85;
                }
                
                if (lowerData.includes("performing maintenance tasks")) {
                    statusText = "Performing System Maintenance...";
                    newProgress = 95;
                }

                // Apply Updates
                if(statusText !== "") {
                    $('#ui-status').text(statusText);
                }

                if(newProgress > currentProgress) {
                    currentProgress = newProgress;
                    $('.progress-bar').css('width', currentProgress + '%');
                    $('#progress-percent').text(currentProgress + '%');
                }
            }

            // ---------------------------------------------------------
            // 3. Completion Check (Success)
            // ---------------------------------------------------------
            if (data.includes("Update Process Finished") && !updateCompleted) {
              updateCompleted = true;
              
              $('#ui-title').text('Update Completed Successfully');
              $('#ui-status').text('All updates have been installed.');
              $('.update-container').addClass('update-complete');
              $('.update-icon').html('<i class="fa fa-check-circle"></i>');
              $('.progress-bar').css('width', '100%'); 
              $('#progress-percent').text('100%');
            }

            if (isScrolledToBottom)
              objDiv.scrollTop = objDiv.scrollHeight;
          });
        });

        $('#toggle-log').click(function() {
            $('#tail-wrapper').slideToggle();
            var text = $(this).text();
            $(this).text(text == "Show Details" ? "Hide Details" : "Show Details");
        });

      });
    </script>
  </head>
  <body>
      <div class="container">
    <div class="header">
       <div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
         <?php if ($_SESSION['CURRENT_PROFILE']) { ?><div class="SmallHeader shLeft noMob"> | <?php echo __( 'Current Profile' ).": ";?> <?php echo $_SESSION['CURRENT_PROFILE']; ?></div><?php } ?>
         <div class="SmallHeader shRight noMob">
               <div id="CheckUpdate"><?php echo $version; ?></div><br />
             </div>
             <h1>WPSD <?php echo __( 'Dashboard' )." - ".__( 'WPSD Update' );?></h1>
             <div class="navbar">
              <script type= "text/javascript">
                 function reloadDateTime(){
                   $( '#timer' ).html( _getDatetime( window.time_format ) );
                   setTimeout(reloadDateTime,1000);
                 }
                 reloadDateTime();
              </script>
              <div class="headerClock">
                <span id="timer"></span>
            </div>
        <a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
        <a class="menubackup" href="/admin/config_backup.php"><?php echo __( 'Backup/Restore' );?></a>
        <a class="menupower" href="/admin/power.php"><?php echo __( 'Power' );?></a>
        <a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
        <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
    </div>
  </div>
  
  <div class="contentwide">
      
      <div class="update-container">
          <div class="update-icon"><i class="fa fa-cog fa-spin"></i></div>
          <div class="update-title" id="ui-title">Running WPSD Update</div>
          <div class="update-status" id="ui-status">Initializing update process...</div>
          
          <div class="progress-wrapper">
              <div class="progress-bar">
                  <span id="progress-percent">5%</span>
              </div>
          </div>

          <button id="toggle-log" class="btn-details">Show Details</button>

          <div id="tail-wrapper">
              <div id="tail"></div>
          </div>
      </div>

  </div>

<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
  </div>
  </body>
  </html>

<?php
} 
?>
