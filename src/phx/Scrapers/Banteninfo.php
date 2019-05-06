<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Banteninfo as BanteninfoProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Banteninfo extends NewsScraper
{
	const targetList = [
		"Banten" => "http://www.banteninfo.com/"
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
					$this->processor = new BanteninfoProcessor($url, $this);
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
			"/(?:<h3 class=\"entry-title td-module-title\"><a href=\")(.*)(?:\")/Usi",
			$string,
			$m
		)) {
			array_walk($m[1], function ($url) use (&$urls) {
				$url = trim(html_entity_decode($url));
				if ($url !== "") {
					$urls[] = $url;
				}
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
