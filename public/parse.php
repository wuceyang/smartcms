<?php
	$file = './xml.xml';

	$xml = simplexml_load_file($file);

	$dict = $xml->dict->array->children();

	$dicts = [];

	foreach ($dict as $k => $v) {
		
		$dicts[] = getEmotion($v);
	}

	function getEmotion($v){

		$text = (string) $v->string[0];
		$path = (string) $v->string[3];

		return ['value' => $text, 'icon' => '/emotion/sina/' . $path];
	}

	echo json_encode($dicts, JSON_UNESCAPED_UNICODE);