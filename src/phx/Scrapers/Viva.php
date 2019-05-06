<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Viva as VivaProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Viva extends NewsScraper
{
	const targetList = [
		"Kalimantan Tengah" => "https://www.viva.co.id/tag/kalimantan-tengah",
		"Kalimantan Utara" => "https://www.viva.co.id/tag/kalimantan-utara",
		"Papua Barat" => "https://www.viva.co.id/tag/papua-barat",
		"Papua" => "https://www.viva.co.id/tag/isu-papua",
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
					$this->processor = new VivaProcessor($url, $this);
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

		$keyword = explode("/", $this->currentUrl);
		$keyword = $keyword[count($keyword) - 1];
		$i = 0;
		do {
			$nn = false;

			if (preg_match("/(?:window\.csrf=\")(.*)(?:\")/Usi", $string, $m)) {
				$token = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$l = $this->exec("https://www.viva.co.id/request/loadmoretag", 			
					[
						CURLOPT_HTTPHEADER => [
							"Accept-Encoding: gzip, deflate, br",
							"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
							"X-Requested-With: XMLHttpRequest"
						],
						CURLOPT_REFERER => $this->currentUrl,
						CURLOPT_POST => true,
						CURLOPT_POSTFIELDS => http_build_query(
							[
								"keyword" => $keyword,
								"start_index" => (12*$i++),
								"content_type" => "article",
								"page" => 1,
								"_token" => $m[1]
							]
						)
					]
				);

				$l["out"] = gzdecode($l["out"]);

				if (preg_match_all(
					"/(?:<a class=\"flex_thumb\" itemprop=\"url\" href=\")(.+)(?:\")/Usi",
					$l["out"],
					$m
				)) {
					$nn = true;
					array_walk($m[1], function ($url, $index) use (&$m, &$urls) {
						$url = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
						if (!empty($url)) {
							$urls[] = $url;
						} else {
							unset($m[1][$index]);
						}
					});
					$urls = array_unique($urls);
					icelog("Got ".count($m[1])." thread(s) on page ".($i).". Total unique thread(s): ".count($urls));
				} else {
					icelog('Reached the end of page');
				}
			} else {
				icelog("Could not get the CSRF token");
			}
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
