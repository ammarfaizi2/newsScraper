<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Detik as DetikProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Detik extends NewsScraper
{
	const targetList = [
		"Nanggroe Aceh Darussalam" => "https://www.detik.com/tag/aceh/",
		"Sumatera Utara" => "https://www.detik.com/tag/sumatera-utara/",
		"Jambi" => "https://www.detik.com/tag/jambi/",
		"Sumatera Selatan" => "https://www.detik.com/tag/palembang/",
		"Bangka Belitung" => "https://www.detik.com/tag/bangka-belitung/",
		"Jakarta" => "https://www.detik.com/tag/jakarta",
		"Banten" => "https://www.detik.com/tag/banten/",
		"Jawa Tengah" => "https://www.detik.com/tag/jawa-tengah/",
		"Nusa Tenggara Timur" => "https://www.detik.com/tag/nusa-tenggara-timur/",
		"Kalimantan Timur" => "https://www.detik.com/tag/kalimantan-timur/",
		"Kalimantan Utara" => "https://www.detik.com/tag/kalimantan-utara/",
		"Sulawesi Barat" => "https://www.detik.com/tag/sulawesi-barat/",
		"Sulawesi Tengah" => "https://www.detik.com/tag/sulawesi-tengah/",
		"Sulawesi Tenggara" => "https://www.detik.com/tag/sulawesi-tenggara/",
		"Sulawesi Selatan" => "https://www.detik.com/tag/sulawesi-selatan/",
		"Gorontalo" => "https://www.detik.com/tag/gorontalo/",
		"Maluku" => "https://www.detik.com/tag/maluku/",
		"Papua" => "https://www.detik.com/tag/papua/"
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

	/**
	 * @return void
	 */
	public function run(): void
	{
		foreach (self::targetList as $key => $url) {
			icelog("Scraping {$url}...");
			$l = $this->exec($this->currentUrl = $url, [
				CURLOPT_FOLLOWLOCATION => false
			]);
			if (isset($l["error"]) && $l["error"]) {
				icelog("An error occured when scraping {$url}: {$l['errno']} {$l['error']}");
			} else {
				$urls = $this->parseThreadUrlList($l["out"]);
				icelog("Scraping ".count($urls)." threads...");
				foreach($urls as $url) {
					$this->processor = new DetikProcessor($url, $this);
					if ($this->processor->run()) {
						icelog("Writing data...");
						$this->processor->setRegional(trim($key, "_"));
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
			"/(?:<article>.+<a.+href=\")(.*)(?:\")/Usi",
			$string,
			$m
		)) {
			array_walk($m[1], function($url) use (&$urls) {
				$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
			});
			icelog("Got ".count($urls)." thread(s)");
		}

		$i = 2; $fails = 0;
		do {
			if ($fails > 4) {
				icelog("Skipping paginator because it was reached the maximum number of fails...");
				break;
			}

			icelog("Scraping ".$this->currentUrl."?page={$i}");
			$e = $this->exec($this->currentUrl."?page={$i}",[
				CURLOPT_FOLLOWLOCATION => false
			]);

			if (isset($e["error"]) && $e["error"]) {
				icelog("An error occured when scraping {$this->currentUrl}?page={$i}: {$e['errno']} {$e['error']}");
				continue;
			}

			if (preg_match_all(
				"/(?:<article>.+<a.+href=\")(.*)(?:\")/Usi",
				$e["out"],
				$m
			)) {

				array_walk($m[1], function($url) use (&$urls) {
					$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
				});
				icelog("Got ".count($m[1])." thread(s), Total thread(s): ".count($urls));
			} else {
				$fails++;
				icelog("Could not get thread");
			}

			$i++;			
		} while (empty($e["info"]["redirect_url"]));
		
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
