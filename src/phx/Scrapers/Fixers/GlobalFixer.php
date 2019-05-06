<?php

namespace Phx\Scrapers\Fixers;

use DB;
use PDO;
use Analyzer;
use PDOStatement;
use Phx\DataFixer;
use Phx\Scrapers\Fixers\ExtendedFixers\Datetime\DatetimeParser;
use Phx\Scrapers\Fixers\ExtendedFixers\Datetime\DatetimeConverter;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Fixers
 * @license MIT
 * @version 0.0.1
 */
final class GlobalFixer extends DataFixer
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
		$this->datetimeFixer();
	}

	/**
	 * @return void
	 */
	private function datetimeFixer(): void
	{
		static $i = 1;
		$sq = $this->pdo->prepare("UPDATE `news` SET `datetime`=:datetime WHERE `id`=:id LIMIT 1;");
		$st = $this->pdo->prepare("SELECT `id`,`datetime` FROM `news`;");
		$st->execute();
		while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
			$sf = new DatetimeConverter($r["datetime"]);
			$type = $sf->getType();
			if ($type !== DatetimeParser::DF_UNKNOWN && $type !== DatetimeParser::DF5) {
				$unix = $sf->toUnix();
				print "Updating ".$r["id"]." from ".$r["datetime"]." to ".$unix."...";
				$sq->execute([":datetime" => ((string)$unix), ":id" => $r["id"]]);
				print "OK\n";
			} else {
				if ($type === DatetimeParser::DF5) {
					print "[".($i++)."]\t".$r["datetime"]." has already been converted to unix time\n";
				} else {
					print "Got unknown format: ".$r["datetime"]."\n";
					file_put_contents(BASEPATH."/storage/unknown_datetime_format.txt", $r["datetime"]."\n", FILE_APPEND | LOCK_EX);
				}
			}
		}
	}
}
