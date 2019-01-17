<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT nom_resau,ip_saisie,mask,ip_low,ip_high,created_at FROM trame_way";

$req = mysqli_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysqli_error());
while($data = mysqli_fetch_assoc($req))
{
    //
    echo '<b>'.$data['nom_reseau'].' '.$data['ip_saisie'].'</b> '.$data['mask'].' '.$data['created_at'].;
    echo ' <i> '.$data['ip_low'].' '.$data['ip_high'].'</i><br>';
}