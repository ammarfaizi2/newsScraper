#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";

$whileTrue = true;
$param = [
	"tribunnews",
	"detik",
];

$noend = "";
if ($whileTrue) {
	$noend = "--while-true";
}
foreach ($param as $key => $value) {
	shell_exec(
		"nohup sh -c \"nohup ".PHP_BINARY." ".__DIR__."/fixer.php {$value} >> ".LOG_DIR."/{$value}_fixer.log 2>&1 &\" >> /dev/null 2>&1 &"
	);
}
