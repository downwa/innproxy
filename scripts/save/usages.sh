#!/bin/bash

/home/administrator/scripts/listusers | sort -n | while read -r uid password datestamp seconds; do
	usage=$(/home/administrator/scripts/usage "$uid")
	datestamp1=$(if [ "$datestamp" -gt 0 ]; then echo "$datestamp"; fi 2>/dev/null)
	seconds1=$(if [ "$seconds" -gt 0 ]; then echo "$seconds"; fi 2>/dev/null)
	if [ "$datestamp1" = "" -a "$seconds1" = "" ]; then
		[ "$datestamp" != "" ] && password="$password $datestamp"
		[ "$seconds"   != "" ] && password="$password $seconds"
		datestamp=0
		seconds=0
	fi
	printf "%s,%s,%s,%s,%7.2f\n" "$uid" "$password" "$datestamp" "$seconds" "$usage"
done >/tmp/usages.csv.tmp
mv /tmp/usages.csv.tmp /tmp/usages.csv
