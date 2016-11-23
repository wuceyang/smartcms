<?php
	return [
		'format' => 'daily', //日志文件格式，可用的有:daily/weekly/monthly/allinone
		'driver' => 'file',  //日志存储引擎，默认为文件存储，见\Library\Log\FileLog,其他方式实现的日志，请按照上述类的方式实现save方法即可
	];