<?php
//
//  Config page handler to unpause all paused moded in a single POST
//
if (isset($_POST['unpause_modes'])) {
    $paused_modes = explode(',', $_POST['paused_modes']);
    foreach ($paused_modes as $mode) {
        $service = strtolower($mode);
        if ($service == 'd-star' || $service == 'dstar') {
            $service = 'ircddb';
        } elseif ($service == 'pocsag') {
            $service = 'dapnet';
        }

        exec("sudo systemctl start " . escapeshellarg($service) . "gateway.timer");
        exec("sudo systemctl start " . escapeshellarg($service) . "gateway.service");

        $command = "sudo /usr/local/sbin/wpsd-mode-manager " . escapeshellarg($mode) . " Enable";
        exec($command);
    }
    if(isset($_GET['imm'])) {
	 header("Location: /admin/index.php?func=mode_man&all_resumed");
    } else {
	header("Location: /admin/configure.php");
    }
    exit();
}
