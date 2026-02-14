<?php
session_start();

function isNanoPi() {
    return file_exists('/boot/armbianEnv.txt') ? 1 : 0;
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if (!isset($_SESSION['oled_state'])) {
        $_SESSION['oled_state'] = 'on'; // Default to 'on'
    }

    if ($action == 'toggle') {
        $_SESSION['oled_state'] = ($_SESSION['oled_state'] == 'off') ? 'on' : 'off';
    }

    // Determine I2C bus number
    $i2c_bus = isNanoPi() ? 0 : 1;

    // Set the OLED state
    $command = ($_SESSION['oled_state'] == 'off')
        ? "sudo i2cset -y $i2c_bus 0x3c 0x00 0xAE"  // Turn off
        : "sudo i2cset -y $i2c_bus 0x3c 0x00 0xAF"; // Turn on

    exec($command);

    exit;
}