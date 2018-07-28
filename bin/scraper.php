#!/usr/bin/env php
<?php

require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

use Phx\NewsScraper;
use Phx\Scrapers\Liputan6;

switch ($argv) {
	case 'liputan6':
		$st = new Liputan6;
		break;
	
	default:
		break;
}

if (!($st instanceof NewsScraper)) {
	print "Error: ".get_class($st)." is not instanceof ".NewsScraper::class."!\n";
	exit(1);
}

$st->run();
$st->getData();
