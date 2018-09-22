#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";

function reg(&$a, Closure $c) {
	$a[] = $c;
}

$whileTrue = true;
$param = [
	"suara",
	"liputan6",
	"tribunnews",
	"detik",
	"kompas",
	"antaranews",
	"viva",
	"jpnn",
	"kabardaerah",
	"indonesiatimur",
	"sindonews",
	"sumutpos",
	"kabarmedan",
	"medansatu",
	"kabarsumut",
	"goaceh",
	"prohaba",
	"modusaceh",
	"ajnn",
	"portalsatu",
	"gonews",
	"matatelinga",
	"beritasumut",
	"gosumut",
	"waspada",
	"hargasumut",
	"kabarsumbar",
	"hariansinggalang",
	"minangkabaunews",
	"kabarpadang",
	"gosumbar"
];


$noend = "";
if ($whileTrue) {
	$noend = "--while-true";
}

$a = [];

foreach ($param as $key => $value) {
	reg($a, function() use ($value, $noend) {
		cli_set_process_title("icetea_worker --module=icetea_scraper.so --target=$value");
		shell_exec(
			"nohup ".PHP_BINARY." ".__DIR__."/scraper.php {$value} {$noend} >> ".LOG_DIR."/{$value}.log 2>&1"
		);
	});
}

foreach ($a as $v) {
	if (!isset($pid) || $pid !== 0) {
		$pid = pcntl_fork();
	}
	if (!$pid) {
		while(true) {
			$v();
		}
	}
}

while (true) {
	sleep(1000);
}
