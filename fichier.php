<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

include ('inc/IP4Calc.php');

if(!isset($_POST['analyser'])) { ?>
    <div class="content">
        <div class="container-fluid">
            <form method="post" action="fichier.php" enctype="multipart/form-data">
                <input type="file" name="json"/></br>
                <input type="submit" name="analyser" value="analyser"/>
            </form>
        </div>
    </div>
    <?php
}
else {

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

        if ($file_size > 10000000000) {
            $errors = 'Le fichier doit être au maximum de 1Go';
        }

        if (empty($errors) == false) {
            echo $errors . '<br/>';
        }
        else {
            $jsonData = file_get_contents($_FILES['json']['tmp_name']);
            $jsons = json_decode($jsonData, true); //lecture json

            $nbrtrame = 0;
            $ipv4 = array();
            $mac = array();
            $ipList = array();
            $macList = array();
            $ipAndMac = array();
            $protocols = array();
            $infraction = array();
            $tempo = array();
            $dest = 0;
            $source = 0;

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

            ?>
            <table>
                <caption><?php echo $file_name ?></caption>

                <thead>
                <tr>
                    <th><?php echo 'Total adresses IP ' ?></th>
                    <th><?php echo 'Total adresses MAC ' ?></th>
                    <th><?php echo 'Total trames ' ?></th>
                </tr>
                </thead>

                <tfoot>
                <tr>

                </tr>
                </tfoot>

                <tbody>
                <tr>
                    <th><?php echo count($ipList); ?></th>
                    <th><?php echo count($macList); ?></th>
                    <th><?php echo $nbrtrame; ?></th>
                </tr>
                </tbody>
            </table>

            <?php
        }
    }
}

if (!empty($protocols)) {

    $sql = "SELECT * FROM reseau;";
    $query = $pdo->prepare($sql);
    $query->execute();
    $SRSX = $query->fetchALL();

    foreach ($ipv4 as $clef) {
        $tempo = explode(' to ', $clef);
        $source = $tempo[0];
        $dest = $tempo[1];

        foreach ($SRSX as $cle => $value) {
            foreach ($value as $key => $valeur) {

                if ($key == 'id') {
                    $infraction[$cle][$key] = $valeur;
                }
                if ($key == 'ip_low') {
                    $infraction[$cle][$key] = $valeur;
                }
                if ($key == 'ip_high') {
                    $infraction[$cle][$key] = $valeur;
                }
                $infraction[$cle]['infraction'] = 0;
                $mask = $key['mask'];
                $low = $key['ip_low'];

                $oIP = new IP4Calc($low, $mask);
                if ($oIP->partOf($source) == true or $oIP->partOf($dest) == true) {

                    echo 'ui';
                    $infraction[$cle]['infraction'] = $infraction[$cle]['infraction']+$valeur;
                }
            }
        }
    }

tab($SRSX);
tab($ipv4);
tab($infraction);

    $total = count($protocols);
    $compteur = 0;

    ?>

    <canvas id="doughnut-chart" width="800" height="450"></canvas>
    <script>

        new Chart(document.getElementById("doughnut-chart"), {
            type: 'doughnut',
            data: {
                labels: [<?php foreach ($protocols as $key => $value) { $compteur++; ?>"<?php echo $key;?>"<?php if ($compteur != $total) { echo ','; }} ?>],
                datasets: [
                    {
                        <?php $compteur = 0; ?>
                        backgroundColor: [<?php foreach ($protocols as $key => $value) { $compteur++; ?>"<?php echo rand_color();?>"<?php  if ($compteur != $total) { echo ','; }} ?><?php $compteur = 0; ?>],
                        data: [<?php foreach ($protocols as $key => $value) { $compteur++; echo "$value"; if ($compteur != $total) { echo ','; }} ?>]
                    }
                ]
            },
            options: {
                title: {
                    display: true
                }
            }
        });
    </script>
    <?php
}
include ('inc/footer.php');
