#!/bin/bash

localdir=$(dirname $(cd `dirname $0`; pwd))
remotedir=/data/Music
exclude_file=$localdir"/doc/exclude.txt"
serveraddr=106.75.148.134

/usr/bin/rsync $localdir/* "root@"$serveraddr":"$remotedir -avzh --no-o --no-g --no-p --exclude-from $exclude_file
