<?php
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

if (!empty($_POST)) {

    if (!empty($_POST['filename']) and is_string($_POST['filename'])) {
        $filename = trim(strip_tags($_POST['filename']));
    }
    else {
        $filename = 'output';
    }

    if(!empty($_POST['format'])){

        $parametre = false;

            if (!empty($_POST['nombre']) and is_numeric($_POST['nombre']) and $_POST['nombre'] >= 10 and $_POST['nombre'] <= 1000000){
                $nombre = trim(strip_tags($_POST['nombre']));
            }
            else {
                $nombre = 10;
            }
            if($_POST['format'] == 'trame') {
                $parametre = ' -c '.$nombre;
            }
            else {
                $parametre = ' -c 10';
            }
            if ($_POST['format'] == 'temps' and $parametre == false) {
                if ($nombre >=  )
                $parametre = ' -a duration:'.$nombre;
            }
            else if ($parametre == false) {
                $parametre = ' -a duration:10';
            }
        }
        else {
            $parametre = ' -c 10';
        }

    exec("sudo touch pcap/input.pcap;");
    exec("sudo chmod o=rw pcap/input.pcap;");
    exec("sudo tshark". $parametre ." -a filesize:1000 -w pcap/input.pcap -F libpcap;");
    exec("sudo touch trames/" . $filename . ".json;");
    exec("sudo chmod o=rw trames/" . $filename . ".json;");
    exec("sudo tshark -r pcap/input.pcap -T json >trames/" . $filename . ".json;");
    exec("sudo rm pcap/input.pcap;");
    exec("sudo at now +1 hours;");
    exec("sudo rm /var/www/html/trames/" . $filename . ".json;");

    ?>
    <br/>
    <a title="Télécharger la capture que vous venez d\'effectuer" href="trames/<?php echo $filename.'.json'; ?>" download="<?php echo $filename.'.json' ?>">Télécharger la capture</a>
<?php }
else { ?>
    <form action="capture.php" method="POST">
    <span>Nom du fichier :<span/><br/>
        <input type="text" name="filename"/><span>.JSON</span><br/>
        <span>selection format:</span>

        <select name="format">
            <option value="temps">Temps</option>
            <option value="trame">Nombre de trame</option>
        </select>
        <input type="number" name="nombre"/>

    <input type="submit" value="Capturer réseau"/>
</form>
<?php }

    include ('inc/footer.php');