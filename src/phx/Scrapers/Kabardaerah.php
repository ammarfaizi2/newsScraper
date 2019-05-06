<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Kabardaerah as KabardaerahProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Kabardaerah extends NewsScraper
{
	const targetList = [
		"Sumatera Barat" => "https://sumbar.kabardaerah.com/",
		"Bangka Belitung" => "https://babel.kabardaerah.com/",
		"Bengkulu" => "https://bengkulu.kabardaerah.com",
		"Sulawesi Barat" => "https://sulbar.kabardaerah.com/",
		# "Gorontalo" => "https://gorontalo.kabardaerah.com/",
		"Maluku" => "https://maluku.kabardaerah.com/"
	];

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
		foreach (self::targetList as $key => $url) {
			icelog("Scraping {$url}...");
			$l = $this->exec(
				$this->currentUrl =	trim($url, "/")
			);
			if (isset($l["error"]) && $l["error"]) {
				icelog("An error occured when scraping {$url}: {$l['errno']} {$l['error']}");
			} else {
				$urls = $this->parseThreadUrlList($l["out"]);
				icelog("Scraping ".count($urls)." threads...");
				foreach($urls as $url) {
					$this->processor = new KabardaerahProcessor($url, $this);
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

		if (preg_match_all(
			"/(?:<div class=\"td-module-thumb\"><a href=\")(.*)(?:\")/Usi",
			$string,
			$m
		)) {
			array_walk($m[1], function ($url, $index) use (&$m, &$urls) {
				$url = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
				if (empty($url)) {
					unset($m[1][$index]);
				} else {
					$urls[] = $url;
				}
			});
			icelog("Got ".count($urls)." thread(s) on page 1");
		}

		$max = null;

		/**
		 * Get last page.
		 */
		if (preg_match(
			"/(?:class=\"last\".+>)(\d+)(?:<)/Usi",
			$string,
			$m
		)) {
			$max = abs((int)$m[1]);
		}
		
		if (isset($max) && $max > 2) {
			icelog("Got last page {$max}");
			for ($i=2; $i <= $max; $i++) { 
				icelog("Scraping ".$this->currentUrl."/page/".$i);
				$l = $this->exec(
					$this->currentUrl."/page/".$i
				);
				if (isset($l["error"]) && $l["error"]) {
					icelog("An error occured when scraping {$url}: {$l['errno']} {$l['error']}");
				} else {
					if (preg_match_all(
						"/(?:<div class=\"td-module-thumb\"><a href=\")(.*)(?:\")/Usi",
						$l["out"],
						$m
					)) {
						array_walk($m[1], function ($url, $index) use (&$m, &$urls) {
							$url = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
							if (empty($url)) {
								unset($m[1][$index]);
							} else {
								$urls[] = $url;
							}
						});
						$urls = array_unique($urls);
						icelog("Got ".count($m[1])." thread(s) on page $i. Total unique thread(s): ".count($urls));
					} else {
						icelog("Could not get the threads");
					}
				}
			}
		}

		$urls = array_unique($urls);
		icelog("Got ".count($urls)." unique threads...");
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
