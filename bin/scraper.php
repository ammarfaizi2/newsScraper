#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

use Phx\NewsScraper;
use Phx\Scrapers\Liputan6;
use Phx\Scrapers\Tribunnews;

if (! isset($argv[1])) {
	print "\$argv[1] is not defined!\n";
	exit(1);
}

$noend = false;
if (isset($argv[2])) {
	if ($argv[2] === "--while-true") {
		$noend = true;
	} else {
		print "Invalid parameter: \"{$argv[2]}\"\n";
		exit(1);
	}
}

switch ($argv[1]) {
	case 'liputan6':
		$st = new Liputan6;
		define("LOG_FILE", "liputan6.log");
		break;
	
	case 'tribunnews':
		$st = new Tribunnews;
		define("LOG_FILE", "tribunnews.log");
		break;

	default:
		print "Invalid argument \"{$argv[1]}\"";
		exit(1);
		break;
}

if (!($st instanceof NewsScraper)) {
	print "Error: ".get_class($st)." is not instanceof ".NewsScraper::class."!\n";
	exit(1);
}

do {
	$st->run();
	$st->getData();
	if ($noend) {
		icelog("Sleeping 500 seconds...");
		sleep(500);
	}
} while ($noend);
