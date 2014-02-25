IPTABLES=/sbin/iptables
        # Enable Internet connection sharing
       echo "1" > /proc/sys/net/ipv4/ip_forward
       $IPTABLES -A FORWARD -i eth1 -o eth0 -m state --state ESTABLISHED,RELATED -j ACCEPT
       $IPTABLES -A FORWARD -i eth0 -o eth1 -j ACCEPT
       $IPTABLES -t nat -A POSTROUTING -o eth1 -j MASQUERADE
