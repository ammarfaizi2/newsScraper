<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

function tq($str, $max = 32)
{
	if (strlen($str) > $max) {
		return substr($str, 0, $max)."...";
	}
	return $str;
}

function es($str)
{
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

function gptz($n)
{
	switch ($n) {
		case 1:
			return "=";
		case 2:
			return "!=";
		case 3:
			return ">";
		case 4:
			return "<";
		case 5:
			return ">=";
		case 6:
			return "<=";
		case 7:
		case 8:
		case 9:
		case 10;
			return "LIKE";
		default:
			return "=";
	}
}

(new Api)->run();
