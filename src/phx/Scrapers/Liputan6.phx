<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Liputan6 as Liputan6Processor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Liputan6 extends NewsScraper
{
	const targetList = [
		"Bali" => "https://www.liputan6.com/tag/bali",
		"Sumatera Selatan" => "https://www.liputan6.com/tag/sumsel",
		"Sumatera Utara" => "https://www.liputan6.com/tag/sumatera-utara",
		"Sumatera Barat" => "https://www.liputan6.com/tag/sumatera-barat",
		"Banten" => "https://www.liputan6.com/tag/banten",
		"Bangka Belitung" => "https://www.liputan6.com/tag/bangka-belitung",
		"Bengkulu" => "https://www.liputan6.com/tag/bengkulu",
		"Lampung" => "https://www.liputan6.com/tag/lampung",
		"Riau" => "https://www.liputan6.com/tag/riau",
		"Nanggroe Aceh Darussalam" => "https://www.liputan6.com/tag/banda-aceh",
		"Nanggroe Aceh Darussalam_" => "https://www.liputan6.com/tag/aceh",
		"Kepulauan Riau" => "https://www.liputan6.com/tag/batam",
		"Kepulauan Riau_" => "https://www.liputan6.com/tag/kepulauan-riau",
		"Jambi" => "https://www.liputan6.com/tag/jambi",
		"Jakarta" => "https://www.liputan6.com/tag/jakarta",
		"Jawa Barat" => "https://www.liputan6.com/tag/jawa-barat",
		"Jawa Timur" => "https://www.liputan6.com/tag/jawa-timur",
		"Jawa Tengah" => "https://www.liputan6.com/tag/jawa-tengah",
		"Daerah Istimewa Yogyakarta" => "https://www.liputan6.com/tag/yogyakarta",
		"Nusa Tenggara Barat" => "https://www.liputan6.com/tag/ntb",
		"Nusa Tenggara Timur" => "https://www.liputan6.com/tag/ntt",
		"Kalimantan Barat" => "https://www.liputan6.com/tag/kalimantan-barat",
		"Kalimantan Tengah" => "https://www.liputan6.com/tag/kalimantan-tengah",
		"Kalimantan Tengah_" => "https://www.liputan6.com/tag/kalteng",
		"Kalimantan Selatan" => "https://www.liputan6.com/tag/kalimantan-selatan",
		"Kalimantan Timur" => "https://www.liputan6.com/tag/kalimantan-timur",
		"Kalimantan Utara" => "https://www.liputan6.com/tag/kalimantan-utara",
		"Sulawesi Utara" => "https://www.liputan6.com/tag/sulawesi-utara",
		"Sulawesi Barat" => "https://www.liputan6.com/tag/sulawesi-barat",
		"Sulawesi Tengah" => "https://www.liputan6.com/tag/sulawesi-tengah",
		"Sulawesi Tenggara" => "https://www.liputan6.com/tag/sulawesi-tenggara",
		"Sulawesi Selatan" => "https://www.liputan6.com/tag/sulawesi-selatan",
		"Gorontalo" => "https://www.liputan6.com/tag/gorontalo",
		"Maluku" => "https://www.liputan6.com/tag/maluku",
		"Maluku Utara" => "https://www.liputan6.com/tag/maluku-utara",
		"Papua" => "https://www.liputan6.com/tag/papua",
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
			$l = $this->exec($url);
			$this->currentUrl = $url;
			if (isset($l["error"]) && $l["error"]) {
				icelog("An error occured when scraping {$url}: {$l['errno']} {$l['error']}");
			} else {
				$urls = $this->parseThreadUrlList($l["out"]);
				icelog("Scraping ".count($urls)." threads...");
				foreach($urls as $url) {
					$this->processor = new Liputan6Processor($url, $this);
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
		$max = null;
		if (preg_match(
			"/(?:class=\"simple-pagination__goto-last\" href=\".+\?page=)(\d+)(?:\")/Usi",
			$string,
			$m
		)) {
			$max = (int) $m[1];
			icelog("Got max page: ".$max);
		}
		if (preg_match_all("/href=\"(https\:\/\/www\.liputan6\.com\/[a-z]+\/read\/.+)\"/Usi", $string, $m)) {
			foreach ($m[1] as $key => $url) {
				$urls[] = html_entity_decode($url, ENT_QUOTES, "UTF-8");
			}
		}

		if (isset($max)) {
			for ($i=2; $i <= $max; $i++) {
				icelog("Scraping ".$this->currentUrl."?page={$i}...");
				$q = $this->exec($this->currentUrl."?page={$i}");
				if (isset($q["error"]) && $q["error"]) {
					icelog("An error occured when scraping {$this->currentUrl}: {$q['errno']} {$q['error']}");
					icelog("Skipping...");
					continue;
				}
				if (preg_match_all("/href=\"(https\:\/\/www\.liputan6\.com\/[a-z]+\/read\/.+)\"/Usi", $q["out"], $m)) {
					icelog("Got ".count($m[1])." thread(s)");
					$m[1] = array_unique($m[1]);
					foreach ($m[1] as $key => $url) {
						$urls[] = html_entity_decode($url, ENT_QUOTES, "UTF-8");
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
