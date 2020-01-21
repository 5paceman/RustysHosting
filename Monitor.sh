#!/bin/bash
WAITTIME="30m"
HOSTNAME=$(hostname)
REDIS="192.168.1.150"
MACHINELOG="reporting:machines:events"

checkResources()
{
DATE=$(date +"%d-%m-%y %T")
CPU=$((grep 'cpu ' /proc/stat;sleep 0.1;grep 'cpu ' /proc/stat)|awk -v RS="" '{print ""($13-$2+$15-$4)*100/($13-$2+$15-$4+$16-$5)""}')
if (($CPU > 80)); then
	redis-cli -h $REDIS RPUSH $MACHINELOG "[$DATE][$HOSTNAME] CPU is higher than 80%"
fi
MEMORY=$(awk '/MemTotal/{t=$2}/MemAvailable/{a=$2}END{print 100-100*a/t""}' /proc/meminfo)
if (($MEMORY > 90)); then
	redis-cli -h $REDIS RPUSH $MACHINELOG "[$DATE][$HOSTNAME] Memory is higher than 90%"
fi
HDD=$(df | awk '/ \/$/{print substr($5, 1, length($5)-1)}')
if (($HDD > 85)); then
	redis-cli -h $REDIS RPUSH $MACHINELOG "[$DATE][$HOSTNAME] HDD is higher than 85%"
fi
echo "CPU: $CPU - RAM: $MEMORY - HDD: $HDD"
USERS=$(wc -l < ServerList.txt)
list="reporting:machines:$HOSTNAME"
redis-cli -h $REDIS RPUSH "$list:cpu" "$CPU"
redis-cli -h $REDIS RPUSH "$list:memory" "$MEMORY"
redis-cli -h $REDIS RPUSH "$list:hdd" "$HDD"
redis-cli -h $REDIS RPUSH "$list:users" "$USERS"

systemctl is-active --quiet Brooce || redis-cli -h $REDIS RPUSH $MACHINELOG "[$DATE][$HOSTNAME] Brooce isn't active on server!"
systemctl is-active --quiet clamav-daemon || redis-cli -h $REDIS RPUSH $MACHINELOG "[$DATE][$HOSTNAME] ClamAV isn't active on server!"
systemctl is-active --quiet vsftpd || redis-cli -h $REDIS RPUSH $MACHINELOG "[$DATE][$HOSTNAME] VSFTP isn't active on server!"
}

while true
do
	echo "Checking resources..."
	checkResources
	echo "Sleeping for $WAITTIME"
	sleep $WAITTIME
done
