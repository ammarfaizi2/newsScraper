<?php

if (PHP_SAPI !== "cli") {
	print "You must run this scripts on cli environment!\nCurrent environment: ".PHP_SAPI;
	exit(1);
}

defined("BASEPATH") or defined("BASEPATH", __DIR__."/..");

function iceteaInternalAutoloader($class)
{
	$class = str_replace("\\", "/", $class);
	if (substr($class, 0, 4) === "Phx/") {
		if (file_exists($f = BASEPATH."/src/phx/".substr($class, 4).".phx")) {
			require $f;
		}
	} else {
		if (file_exists($f = BASEPATH."/src/classes/".$class.".php")) {
			require $f;
		}
	}
}

spl_autoload_register("iceteaInternalAutoloader");

require __DIR__."/../src/helpers.php";
