#!/bin/sh
# Purpose: Keep *either* WiFI *or* ethernet up but not both (avoid routing loops)

WIF="wlan0"
WCONF="/etc/wpa_supplicant/wpa_supplicant.conf"
DPID="/var/run/dhclient.${WIF}.pid"
DLEASE="/var/lib/dhcp/dhclient.${WIF}.leases"
LAN="eth1"
LIP="192.168.200.14"
LGW="192.168.200.250"
GIP="8.8.8.8"

onerun() {
	date +"%D %T wlan check"
	local fail=0
	ifconfig $WIF | grep -q "inet addr:" || fail=1
	[ "$fail" = "0" ] && { ping -c 1 $LGW >/dev/null || fail=2; }
	[ "$fail" = "0" ] && { ping -c 1 $GIP >/dev/null || fail=3; }
	if [ "$fail" -gt 0 ]; then
		date +"%D %T fail $fail: RETRYING..."
		killall wpa_supplicant
		killall dhclient3
	fi	
	pidof wpa_supplicant >/dev/null || {
		date +"%D %T wpa_supplicant -B -c$WCONF -i${WIF}";
		wpa_supplicant -B -c$WCONF -i${WIF};
	}
	pidof dhclient3 >/dev/null || {
		date +"%D %T dhclient3 -e IF_METRIC=100 -pf $DPID -lf $DLEASE -1 $WIF";
		dhclient3 -e IF_METRIC=100 -pf $DPID -lf $DLEASE -1 $WIF;
	}
}

lanrun() {
	date +"%D %T lan check"
	local fail=0
	ifconfig $LAN $LIP
	route add default gw $LGW
	ifconfig $LAN | grep -q "inet addr:" || fail=1
	[ "$fail" = "0" ] && { ping -c 1 $LGW >/dev/null || fail=2; }
	[ "$fail" = "0" ] && { ping -c 1 $GIP >/dev/null || fail=3; }
	if [ "$fail" -gt 0 ]; then
		date +"%D %T lan fail $fail: Try WiFI..."
		ifconfig $LAN $LIP
	else
		date +"%D %T lan deconfigured WiFI"
		ifconfig $WIF 0.0.0.0
		ifconfig $WIF down
	fi
	date +"%D %T lan return $fail"
	return $fail
}

main() {
  while [ true ]; do
	lanrun >>/var/log/lan.log 2>&1 || onerun >/var/log/wifi.log 2>&1
	sleep 30
	gzip /var/log/lan.log
	gzip /var/log/wifi.log
  done
}

init() {
date +"%D %T wifi start: proxydaemon"
start choggiung.proxydaemon # Make sure this is running after reboot
date +"%D %T wifi: apache2"
/etc/init.d/apache2 start
date +"%D %T wifi: zentyal"
/etc/init.d/zentyal start
date +"%D %T wifi: main"
main
} >/var/log/lan.log

init
