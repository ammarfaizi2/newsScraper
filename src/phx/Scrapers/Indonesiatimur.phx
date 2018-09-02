<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Indonesiatimur as IndonesiatimurProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Indonesiatimur extends NewsScraper
{
	const targetList = [
		"Nusa Tenggara Timur" => "https://indonesiatimur.co/k/propinsi/nusa-tenggara-timur-daerah/",
		"Sulawesi Barat" => "https://indonesiatimur.co/k/propinsi/sulawesi-barat-daerah/",
		"Sulawesi Tengah" => "https://indonesiatimur.co/k/propinsi/sulawesi-tengah-daerah/",
		"Maluku Utara" => "https://indonesiatimur.co/k/propinsi/maluku-utara-daerah/",
		"Papua Barat" => "https://indonesiatimur.co/k/propinsi/papua-barat-daerah/",
		"Papua" => "https://indonesiatimur.co/k/propinsi/papua-daerah/",
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
					$this->processor = new IndonesiatimurProcessor($url, $this);
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

		icelog("Scraping page 1...");
		if (preg_match_all(
			"/(?:<div class=\"post-content\">.+href=\")(.*)(?:\")/Usi",
			$string,
			$m
		)) {
			array_walk($m[1], function ($url) use (&$urls) {
				$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
			});
			$urls = array_unique($urls);
			icelog("Got %d thread(s), Total unique threads: %d", count($m[1]), count($urls));
		}

		$cleanUrl = trim($this->currentUrl, "/");
		$i = 2;

		do {
			$nnn = false;
			$nextUrl = sprintf("%s/page/%d/", $cleanUrl, $i);
			icelog("Scraping next page %s...", $nextUrl);
			$l = $this->exec($nextUrl, [CURLOPT_FOLLOWLOCATION => true]);
			if (isset($l["error"], $l["errno"]) && $l["errno"]) {
				icelog("An error occured when scraping %s: (%d): %s", $nextUrl, $l["errno"], $l["error"]);
			} else {
				if (isset($l["out"])) {
					if (preg_match_all(
						"/(?:<div class=\"post-content\">.+href=\")(.*)(?:\")/Usi",
						$l["out"],
						$m
					)) {
						$oldUniqueThreadsCount = count($urls);
						array_walk($m[1], function ($url) use (&$urls) {
							$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
						});
						$urls = array_unique($urls);
						$newUniqueThreadsCount = count($urls);
						icelog("Got %d thread(s), Total unique threads: %d", count($m[1]), $newUniqueThreadsCount);
						if ($newUniqueThreadsCount > $oldUniqueThreadsCount) {
							$nnn = true;
							$i++;
						} else {
							icelog("Could not find unique thread");
							icelog("Ending loop...");
						}
					}
				} else {
					icelog("Could not find out index.");
				}
			}
			break;
		} while ($nnn);

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
