<?php
include('function.php');
define('NL', "\n");
require('IP4Calc.php');
if(!isset($_POST['calculer'])) { ?>
    <form method="post" action="serveur.php">
        <label for="adresse">Saisir une adresse :</label>
        <input type="text" name="adresse"/>
        <label for="masque">Saisir le masque de sous réseau</label>
        <input type="text" name="masque"/>
        <input type="submit" name="calculer" value="calculer"/>
    </form>
    <?php
}

else {

        $addr = $_POST['adresse'];
        $mask = $_POST['masque'];

        echo 'Adresse saisie : '.$addr.'/ masque :'.$mask;br();
//echo('Test for 172.23.10.221/16'.NL);
        $oIP = new IP4Calc($addr, $mask);
        echo('masque sous-réseau (Quad): ' . $oIP->get(IP4Calc::NETMASK, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('masque sous-réseau (Bin): ' . $oIP->get(IP4Calc::NETMASK, IP4Calc::BIN) . NL);
        br();
        echo('masque sous-réseau  (Hex): ' . $oIP->get(IP4Calc::NETMASK, IP4Calc::HEX) . NL);
        br();
        echo('Réseaux (Quad): ' . $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Broadcast (Quad): ' . $oIP->get(IP4Calc::BROADCAST, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Adresse IP la plus basse du sous-réseau (Quad): ' . $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Adresse IP la plus haute du sous-réseau (Quad): ' . $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Adresse IP précédente(Quad): ' . $oIP->get(IP4Calc::PREVIOUS_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Adresse IP suivante(Quad): ' . $oIP->get(IP4Calc::NEXT_HOST, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Le réseau précédent avec le même masque (Quad): ' . $oIP->get(IP4Calc::PREVIOUS_NETWORK, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Le prochain réseau avec le même masque  (Quad): ' . $oIP->get(IP4Calc::NEXT_NETWORK, IP4Calc::QUAD_DOTTED) . NL);
        br();
        echo('Nombre d\'adresses IP utilisables pour les hôtes de ce sous-réseau: ' . $oIP->count() . NL);
        br();
//echo('Is 172.23.254.10 part of this subnet ? ');br();
//var_dump($oIP->partOf('172.23.254.10'));
//echo('Is 192.168.1.100 part of this subnet ? ');br();
//var_dump($oIP->partOf('192.168.1.100'));
//}


    }