#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

use Phx\NewsScraper;
use Phx\Scrapers\Jpnn;
use Phx\Scrapers\Viva;
use Phx\Scrapers\Suara;
use Phx\Scrapers\Detik;
use Phx\Scrapers\Gonews;
use Phx\Scrapers\Kompas;
use Phx\Scrapers\Liputan6;
use Phx\Scrapers\Tribunnews;
use Phx\Scrapers\Antaranews;
use Phx\Scrapers\Kabardaerah;
use Phx\Scrapers\Indonesiatimur;
use Phx\Scrapers\Sindonews;
use Phx\Scrapers\Banteninfo;

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
	case 'suara':
		$st = new Suara;
		#define("LOG_FILE", "suara.log");
		break;
		
	case 'detik':
		$st = new Detik;
		#define("LOG_FILE", "detik.log");
		break;

	case 'liputan6':
		$st = new Liputan6;
		#define("LOG_FILE", "liputan6.log");
		break;
	
	case 'tribunnews':
		$st = new Tribunnews;
		#define("LOG_FILE", "tribunnews.log");
		break;

	case 'kompas':
		$st = new Kompas;
		#define("LOG_FILE", "kompas.log");
		break;

	case 'antaranews':
		$st = new Antaranews;
		#define("LOG_FILE", "antaranews.log");
		break;

	case 'viva':
		$st = new Viva;
		#define("LOG_FILE", "viva.log");
		break;

	case 'kabardaerah':
		$st = new Kabardaerah;
		#define("LOG_FILE", "kabardaerah.log");
		break;

	case 'jpnn':
		$st = new Jpnn;
		#define("LOG_FILE", "jpnn.log");
		break;

	case 'indonesiatimur':
		$st = new Indonesiatimur;
		#define("LOG_FILE", "indonesiatimur.log");
		break;

	case 'sindonews':
		$st = new Sindonews;
		#define("LOG_FILE", "sindonews.log");
		break;

	case 'gonews':
		$st = new Gonews;
		#define("LOG_FILE", "gonews.log");
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
