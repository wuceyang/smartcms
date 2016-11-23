#!/bin/sh

doc_dir=$(cd `dirname $0`; pwd)
cmdfile=$doc_dir"/cmd.txt"


for cmd in `cat $cmdfile`
do
    procnum=`ps aux | grep $cmd | wc -l`
    if [ $procnum -gt 1 ]; then
            #`ps aux | grep $cmd | awk '{print $2}' | xargs kill -9` > /dev/null`
            `ps aux | grep $cmd | awk '{print $2}' | xargs kill -9 > /dev/null`
    fi
done