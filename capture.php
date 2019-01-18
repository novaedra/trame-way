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

<?php
include('inc/function.php');

 ?>
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


<form action="capture.php" method="POST">
    <span>Nom du fichier :<span/><br/>
        <input type="text" name="filename"/><span>.JSON</span><br/>
        <span>selection format:</span>

        <select name="format">
            <option value="temps">Temps</option>
            <option value="trame">Nombre de trame</option>
        </select>
        <input type="number" name="nombre"/>

    <input type="submit" name="capture" value="Capturer réseau"/>
    </form>

<?php
if (!empty($_POST['capture'])) {
    $start = false;
    $effacer = false;
    if (!empty($_POST['filename']) and is_string($_POST['filename'])) {
        $filename = trim(strip_tags($_POST['filename']));
    }
    else {
        $filename = 'output';
    }
    if(!empty($_POST['format'])){
        $parametre = false;
        if (!empty($_POST['nombre']) and is_numeric($_POST['nombre']) and $_POST['nombre'] >= 10 and $_POST['nombre'] <= 1000000){
            $nombre = trim(strip_tags($_POST['nombre']));
        }
        else {
            $nombre = 10;
        }
        if($_POST['format'] == 'trame') {
            $parametre = ' -c '.$nombre;
        }
        if ($_POST['format'] == 'temps') {
            if ($nombre >= 3600 or $nombre <= 10) {
                $nombre = 10;
            }
            $parametre = ' -a duration:'.$nombre;
        }
        if ($parametre == false) {
            $parametre = ' -c 10';
        }
    }
    else {
        $parametre = ' -c 10';
    }
    exec("sudo touch pcap/input.pcap;");
    exec("sudo chmod o=rw pcap/input.pcap;");
    exec("sudo tshark". $parametre ." -w pcap/input.pcap -F libpcap;");
    exec("sudo touch trames/" . $filename . ".json;");
    exec("sudo chmod o=rw trames/" . $filename . ".json;");
    exec("sudo tshark -r pcap/input.pcap -T json >trames/" . $filename . ".json;");
    exec("sudo rm pcap/input.pcap;");
    $start = true;
    ?>
    <br/>
    <a title="Télécharger la capture que vous venez d\'effectuer" href="trames/<?php echo $filename.'.json'; ?>" download="<?php echo $filename.'.json' ?>">Télécharger la capture</a>
<?php }

if (!empty($_POST['capture']) and $start == true) {
    exec("sudo find /var/www/html/trames -type f -mmin +60 -delete");
    $effacer = true;
  }


?>
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
