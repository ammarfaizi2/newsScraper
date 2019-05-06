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
final class Detik extends DataFixer
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
		$this->duplicatesFixer();
	}

	/**
	 * @return void
	 */
	private function duplicatesFixer()
	{
		icelog("Running get all query...");
		$st = $this->pdo->prepare(
			"SELECT `id` FROM `news` WHERE `url` LIKE '%detik.com%';"
		);
		$st->execute();
		while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
			icelog("Fixing ".$r["id"]."...");
			$this->authorFixer($r);
			$this->imagesFixer($r);
		}
	}

	/**
	 * @param array $r
	 * @return void
	 */
	private function authorFixer(array $r): void
	{
		$st = $this->pdo->prepare(
			"SELECT `authors`.`id`,`authors`.`author_name` FROM `news` INNER JOIN `authors` ON `authors`.`news_id` = `news`.`id` WHERE `news`.`id` = :id;"
		);
		$st->execute(
			[
				":id" => $r["id"]
			]
		);
		$authors = [];
		$delete = [];
		while($rr = $st->fetch(PDO::FETCH_ASSOC)) {
			if (isset($authors[$rr["author_name"]])) {
				if (isset($delete[$rr["author_name"]])) {
					$delete[$rr["author_name"]]++;
				} else {
					$delete[$rr["author_name"]] = 1;
				}
			} else {
				$authors[$rr["author_name"]] = 1;
			}
		}
		$c = count($delete);
		if ($c > 0) {
			icelog("Got {$c} duplicate data");
			icelog("Deleting...");
			foreach ($delete as $key => $value) {
				$q = "DELETE FROM `authors` WHERE `author_name` = :author_name AND `news_id` = :news_id LIMIT {$value};";
				$bind = [
						":author_name" => $key,
						":news_id" => $r["id"]
					];
				icelog("Executing {$q} with bind param ".json_encode($bind)."...");

				$this->pdo->prepare($q)->execute($bind);
			}
		}
		icelog("Data has been fixed");
	}

	/**
	 * @param array $r
	 * @return void
	 */
	private function imagesFixer(array $r): void
	{
		
		$st = $this->pdo->prepare(
			"SELECT `images`.`id`,`images`.`image_url` FROM `news` INNER JOIN `images` ON `images`.`news_id` = `news`.`id` WHERE `news`.`id` = :id;"
		);
		$st->execute(
			[
				":id" => $r["id"]
			]
		);
		$images = [];
		$delete = [];
		while($rr = $st->fetch(PDO::FETCH_ASSOC)) {
			if (isset($images[$rr["image_url"]])) {
				if (isset($delete[$rr["image_url"]])) {
					$delete[$rr["image_url"]]++;
				} else {
					$delete[$rr["image_url"]] = 1;
				}
			} else {
				$images[$rr["image_url"]] = 1;
			}
		}
		$c = count($delete);
		if ($c > 0) {
			icelog("Got ".count($delete)." duplicate data");
			icelog("Deleting...");
			foreach ($delete as $key => $value) {
				$q = "DELETE FROM `images` WHERE `image_url` = :image_url AND `news_id` = :news_id LIMIT {$value};";
				$bind = [
						":image_url" => $key,
						":news_id" => $r["id"]
					];
				icelog("Executing {$q} with bind param ".json_encode($bind)."...");

				$this->pdo->prepare($q)->execute($bind);
			}
		}
		icelog("Data has been fixed");
	}
}
