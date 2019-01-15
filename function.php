<?php
function countprotocol($data,$protocols){

    $protocol = explode(':', $data['_source']['layers']['frame']['frame.protocols']); // découper frame protocol
    $protocol = end($protocol); //prendre dernière valeur du tableau (protocol)

    if (isset($protocols[$protocol])) { $protocols[$protocol] ++ ; } // incrémentation protocol
    else { $protocols[$protocol] = 1 ; } // sinon initialiser le nouveau protocol
    return $protocols;
}