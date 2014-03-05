#!/bin/sh

main() {
        /usr/sbin/apache2 -f /var/lib/iglooportal/apache2.conf -DNO_DETACH &
        sleep 1
        netstat -nlp | egrep ":80|:443|:447"
}

main
