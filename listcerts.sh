#!/bin/sh

certs=$(for pid in $(netstat -nlp | grep 443 | awk '{print $7}' | cut -d '/' -f 1); do strings /proc/$pid/cmdline | grep /var/lib | sed -e 's@.*/var/lib@/var/lib@g'; done | while read conf; do grep pem "$conf"; done | awk '{print $2}' | cut -d ';' -f 1 | sort | uniq)

less $certs
