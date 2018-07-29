#!/usr/bin/env php
<?php

$whileTrue = true;
$param = [
	"liputan6",
	"tribunnews"
];


$noend = "";
if ($whileTrue) {
	$noend = "--while-true";
}
foreach ($param as $key => $value) {
	shell_exec(
		"nohup sh -c \"nohup ".PHP_BINARY." ".__DIR__."/scraper.php {$value} {$noend} >> /dev/null 2>&1 &\" >> /dev/null 2>&1 &"
	);
}
