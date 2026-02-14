<?php

/*
//
// WPSD Basic System API; can be used for remote calls, Nextion button presses, etc.
//
// json output - the default (command output + exit staus) & plain text outputs (exit status only) are supoored.
//
// Usage examples:
//     curl -u pi-star:raspberry 'http://localhost/admin/system_api.php?action=restart_wpsd_services&format=json'
//     curl -u pi-star:raspberry 'http://localhost/admin/system_api.php?action=restart_wpsd_services&format=text'
//
// Current, valid `action=` arguments:
//     shutdown, reboot, get_ip, update_wpsd, stop_wpsd_services,
//     restart_wpsd_services, update_hostfiles, reload_wifi
//
// NOTE: `get_ip` only returns the IP; no exit status (deliberate).
//
*/
define('MMDVM_FUNC_DEFS_ONLY', true);  // do not autoexecute logs reading code in mmdvmhost/functions.php
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/class.BMApi.php';    // Brandmeister API


function executeCommand($command) {
    $output = null;
    $exit_status = null;
    exec($command, $output, $exit_status);
    return array('output' => $output, 'exit_status' => $exit_status);
}

function getActiveInterfaceIP() {
    $output = null;
    $exit_status = null;

    exec("ip route get 1 | awk '/src/ {print \$7}'", $output, $exit_status);

    if ($exit_status === 0 && isset($output[0])) {
        return trim($output[0]);
    }

    return null;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'json';

if ($format !== 'json' && $format !== 'text') {
    echo json_encode(array('error' => 'Invalid format'));
    die();
}

switch ($action) {
    case 'shutdown':
        $result = executeCommand("sudo shutdown -h now");
        break;

    case 'reboot':
        $result = executeCommand("sudo reboot");
        break;

    case 'get_ip':
        $ip = getActiveInterfaceIP();
        if ($format === 'text') {
            echo $ip;
            exit;
        } else {
            $result = array('ip' => $ip);
        }
        break;

    case 'update_wpsd':
        $result = executeCommand("sudo wpsd-update");
        break;

    case 'stop_wpsd_services':
        $result = executeCommand("sudo wpsd-services fullstop");
        break;

    case 'restart_wpsd_services':
        $result = executeCommand("sudo wpsd-services restart");
        break;

    case 'update_hostfiles':
        $result = executeCommand("sudo FORCE=1 wpsd-hostfile-update");
        break;

    case 'reload_wifi':
        $result = executeCommand("sudo /usr/local/sbin/.wpsd-reload-wifi");
        break;

    case 'dmrnet_set_status':
        $selectedNet = $_GET['dmrNet'];
        $state = $_GET['netState'];
        $result = [
            'commandOutput' => dmrGwSetNetStatus($selectedNet, $state != 'disable'),
        ];
        break;

    case 'bm_manager':
        $bmApi = BMApi::getInstance();
        if ($_GET['cmd'] == 'link_static') {
            if (!isset($_GET['tg']) || !isset($_GET['slot'])) {
                $result = ['error' => 'tg and slot params required'];
            } else {
                $bmApi->linkStaticTG($_GET['tg'], $_GET['slot']);
                $result = ['success' => true];
            }
        } elseif ($_GET['cmd'] == 'drop_static') {
            if (!isset($_GET['tg']) || !isset($_GET['slot'])) {
                $result = ['error' => 'tg and slot params required'];
            } else {
                $bmApi->dropStaticTG($_GET['tg'], $_GET['slot']);
                $result = ['success' => true];
            }
        } elseif ($_GET['cmd'] == 'drop_dynamic') {
            if (!isset($_GET['slot'])) {
                $result = ['error' => 'slot param required'];
            } else {
                $bmApi->dropDynamicTGs($_GET['slot']);
                $result = ['success' => true];
            }
        } elseif ($_GET['cmd'] == 'drop_qso') {
            if (!isset($_GET['slot'])) {
                $result = ['error' => 'slot param required'];
            } else {
                $bmApi->dropQSO($_GET['slot']);
                $result = ['success' => true];
            }
        } else {
            $result = ['error' => 'Bad command: ' . $_GET['cmd']];
        }
        break;

    default:
        $result = array('error' => 'Invalid action');
        break;
}

if ($format === 'json') {
    echo json_encode($result);
} elseif ($format === 'text') {
    // For "get_ip" action, the IP address has already been echoed above (if $format === 'text')
    if ($action !== 'get_ip') {
        echo $result['exit_status'];
    }
}