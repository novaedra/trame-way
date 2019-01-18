<?php
function countprotocol($data,$protocols){

    $protocol = explode(':', $data['_source']['layers']['frame']['frame.protocols']); // découper frame protocol
    $protocol = end($protocol); //prendre dernière valeur du tableau (protocol)

    if (isset($protocols[$protocol])) { $protocols[$protocol] ++ ; } // incrémentation protocol
    else { $protocols[$protocol] = 1 ; } // sinon initialiser le nouveau protocol
    return $protocols;
}

function tab($array){
  echo '<pre>';
  print_r ($array);
  echo '</pre>';
}

function br(){
    echo '<br/>';
}

function generateRandomString($length){
    $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $charsLength = strlen($characters) -1;
    $string = "";
    for($i=0; $i<$length; $i++){
        $randNum = mt_rand(0, $charsLength);
        $string .= $characters[$randNum];
    }
    return $string;
}

function isLogged(){
  if(!empty($_SESSION['user']['id'])  && !empty($_SESSION['user']['mail']) && !empty($_SESSION['user']['status']) && !empty($_SESSION['user']['ip'])) {
    if($_SESSION['user']['ip'] = $_SERVER['REMOTE_ADDR']){
    return true;
  }
}
return false;
}

function pourcentage($numerateur, $denominateur)
{
    if ($denominateur == 0) {
        echo 'PAS PAR ZERO !';
    } else {
        $result = $numerateur/$denominateur;
        $result = round($result*100,3);
        return $result.' %' ;
    }
}

function rand_color() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}