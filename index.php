<?php

//include ('inc/PDO.php');
include ('function.php');
define('NL', "\n");
require('IP4Calc.php');
if(!isset($_POST['calculer'])) { ?>
    <div class="content">
        <div class="container-fluid">
            <form method="post" action="index.php" enctype="multipart/form-data">
                <label for="adresse">Saisir une adresse IPv4:</label>
                <input type="text" name="adresse"/>
                <label for="masque">Saisir le masque de sous réseau</label>
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
                </select>
                <input type="file" name="json"/>
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


    if (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {

        $serveur = array();

        echo("$addr Address IP valide");

        echo 'Adresse saisie : '.$addr.'/ masque :'.$mask;br();
//echo('Test for 172.23.10.221/16'.NL);
        $oIP = new IP4Calc($addr, $mask);
        echo('masque sous-réseau (Quad): ' . $oIP->get(IP4Calc::NETMASK, IP4Calc::QUAD_DOTTED) . NL);
        br();


        echo('Réseaux (Quad): ' . $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Broadcast (Quad): ' . $oIP->get(IP4Calc::BROADCAST, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Adresse IP la plus basse du sous-réseau (Quad): ' . $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br(); $serveur[] = $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED) . NL;
        echo('Adresse IP la plus haute du sous-réseau (Quad): ' . $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br(); $serveur[] = $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED) . NL;
        tab($oIP);
        $addtest = '191.11.13.5';

        $oIPT= new IP4Calc($addtest, $mask);
        tab($oIPT);

        var_dump($oIP->partOf('191.168.1.100'));



        //echo('Adresse IP précédente(Quad): ' . $oIP->get(IP4Calc::PREVIOUS_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br();
        //echo('Adresse IP suivante(Quad): ' . $oIP->get(IP4Calc::NEXT_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br();

        echo('Nombre d\'adresses IP utilisables pour les hôtes de ce sous-réseau: ' . $oIP->count() . NL);
        br();

//}
    } else {
        echo("$addr  Address IP invalide");
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

    if ($file_size > 125000000) {
        $errors = 'Le fichier doit être au maximum de 1 GB';
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
        $infraction = 0;
        foreach ($ipv4 as $key => $value) {
            $toto =array();
            $toto = explode(' to ',$key);
            foreach ($toto as $to) {

                if( $oIP->partOf($to) == false )
                {
                    echo $key . ' => <span style="color :red;">' . $value . '</span><br/>';
                    $infraction += $value ;
                }else
                {
                    echo $key . ' => ' . $value . '<br/>';
                }
            }
        }
        echo $infraction;

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