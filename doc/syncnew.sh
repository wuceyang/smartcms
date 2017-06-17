#!/bin/bash

localdir=$(dirname $(cd `dirname $0`; pwd))
remotedir=/web/music
exclude_file=$localdir"/doc/exclude.txt"
serveraddr=106.75.137.82

/usr/bin/rsync $localdir/* "root@"$serveraddr":"$remotedir -avzh --no-o --no-g --no-p --exclude-from $exclude_file
