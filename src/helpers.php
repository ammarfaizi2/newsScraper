<?php

if (! function_exists("icelog")) {
	function icelog($logMsg, ...$params)
	{
		fprintf(STDOUT, $logMsg = sprintf("[".date("d F Y h:i:s A")."] %s\n", 
			sprintf($logMsg, ...$params)
		));
		if (defined("LOG_FILE")) {
			file_put_contents(LOG_DIR."/".LOG_FILE, $logMsg, FILE_APPEND | LOCK_EX);
		}
	}
}
