<?php

use PhpPy\PhpPy;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @version 0.0.1
 */
final class Analyzer
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		define("PY_HABITAT", BASEPATH."/py");
		$this->py = new PhpPy;
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		$maxProcesses = 10;
		$st = DB::pdo()->prepare("SELECT `id`,`title` FROM `news` WHERE `title` != '';");
		$st->execute();
		
		pcntl_signal(SIGCHLD, SIG_IGN);
		
		$i = 0;
		while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
			
			if (($i > 0) && ($i % 10 === 0)) {
				pcntl_waitpid(-1, $status, WUNTRACED);
				var_dump($status);
			}

			$pid = pcntl_fork();

			if ($pid === 0) {
				print "Processing...";
				DB::getInstance()->__destruct();

				$sentiment = trim($this->py->run("sentistrength_id.py", $r["title"]));
				if ($sentiment !== "") {
					$pdo = new PDO(
						"mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
						DB_USER,
						DB_PASS,
						[
							PDO::ATTR_CASE => PDO::CASE_NATURAL,
							PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
							PDO::ATTR_PERSISTENT => true
						]
					);
					$si = $pdo->prepare("INSERT INTO `sentiment` (`news_id`,`sentiment`) VALUES (:news_id, :sentiment);");
					$si->execute(
						[
							"news_id" => $r["id"],
							"sentiment" => $sentiment
						]
					);
					print "OK\n";
					unset($pdo);
				} else {
					print "Skipped\n";
				}
				exit(0);
			}
		}
	}
}
