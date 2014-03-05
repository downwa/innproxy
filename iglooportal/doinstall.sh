#!/bin/sh

service apache2 stop

if [ "$1" = "--install" ]; then
	# Install needed software
	apt-get install arptables bridge-utils conntrack dnsutils ebtables git nmap ufw php5 libapache2-mod-php5 realpath dnsmasq isc-dhcp-server
fi

cp -av * /
#cp -av /var/lib/zentyal-captiveportal/ssl/ /var/lib/iglooportal/
mkdir -p /var/log/iglooportal/
/etc/init.d/iglooportal restart

