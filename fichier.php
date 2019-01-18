
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

<?php
define('NL', "\n");
require ('IP4Calc.php');
if(!isset($_POST['analyser'])) { ?>
    <div class="content">
        <div class="container-fluid">
            <form method="post" action="fichier.php" enctype="multipart/form-data">
                <input type="file" name="json"/></br>
                <input type="submit" name="analyser" value="analyser"/>
            </form>
        </div>
    </div>
    <?php
}
else {
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
        if ($file_size > 10000000000) {
            $errors = 'Le fichier doit être au maximum de 1Go';
        }
        if (empty($errors) == false) {
            echo $errors . '<br/>';
        }
        else {
            $jsonData = file_get_contents($_FILES['json']['tmp_name']);
            $jsons = json_decode($jsonData, true); //lecture json
            $nbrtrame = 0;
            $ipv4 = array();
            $mac = array();
            $ipList = array();
            $macList = array();
            $ipAndMac = array();
            $protocols = array();
            $infraction = array();
            $tempo = array();
            $dest = 0;
            $source = 0;
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
            ?>
            <table>
                <caption><?php echo $file_name ?></caption>

                <thead>
                <tr>
                    <th><?php echo 'Total adresses IP ' ?></th>
                    <th><?php echo 'Total adresses MAC ' ?></th>
                    <th><?php echo 'Total trames ' ?></th>
                </tr>
                </thead>

                <tfoot>
                <tr>

                </tr>
                </tfoot>

                <tbody>
                <tr>
                    <th><?php echo count($ipList); ?></th>
                    <th><?php echo count($macList); ?></th>
                    <th><?php echo $nbrtrame; ?></th>
                </tr>
                </tbody>
            </table>

            <?php
        }
    }
}
if (!empty($protocols)) {
    $sql = "SELECT * FROM reseau;";
    $query = $pdo->prepare($sql);
    $query->execute();
    $SRSX = $query->fetchALL();
    foreach ($ipv4 as $key => $value) {
        $tempo = array();
        $tempo = explode(' to ', $key);
        foreach ($SRSX as $cle => $value) {
            foreach ($value as $key => $valeur) {
                $mask = $value['mask'];
                $low = $value['ip_low'];
                $oIP = new IP4Calc($low, $mask);
                $temp = true;
                foreach ($tempo as $to) {
                    if ($oIP->partOf($to) == false) {
                        $infraction[$value['id']]['infraction']++;
                    }
                }
            }
        }
    }
    $total = count($protocols);
    $compteur = 0;
    ?>

    <canvas id="doughnut-chart" width="800" height="450"></canvas>
    <script>
        new Chart(document.getElementById("doughnut-chart"), {
            type: 'doughnut',
            data: {
                labels: [<?php foreach ($protocols as $key => $value) { $compteur++; ?>"<?php echo $key;?>"<?php if ($compteur != $total) { echo ','; }} ?>],
                datasets: [
                    {
                        <?php $compteur = 0; ?>
                        backgroundColor: [<?php foreach ($protocols as $key => $value) { $compteur++; ?>"<?php echo rand_color();?>"<?php  if ($compteur != $total) { echo ','; }} ?><?php $compteur = 0; ?>],
                        data: [<?php foreach ($protocols as $key => $value) { $compteur++; echo "$value"; if ($compteur != $total) { echo ','; }} ?>]
                    }
                ]
            },
            options: {
                title: {
                    display: true
                }
            }
        });
    </script>
    <?php
}
?>


<!-- CHARTS -->

<canvas id="myChart"></canvas>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
      type: 'bar',

      data: {
        labels: ["Total des trames", "Erreurs", "sdffz"],
        datasets: [{
          label: "My First dataset",
          backgroundColor: 'rgb(255, 99, 132)',
          borderColor: 'rgb(255, 99, 132)',
          data: [<?php echo join(',', $infraction); ?>],
        }]
      },

      // Configuration options go here
      options: {}
    });
</script>




















<!-- FIN CHARTS -->



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
