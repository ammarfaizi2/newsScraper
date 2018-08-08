<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Antaranews as AntaranewsProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Antaranews extends NewsScraper
{
	const targetList = [
		"Nanggroe Aceh Darussalam" => "https://aceh.antaranews.com/",
		"Sumatera Utara" => "https://sumut.antaranews.com/",
		"Sumatera Barat" => "https://sumbar.antaranews.com/",
		"Kepulauan Riau" => "https://kepri.antaranews.com/",
		"Sulawesi Selatan" => "https://sumsel.antaranews.com/",
		"Bangka Belitung" => "https://babel.antaranews.com/",
		"Bengkulu" => "https://bengkulu.antaranews.com/",
		"Jawa Barat" => "https://jabar.antaranews.com/",
		"Banten" => "https://banten.antaranews.com/",
		"Jawa Tengah" => "https://jateng.antaranews.com/",
		"Daerah Istimewa Yogyakarta" => "https://jogja.antaranews.com/",
		"Jawa Timur" => "https://jatim.antaranews.com/",
		"Bali" => "https://bali.antaranews.com/",
		"Nusa Tenggara Barat" => "https://mataram.antaranews.com/",
		"Nusa Tenggara Timur" => "https://kupang.antaranews.com/",
		"Kalimantan Barat" => "https://kalbar.antaranews.com/",
		"Kalimantan Timur" => "https://kalteng.antaranews.com/",
		"Kalimantan Selatan" => "https://kalsel.antaranews.com/",
		"Kalimantan Selatan_" => "https://kalsel.antaranews.com/seputar-kalsel/banjarmasin",
		"Kalimantan Timur" => "https://kaltim.antaranews.com/",
		"Kalimantan Utara" => "https://kaltara.antaranews.com/",
		"Sulawesi Utara" => "https://manado.antaranews.com/",
		"Sulawesi Barat" => "https://makassar.antaranews.com/sulbar",
		"Sulawesi Tengah" => 	"https://sulteng.antaranews.com/",
		"Sulawesi Tenggara" => "https://sultra.antaranews.com/",
		"Sulawesi Selatan" => "https://makassar.antaranews.com/",
		"Gorontalo" => "https://gorontalo.antaranews.com/",
		"Maluku" => "https://ambon.antaranews.com/",
		"Maluku Utara_" => "https://ambon.antaranews.com/",
		"Papua Barat" => "https://papuabarat.antaranews.com/",
		"Papua" => "https://papua.antaranews.com/"
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
					$this->processor = new AntaranewsProcessor($url, $this);
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
			"/(?:href=\")(https?:\/\/[a-z]{3,16}\.antaranews\.com\/berita\/\d{4,6}\/.+)(?:\")/Usi",
			$string,
			$m
		)) {
			icelog("Got ".count($m[1])." thread(s)");
			array_walk($m[1], function ($url) use (&$urls) {
				$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
			});
		}

		$urls = array_unique($urls);
		icelog("Filtering scraped threads...");
		icelog("Got ".count($urls)." unique threads...");
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
