<?php

if($_POST['action'] == 'enable') {
    exec('sudo touch /etc/.TGNAMES');
}

if($_POST['action'] == 'disable') {
    exec('sudo rm -rf /etc/.TGNAMES');
}