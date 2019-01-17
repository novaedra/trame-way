<<<<<<< HEAD
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


=======
<?php
include ('inc/pdo.php');
include ('inc/function.php');
include ('inc/header.php');
include ('inc/nav.php');

$sql = "SELECT * FROM trame_way;";
$query = $pdo -> prepare($sql);
$query -> execute();



>>>>>>> 0b51d901074240c48a0d22abb62be0ceccb944fa
