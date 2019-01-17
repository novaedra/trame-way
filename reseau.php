<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT * FROM reseau;";
$query = $pdo -> prepare($sql);
$query -> execute();
$return = $query -> fetchALL();

tab($return);

echo '<form method="post">';

foreach ($return as $var) {
    echo '<input type="checkbox" name="reseau" value="'.$var['id'].'">'.$var['id'].': Nom du réseau : '.$var['nom_reseau'].'ip : '.$var'ip_saisie'].' masque : '.$var['mask'].'ip - : '.$var['ip_low'].' ip + : '.$var['ip_high'].' date :'.$var['created_at'].'</input>';
}
