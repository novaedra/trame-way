
<?php
include ('inc/function.php');
define('NL', "\n");
include('IP4Calc.php');
?>

<!DOCTYPE html>
<html lang="fr">
<!--classe computer, rule, error -->
  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Vue d'ensemble</title>

    <!-- Bootstrap core CSS-->
    <link href="front/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="front/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="front/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="front/css/sb-admin.css" rel="stylesheet">

  </head>

  <body id="page-top">

    <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

      <a class="navbar-brand mr-1" href="index.php">Trame-Ouais</a>

      <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
      </button>


      <!-- Navbar -->


    </nav>

    <div id="wrapper">

      <!-- Sidebar -->
      <ul class="sidebar navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="fichier.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Analyse réseau</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="capture.php">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Capture réseau</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="reseau.php">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Ajout/suppression réseau</span></a>
        </li>
      </ul>

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Breadcrumbs-->
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="#">Vue d'ensemble</a>
            </li>
          </ol>

          <section class="container">



            <form method="post" action="reseau.php">

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
                </select>
                </br>

                <input type="submit" name="calculer" value="Ajouter un nouveau réseau"/>
            </form>
<?php
if (!empty($_POST['calculer'])) {
    $addr = trim(strip_tags($_POST['adresse']));
    $mask = trim(strip_tags($_POST['masque']));
    $nom_reseau = trim(strip_tags($_POST['nom_reseau']));
    if (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $oIP = new IP4Calc($addr, $mask);
        $sql = "INSERT INTO reseau (nom_reseau,ip_saisie,mask,ip_low,ip_high,created_at) VALUES (:nom_reseau,:addr,:mask,:ip_low,:ip_high , NOW())";
        $query = $pdo -> prepare($sql);
        $query -> bindValue(':nom_reseau', $nom_reseau, PDO::PARAM_STR);
        $query -> bindValue(':addr', $addr, PDO::PARAM_STR);
        $query -> bindValue(':mask', $mask, PDO::PARAM_STR);
        $query -> bindValue(':ip_low', $oIP->get(IP4Calc::MIN_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);
        $query -> bindValue(':ip_high', $oIP->get(IP4Calc::MAX_HOST, IP4Calc::QUAD_DOTTED), PDO::PARAM_STR);
        $query -> execute();
    }
    else {
        echo $addr.'Adresse IP invalide verifiez que l\'adresse  entrer  est une adresse valide ';
        br();
        echo '<a href="reseau.php">retour</a>';
    }
}
?>

<form action="reseau.php" method="POST">

<?php
if (!empty($_POST['supprimer'])) {
    foreach ($_POST as $key => $value) {
        $key = trim(strip_tags($key));
        if (is_numeric($key)) {
            $sql = "DELETE FROM reseau WHERE id=:id;";
            $query = $pdo -> prepare($sql);
            $query->bindValue(':id', $key, PDO::PARAM_INT);
            $query -> execute();
        }
    }
    $sql = "SELECT * FROM reseau;";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    $return = $query -> fetchALL();
    foreach ($return as $var) { ?>
        <input type="checkbox" name="<?php echo $var['id']; ?>" value="supprime moi vite stp"><?php echo $var['nom_reseau']; ?></input><br/>
    <?php }
}
else {
    $sql = "SELECT * FROM reseau;";
    $query = $pdo -> prepare($sql);
    $query -> execute();
    $return = $query -> fetchALL();
    foreach ($return as $var) { ?>
        <input type="checkbox" name="<?php echo $var['id']; ?>" value="supprime moi vite stp"><?php echo $var['nom_reseau']; ?></input><br/>
    <?php }
}
?>
<input type="submit" name="supprimer" value="Supprimer"></input>
</form>

<!-- Sticky Footer -->
<footer class="sticky-footer">
  <div class="container my-auto">
    <div class="copyright text-center my-auto">
      <span>Copyright © Trame-Ouais 2018</span>
    </div>
  </div>
</footer>

</div>
<!-- /.content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
<i class="fas fa-angle-up"></i>
</a>

<!-- Bootstrap core JavaScript-->
<script src="front/vendor/jquery/jquery.min.js"></script>
<script src="front/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="front/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Page level plugin JavaScript-->
<script src="front/vendor/chart.js/Chart.min.js"></script>
<script src="front/vendor/datatables/jquery.dataTables.js"></script>
<script src="front/vendor/datatables/dataTables.bootstrap4.js"></script>

<!-- Custom scripts for all pages-->
<script src="front/js/sb-admin.min.js"></script>

<!-- Demo scripts for this page-->
<script src="front/js/demo/datatables-demo.js"></script>
<script src="front/js/demo/chart-area-demo.js"></script>

</body>

</html>
