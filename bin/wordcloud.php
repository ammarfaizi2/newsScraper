#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

use Phx\WordCloud;

$regional = "Jakarta";

$st = new WordCloud($regional);
$st->runTitleWordCloud();
