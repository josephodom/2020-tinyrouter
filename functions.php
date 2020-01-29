<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function code($str)
{
	$str = htmlentities($str);
	
	$str = preg_replace('/\'([^\']+)\'/','<span class="string">\'$1\'</span>', $str);
	$str = preg_replace('/\/\/(.*)$/m', '<span class="comment">//$1</span>', $str);
	$str = preg_replace('/\$([a-zA-Z0-9_]+)/', '<span class="variable">$$1</span>', $str);
	$str = preg_replace('/([a-zA-Z0-9]+)\(/', '<span class="method">$1</span>(', $str);
	
	return '<code>' . $str . '</code>';
}

function codeFile($filename)
{
	if(!file_exists($filename))
	{
		return 'File not found: ' . $filename;
	}
	
	$str = file_get_contents($filename);
	
	return code($str);
}

?>