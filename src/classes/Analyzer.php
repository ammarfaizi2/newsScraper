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
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		define("PY_HABITAT", BASEPATH."/py");
		$this->py = new PhpPy;
		$this->pdo = DB::pdo();
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		$st = $this->pdo->prepare("SELECT `id`,`title` FROM `news` WHERE `title` != '';");
		$st->execute();
		while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
			print $this->py->run("sentistrength_id.py", $r["title"]);
		}
	}
}
