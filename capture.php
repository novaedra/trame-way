<?php
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');
?>

<form action="capture.php" method="POST">
    <span>Nom du fichier :<span/><br/>
        <input type="text" name="filename"/><span>.JSON</span><br/>
        <span>selection format:</span>

        <select name="format">
            <option value="temps">en seconde</option>
            <option value="trame">en trame</option>
        </select>

        <?php if(!empty($_POST['format']))
        {
            if($_POST['format'] == 'select_time')
            {
                echo '<input type="number" name="time"/>';
            }
            else if ($_POST['format'] == 'select_nbtrame') {
                    echo '<input type="number" name="trame"/>';
            }
        } ?>

    <input type="submit" value="Capturer réseau"/>
</form>
<?php

if (!empty($_POST)) {
    print_r($_POST);
    if (!empty($_POST['filename']) and is_string($_POST['filename']) ) {

        /*if(!empty($_POST['trame'])){
            $tram=trim(strip_tags($_POST['tram']));
            if($tram >= 10 and is_numeric($tram))
            {
                $filename = trim(strip_tags($_POST['filename']));
                exec("sudo touch pcap/input.pcap;");
                exec("sudo chmod o=rw pcap/input.pcap;");
                exec("sudo tshark -c 100 -w pcap/input.pcap -F libpcap;");
                exec("sudo touch trames/".$filename.".json;");
                exec("sudo chmod o=rw trames/".$filename.".json;");
                exec("sudo tshark -r pcap/input.pcap -T json >trames/".$filename.".json;");
                exec("sudo rm pcap/input.pcap;");
            }
        }*/

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
    ?>
    <br/>
    <a title="Télécharger la capture que vous venez d\'effectuer" href="trames/<?php echo $filename.'.json'; ?>" download="<?php echo $filename.'.json' ?>">Télécharger la capture</a>
    <?php

    if (file_exists($filename.'.json')) {
        if (filesize($filename.'.json') == false) {
            echo 'fichier non rempli';
        }
        else {
            echo 'le fichier est rempli';
        }
    }
}
