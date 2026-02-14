<?php

if($_POST['action'] == 'enable') {
    exec('sudo touch /etc/.CALLERDETAILS');
}

if($_POST['action'] == 'disable') {
    exec('sudo rm -rf /etc/.CALLERDETAILS');
    exec('sudo rm -rf /tmp/Callsign_Name*');
}