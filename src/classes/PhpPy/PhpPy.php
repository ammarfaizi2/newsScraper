<?php

namespace PhpPy;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package \PhpPy
 * @since 0.0.1
 */
final class PhpPy
{	
	/**
	 * @var array
	 */
	private $env = [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if (! defined("PY_HABITAT")) {
			print "PY_HABITAT is not defined!\n";
			exit(1);
		}
		$this->env = $_SERVER;
		unset($this->env["argv"], $this->env["argc"]);
	}

	/**
	 * @param string $file
	 * @param string $stdin
	 * @return string
	 */
	public function run(string $file, string $stdin = ""): string
	{
		/**
		 * @see https://secure.php.net/manual/en/function.proc-open.php
		 */

		$descriptorspec = array(
		   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		   2 => array("file", "/tmp/error-output.txt", "w") // stderr is a file to write to
		);

		$cwd = PY_HABITAT;
		$process = proc_open("python3 \"{$file}\"", $descriptorspec, $pipes, $cwd, $this->env);

		if (is_resource($process)) {
			
		    // $pipes now looks like this:
		    // 0 => writeable handle connected to child stdin
		    // 1 => readable handle connected to child stdout
		    // Any error output will be appended to /tmp/error-output.txt

		    fwrite($pipes[0], $stdin);
		    fclose($pipes[0]);

		    $stdout = stream_get_contents($pipes[1]);
		    fclose($pipes[1]);

		    // It is important that you close any pipes before calling
		    // proc_close in order to avoid a deadlock
		    $return_value = proc_close($process);
		}

		$stdout .= file_get_contents("/tmp/error-output.txt");

		return trim($stdout);
	}
}
