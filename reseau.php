<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT nom_resau,ip_saisie,mask,ip_low,ip_high,created_at FROM trame_way ";
$query = $pdo -> prepare($sql);
$query -> bindValue(':nom_reseau', $nom_reseau, PDO::PARAM_STR);
$query -> bindValue(':addr', $addr, PDO::PARAM_STR);
$query -> bindValue(':mask', $mask, PDO::PARAM_STR);
$query -> bindValue(':ip_low', $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);
$query -> bindValue(':ip_high', $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);

$query -> execute();
}



