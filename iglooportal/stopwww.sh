#!/bin/sh

pids=$(ps waxf | grep iglooportal | grep -v grep | awk '{print $1}')
echo "PIDS: $pids"
[ "$pids" != "" ] && kill -9 $pids

true
