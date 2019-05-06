<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Tribunnews as TribunnewsPrrocessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Tribunnews extends NewsScraper
{
	const targetList = [
		"Bali" => "http://bali.tribunnews.com/",
		"Nusa Tenggara Timur" => "http://kupang.tribunnews.com/tag/nusa-tenggara-timur",
		"Kalimantan Barat" => "http://www.tribunnews.com/tag/kalimantan-barat",
		"Kalimantan Barat_" => "http://pontianak.tribunnews.com/",
		"Kalimantan Tengah" => "http://kalteng.tribunnews.com/",
		"Kalimantan Selatan" => "http://banjarmasin.tribunnews.com/",
		"Kalimantan Timur" => "http://kaltim.tribunnews.com/",
		"Kalimantan Utara" => "http://www.tribunnews.com/tag/kalimantan-utara",
		"Sulawesi Utara" => "http://manado.tribunnews.com/",
		"Sulawesi Barat" => "http://www.tribunnews.com/tag/sulawesi-barat",
		"Sulawesi Tengah" => "http://www.tribunnews.com/tag/sulawesi-tengah",
		"Sulawesi Tenggara" => "http://www.tribunnews.com/tag/sulawesi-tenggara",
		"Sulawesi Selatan" => "http://makassar.tribunnews.com/",
		"Gorontalo" => "http://www.tribunnews.com/tag/gorontalo",
		"Maluku" => "http://www.tribunnews.com/tag/maluku",
		"Maluku Utara" => "http://www.tribunnews.com/tag/maluku-utara",
		"Papua Barat" => "http://www.tribunnews.com/tag/papua-barat",
		"Papua" => "http://www.tribunnews.com/tag/papua",
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
					$this->processor = new TribunnewsPrrocessor($url, $this);
					$this->processor->useSecondHandler = preg_match("/tag/", $this->currentUrl);
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
		if (preg_match("/tag/", $this->currentUrl)) {

				if (preg_match(
					"/(?:<a href=\".+\?page=)(\d+)(?:\">Last \&rsaquo\;)/Usi",
					$string,
					$m
				)) {
					$max = (int) $m[1];
				}

				if (preg_match_all(
					"/(?:<div class=\"fr mt3 pos_rel\">.+<a href=\")(.*)(?:\")/Usi",
					$string,
					$m
				)) {
					array_walk($m[1], function (&$a) {
						$a = html_entity_decode($a, ENT_QUOTES, "UTF-8");
					});
					$urls = $m[1];
				}

				if (isset($max)) {
					for ($i=2; $i <= $max; $i++) { 
						icelog("Scraping {$this->currentUrl}?page={$i}...");
						$q = $this->exec($this->currentUrl."?page={$i}");
						if (isset($q["error"]) && $q["error"]) {
							icelog("An error occured when scraping {$this->currentUrl}?page={$i}: {$q['errno']} {$q['error']}");
							continue;
						}
						$c = 0;
						if (preg_match_all(
							"/(?:<div class=\"fr mt3 pos_rel\">.+<a href=\")(.*)(?:\")/Usi",
							$q["out"],
							$m
						)) {
							$c = count($m[1]);
							array_walk($m[1], function (&$a) use (&$urls) {
								$urls[] = html_entity_decode($a, ENT_QUOTES, "UTF-8");
							});
						}
						icelog("Got ".$c." thread(s)");
					}
				}
		} else {
			$this->currentUrl = str_replace(
				["/", "."],
				["\/", "\\."],
				$this->currentUrl
			);
			if (preg_match_all("/href=\"({$this->currentUrl}(\/[a-z]{3,32})?\/20\d{2}\/\d{2}\/\d{2}.+)\"/Usi", $string, $m)) {
				foreach ($m[1] as $key => $url) {
					$urls[] = html_entity_decode($url, ENT_QUOTES, "UTF-8");
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
