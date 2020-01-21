#!/bin/bash
SERVICEID=$1

systemctl stop $SERVICEID
/usr/games/steamcmd +login anonymous +force_install_dir "/home/$SERVICEID/rust/"  +app_update 258550 +quit
cd "/home/$SERVICEID/rust/"
wget https://umod.org/games/rust/download/develop
unzip -o develop
systemctl start $SERVICEID
