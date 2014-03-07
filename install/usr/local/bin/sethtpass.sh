#!/bin/sh

[ "$2" = "" ] && echo "Missing username" 1>&2 && exit 1

htpasswd /etc/apache2/passwd/passwords "$1"
