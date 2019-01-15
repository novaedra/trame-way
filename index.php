<div class="content">
    <div class="container-fluid">
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="json"/>
            <br/>
            <input type="submit"/>
        </form>
    </div>
</div>

<form action="index.php" method="POST">
    Adresse début réseau : <input type="text" name="StartIP"><br>
    Adresse fin de réseau : <input type="text" name="EndIP"><br>
    Masque de sous-réseau : <input type="text" name="CIDR"><br>
    <input type="submit"/>
</form>

<?php

//include ('inc/PDO.php');
include ('function.php');

if (!empty($_POST)) {
    if (!empty($_POST['StartIP'])) {
        echo 'IP start : '.$_POST['StartIP'].'<br/>';
    }

    if (!empty($_POST['EndIP'])) {
        echo 'IP end : '.$_POST['EndIP'].'<br/>';
    }

    if (!empty($_POST['CIDR'])) {
        echo 'CIDR : '.$_POST['CIDR'].'<br/>';
    }
}

if (isset($_FILES['json'])) {

    $errors = array();
    $file_name = $_FILES['json']['name'];
    $file_size = $_FILES['json']['size'];
    $file_tmp = $_FILES['json']['tmp_name'];
    $file_type = $_FILES['json']['type'];
    $file_ext = strrchr($_FILES['json']['name'], '.');
    $expensions = array(".json");

    if (in_array($file_ext, $expensions) === false) {
        $errors = "Extension non autorisé, veuillez séléectionner un fichier .JSON";
    }

    if ($file_size > 209715200) {
        $errors = 'Le fichier doit être au maximum de 2 MB';
    }

    if (empty($errors) == false) {
        echo $errors . '<br/>';
    } else {
        $jsonData = file_get_contents($_FILES['json']['tmp_name']);
        $jsons = json_decode($jsonData, true); //lecture json

        $nbrtrame = 0;
        $tempo = 0;
        $ipv4 = array();
        $mac = array();
        $ipList = array();
        $macList = array();
        $tout = array();
        $temp = array();
        $ipAndMac = array();
        $protocols = array();

        foreach ($jsons as $json) { //lecture trame par trame

            $nbrtrame = $nbrtrame + 1; //compteur nombre de trame
            $protocols = countprotocol($json, $protocols); //compteur de protocol

            if (!empty($json['_source']['layers']['ip']['ip.src']) and !empty($json['_source']['layers']['ip']['ip.dst'])) {

                $sourceIpv4 = $json['_source']['layers']['ip']['ip.src'];
                $destinataireIpv4 = $json['_source']['layers']['ip']['ip.dst']; //adresse IPV4

                if (array_key_exists($sourceIpv4 . ' to ' . $destinataireIpv4, $ipv4)) {
                    $ipv4[$sourceIpv4 . ' to ' . $destinataireIpv4]++;

                    if (!in_array($sourceIpv4, $ipList)) {
                        $ipList[] = $sourceIpv4;
                    }
                    if (!in_array($destinataireIpv4, $ipList)) {
                        $ipList[] = $destinataireIpv4;
                    }
                }
                else {
                    $ipv4[$sourceIpv4 . ' to ' . $destinataireIpv4] = 1;
                }

                if (!empty($json['_source']['layers']['eth']['eth.src']) and !empty($json['_source']['layers']['eth']['eth.dst'])) {

                    $sourceMac = $json['_source']['layers']['eth']['eth.src'];
                    $destinataireMac = $json['_source']['layers']['eth']['eth.dst']; //adresse IPV4

                    if (array_key_exists($sourceMac . ' to ' . $destinataireMac, $mac)) {
                        $mac[$sourceMac . ' to ' . $destinataireMac]++;

                        if (!in_array($sourceMac, $macList)) {
                            $macList[] = $sourceMac;
                        }
                        if (!in_array($destinataireMac, $macList)) {
                            $macList[] = $destinataireMac;
                        }
                    }
                    else {
                        $mac[$sourceMac . ' to ' . $destinataireMac] = 1;
                    }
                }
            }
        }
// --------------------------------------------------------------------------------------------------------------------
        /*

                foreach ($jsons as $json) { //lecture trame par trame

                    if (!empty($json['_source']['layers']['ip']['ip.src']) and !empty($json['_source']['layers']['eth']['eth.src'])) { //si il y a une adresse ip source et mac source

                        if (isset($tout[$json['_source']['layers']['eth']['eth.src']])) {   //si le tableau tout contient deja cette adresse mac
                            $tout[$json['_source']['layers']['eth']['eth.src']] .= '<br/>'.$json['_source']['layers']['ip']['ip.src'];
                        }
                        else { //si le tableau tout ne contient pas cette adresse mac
                            $tout[$json['_source']['layers']['eth']['eth.src']] = $json['_source']['layers']['ip']['ip.src'];
                        }
                    }

                    if (!empty($json['_source']['layers']['ip']['ip.dst']) and !empty($json['_source']['layers']['eth']['eth.dst'])) {

                        if (isset($tout[$nbrtrame][$json['_source']['layers']['eth']['eth.dst']])) {
                            $tout[$json['_source']['layers']['eth']['eth.dst']] .= '<br/>'.$json['_source']['layers']['ip']['ip.dst'];
                        }
                        else {
                            $tout[$json['_source']['layers']['eth']['eth.dst']] = $json['_source']['layers']['ip']['ip.dst'];
                        }
                    }

                    if (!empty($json['_source']['layers']['ip']['ip.src']) and !isset($json['_source']['layers']['eth']['eth.src'])) {

                        if (isset($tout['X'])) {
                            $tout['X'] .= '<br/>'.$json['_source']['layers']['ip']['ip.src'];
                        }
                        else {
                            $tout['X'] = $json['_source']['layers']['ip']['ip.src'];
                        }
                    }

                    if (!empty($json['_source']['layers']['ip']['ip.dst']) and !isset($json['_source']['layers']['eth']['eth.dst'])) {

                        if (isset($tout['X'])) {
                            $tout['X'] .= '<br/>'.$json['_source']['layers']['ip']['ip.dst'];
                        }
                        else {
                            $tout['X'] = $json['_source']['layers']['ip']['ip.dst'];
                        }
                    }
                }

                foreach ($tout as $key => $value) {
                    $temp = explode('<br/>',$value);
                    $temp = array_unique($temp);

                    foreach ($temp as $valeur) {
                        if (!empty($ipAndMac[$key])) {
                            $ipAndMac[$key] .= '<br/>'.$valeur;
                        }
                        else {
                            $ipAndMac[$key] = $valeur;
                        }
                    }
                }

                echo '<pre>';
                print_r($ipAndMac);
                echo '</pre>';
                echo '<br/>';
        */

//-----------------------------------------------------------------------------------------------------------------------------------------------
        ?>
        <table>
            <caption><?php echo $file_name ?></caption>

            <thead> <!-- En-tête du tableau -->
            <tr>
                <th>IP</th>
                <th>MAC</th>
                <th>Communications</th>
            </tr>
            </thead>

            <tfoot> <!-- Pied de tableau -->
            <tr>
                <th><?php echo 'Total adresses IP : '.'<br/>'.count($ipList) ?></th>
                <th><?php echo 'Total adresses MAC : '.'<br/>'.count($macList) ?></th>
                <th><?php echo 'Total trames : '.'<br/>'.$nbrtrame ?></th>
            </tr>
            </tfoot>

            <tbody>
            <tr>
                <th>Nom</th>
                <th>Âge</th>
                <th>Pays</th>
            </tr>
            </tbody>
        </table>
        <?php

        asort($protocols);

        foreach ($protocols as $key => $value) {

            echo $key . ' : ' . $value . '<br/>';
        }

        echo '<br/>';

        asort($ipv4);
        asort($mac);
        asort($ipList);
        asort($macList);

        echo 'Communication IP à IP : ';
        echo '<div class="ip">';
        foreach ($ipv4 as $key => $value) {
            echo $key . ' => ' . $value . '<br/>';
        }

        echo '</div>';
        echo '<br/>';

        echo 'Communication MAC à MAC : ';
        echo '<div class="mac">';
        foreach ($mac as $key => $value) {
            echo $key . ' => ' . $value . '<br/>';
        }
        echo '</div>';
        echo '<br/>';

        echo 'Liste des adresses IP : '.'<br/>'.count($ipList).' adresses IP différentes';
        echo '<pre>';
        print_r($ipList);
        echo '</pre>';
        echo '<br/>';

        echo 'Liste des adresses MAC : '.'<br/>'.count($macList).' adresses MAC différentes';
        echo '<pre>';
        print_r($macList);
        echo '</pre>';
        echo '<br/>';
    }
}

?>
<style>
    * {
        text-align:center;
        color:white;
        margin: 0 auto;
        background-color:black;
        border:none;
    }
</style>