#!/bin/bash
serverlist="ServerList.txt"
redisServer="192.168.1.150"

checkServer() {
echo $server
running=$(systemctl show -p SubState --value $server)
echo " - $running"
mainpid=$(systemctl show -p MainPID --value $server)
echo " - $mainpid"
rustpid=$(pgrep -P $mainpid)
echo " - $rustpid"
log=$(journalctl --unit=$server -n 100 --no-pager)
json=$(jq -n \
                --arg running "$running" \
                --arg mainpid "$mainpid" \
                --arg rustpid "$rustpid" \
                --arg log "$log" \
                '{running: $running, mainpid: $mainpid, rustpid: $rustpid, log: $log}')

variable="reporting:servers:status:$server"
redis-cli -h $redisServer SET $variable "$json"
}

while IFS= read -r line
do
server=$line
checkServer
done < "$serverlist"
