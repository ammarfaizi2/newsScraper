<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Sindonews as SindonewsProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Sindonews extends NewsScraper
{
	const urlDaerah = "https://daerah.sindonews.com/more/";

	const targetList = [
			"https://daerah.sindonews.com/batam",
			"https://daerah.sindonews.com/palembang",
			"https://daerah.sindonews.com/jabar",
			"https://daerah.sindonews.com/jateng",
			"https://daerah.sindonews.com/diy",
			"https://daerah.sindonews.com/jatim",
			"https://daerah.sindonews.com/topic/10472/pemprov-kalimantan-tengah",
			"https://daerah.sindonews.com/manado",
			"https://search.sindonews.com/search?q=maluku&type=artikel",
	];

	const targetListId = [
		"Jawa Barat"	=> "21",
		"Jawa Tengah"	=> "22",
		"Jawa Timur"	=> "23",
		"Batam"			=> "194",
		"Palembang"		=> "190",
		"Daerah Istimewa Yogyakarta" => "189",
		"Manado"		=> "193",
	];

	/**
	 * @var bool
	 * Set to true if you want to scrap just the first page
	 */
	private $justFirstPage = true;

	/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * @var string
	 */
	private $currentUrl = ""; 

	/**
	 * @var string
	 */
	private $html = "";

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function repair(Closure $repair)
	{
		$repair();
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		foreach (self::targetListId as $key => $id) {
			$url 	= self::urlDaerah . $id;
			// icelog("Scraping {$url}");
			$l = $this->exec(
				$this->currentUrl =	trim($url, "/")
			);

			if (isset($l["error"]) && $l["error"]) {
				icelog("An error occured when scraping {$url}: {$l['errno']} {$l['error']}");
			} else {
				$urls = $this->parseThreadUrlList($l["out"]);
				// icelog("Scraping ".count($urls)." threads...");
				foreach($urls as $url) {
					icelog("Scraping {$url} ");
					$this->processor = new SindonewsProcessor($url, $this);
					if ($this->processor->run()) {
						$this->processor->setRegional(trim($key, "_"));
						icelog("Writing data...");
						$this->insert();
						icelog("Write OK");
					}
				}
			}
		}

	}

	/**
	 * @param string
	 * @return array
	 */
	protected function parseThreadUrlList(string $string): array
	{
		$urls = [];
		$still_scrap 	= true;

		$this->html = $string;

		$i = 1;

		do {
			// icelog("Iteration " . $i);

			$currentPage = $this->html;

			if( preg_match(
				"/href=\"(\S*?)\">\&rsaquo\;<\/a><\/li>/",
				$this->html,
				$m
			) ) {

				$next_page = $m[1];

				if( preg_match_all(
					"/href=\"(.*?)\"/",
					$next_page,
					$arr_url_next
				) ) {
					$arr 			= $arr_url_next[1];
					$index_real 	= count($arr) - 1;
					$url_next 		= $arr[$index_real];

				} else {
					$url_next = $next_page;
				}

				// icelog("next page is " . $url_next);

				$l = $this->exec($url_next);
				if (isset($l["error"]) && $l["error"]) {
					$this->html = "";
					icelog("An error occured when scraping next page {$next_page}: {$l['errno']} {$l['error']}");
				} else {
					$this->html = $l["out"];
				}

			} else {
				icelog("End of pages...");
				$still_scrap = false;
			}

			if(
				preg_match_all(
					"/<p><a href=\"(.*?)\">/",
					$currentPage,
					$n
				)
			) {
				array_walk($n[1], function ($url) use (&$urls) {
						$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
					});
			} else {
				icelog("Urls not found");
			}

			if( $this->justFirstPage ) {
				break;
			}

			$i++;
		} while( $still_scrap );

		$urls = array_unique($urls);
		icelog("Total unique threads: ".count($urls));
		icelog("Filtering scraped threads...");
		$i = 0;

		foreach ($urls as $key => $url) {
			if ($this->hashCheck($url)) {
				$i++;
				unset($urls[$key]);
			}
		}

		if ($i > 0) {
			icelog("{$i} thread(s) ".($i > 1 ? "are" : "is")." skipped because ".($i > 1 ? "they have" : "it has")." already been scraped");
			icelog(count($urls)." thread(s) remaining...");
		}

		return array_values($urls);
	}

	/**
	 * @return void
	 */
	public function getData(): array
	{
		$data = [];

		return $data;
	}
}
