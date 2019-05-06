<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Suara as SuaraPrrocessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Suara extends NewsScraper
{
	const targetList = [
		"Sumatera Barat" => "https://www.suara.com/tag/sumatera-barat",
		"Bangka Belitung" => "https://www.suara.com/tag/bangka-belitung",
		"Kalimantan Barat" => "https://www.suara.com/tag/kalimantan-barat",
		"Kalimantan Selatan" => "https://www.suara.com/tag/kalimantan-selatan",
		"Kalimantan Utara" => "https://www.suara.com/tag/kalimantan-utara",
		"Sulawesi Tenggara" => "https://www.suara.com/tag/sulawesi-tenggara",
		"Gorontalo" => "https://www.suara.com/tag/gorontalo",
		"Maluku" => "https://www.suara.com/tag/maluku",
		"Maluku Utara" => "https://www.suara.com/tag/maluku-utara",
		"Papua Barat" => "https://www.suara.com/tag/papua-barat",
		"Papua" => "https://www.suara.com/tag/papua",
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
					$this->processor = new SuaraPrrocessor($url, $this);
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
			"/(?:<div class=\"post-thumb pull-left\">.+<a.+href=\")(https:\/\/www\.suara\.com\/.+\/.+)(?:\")/Usi",
			$string,
			$m
		)) {
			array_walk($m[1], function ($url) use (&$urls) {
				if (! preg_match("/^https:\/\/www\.suara\.com\/video/Usi", $url)) {
					$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
				}
			});
			icelog("Got ".(count($m[1]))." thread(s), Total threads: ".count($urls));
		}

		$i = 2;

		do {
			$nn = 0;

			icelog("Scraping {$this->currentUrl}/page--{$i}...");

			$lq = $this->exec(
				$this->currentUrl."/page--{$i}"
			);

			if (isset($lq["error"]) && $lq["error"]) {
				icelog("An error occured when scraping {$this->currentUrl}/page--{$i}: {$lq['errno']} {$lq['error']}");
				continue;
			}

			if (preg_match_all(
				"/(?:<div class=\"post-thumb pull-left\">.+<a.+href=\")(https:\/\/www\.suara\.com\/.+\/.+)(?:\")/Usi",
				$lq["out"],
				$m
			)) {
				array_walk($m[1], function ($url) use (&$urls) {
					if (! preg_match("/^https:\/\/www\.suara\.com\/video/Usi", $url)) {
						$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
					}
				});
				icelog("Got ".($nn = count($m[1]))." thread(s), Total threads: ".count($urls));
			}

			$i++;

		} while ($nn);

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
