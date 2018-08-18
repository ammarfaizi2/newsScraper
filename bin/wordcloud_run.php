#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

function reg(&$a, Closure $c) {
	$a[] = $c;
}

$st = DB::pdo()->prepare("SELECT `regional` FROM `regional`");
$st->execute();

while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	reg($a, function() use ($r) {
		cli_set_process_title("icetea_worker --module=wordcloud.so --target=\"{$r['regional']}\"");
		shell_exec(
			"nohup ".PHP_BINARY." ".__DIR__."/wordcloud.php {$r['regional']} >> ".LOG_DIR."/wordcloud/".str_replace(" ", "_", $r['regional']).".log 2>&1"
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