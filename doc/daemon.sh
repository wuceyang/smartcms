#!/bin/sh

phpbin=/usr/local/php5.6.20/bin/php
setsid=/usr/bin/setsid
doc_dir=$(cd `dirname $0`; pwd)
entry=$(cd `dirname $doc_dir`; pwd)"/cli/Index.php"
cmdfile=$doc_dir"/cmd.txt"

for cmd in `cat $cmdfile`
do
	cmd=`echo $cmd | sed s/[[:space:]]//g`
    procnum=`ps aux | grep $cmd | grep -v grep | wc -l`
    if [ $procnum -lt 1 ]; then
            $setsid $phpbin $entry $cmd > /dev/null &
    fi
done
