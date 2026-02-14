<?php
session_set_cookie_params(0, "/");
session_name("WPSD_Session");
session_id('wpsdsession');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
unset($_SESSION['WPSDdashConfig']);
unset($_SESSION['MMDVMHostConfigs']);
checkSessionValidity();

$displayType = getConfigItem("General", "Display", $_SESSION['MMDVMHostConfigs']);

$themes_filepath = $_SERVER['DOCUMENT_ROOT'].'/includes/wpsd-themes.json';
$themes = [];

if (file_exists($themes_filepath)) {
    $json_content = file_get_contents($themes_filepath);
    $decoded_themes = json_decode($json_content, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_themes)) {
        $themes = $decoded_themes;
    } else {
        error_log("WPSD Dashboard: Error decoding wpsd-themes.json or it's not a valid array.");
        $themes = [];
    }
} else {
    error_log("WPSD Dashboard: wpsd-themes.json not found at " . $themes_filepath);
    $themes = [];
}

$default_theme_key = 'WPSD Light';

$filepath_ini = '/etc/wpsd-css.ini';
$parsed_ini = null;
$use_default_default_for_ini_file = false;

if (file_exists($filepath_ini)) {
    $parsed_ini_content = parse_ini_file($filepath_ini, true);
    if ($parsed_ini_content === false || empty($parsed_ini_content)) {
        error_log("WPSD Dashboard: /etc/wpsd-css.ini is unparseable or empty. Using 'WPSD Light' theme as default and attempting to recreate file.");
        $use_default_default_for_ini_file = true;
    } else {
        $parsed_ini = $parsed_ini_content;
    }
} else {
    error_log("WPSD Dashboard: /etc/wpsd-css.ini not found. Using 'WPSD Light' theme as default and creating file.");
    $use_default_default_for_ini_file = true;
}

if ($use_default_default_for_ini_file) {
    if (isset($themes[$default_theme_key])) {
        $parsed_ini = $themes[$default_theme_key];

        $content = "";
        foreach ($themes[$default_theme_key] as $section => $values) {
            if (!is_array($values)) continue;
            $content .= "[" . $section . "]\n";
            foreach ($values as $key => $value) {
                $content .= $key . "=" . $value . "\n";
            }
            $content .= "\n";
        }

        $temp_ini_path = "/tmp/wpsd_default_default.ini";
        if (file_put_contents($temp_ini_path, $content) !== false) {
            exec('sudo cp ' . escapeshellarg($temp_ini_path) . ' ' . escapeshellarg($filepath_ini));
            exec('sudo chmod 644 ' . escapeshellarg($filepath_ini));
            exec('sudo chown root:root ' . escapeshellarg($filepath_ini));
            error_log("WPSD Dashboard: /etc/wpsd-css.ini has been created/overwritten with 'WPSD Light' theme values.");
        } else {
            error_log("WPSD Dashboard: Failed to write temporary INI file for 'WPSD Light' default at " . $temp_ini_path);
        }
    } else {
        error_log("WPSD Dashboard: CRITICAL - '{$default_theme_key}' theme is not defined in \$themes array. Cannot set default INI.");
        $parsed_ini = [];
    }
}

if (!is_array($parsed_ini)) {
    error_log("WPSD Dashboard: \$parsed_ini could not be initialized from file or 'WPSD Light' theme. Defaulting to empty array.");
    if(isset($themes[$default_theme_key])) {
        $parsed_ini = $themes[$default_theme_key];
    } else {
        $parsed_ini = [];
    }
}

function compare_color_settings($config_colors, $theme_colors) {
    if (!is_array($config_colors) || !is_array($theme_colors)) {
        return is_array($config_colors) === is_array($theme_colors) && empty($config_colors) && empty($theme_colors);
    }
    if (count($config_colors) !== count($theme_colors)) {
        return false;
    }
    return empty(array_diff_assoc($config_colors, $theme_colors));
}

function compare_extra_settings($config_extra, $theme_extra) {
    if (!is_array($config_extra) || !is_array($theme_extra)) {
        return is_array($config_extra) === is_array($theme_extra) && empty($config_extra) && empty($theme_extra);
    }
    if (count($config_extra) !== count($theme_extra)) {
        return false;
    }
    return empty(array_diff_assoc($config_extra, $theme_extra));
}

$ini_background_colors = isset($parsed_ini['Background']) ? $parsed_ini['Background'] : [];
$ini_text_colors = isset($parsed_ini['Text']) ? $parsed_ini['Text'] : [];
$ini_extra_settings = isset($parsed_ini['ExtraSettings']) ? $parsed_ini['ExtraSettings'] : [];
$selected_theme_on_load = "custom";

foreach ($themes as $theme_key => $theme_data) {
    $theme_background_colors = isset($theme_data['Background']) ? $theme_data['Background'] : [];
    $theme_text_colors = isset($theme_data['Text']) ? $theme_data['Text'] : [];
    $theme_extra_settings = isset($theme_data['ExtraSettings']) ? $theme_data['ExtraSettings'] : [];

    if (compare_color_settings($ini_background_colors, $theme_background_colors) &&
        compare_color_settings($ini_text_colors, $theme_text_colors) &&
        compare_extra_settings($ini_extra_settings, $theme_extra_settings)) {
        $selected_theme_on_load = $theme_key;
        break;
    }
}

// Retrieve TextSectionColor instead of BannersColor to ensure visibility on content background
$sectionTextColor = isset($parsed_ini['Text']['TextSectionColor']) ? $parsed_ini['Text']['TextSectionColor'] : '#000000';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
    <head>
        <meta name="language" content="English" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="pragma" content="no-cache" />
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
        <meta http-equiv="Expires" content="0" />
        <title>WPSD Dashboard - Appearance Settings</title>
        <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
        <script type="text/javascript" src="/css/farbtastic/farbtastic.min.js?version=<?php echo $versionCmd; ?>"></script>
        <link rel="stylesheet" type="text/css" href="/css/farbtastic/farbtastic.css" />
        <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
        
        <style id="wpsd-live-preview"></style>
        
        <?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
        
        <style type="text/css" media="screen">
            /* Styles strictly for layout and positioning. Colors inherited from WPSD global CSS. */
            
            /* Apply TextSectionColor to Headers and Labels for proper contrast against content background */
            h2.ConfSec, h3.ConfSec, .wpsd-label, .wpsd-setting-card label, .wpsd-info, .wpsd-flex-row label, .customizer-trigger {
                color: <?php echo $sectionTextColor; ?>;
            }

            .colorwell {
                width: 100%;
                max-width: 150px;
                text-align: center;
                cursor: pointer;
                font-weight: bold;
                padding: 5px;
                margin: 0 auto;
                display: block;
            }
            body .colorwell-selected {
                box-shadow: 0 0 5px rgba(0,0,0,0.5);
            }
            #colorpicker {
                position: absolute !important;
                z-index: 1000;
                /* Default fallback, overridden by JS */
                background-color: #f5f5f5; 
                padding: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.4);
            }

            /* Layout Containers */
            .wpsd-section {
                margin-bottom: 25px;
                padding: 10px;
                border-bottom: 1px solid rgba(128, 128, 128, 0.2);
            }

            .wpsd-flex-row {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 15px;
                margin-bottom: 10px;
            }

            .wpsd-label {
                font-weight: bold;
                min-width: 150px;
                text-align: right;
            }

            .wpsd-info {
                font-size: 0.9em;
                font-style: italic;
                opacity: 0.8;
                margin-left: 10px;
            }
            
            /* Customizer Toggle */
            .customizer-trigger {
                cursor: pointer;
                font-weight: bold;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                margin: 0;
                user-select: none;
            }
            .customizer-trigger i {
                transition: transform 0.3s ease;
            }
            .customizer-trigger.active i {
                transform: rotate(90deg);
            }

            /* Grid System for Color Settings */
            .wpsd-grid-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 15px;
                margin-top: 15px;
            }

            .wpsd-setting-card {
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                text-align: center;
                background: rgba(0,0,0,0.05);
                padding: 15px 10px;
                border-radius: 4px;
                border: 1px solid rgba(128, 128, 128, 0.1);
            }

            .wpsd-setting-card label {
                font-weight: bold;
                margin-bottom: 8px;
                display: block;
                width: 100%;
            }
            
            .wpsd-setting-card .input-wrapper {
                width: 100%;
                display: flex;
                justify-content: center;
                margin-bottom: 5px;
            }

            .wpsd-setting-card .desc {
                font-size: 0.75rem;
                margin-top: 5px;
                opacity: 0.7;
                line-height: 1.2;
                min-height: 2.5em; 
                max-width: 90%;
            }

            .btn-action {
                margin: 5px 0;
	    }

            #themeSelector {
                color: <?php echo $textContent; ?> !important;
                background-color: <?php echo $tableRowEvenBg; ?> !important;
                font-family: 'Source Sans Pro', sans-serif !important;
            }

            @media (max-width: 768px) {
                .wpsd-label {
                    text-align: left;
                    min-width: auto;
                }
                .wpsd-flex-row {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 5px;
                }
                .wpsd-info {
                    margin-left: 0;
                }
                .wpsd-grid-container {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <script type="text/javascript">
            const WPSD_THEMES = <?php echo json_encode($themes); ?>;
            const WPSD_CURRENT_CONFIG_FROM_INI = <?php echo json_encode($parsed_ini); ?>;
            const INITIAL_SELECTED_THEME_KEY = <?php echo json_encode($selected_theme_on_load); ?>;

            function cssDownload() {
                window.location.href = "/admin/advanced/css_download.php";
            }

            function cssUpload() {
                document.getElementById('fileid').addEventListener('change', submitForm);
                document.getElementById('fileid').click();
            }

            function submitForm() {
                document.getElementById('cssUpload').submit();
            }

            // Function to toggle the customization panel
            function toggleCustomization() {
                $('#customizationPanel').slideToggle();
                $('.customizer-trigger').toggleClass('active');
            }


            function updateLivePreview() {
                function getVal(section, key) {
                    var name = section + "[" + key + "]";
                    var selector = 'input[name="' + name.replace(/\[/g, '\\[').replace(/\]/g, '\\]') + '"]';
                    return $(selector).val();
                }

                var bgPage = getVal('Background', 'PageColor');
                var bgContent = getVal('Background', 'ContentColor');
                var bgBanners = getVal('Background', 'BannersColor');
                var bgNavbar = getVal('Background', 'NavbarColor');
                var bgNavbarHover = getVal('Background', 'NavbarHoverColor');
                var bgNavPanel = getVal('Background', 'NavPanelColor');
                var bgEven = getVal('Background', 'TableRowBgEvenColor');
                var bgOdd = getVal('Background', 'TableRowBgOddColor');
                
                var txtMain = getVal('Text', 'TextColor');
                var txtSection = getVal('Text', 'TextSectionColor');
                var txtBanners = getVal('Text', 'BannersColor');
                var txtNavbar = getVal('Text', 'NavbarColor');
                var txtNavbarHover = getVal('Text', 'NavbarHoverColor');
                var txtLink = getVal('Text', 'TextLinkColor');

                // NOTE: We also style #colorpicker here to match the active theme's content background
                var styles = `
                    body { background-color: ${bgPage} !important; color: ${txtMain} !important; }
                    
                    .container, .content, .contentwide, .wpsd-section, table { 
                        background-color: ${bgContent} !important; 
                    }
                    
                    /* Make the color picker match the content background so it looks native */
                    #colorpicker {
                        background-color: ${bgContent} !important;
                        border: 1px solid ${txtSection} !important;
                        box-shadow: 0 5px 20px rgba(0,0,0,0.5) !important;
                    }

                    .header, .footer, .divTableHead, table th, .wpsd-flex-row[style*="background"] { 
                        background-color: ${bgBanners} !important; 
                        color: ${txtBanners} !important; 
                    }
                    
                    .navbar, .mainnav, .mainnav li { 
                        background-color: ${bgNavbar} !important; 
                    }
                    
                    .navbar a, .mainnav li a, .dropbutton {
                        color: ${txtNavbar} !important;
                        background-color: ${bgNavbar} !important;
                    }
                    
                    .navbar a:hover, .mainnav li:hover, .mainnav li:hover a, .dropdown:hover .dropbutton {
                        background-color: ${bgNavbarHover} !important;
                        color: ${txtNavbarHover} !important;
                    }

                    .nav { background-color: ${bgNavPanel} !important; }
                    
                    table tr:nth-child(even), .divTableBody .divTableRow:nth-child(even), .info { 
                        background-color: ${bgEven} !important; 
                    }
                    
                    table tr:nth-child(odd), .divTableBody .divTableRow:nth-child(odd), .divTableCell { 
                        background-color: ${bgOdd} !important; 
                    }
                    
                    /* Use TextSectionColor for content headers/labels so they match content background contrast */
                    .content, .contentwide, h2, h3, .ConfSec, label, .wpsd-label, .wpsd-info, .wpsd-setting-card label, .customizer-trigger { 
                        color: ${txtSection} !important; 
                    }
                    
                    td, .divTableCell, .desc { 
                        color: ${txtMain} !important; 
                    }
                    
                    a { color: ${txtLink} !important; }
                    
                    /* Target Buttons specifically to use Banner/Header colors */
                    input[type="submit"], input[type="button"], button {
                        background-color: ${bgBanners} !important;
                        color: ${txtBanners} !important;
                        border: 1px solid ${txtSection} !important;
                    }

                    /* Override general text inputs but NOT colorwells */
                    select, input[type="text"]:not(.colorwell) {
                        color: ${txtMain} !important;
                        background-color: ${bgEven} !important;
                        border-color: ${txtSection} !important;
                    }
                `;

                $('#wpsd-live-preview').html(styles);
            }

            $(document).ready(function() {
                var f = $.farbtastic('#colorpicker');
                var p = $('#colorpicker').css('opacity', 1).hide();
                var selected;

                function getContrastColor(hexcolor){
                    if (!hexcolor || typeof hexcolor !== 'string' || hexcolor.toLowerCase() === 'none') return '#000000';
                    let hex = hexcolor.replace("#", "");
                    if (hex.length === 3) {
                        hex = hex.split('').map(char => char + char).join('');
                    }
                    if (hex.length !== 6) return '#000000';

                    const r = parseInt(hex.substr(0,2),16);
                    const g = parseInt(hex.substr(2,2),16);
                    const b = parseInt(hex.substr(4,2),16);
                    if (isNaN(r) || isNaN(g) || isNaN(b)) return '#000000';

                    const yiq = ((r*299)+(g*587)+(b*114))/1000;
                    return (yiq >= 128) ? '#000000' : '#FFFFFF';
                }

                $('.colorwell')
                    .each(function () {
                        $(this).css('background-color', $(this).val());
                        $(this).css('color', getContrastColor($(this).val()));
                        $(this).css('opacity', 1);
                    })
                    .on('change input', function() {
                        $(this).css('background-color', $(this).val());
                        $(this).css('color', getContrastColor($(this).val()));
                        updateLivePreview();
                    })
                    .focus(function() {
                        if (selected) {
                            $(selected).removeClass('colorwell-selected');
                        }
                        f.linkTo(this);
                        selected = this;
                        $(this).addClass('colorwell-selected');

                        var inputOffset = $(this).offset();
                        var inputHeight = $(this).outerHeight();
                        var inputWidth = $(this).outerWidth();
                        var pickerWidth = p.outerWidth();
                        var pickerHeight = p.outerHeight();

                        var top = inputOffset.top + inputHeight + 5; 
                        var left = inputOffset.left;

                        // Center picker relative to input
                        left = inputOffset.left + ($(this).outerWidth() / 2) - (pickerWidth / 2);

                        if (left + pickerWidth > $(window).width()) {
                            left = $(window).width() - pickerWidth - 10; 
                        }
                        if (left < 0) left = 10;

                        if (top + pickerHeight > $(window).scrollTop() + $(window).height()) {
                            top = inputOffset.top - pickerHeight - 5; 
                            if (top < $(window).scrollTop()) { 
                                top = $(window).scrollTop() + 10; 
                            }
                        }

                        p.css({
                            top: top + 'px',
                            left: left + 'px'
                        }).show();
                    });

                f.linkTo(function(color) {
                    if (selected) {
                        $(selected).val(color).css({
                            backgroundColor: color,
                            color: getContrastColor(color)
                        }).trigger('change');
                    }
                });

                $(document).mousedown(function(event) {
                    if (!$(event.target).closest('#colorpicker').length && !$(event.target).is('.colorwell')) {
                        if (p.is(":visible")) {
                            p.hide();
                            if (selected) {
                                $(selected).removeClass('colorwell-selected');
                                selected = null;
                            }
                        }
                    }
                });

                $('#themeSelector').val(INITIAL_SELECTED_THEME_KEY);

                $('#themeSelector').change(function() {
                    const selectedThemeName = $(this).val();
                    let sourceData;
                    let isPredefinedTheme = false;

                    if (selectedThemeName === 'custom') {
                        sourceData = WPSD_CURRENT_CONFIG_FROM_INI;
                        isPredefinedTheme = false;
                    } else if (selectedThemeName && WPSD_THEMES[selectedThemeName]) {
                        sourceData = WPSD_THEMES[selectedThemeName];
                        isPredefinedTheme = true;
                    } else {
                        return;
                    }

                    for (const section in sourceData) {
                       if (sourceData.hasOwnProperty(section)) {
                           for (const key in sourceData[section]) {
                               if (sourceData[section].hasOwnProperty(key)) {
                                   const inputName = section + '[' + key + ']';
                                   const inputValue = sourceData[section][key];
                                   const inputElement = $('input[name="' + inputName + '"]');

                                   if (inputElement.length) {
                                       inputElement.val(inputValue);
                                       if (inputElement.hasClass('colorwell')) {
                                           inputElement.css('background-color', inputValue);
                                           inputElement.css('color', getContrastColor(inputValue));
                                       }
                                       if (isPredefinedTheme) {
                                           inputElement.triggerHandler('change');
                                       }
                                   }
                               }
                           }
                       }
                    }
                    updateLivePreview();
                });
                
                if (INITIAL_SELECTED_THEME_KEY !== "" && INITIAL_SELECTED_THEME_KEY !== null) {
                   $('#themeSelector').triggerHandler('change');
                }
            });
        </script>
    </head>
    <body>
        <div class="container">
            <?php include $_SERVER['DOCUMENT_ROOT'].'/admin/advanced/header-menu.inc'; ?>
            <div class="contentwide">
                <?php
                $filepath = '/tmp/bW1kd4jg6b3N0DQo.tmp';

                if (empty($_POST['CallLookupProvider']) != TRUE) {
                    exec('sudo sed -i "/CallLookupProvider = /c\\\CallLookupProvider = '.escapeshellcmd($_POST['CallProvider']).'" ' . $config_file . '');
                    unset($_POST);
                    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},0);</script>';
                    die();
                }
                if (isset($_POST['phoneticCallsigns'])) {
                    $phoneticCallsigns = escapeshellcmd($_POST['phoneticCallsigns']);
                    $output = shell_exec("grep -c '^PhoneticCallsigns =' $config_file");
                    if (trim($output) == '0') {
                        exec("echo 'PhoneticCallsigns = $phoneticCallsigns' | sudo tee -a $config_file > /dev/null");
                    } else {
                        exec("sudo sed -i '/PhoneticCallsigns = /c\\PhoneticCallsigns = $phoneticCallsigns' $config_file");
                    }
                    unset($_POST);
                    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},0);</script>';
                    die();
                }

                if (file_exists($filepath_ini)) {
                    exec('sudo cp '.$filepath_ini.' '.$filepath);
                    exec('sudo chown www-data:www-data '.$filepath);
                    exec('sudo chmod 664 '.$filepath);
                }

                if($_POST) {
                    $data = $_POST;
                    if (empty($_POST['cssDownload']) != TRUE) {
                    } else if (empty($_POST['cssUpload']) != TRUE) {
                        echo "<div class='wpsd-section'><h3>Appearance Upload</h3>";
                        if (isset($_FILES['cssFile']) && $_FILES['cssFile']['error'] === UPLOAD_ERR_OK) {
                            $output = "Uploading your appearance settings data.\n";
                            $target_dir = "/tmp/css_restore/";
                            $okay = false;
                            shell_exec("sudo rm -rf $target_dir 2>&1");
                            shell_exec("mkdir $target_dir 2>&1");
                            if($_FILES["cssFile"]["name"]) {
                                $filename = $_FILES["cssFile"]["name"];
                                $source = $_FILES["cssFile"]["tmp_name"];
                                $type = $_FILES["cssFile"]["type"];
                                $name = explode(".", $filename);
                                $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                                foreach($accepted_types as $mime_type) {
                                    if($mime_type == $type) {
                                        $okay = true;
                                        break;
                                    }
                                }
                            }
                            $continue = false;
                            if (isset($name)) {
                                $continue = strtolower($name[1]) == 'zip' ? true : false;
                            }
                            if ($okay == false || $continue == false) {
                                $output .= "The file you are trying to upload is not a .zip file. Please try again.\n";
                                echo "<pre>$output</pre>";
                            } else {
                                if (isset($filename)) {
                                    $target_path = $target_dir.$filename;
                                }
                                if(isset($target_path) && move_uploaded_file($source, $target_path)) {
                                    $zip = new ZipArchive();
                                    $x = $zip->open($target_path);
                                    if ($x === true) {
                                        $zip->extractTo($target_dir);
                                        $zip->close();
                                        unlink($target_path);
                                    }
                                    $output .= "Your .zip file was uploaded and unpacked.\n";
                                    $output .= "Copying appearance setttings...\n";
                                    $output .= shell_exec("sudo mv -v -f /tmp/css_restore/wpsd-css.ini ".$filepath_ini." 2>&1")."\n";
                                    $output .= "Appearance Restoration Complete.\n";
                                    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;}, 4000);</script>';
                                } else {
                                    $output .= "There was a problem with the upload. Please try again.<br />";
                                    $output .= "\n".'<button onclick="goBack()">Go Back</button><br />'."\n";
                                    $output .= '<script>'."\n";
                                    $output .= 'function goBack() {'."\n";
                                    $output .= '    window.history.back();'."\n";
                                    $output .= '}'."\n";
                                    $output .= '</script>'."\n";
                                }
                                echo "<pre>$output</pre>";
                            }
                        } else {
                            echo "<pre>No file uploaded or an error occurred.</pre>";
                        }
                        echo "</div>";
                    } else {
                        if (update_ini_file($data, $filepath)) {
                            exec('sudo cp '.$filepath.' '.$filepath_ini);
                            exec('sudo chmod 644 '.$filepath_ini);
                            exec('sudo chown root:root '.$filepath_ini);
                            echo '<script type="text/javascript">window.location=window.location.href.split("?")[0];</script>';
                            die();
                        } else {
                            echo "<div class='wpsd-section'>Error updating INI file.</div>";
                        }
                    }
                }

                function update_ini_file($data, $filepath_to_update) {
                    $content = "";
                    foreach($data as $section=>$values) {
                        if (!is_array($values)) continue;
                        $section_for_ini = str_replace(" ", "_", $section);
                        $content .= "[".$section_for_ini."]\n";
                        foreach($values as $key=>$value) {
                            if ($value == '') {
                                $content .= $key."=none\n";
                            } else {
                                $content .= $key."=".$value."\n";
                            }
                        }
                        $content .= "\n";
                    }
                    if (!$handle = fopen($filepath_to_update, 'w')) {
                        return false;
                    }
                    $success = fwrite($handle, $content);
                    fclose($handle);
                    return $success;
                }
                ?>
                
                <div class="wpsd-section">
                    <h2 class="ConfSec">Themes</h2>
                    
                    <form action="" method="post" name="edit-css">
                        <div class="wpsd-flex-row" style="margin-bottom: 20px; background: rgba(0,0,0,0.1); padding: 10px; border-radius: 4px;">
                            <label for="themeSelector" class="wpsd-label" style="text-align: left;"><strong>Select Theme:</strong></label>
                            <select id="themeSelector">
                                <option value="" disabled>-- Select a Theme --</option>
                                <?php
                                if ($selected_theme_on_load === 'custom') {
                                    echo '<option value="custom" selected>Custom Configuration</option>';
                                }
                                foreach ($themes as $key => $theme_data_loop):
                                    $selected_attr = ($selected_theme_on_load === $key) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($key) . '" ' . $selected_attr . '>' . htmlspecialchars($key) . '</option>';
                                endforeach;
                                ?>
                            </select>
                            <input type="submit" value="<?php echo __( 'Apply Theme' ); ?>" />
                            <span class="wpsd-info"><i class="fa fa-info-circle"></i> Changes preview instantly. Click Apply to save.</span>
                        </div>
                        
                        <h3 class="customizer-trigger ConfSec" onclick="toggleCustomization()">
                            <i class="fa fa-caret-right"></i> Customize Theme
                        </h3>

                        <div id="customizationPanel" style="display:none;">
                            <div id="colorpicker"></div>

                            <?php
                            foreach($parsed_ini as $section=>$values_in_section) {
                                echo "<div class='wpsd-section'>";
                                echo "<h3 class='ConfSec'>" . htmlspecialchars($section) . "</h3>";
                                echo "<div class='wpsd-grid-container'>";
                                
                                if (is_array($values_in_section)) {
                                    foreach($values_in_section as $key=>$value) {
                                        $key_display = htmlspecialchars($key);
                                        $value_display = htmlspecialchars($value);
                                        $section_name_for_input = htmlspecialchars(str_replace(" ", "_", $section));
                                        $key_name_for_input = htmlspecialchars($key);
                                        $input_name = "{$section_name_for_input}[{$key_name_for_input}]";

                                        echo "<div class='wpsd-setting-card'>";
                                        echo "<label>$key_display</label>";
                                        echo "<div class='input-wrapper'>";
                                        
                                        if (endsWith($key, 'SectionColor')) {
                                            echo "<input type='text' class='colorwell' name='$input_name' value='$value_display' />";
                                            echo "</div><div class='desc'>Section heading font color (default #000000).</div>";
                                        } elseif ($key == 'TextColor') {
                                            echo "<input type='text' class='colorwell' name='$input_name' value='$value_display' />";
                                            echo "</div><div class='desc'>Main Content font color (default #000000).</div>";
                                        } elseif (endsWith($key, 'Color')) {
                                            echo "<input type='text' class='colorwell' name='$input_name' value='$value_display' />";
                                            echo "</div><div class='desc'></div>"; // Spacer
                                        } elseif (startsWith($key, 'MainFontSize')) {
                                            echo "<input type='text' name='$input_name' value='$value_display' size='3' maxlength='2' style='text-align: center;' />";
                                            echo "</div><div class='desc'>Main font size (px). Default 18.</div>";
                                        } elseif (startsWith($key, 'BodyFontSize')) {
                                            echo "<input type='text' name='$input_name' value='$value_display' size='3' maxlength='2' style='text-align: center;' />";
                                            echo "</div><div class='desc'>Body font size (px). Default 17.</div>";
                                        } elseif (startsWith($key, 'HeaderFont')) {
                                            echo "<input type='text' name='$input_name' value='$value_display' size='3' maxlength='2' style='text-align: center;' />";
                                            echo "</div><div class='desc'>Header font size (px). Default 34.</div>";
                                        } elseif (endsWith($key, 'HeardRows')) {
                                            echo "<input type='text' name='$input_name' value='$value_display' size='3' maxlength='3' style='text-align: center;' />";
                                            echo "</div><div class='desc'>Rows displayed (Default 40, Max 100*).</div>";
                                        } else {
                                            echo "<input type='text' class='colorwell' name='$input_name' value='$value_display' />";
                                            echo "</div><div class='desc'></div>";
                                        }
                                        
                                        echo "</div>"; // End card
                                    }
                                }
                                echo "</div>"; // End Grid
                                echo "</div>"; // End Section
                            }
                            ?>
                            
                            <div class="wpsd-section" style="border:none; text-align: left;">
                                 <input type="submit" value="<?php echo __( 'Apply Customizations' ); ?>" />
                                 <p class="wpsd-info" style="display:inline-block;">* MMDVMHost logging limitations may affect actual row count displayed.</p>
                            </div>
                        </div> </form>
                </div>

                <?php if ($displayType == "OLED") { ?>
                <div class="wpsd-section">
                    <h2 class="ConfSec">OLED Display Control</h2>
                    <script>
                        function toggleOLED() {
                            var xhr = new XMLHttpRequest();
                            xhr.open('GET', '/admin/OLED_ajax.php?action=toggle', true);
                            xhr.send();
                        }
                    </script>
                    <div class="wpsd-flex-row">
                        <button id="toggleButton" onclick="toggleOLED()">Toggle OLED Display Off/On</button>
                    </div>
                </div>
                <?php } ?>

                <div class="wpsd-section">
                    <h2 class="ConfSec">Callsign Link Provider</h2>
                    <form method="post" action="">
                        <div class="wpsd-flex-row">
                            <input type="radio" name="CallProvider" value="RadioID" id="RadioID" <?php if (isset($_SESSION['WPSDdashConfig']['WPSD']['CallLookupProvider']) && $_SESSION['WPSDdashConfig']['WPSD']['CallLookupProvider'] == "RadioID") {  echo 'checked="checked"'; } ?> />
                            <label for="RadioID">RadioID</label>
                            
                            <input type="radio" name="CallProvider" value="QRZ" id="QRZ" <?php if (isset($_SESSION['WPSDdashConfig']['WPSD']['CallLookupProvider']) && $_SESSION['WPSDdashConfig']['WPSD']['CallLookupProvider'] == "QRZ") {  echo 'checked="checked"'; } ?> />
                            <label for="QRZ">QRZ</label>
                            
                            <input name="CallLookupProvider" type="submit" value="Apply Change" />
                        </div>
                    </form>
                </div>

                <div class="wpsd-section">
                    <h2 class="ConfSec">Phonetic Callsigns</h2>
                    <form method="post" action="">
                        <div class="wpsd-flex-row">
                            <input type="radio" name="phoneticCallsigns" value="0" id="phoneticCallsign-false" <?php if (!isset($_SESSION['WPSDdashConfig']['WPSD']['PhoneticCallsigns']) || (isset($_SESSION['WPSDdashConfig']['WPSD']['PhoneticCallsigns']) && $_SESSION['WPSDdashConfig']['WPSD']['PhoneticCallsigns'] == "0")) {  echo 'checked="checked"'; } ?> />
                            <label for="phoneticCallsign-false">Disabled</label>
                            
                            <input type="radio" name="phoneticCallsigns" value="1" id="phoneticCallsign-true" <?php if (isset($_SESSION['WPSDdashConfig']['WPSD']['PhoneticCallsigns']) && $_SESSION['WPSDdashConfig']['WPSD']['PhoneticCallsigns'] == "1") {  echo 'checked="checked"'; } ?> />
                            <label for="phoneticCallsign-true">Enabled</label>
                            
                            <input name="phoneticCallsignsSubmit" type="submit" value="Apply Change" />
                        </div>
                        <div class="wpsd-info"><i class="fa fa-question-circle"></i> When enabled an additional label will be displayed with the phonetic version of callsigns</div>
                    </form>
                </div>

                <div class="wpsd-section" style="border:none;">
                    <form id="cssUpload" action="" method="POST" enctype="multipart/form-data">
                        <div><input id="fileid" name="cssFile" type="file" hidden/></div>
                        <div><input type="hidden" name="cssUpload" value="1" /></div>
                    </form>
                    <h3 class="ConfSec">Backup & Restore</h3>
                    <div class="wpsd-flex-row">
                        <input type="button" onclick="javascript:cssDownload();" value="Download Appearance Settings" />
                        <input type="button" onclick="javascript:cssUpload();" value="Upload &amp; Apply Appearance Settings (zip file only!)" />
                    </div>
                </div>
            </div>
            <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'; ?>
        </div>
    </body>
</html>
