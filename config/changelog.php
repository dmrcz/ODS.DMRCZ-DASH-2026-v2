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

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/config/changelog.php") {
    // Sanity Check Passed.
    header('Cache-Control: no-cache');
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
	    <title>WPSD ChangeLog</title>
	    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
        <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
        <script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
        <script type="text/javascript">
          $.ajaxSetup({ cache: false });
        </script>
<style type="text/css">
.cl_wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-height: 100vh;
}

.ChangeLogData {
  font-size: 1em;
  padding: 1em;
  font-family: 'Inconsolata', monospace;
  background-color: black;
  color: lightgray;
  text-align: left;
  width: 75%
}

.foreground-1 { color: #ff002f; }
.foreground-2 { color: #30fe00; }
.foreground-3 { color: #e3ff00; }
.foreground-4 { color: #4d4dff; font-weight:bold; }
.foreground-5 { color: #ff32ff; }
.foreground-6 { color: #00ffff; }
.foreground-7 { color: white; }

.bold.foreground-1 { color: #ff002f; font-weight:bold; }
.bold.foreground-2 { color: #30fe00; font-weight:bold; }
.bold.foreground-3 { color: #e3ff00; font-weight:bold; }
.bold.foreground-4 { color: #4d4dff; font-weight:bold; }
.bold.foreground-5 { color: #ff32ff; font-weight:bold; }
.bold.foreground-6 { color: #00ffff; font-weight:bold; }
.bold.foreground-7 { color: white; font-weight:bold; }

</style>
	</head>
	<body>
	    <div class="container">
		<div class="header">
		    <div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
		    <div class="SmallHeader shRight">
		       <div id="CheckUpdate">
		       <?php
		          include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php';
		       ?>
		       </div><br />
		    </div>
		    <h1>WPSD ChangeLog</h1>
		    <p>
			<div class="navbar">
			    <a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
			    <a class="menubackup" href="/admin/config_backup.php"><?php echo __( 'Backup/Restore' );?></a>
			    <a class="menuupdate" href="/admin/update.php"><?php echo __( 'WPSD Update' );?></a>
			    <a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
			    <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
			</div>
		    </p>
		</div>
		<div class="contentwide">

                  <div class="divTable">
                    <div class="divTableBody">
                      <div class="divTableRow">
                        <div class="divTableCellSans">
                          <p><b>The Last 15 Changes/Commits of the Dashboard Code:</b></p>
                            <div class="cl_wrapper">
			      <div class="ChangeLogData"> 
				<?php
				  $uaStr="WPSD-ChangeLog-Viewer";
				  $out = shell_exec('/usr/local/bin/WPSD-CL-to-html /var/www/dashboard');
				  $out = str_replace("\n", "<br />", $out);
				  echo $out;
				?>
			      </div>
			    </div>
			    <div style="text-align:center;">
			      <a href="https://wpsd-swd.w0chp.net/WPSD-SWD/WPSD-WebCode/graph?branch=refs%2Fheads%2Fmaster" target="_blank" style="text-decoration:underline;color:inherit;">(View the entire change/commit history...)</a>
			    </div>
			</div>
		      </div>
		    </div>
		  </div>

                  <div class="divTable">
                    <div class="divTableBody">
                      <div class="divTableRow">
                        <div class="divTableCellSans">
                          <p><b>The Last 15 Changes/Commits of Scripts and Programs Code:</b></p>
                            <div class="cl_wrapper">
                              <div class="ChangeLogData"> 
                                <?php
                                  $uaStr="WPSD-ChangeLog-Viewer";
                                  $out = shell_exec('/usr/local/bin/WPSD-CL-to-html /usr/local/sbin');
                                  $out = str_replace("\n", "<br />", $out);
                                  echo $out;
                                ?>
                              </div>
                            </div>
                            <div style="text-align:center;">
                              <a href="https://wpsd-swd.w0chp.net/WPSD-SWD/WPSD-Scripts/graph?branch=refs%2Fheads%2Fmaster" target="_blank" style="text-decoration:underline;color:inherit;">(View the entire change/commit history...)</a>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
<?php
if (isDVmegaCast() == 1) {
?>
                  <div class="divTable">
                    <div class="divTableBody">
                      <div class="divTableRow">
                        <div class="divTableCellSans">
                          <p><b>The Last 15 Changes/Commits of the DVMEGA Cast Code:</b></p>
                            <div class="cl_wrapper">
                              <div class="ChangeLogData"> 
                                <?php
                                  $uaStr="WPSD-ChangeLog-Viewer";
                                  $out = shell_exec('/usr/local/bin/WPSD-CL-to-html /opt/cast');
                                  $out = str_replace("\n", "<br />", $out);
                                  echo $out;
                                ?>
                              </div>
                            </div>
                            <div style="text-align:center;">
                              <a href="https://wpsd-swd.w0chp.net/WPSD-SWD/DVMega-Cast/graph?branch=refs%2Fheads%2Fmaster" target="_blank" style="text-decoration:underline;color:inherit;">(View the entire change/commit history...)</a>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
<?php
}
?>

		</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>	
	    </div>
	</body>
    </html>
<?php
}