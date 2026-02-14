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

// Load the language support
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
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
        <title>WPSD Dashboard - Advanced Editor</title>
        <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
        <?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
    </head>
    <body>
        <div class="container">
            <?php include './header-menu.inc'; ?>
            <div class="contentwide">
                <?php
                if (strpos($editorname, 'NetworkManager') !== false) {
                    // NetworkManager handling
                    // Get all .nmconnection files
                    $connectionFiles = glob($configDirectory . '*.nmconnection');

                    // Check if we need to display a dropdown (more than one connection file)
                    if (count($connectionFiles) > 1) {
                        // Store the previously selected profile across form submissions
                        if (isset($_POST['selected_profile']) && !empty($_POST['selected_profile'])) {
                            $configfile = $_POST['selected_profile'];
                            // Store in a hidden field to maintain selection when submitting edits
                            $selectedProfile = $configfile;
                        } else if (isset($_POST['profile_path']) && !empty($_POST['profile_path'])) {
                            // Recover from edit submission
                            $configfile = $_POST['profile_path'];
                            $selectedProfile = $configfile;
                        } else {
                            // Default to null (will show placeholder)
                            $configfile = null;
                            $selectedProfile = "";
                        }

                        // Display dropdown for profile selection
                        echo '<form method="POST" id="profileSelector">';
                        echo '<select name="selected_profile" onchange="this.form.submit()">';

                        // Add placeholder option
                        echo '<option value="" ' . (empty($selectedProfile) ? 'selected' : '') . ' disabled>Select network...</option>';

                        foreach ($connectionFiles as $file) {
                            $selected = ($file == $selectedProfile) ? 'selected' : '';
                            $filename = basename($file);
                            // Remove .nmconnection from the displayed name
                            $displayName = str_replace('.nmconnection', '', $filename);
                            echo "<option value=\"$file\" $selected>$displayName</option>";
                        }

                        echo '</select>';
                        echo '</form>';

                        // If no profile is selected yet, don't proceed with editing
                        if (empty($selectedProfile)) {
                            echo '<p>Please select a network connection profile to edit.</p>';
                            exit;
                        }
                    } else if (count($connectionFiles) == 1) {
                        // Only one profile, no need for dropdown
                        $configfile = $connectionFiles[0];
                        $selectedProfile = $configfile;
                    } else {
                        // No connection files found
                        echo "<p>No NetworkManager connection profiles found in $configDirectory</p>";
                        exit;
                    }
                }

                // File Wrangling
                exec('sudo cp '.$configfile.' '.$tempfile);
                exec('sudo chown www-data:www-data '.$tempfile);
                exec('sudo chmod 664 '.$tempfile);

                if (isset($_POST['data'])) {
                    // Open the file and write the data
                    $filepath = $tempfile;
                    $fh = fopen($filepath, 'w');
                    $data = str_replace("\r", "", $_POST['data']);

                    if (function_exists('process_before_saving')) {
                        process_before_saving($data);
                    }

                    fwrite($fh, $data);
                    fclose($fh);

                    exec('sudo cp '.$tempfile.' '.$configfile);
                    exec('sudo chmod 644 '.$configfile);
                    exec('sudo chown root:root '.$configfile);

                    // Reload the affected daemon
                    if (isset($servicenames) && (count($servicenames) > 0)) {
                        foreach ($servicenames as $servicename) {
                            exec('sudo systemctl restart '.$servicename); // Reload the daemon
                        }
                    }
                }

                $theData = file_exists($tempfile)? file_get_contents($tempfile): "";

                if (strpos($editorname, 'Hosts') !== false) { // if it's a host file edit, update the hostfiles...
                    exec('sudo env FORCE=1 /usr/local/sbin/wpsd-hostfile-update > /dev/null 2>&1 &');
                }
                ?>

                <form name="test" method="post" action="">
                    <label for="data" class="header" style="display:block;text-align:center;"><?php echo $editorname ?></label>
                    <textarea id="data" name="data" class="fulledit"><?php echo $theData; ?></textarea><br />
                    <?php if (strpos($editorname, 'NetworkManager') !== false): ?>
                    <input type="hidden" name="profile_path" value="<?php echo htmlspecialchars($selectedProfile); ?>" />
                    <?php endif; ?>
                    <input type="submit" name="submit" value="<?php echo __('Apply Changes'); ?>" />
                </form>

            </div>
            <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
        </div>
    </body>
</html>
