<?php

namespace Phx\Scrapers\Fixers;

use DB;
use PDO;
use Analyzer;
use PDOStatement;
use Phx\DataFixer;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Fixers
 * @license MIT
 * @version 0.0.1
 */
final class Tribunnews extends DataFixer
{	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @return void
	 */
	public function run(): void
	{
		$this->kupangFixer();
	}

	/**
	 * @return void
	 */ 
	private function kupangFixer(): void
	{
		$st = $this->pdo->prepare(
			"SELECT `id`,`url` FROM `news` WHERE SUBSTR(`url`, 1, 29) = 'http://kupang.tribunnews.com/';"
		);
		$st->execute();
		$st2 = $this->pdo->prepare(
			"UPDATE `news` SET `datetime` = :datetime WHERE `id`=:id LIMIT 1;"
		);
		while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
			$this->kupangDatetimeFixer($r["url"], (int)$r["id"], $st2);
		}
	}

	/**
	 * @param string 		$url
	 * @param int 	 		$id
	 * @param \PDOStatement $st2
	 * @return void
	 */
	private function kupangDatetimeFixer(string $url, int $id, PDOStatement $st2): void
	{
		icelog("Scraping {$url}...");
		$l = $this->exec($url);

		if (isset($l["error"]) && $l["error"]) {
			icelog("An error occured when scraping {$this->url}: {$l['errno']} {$l['error']}");
			icelog("Trying...");
			icelog("Scraping {$url}...");
			$l = $this->exec($url);
			if (isset($l["error"]) && $l["error"]) {
				icelog("An error occured when scraping {$this->url}: {$l['errno']} {$l['error']}");
				return;
			}
		}

		/**
		 * Get date and time.
		 */
		if (preg_match(
			"/(?:<time( class=\"grey\" style=\"display:inline-block;\")?>)(.*)(?:<\/time>)/Usi",
			$l["out"],
			$m
		)) {
			$datetime = trim(html_entity_decode($m[2], ENT_QUOTES, "UTF-8"));
			$st2->execute([":id" => $id, ":datetime" => $datetime]);
			icelog("Got date and time: {$datetime}");
		} else {
			icelog("Could not get the date and time");
		}
	}
}
