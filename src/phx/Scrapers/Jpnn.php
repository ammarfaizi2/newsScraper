<?php

namespace Phx\Scrapers;

use Phx\NewsScraper;
use Phx\Scrapers\Processors\Jpnn as JpnnProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Jpnn extends NewsScraper
{
	const targetList = [
		"Sumatera Utara" => "https://www.jpnn.com/daerah/sumut",
		"Jambi" => "https://www.jpnn.com/daerah/jambi",
		"Sumatera Selatan" => "https://www.jpnn.com/daerah/sumsel",
		"Kalimantan Barat" =>  "https://www.jpnn.com/daerah/kalbar",
		"Kalimantan Tengah" => "https://www.jpnn.com/daerah/kalteng",
		"Kalimantan Selatan" => "https://www.jpnn.com/daerah/kalsel",
		"Kalimantan Timur" => "https://www.jpnn.com/daerah/kaltim",
		"Kalimantan Utara" => "https://www.jpnn.com/daerah/kaltara",
		"Sulawesi Utara" => "https://www.jpnn.com/daerah/sultra",
		"Sulawesi Selatan" => "https://www.jpnn.com/daerah/sulsel",
		"Gorontalo" => "https://www.jpnn.com/daerah/gorontalo",
		"Maluku" => "https://www.jpnn.com/daerah/maluku",
		"Maluku Utara" => "https://www.jpnn.com/daerah/maluku-utara",
		"Papua Barat" => "https://www.jpnn.com/daerah/papua-barat",
		"Papua" => "https://www.jpnn.com/daerah/papua"
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
					$this->processor = new JpnnProcessor($url, $this);
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
		$reg = explode("/", $this->currentUrl);
		$reg = end($reg);
		$ppg = 1;
		do {
			$nn = false;
			icelog("Scraping offset $i (page $ppg)...");
			$ppg++;
			$l = $this->exec("https://www.jpnn.com/ajax/loadmore_subrubrik",
				[
					CURLOPT_HTTPHEADER => [
						"Accept-Encoding: gzip",
						"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
						"X-Requested-With: XMLHttpRequest"
					],
					CURLOPT_REFERER => $this->currentUrl,
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => http_build_query(
						[
							"offset" => $i,
							"subrubrik" => $reg
						]
					)
				]
			);
			$l["out"] = gzdecode($l["out"]);

			// file_put_contents("a.tmp", $l["out"]);
			// $l["out"] = file_get_contents("a.tmp");

			if (preg_match_all("/(?:<a href=\")(.*)(?:\" title=)/Usi", $l["out"], $m)) {
				array_walk($m[1], function ($url) use (&$urls) {
					$urls[] = trim(html_entity_decode($url, ENT_QUOTES, "UTF-8"));
				});
				$urls = array_unique($urls);
				icelog("Got ".($cc = count($m[1]))." thread(s), Total unique threads: ".count($urls));
				$i += $cc;
				$nn = true;
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
