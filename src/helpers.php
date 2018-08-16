<?php

if (! function_exists("icelog")) {
	/**
	 * @param string $logMsg
	 * @param mixed	 ...$params
	 */
	function icelog(string $logMsg, ...$params)
	{
		fprintf(STDOUT, $logMsg = sprintf("[".date("d F Y h:i:s A")."] %s\n", 
			sprintf($logMsg, ...$params)
		));
		if (defined("LOG_FILE")) {
			file_put_contents(LOG_DIR."/".LOG_FILE, $logMsg, FILE_APPEND | LOCK_EX);
		}
	}
}

if (! function_exists("reg")) {
	/**
	 * @param array		$&a
	 * @param \Closure	$c
	 * @return void
	 */
	function reg(array &$a, Closure $c) {
		$a[] = $c;
	}
}
