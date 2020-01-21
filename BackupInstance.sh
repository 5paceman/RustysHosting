#!/bin/bash
INSTANCEID=$1
DATETIME=`date +%Y%m%d-%H_%M_%S`
SPACE="rustyshosting-eu"
REDIS="192.168.1.150"
echo "7zipping /home/$INSTANCEID/rust/"
tar cfJ /usr/local/bin/backups/$INSTANCEID-$DATETIME.tar.xz --exclude="/home/$INSTANCEID/rust/oxide/Steam/" --exclude="/home/$INSTANCEID/rust/RustDedicated" --exclude="/home/$INSTANCEID/rust/RustDedicated_Data" --exclude="/home/$INSTANCEID/rust/steamapps" --exclude="/home/$INSTANCEID/rust/Bundles" --exclude="/home/$INSTANCEID/rust/develop" --exclude="/home/$INSTANCEID/rust/Compiler.x86_x64" --exclude="/home/$INSTANCEID/rust/runds.sh" --exclude="/home/$INSTANCEID/rust/libsteam_api.so" --exclude="/home/$INSTANCEID/rust/steamclient.so" /home/$INSTANCEID/rust/
SIZE=$(stat -c%s "/usr/local/bin/backups/$INSTANCEID-$DATETIME.tar.xz")
echo "Uploading to $SPACE"
s3cmd put /usr/local/bin/backups/$INSTANCEID-$DATETIME.tar.xz s3://$SPACE
echo "Removing 7z file"
rm /usr/local/bin/backups/$INSTANCEID-$DATETIME.tar.xz
echo "Sending to message queue to log entry"
redis-cli -h $REDIS LPUSH brooce:queue:webserver:pending "BackupJob.php $INSTANCEID $INSTANCEID-$DATETIME.tar.xz $SPACE $SIZE"
echo "Finished."
