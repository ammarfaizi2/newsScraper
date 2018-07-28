<?php

if (! function_exists("icelog")) {
	function icelog($logMsg)
	{
		fprintf(STDOUT, "[".date("d F Y h:i:s A")."] %s\n", $logMsg);
	}
}
