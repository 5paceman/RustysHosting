#!/bin/bash
# Script to create a users hosting space, applies CGroup system resource limiting
# sets up SFTP access and copies the necessary game files over 
#
# SYNTAX: MakeUser.sh instanceid plan disklimit password gameport
#
DEBUG=true
if [ "$DEBUG" = false ] ; then
    exec 3>&1 4>&2
    trap 'exec 2>&4 1>&3' 0 1 2 3
    exec 1>/var/log/MakeUser.log 2>&1
fi

GAMEPORT=$5
INSTANCEID=$1
PLAN=$2
HARDLIMIT=$3
PASSWORD=$4
SOFTLIMIT=$(((HARDLIMIT / 100) * 80))
GAMEDIR=/usr/local/games/rust

echo "========== MakeUser.sh =========="
echo "InstanceID is $INSTANCEID"
echo "Plan is $PLAN"
echo "Disk quota is $HARDLIMIT hardlimit and $SOFTLIMIT softlimit."
echo "Creating user.."
useradd $INSTANCEID --create-home --shell /bin/bash --groups $PLAN
echo -e "$PASSWORD\n$PASSWORD" | passwd $INSTANCEID
echo "Setting quota.."
setquota -u $INSTANCEID $SOFTLIMIT $HARDLIMIT 0 0 / 
USERID=$(id -u $INSTANCEID)
echo "Assigned port number is $GAMEPORT"
echo "Opening port.."
ufw allow $GAMEPORT
ufw allow $(($GAMEPORT + 1))
ufw allow $(($GAMEPORT + 2))
echo "Copying game to home directory"
cp -R $GAMEDIR /home/$INSTANCEID 
sed -i "s:SERVERPORT=.*:SERVERPORT=$GAMEPORT:g" /home/$INSTANCEID/rust/runds.sh
sed -i "s:RCONPORT=.*:RCONPORT=$(($GAMEPORT + 1)):g" /home/$INSTANCEID/rust/runds.sh
sed -i "s:RCONPASSWORD=.*:RCONPASSWORD=\"$PASSWORD\":g" /home/$INSTANCEID/rust/runds.sh
chown -R $INSTANCEID /home/$INSTANCEID/rust/
usermod --home /home/$INSTANCEID/rust/oxide/ $INSTANCEID
echo "Creating service..."
cp /usr/local/bin/TemplateService.service /etc/systemd/system/$INSTANCEID.service
sed -i "s:#description#:$INSTANCEID game service:g" /etc/systemd/system/$INSTANCEID.service
sed -i "s:#user#:$INSTANCEID:g" /etc/systemd/system/$INSTANCEID.service
sed -i "s:#execpath#:/home/$INSTANCEID/rust/runds.sh:g" /etc/systemd/system/$INSTANCEID.service
sed -i "s:#workingpath#:/home/$INSTANCEID/rust/:g" /etc/systemd/system/$INSTANCEID.service
systemctl enable $INSTANCEID
systemctl start $INSTANCEID
sed -i -e '$a\' -e "$INSTANCEID" ServerList.txt
echo "========== END =========="
