<?php
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');
?>

<form action="capture.php" method="POST">
    <span>Nom du fichier :<span/><br/>
        <input type="text" name="filename"><span>.JSON</span><br/>
    <input type="submit" value="Capturer réseau"/>
</form>
<?php

if (!empty($_POST)) {
    if (!empty($_POST['filename']) and is_string($_POST['filename'])) {
        $filename = trim(strip_tags($_POST['filename']));

        exec("sudo touch pcap/input.pcap;");
        exec("sudo chmod o=rw pcap/input.pcap;");
        exec("sudo tshark -c 100 -w pcap/input.pcap -F libpcap;");
        exec("sudo touch trames/".$filename.".json;");
        exec("sudo chmod o=rw trames/".$filename.".json;");
        exec("sudo tshark -r pcap/input.pcap -T json >trames/".$filename.".json;");
        exec("sudo rm pcap/input.pcap;");
    }
    else {
        $filename = 'output';
        exec("sudo touch pcap/input.pcap;");
        exec("sudo chmod o=rw pcap/input.pcap;");
        exec("sudo tshark -c 100 -w pcap/input.pcap -F libpcap;");
        exec("sudo touch trames/".$filename.".json;");
        exec("sudo chmod o=rw trames/".$filename.".json;");
        exec("sudo tshark -r pcap/input.pcap -T json >trames/".$filename.".json;");
        exec("sudo rm pcap/input.pcap;");
    }

    if (file_exists($filename.'.json')) {
        if (filesize($filename.'.json') == false) {
            echo 'erreur lors de la création du fichier';
        }
        else {
            echo '<a title="Titre du lien" href="trames/'.$filename.".json".'>Télécharger Capture</a>';
        }
    }
}
