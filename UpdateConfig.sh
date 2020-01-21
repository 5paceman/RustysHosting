#!/bin/bash


DEBUG=true
if [ "$DEBUG" = false ] ; then
    exec 3>&1 4>&2
    trap 'exec 2>&4 1>&3' 0 1 2 3
    exec 1>/var/log/UpdateConfig.log 2>&1
fi

SERVICEID=$1
HOSTNAME=$2
WORLDSIZE=$3
SEED=$4
TICKRATE=$5
MAXPLAYERS=$6
DESCRIPTION=$7
HEADERIMAGE=$8
GLOBALCHAT=$9
SERVICEPASSWORD=${10}
echo "========== UpdateConfig.sh =========="
echo "ServiceID is $SERVICEID"
echo "New Settings: "
echo "   HOSTNAME: $HOSTNAME"
echo "   WORLDSIZE: $WORLDSIZE"
echo "   SEED: $SEED"
echo "   TICKRATE: $TICKRATE"
echo "   MAXPLAYERS: $MAXPLAYERS"
echo "   DESCRIPTION: $DESCRIPTION"
echo "   HEADERIMAGE: $HEADERIMAGE"
echo "   GLOBALCHAT: $GLOBALCHAT"
echo "   SERVICEPASSWORD: $SERVICEPASSWORD"
sed -i "s:HOSTNAME=.*:HOSTNAME=\"$HOSTNAME\":g" /home/$SERVICEID/rust/runds.sh
sed -i "s:WORLDSIZE=.*:WORLDSIZE=$WORLDSIZE:g" /home/$SERVICEID/rust/runds.sh
sed -i "s:SERVERSEED=.*:SERVERSEED=\"$SEED\":g" /home/$SERVICEID/rust/runds.sh
sed -i "s:TICKRATE=.*:TICKRATE=$TICKRATE:g" /home/$SERVICEID/rust/runds.sh
sed -i "s:MAXPLAYERS=.*:MAXPLAYERS=$MAXPLAYERS:g" /home/$SERVICEID/rust/runds.sh
sed -i "s:DESCRIPTION=.*:DESCRIPTION=\"$DESCRIPTION\":g" /home/$SERVICEID/rust/runds.sh
sed -i "s:HEADERIMAGE=.*:HEADERIMAGE=\"$HEADERIMAGE\":g" /home/$SERVICEID/rust/runds.sh
sed -i "s:GLOBALCHAT=.*:GLOBALCHAT=\"$GLOBALCHAT\":g" /home/$SERVICEID/rust/runds.sh
sed -i "s:RCONPASSWORD=.*:RCONPASSWORD=\"$SERVICEPASSWORD\":g" /home/$SERVICEID/rust/runds.sh
echo -e "$SERVICEPASSWORD\n$SERVICEPASSWORD" | passwd $INSTANCEID
echo "========== END =========="
