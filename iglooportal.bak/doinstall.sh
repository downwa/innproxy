#!/bin/sh

if [ "$1" = "--install" ]; then

	# Don't run DHCP on this bridge (if we do, we need to disable other DHCP server(s))
	/etc/init.d/isc-dhcp-server stop # Stop DHCP if it's running
	apt-get remove isc-dhcp-server

	# Install needed software
	apt-get install arptables bridge-utils conntrack dnsutils ebtables git nmap ufw php5 libapache2-mod-php5 realpath dnsmasq
fi

# Remove existing IP addresses
ifconfig eth0 0.0.0.0
ifconfig eth1 0.0.0.0

# Create bridge
brctl addbr br0
brctl addif br0 eth0
brctl addif br0 eth1
ip link set br0 up

# Reset firewall
iptables -P INPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -P OUTPUT ACCEPT
# Flush and delete chains
iptables -F
iptables -X
iptables -t nat -F
iptables -t nat -X
iptables -t mangle -F
iptables -t mangle -X
iptables -t raw -F
iptables -t raw -X

# Add a management IP address and gateway to the bridge
ip addr add 192.168.200.23/24 brd + dev br0
route add default gw 192.168.200.250

# Verify configuration (STP=Spanning Tree Protocol, default not enabled)
brctl show

# Install firewall login page
rm /var/www/index.html
cp -av /home/pi/innproxy/iglooportal/usr/share/iglooportal/www/index.php /var/www/

cp -av * /
#cp -av /var/lib/zentyal-captiveportal/ssl/ /var/lib/iglooportal/
/etc/init.d/iglooportal restart
mkdir -p /var/log/iglooportal/

