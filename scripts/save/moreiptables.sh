set -e
/sbin/iptables -t nat -D captive -s 192.168.2.1 -m mac --mac-source 1c:65:9d:31:56:cc -m comment --comment 'user:1' -j RETURN
/sbin/iptables -D fcaptive -s 192.168.2.1 -m mac --mac-source 1c:65:9d:31:56:cc -m comment --comment 'user:1' -j RETURN
/sbin/iptables -D icaptive -s 192.168.2.1 -m mac --mac-source 1c:65:9d:31:56:cc -m comment --comment 'user:1' -j RETURN
conntrack -D --src 192.168.2.1
