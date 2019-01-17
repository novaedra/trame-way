<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

define('NL', "\n");
require('IP4Calc.php');

if(!isset($_POST['calculer'])) { ?>
    <div class="content">
        <div class="container-fluid">
            <form method="post" action="fichier.php" enctype="multipart/form-data">

                <label for="adresse">Saisir le nom du réseau:</label><br/>
                <input type="text" name="nom_reseau"/></br>

                <label for="adresse">Saisir une adresse IPv4:</label><br/>
                <input type="text" name="adresse"/></br>

                <label for="masque">Saisir le masque de sous réseau</label><br/>
                <select name="masque">
                    <option value="255.0.0.0">255.0.0.0/8</option>
                    <option value="255.128.0.0">255.128.0.0/9</option>
                    <option value="255.192.0.0">255.192.0.0/10</option>
                    <option value="255.224.0.0">255.224.0.0/11</option>
                    <option value="255.240.0.0">255.240.0.0/12</option>
                    <option value="255.248.0.0">255.248.0.0/13</option>
                    <option value="255.252.0.0">255.252.0.0/14</option>
                    <option value="255.254.0.0">255.254.0.0/15</option>
                    <option value="255.255.0.0">255.255.0.0/16</option>
                    <option value="255.255.128.0">255.255.128.0/17</option>
                    <option value="255.255.192.0">255.255.192.0/18</option>
                    <option value="255.255.224.0">255.255.224.0/19</option>
                    <option value="255.255.240.0">255.255.240.0/20</option>
                    <option value="255.255.248.0">255.255.248.0/21</option>
                    <option value="255.255.252.0">255.255.252.0/22</option>
                    <option value="255.255.254.0">255.255.254.0/23</option>
                    <option value="255.255.255.0">255.255.255.0/24</option>
                    <option value="255.255.255.128">255.255.255.128/25</option>
                    <option value="255.255.255.192">255.255.255.192/26</option>
                    <option value="255.255.255.224">255.255.255.224/27</option>
                    <option value="255.255.255.240">255.255.255.240/28</option>
                    <option value="255.255.255.248">255.255.255.248/29</option>
                    <option value="255.255.255.252">255.255.255.252/30</option>
                    <option value="255.255.255.254">255.255.255.254/31</option>
                    <option value="255.255.255.255">255.255.255.255/32</option>
                </select></br>
                <input type="file" name="json"/></br>
                <br/>

                <input type="submit" name="calculer" value="calculer"/>
            </form>
        </div>
    </div>
    <?php
}

else {

    $addr = trim(strip_tags($_POST['adresse']));
    $mask = trim(strip_tags($_POST['masque']));
    $nom_reseau = trim(strip_tags($_POST['nom_reseau']));


    if (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {

        $oIP = new IP4Calc($addr, $mask);

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

        ?>
        <table>
            <caption><?php echo $file_name ?></caption>

            <thead> <!-- En-tête du tableau -->
            <tr>
                <th><?php echo 'Total adresses IP ' ?></th>
                <th><?php echo 'Total adresses MAC ' ?></th>
                <th><?php echo 'Total trames ' ?></th>
            </tr>
            </thead>

            <tfoot> <!-- Pied de tableau -->
            <tr>

            </tr>
            </tfoot>

            <tbody>
            <tr>
                <th><?php echo count($ipList) ?></th>
                <th><?php echo count($macList) ?></th>
                <th><?php echo  $nbrtrame ?></th>
            </tr>
            </tbody>
        </table>
        <table>
            <caption><?php echo 'Adresse reseau : '.$addr.' et adresse mac : '.$mask ; ?></caption>

            <thead> <!-- En-tête du tableau -->
            <tr>
                <th>Adresse réseau</th>
                <th>Adresse broadcast</th>
                <th>Adresse la plus basse du réseau</th>
                <th>Adresse la plus haute du réseau</th>
                <th>Nombre hots sous réseau</th>
            </tr>
            </thead>

            <tfoot> <!-- Pied de tableau -->
            <tr>

            </tr>
            </tfoot>

            <tbody>
            <tr>
                <th><?php echo $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED) . NL; ?></th>
                <th><?php echo $oIP->get(IP4Calc::BROADCAST, IP4Calc::QUAD_DOTTED) . NL; ?></th>
                <th><?php echo $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED) . NL; ?></th>
                <th><?php echo $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED) . NL; ?></th>
                <th><?php echo $oIP->count() . NL; ?></th>
            </tr>
            </tbody>
        </table>
        <?php

        asort($protocols);


        echo '<br/>';

        asort($ipv4);
        asort($mac);
        asort($ipList);
        asort($macList);

        echo 'Communication IP à IP : ';
        echo '<div class="ip">';
        $infraction = 0;
        foreach ($ipv4 as $key => $value) {
            $toto =array();
            $toto = explode(' to ',$key);

            $tempo = true;
            foreach ($toto as $to) {

                if ($oIP->partOf($to) == false) {
                    $tempo = false;
                }
            }
            if ($tempo == false) {
                $infraction += $value ;
            }
        }
        echo $infraction;
        br();
        $pcalc = pourcentage($infraction,$nbrtrame);
        echo 'Erreur : '.$pcalc;

        echo '</div>';
        echo '<br/>';

        $sql = "INSERT INTO reseau (nom_reseau,ip_saisie,mask,ip_low,ip_high,created_at) VALUES (:nom_reseau,:addr,:mask,:ip_low,:ip_high , NOW())";
        $query = $pdo -> prepare($sql);
        $query -> bindValue(':nom_reseau', $nom_reseau, PDO::PARAM_STR);
        $query -> bindValue(':addr', $addr, PDO::PARAM_STR);
        $query -> bindValue(':mask', $mask, PDO::PARAM_STR);
        $query -> bindValue(':ip_low', $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);
        $query -> bindValue(':ip_high', $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);

        $query -> execute();

        /* echo 'Communication MAC à MAC : ';
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
         echo '<br/>';*/
    }
}
}
else {
    echo $addr.'Adresse IP invalide verifiez que l\'adresse  entrer  est une adresse valide ';br();
    echo '<a href="fichier.php">retour</a>';
}






}

if (!empty($protocols)) {

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
                            label: "Population (millions)",
                            backgroundColor: [<?php foreach ($protocols as $key => $value) { $compteur++; ?>"<?php echo rand_color();?>"<?php  if ($compteur != $total) { echo ','; }} ?><?php $compteur = 0; ?>],
                            data: [<?php foreach ($protocols as $key => $value) { $compteur++; echo "$value"; if ($compteur != $total) { echo ','; }} ?>]
                        }
                    ]
                },
                options: {
                    title: {
                        display: true,
                        text: 'Predicted world population (millions) in 2050'
                    }
                }
            });
        </script>

    <?php
}
