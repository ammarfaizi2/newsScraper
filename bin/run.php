#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";

$whileTrue = true;
$param = [
	"liputan6",
	"tribunnews",
	"detik",
	"kompas",
	"antaranews"
];


$noend = "";
if ($whileTrue) {
	$noend = "--while-true";
}
foreach ($param as $key => $value) {
	shell_exec(
		"nohup sh -c \"nohup ".PHP_BINARY." ".__DIR__."/scraper.php {$value} {$noend} >> ".LOG_DIR."/{$value}.log 2>&1 &\" >> /dev/null 2>&1 &"
	);
}
