#!/bin/bash
SPACE=$1
FILE=$2
echo "Deleting backup $FILE"
s3cmd del s3://$SPACE/$FILE
echo "Finished."
