#!/bin/bash
INSTANCEID=$1
BACKUP_FILE=$2
SPACE=$3
echo "Stopping service"
systemctl stop $INSTANCEID
echo "Downloading backup"
s3cmd get s3://$SPACE/$BACKUP_FILE /usr/local/bin/backups/restore/$BACKUP_FILE
echo "Extracting files"
tar -C / -zxvf /usr/local/bin/backups/restore/$BACKUP_FILE
echo "Removing temp files"
rm /usr/local/bin/backups/restore/$BACKUP_FILE
echo "Restarting service"
systemctl start $INSTANCEID
echo "Finished."
