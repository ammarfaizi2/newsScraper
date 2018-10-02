#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

$st = new Analyzer;
$st->run();
