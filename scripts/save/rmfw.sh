iptables -vnL --line-numbers | grep "user:" #&& { iptables -D fcaptive 1; iptables -D icaptive 1;

#add
iptables -A fcaptive -s 192.168.2.68 -m mac --mac-source d4:ae:52:e8:e8:be -j RETURN -m comment --comment "user:218"
#list
iptables -vnL --line-numbers | egrep "user:|192.168.2.68" #&& { iptables -D fcaptive 1; iptables -D icaptive 1;
#delete
iptables -D fcaptive 4
