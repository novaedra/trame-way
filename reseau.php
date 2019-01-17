<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT * FROM trame_way ";
$query = $pdo -> prepare($sql);
$return = $query -> fetchAll();
tab($return);
