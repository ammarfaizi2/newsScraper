<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Gonews as GonewsProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Gonews extends NewsScraper
{
	const targetList = [
		"Kalimantan Barat" => "https://www.gonews.co/berita/kalimantan-barat/",
		"Kalimantan Utara" => "https://www.gonews.co/berita/kalimantan-utara/",
		"Sulawesi Barat" => "https://www.gonews.co/berita/sulawesi-barat/",
		"Sulawesi Tengah" => "https://www.gonews.co/berita/sulawesi-tengah/",
		"Gorontalo" => "https://www.gonews.co/berita/gorontalo/"
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
					$this->processor = new GonewsProcessor($url, $this);
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
			"/(?:<a class=\"headline-sublink\" href=\")(.*)(?:\")/Usi",
			$string,
			$m
		)) {
			array_walk($m[1], function ($url) use (&$urls) {
				$url = trim($url);
				if (filter_var($url, FILTER_VALIDATE_URL)) {
					$urls[] = html_entity_decode($url, ENT_QUOTES, "UTF-8");
				} else {
					$urls[] = "https://www.gonews.co".html_entity_decode($url, ENT_QUOTES, "UTF-8");
				}
			});
		}


		// // Next time we need the paginate scraper.
		// // Due to the short deadline we are skipped this section.
		//
		//
		// $keyword = explode("/", $this->currentUrl);
		// $keyword = $keyword[count($keyword) - 1];
		// $i = 0;
		// $reg = explode("/", $this->currentUrl);
		// $reg = end($reg);
		// $ppg = 1;

		// do {

		// } while ($nn);
		
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
