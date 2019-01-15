<html>
<head>
</head>
<body>
<center>

    <div class="circle4" id="loading" >
        <div class="circle3" id="loading" >
            <div class="circle2" id="loading" >
                <div class="circle1" id="loading" >
                    <div class="circle" id="loading">
                    </div>
                </div>
            </div>
        </div>
    </div></center>
</body>
</html>

<?php
exec("sudo touch pcap/input.pcap;");
echo 'fichier PCAP : Fait <br/>';

exec("sudo chmod o=rw pcap/input.pcap;");
echo 'Droits modifiés <br/>';

exec("sudo tshark -c 10000 -w pcap/input.pcap -F libpcap;");
echo 'Capture terminée <br/>';

exec("sudo touch trames/output.json;");
echo 'Fichier JSON : Fait <br/>';

exec("sudo chmod o=rw trames/output.json;");
echo 'Droits modifiés <br/>';

exec("sudo tshark -r pcap/input.pcap -T json >trames/output.json;");
echo 'JSON modifié <br/>';

exec("sudo rm pcap/input.pcap;");
echo 'Fichier PCAP effacé <br/>';

$filename = '/var/www/html/trames/output.json';

if (file_exists($filename)) {

}
else {

}