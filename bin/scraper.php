#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

use Phx\NewsScraper;
use Phx\Scrapers\Liputan6;

if (! isset($argv[1])) {
	print "\$argv[1] is not defined!\n";
	exit(1);
}

switch ($argv[1]) {
	case 'liputan6':
		$st = new Liputan6;
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

$st->run();
$st->getData();
