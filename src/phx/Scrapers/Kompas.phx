<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Kompas as KompasProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Kompas extends NewsScraper
{
	const targetList = [
		"Nanggroe Aceh Darussalam" => "http://indeks.kompas.com/tag/aceh",
		"Sumatera Utara" => "http://indeks.kompas.com/tag/sumatera-utara",
		"Sumatera Utara_" => "http://indeks.kompas.com/tag/sumut",
		"Sumatera Barat" => "http://indeks.kompas.com/tag/sumatera-barat",
		"Kepulauan Riau" => "http://indeks.kompas.com/tag/kepulauan-riau",
		"Sumatera Selatan" => "https://indeks.kompas.com/tag/palembang",
		"Bangka Belitung" => "http://indeks.kompas.com/tag/bangka-belitung",
		"Lampung" => "https://indeks.kompas.com/tag/lampung",
		"Jawa Barat" => "https://indeks.kompas.com/tag/jawa-barat",
		"Banten" => "http://indeks.kompas.com/tag/banten",
		"Jawa Tengah" => "http://indeks.kompas.com/tag/jawa-tengah",
		"Daerah Istimewa Yogyakarta" => "http://indeks.kompas.com/tag/yogyakarta",
		"Jawa Timur" => "http://indeks.kompas.com/tag/jawa-timur",
		"Bali" => "http://indeks.kompas.com/tag/Bali",
		"Nusa Tenggara Timur" => "https://indeks.kompas.com/tag/nusa-tenggara-timur",
		"Kalimantan Tengah" => "https://indeks.kompas.com/tag/KALIMANTAN.TENGAH",
		"Kalimantan Timur" => "https://indeks.kompas.com/tag/kalimantan-timur",
		"Kalimantan Utara" => "https://indeks.kompas.com/tag/kalimantan-utara",
		// "Sulawesi Utara" => "https://kilasdaerah.kompas.com/sulawesi-utara",
		"Sulawesi Barat" => "https://indeks.kompas.com/tag/sulawesi-barat",
		"Sulawesi Tengah" => "https://indeks.kompas.com/tag/sulawesi-tengah",
		"Sulawesi Tenggara" => "https://indeks.kompas.com/tag/sulawesi-tenggara",
		"Sulawesi Selatan" => "https://indeks.kompas.com/tag/sulawesi-selatan",
		"Gorontalo" => "https://indeks.kompas.com/tag/GORONTALO",
		"Maluku" => "https://indeks.kompas.com/tag/Maluku",
		"Maluku Utara" => "https://indeks.kompas.com/tag/maluku-utara",
		"Papua" => "https://indeks.kompas.com/tag/Papua"
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
				CURLOPT_FOLLOWLOCATION => true
			]);
			if (isset($l["error"]) && $l["error"]) {
				icelog("An error occured when scraping {$url}: {$l['errno']} {$l['error']}");
			} else {
				$urls = $this->parseThreadUrlList($l["out"]);
				icelog("Scraping ".count($urls)." threads...");
				foreach($urls as $url) {
					$this->processor = new KompasProcessor($url, $this);
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
			"/(?:<a class=\"article__link\" href=\")(.*)(?:\")/Usi",
			$string,
			$m
		)) {
			$i = 0;
			foreach ($m[1] as $url) {
				$i++;
				$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
			}
			icelog("Got {$i} thread(s) from page 1");
		}

		/**
		 * Get max page.
		 */
		icelog("Getting max page");
		if (preg_match_all(
			"/(?:data-ci-pagination-page=\")(\d+)(?:\")/Usi",
			$string,
			$m
		)) {
			$max = (int) max($m[1]);
			icelog("Got max page: {$max}");
			for ($i=2; $i <= $max; $i++) {
				icelog("Scraping ".$this->currentUrl."/desc/{$i}...");

				$e = $this->exec($this->currentUrl."/desc/{$i}");

				if (isset($e["error"]) && $e["error"]) {
					icelog(
						"An error occured when scraping {$this->currentUrl}/desc/{$i}: {$e['errno']} {$e['error']}"
					);
					continue;
				}

				if (preg_match_all(
					"/(?:<a class=\"article__link\" href=\")(.*)(?:\")/Usi",
					$e["out"],
					$m
				)) {
					$qq = 0;
					foreach ($m[1] as $url) {
						$qq++;
						$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
					}
					icelog("Got {$qq} thread(s) from page {$i}, Total thread(s): ".count($urls = array_unique($urls)));
				}
			}
		} else {
			icelog("Could not get max page!");
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
