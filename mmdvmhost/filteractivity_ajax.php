<?php

if($_POST['action'] == 'true') {
    exec('sudo touch /etc/.FILTERACTIVITY');
}

if($_POST['action'] == 'false') {
    exec('sudo rm -rf /etc/.FILTERACTIVITY');
}