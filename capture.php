<?php
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');
?>

<form action="capture.php" method="POST">
    Nom du fichier : <input type="text" name="filename"><span>.JSON</span><br/>
    <input type="submit" value="Capturer réseau"/>
</form>
<?php

if (isset($_POST)) {
    if (!empty($_POST['filename']) and is_string($_POST['filename'])) {
        $filename = trim(strip_tags($_POST['filename']));
    }
    else {
        $filename = 'output';
    }

    exec("sudo touch pcap/input.pcap;");
    exec("sudo chmod o=rw pcap/input.pcap;");
    exec("sudo tshark -c 10000 -w pcap/input.pcap -F libpcap;");
    exec("sudo touch trames/".$filename.".json;");
    exec("sudo chmod o=rw trames/".$filename.".json;");
    exec("sudo tshark -r pcap/input.pcap -T json >trames/".$filename.".json;");
    exec("sudo rm pcap/input.pcap;");

    if (file_exists($filename.'.json')) {
        if (filesize($filename.'.json') == false) {
            echo 'fichier non rempli';
        }
        else {
            echo 'le fichier est rempli';
        }
    }
}
