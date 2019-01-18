<?php
include ('inc/function.php');
include ('inc/header.php');
?>
    <nav class="header">
        <a href="index.php" class="logo"><img class="logo" src="inc/img/shinkansen.svg"></a>
        <div class="header-right">
            <a href="index.php">Accueil</a>
            <a href="fichier.php">Analyse</a>
            <a class="active" href="capture.php">Capture</a>
            <a href="reseau.php">Réseau</a>
        </div>
    </nav>

<form action="capture.php" method="POST">
    <span>Nom du fichier :<span/><br/>
        <input type="text" name="filename"/><span>.JSON</span><br/>
        <span>selection format:</span>

        <select name="format">
            <option value="temps">Temps</option>
            <option value="trame">Nombre de trame</option>
        </select>
        <input type="number" name="nombre"/>

    <input type="submit" name="capture" value="Capturer réseau"/>
    </form>

<?php
if (!empty($_POST['capture'])) {

    $start = false;
    $effacer = false;

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
        if ($_POST['format'] == 'temps') {
            if ($nombre >= 3600 or $nombre <= 10) {
                $nombre = 10;
            }
            $parametre = ' -a duration:'.$nombre;
        }
        if ($parametre == false) {
            $parametre = ' -c 10';
        }
    }
    else {
        $parametre = ' -c 10';
    }

    exec("sudo touch pcap/input.pcap;");
    exec("sudo chmod o=rw pcap/input.pcap;");
    exec("sudo tshark". $parametre ." -w pcap/input.pcap -F libpcap;");
    exec("sudo touch trames/" . $filename . ".json;");
    exec("sudo chmod o=rw trames/" . $filename . ".json;");
    exec("sudo tshark -r pcap/input.pcap -T json >trames/" . $filename . ".json;");
    exec("sudo rm pcap/input.pcap;");
    $start = true;

    ?>
    <br/>
    <a title="Télécharger la capture que vous venez d\'effectuer" href="trames/<?php echo $filename.'.json'; ?>" download="<?php echo $filename.'.json' ?>">Télécharger la capture</a>
<?php }

include ('inc/footer.php');

if (!empty($_POST['capture']) and $start == true) {

    exec("sudo find /var/www/html/trames -type f -mmin +60 -delete");
    $effacer = true;
}
