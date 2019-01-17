<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT nom_resau, ip_saisie, mask, ip_low, ip_high, created_at FROM trame_way ";
$query = $pdo -> prepare($sql);
$return = $query -> fetchAll();
}
tab($return);


