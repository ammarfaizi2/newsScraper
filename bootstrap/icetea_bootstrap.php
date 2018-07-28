<?php

defined("BASEPATH") or defined("BASEPATH", __DIR__."/..");

function iceteaInternalAutoloader($class)
{
	$class = str_replace("\\", "/", $class);
	if (file_exists($f = BASEPATH."/src/classes/".$class.".php")) {
		require $f;
	} elseif (file_exists($f = BASEPATH."/src/phx/".$class.".phx")) {
		require $f;
	}
}

spl_autoload_register("iceteaInternalAutoloader");

require __DIR__."/../src/helpers.php";
