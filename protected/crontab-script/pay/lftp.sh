#!/bin/sh
fileName=$1
lftp -u edaijia,edj@798 sftp://202.96.33.145:3044 <<EOF
get $fileName .
quit
EOF
