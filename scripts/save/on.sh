iptables -I fcaptive -s 192.168.2.68 -m mac --mac-source 1C:65:9D:31:56:CC -j RETURN -m comment --comment "user:1"
iptables -I icaptive -s 192.168.2.68 -m mac --mac-source 1C:65:9D:31:56:CC -j RETURN -m comment --comment "user:1"
