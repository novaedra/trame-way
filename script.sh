sudo touch pcap/input.pcap;
sudo chmod o=rw pcap/input.pcap;
sudo tshark -c 100 -w pcap/input.pcap -F libpcap;
sudo touch json/output.json;
sudo chmod o=rw json/output.json;
sudo tshark -r pcap/input.pcap -T json >json/output.json;
sudo rm pcap/input.pcap;