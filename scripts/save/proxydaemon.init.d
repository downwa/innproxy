#!/bin/sh

LOG="/var/log/proxydaemon.log"

onerun() {
	date +"%D %T: usages/logouts/timeouts"
	/home/administrator/scripts/usages     # Keep track of usage of each user
	/home/administrator/scripts/timeouts >>/tmp/timeouts.log 2>&1  # Remove accounts of timed out users
}

daemon() {
	date +"%D %T: Proxy Daemon"
	olog=""
	while [ true ]; do
		LOG=$(date +"/var/log/proxydaemon-%A.log")
		if [ "$LOG" != "$olog" -a "$olog" != "" ]; then # Rotate log
			gzip "$olog"
		fi
		onerun >>"$LOG"
		sleep 60
		olog=$LOG
	done
}

case $1 in
	start)
		echo "Starting proxydaemon..."
		nohup "$0" daemon >/var/log/proxydaemon.err 2>&1 </dev/null
		"$0" status
	;;
	stop)
		echo "Stopping proxydaemon..."
                pid=$(ps waxf | grep proxydaemon | grep "proxydaemon daemon" | grep -v grep | awk '{print $1}')
                if [ "$pid" != "" ]; then
			kill $pid 2>/dev/null
			sleep 1
			kill -9 $pid 2>/dev/null
                        echo "Stopped"
                else
                        echo "Not running"
                fi

	;;
	restart)
		"$0" stop
		"$0" start
	;;
	status)
		pid=$(ps waxf | grep proxydaemon | grep "proxydaemon daemon" | grep -v grep | awk '{print $1}')
		if [ "$pid" != "" ]; then
			echo "Running (pid $pid)"
		else
			echo "Stopped"
		fi
	;;
	daemon)
		daemon &
	;;
	*)
	;;
esac
