install:
	cp -av * /
	#cp -av /var/lib/zentyal-captiveportal/ssl/ /var/lib/iglooportal/
	apt-get install conntrack php5 libapache2-mod-php5
	/etc/init.d/iglooportal restart
	mkdir -p /var/log/iglooportal/

run: install
	/usr/sbin/apache2 -f /var/lib/iglooportal/apache2.conf -DNO_DETACH &
	netstat -nlp | egrep ":8080|:447"

stop:
	./stopwww.sh
