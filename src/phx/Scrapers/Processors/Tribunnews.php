<?php

namespace Phx\Scrapers\Processors;

use Phx\NewsScraper;
use Contracts\PhxScraperProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors
 * @license MIT
 * @version 0.0.1
 */
final class Tribunnews implements PhxScraperProcessor
{
	/**
	 * @var string
	 */
	private $url = "";

	/**
	 * @var \Phx\NewsScraper
	 */
	private $newsScraper = "";

	/**
	 * @var arry
	 */
	private $tags = [];

	/**
	 * @var string
	 */
	private $title = "";

	/**
	 * @var array
	 */
	private $images = [];

	/**
	 * @var string
	 */
	private $datetime = "";

	/**
	 * @var array
	 */
	private $authors = [];

	/**
	 * @var string
	 */
	private $regional = "";

	/**
	 * @var array
	 */
	private $category = [];

	/**
	 * @var bool
	 */
	private $imagesOnly = false;

	/**
	 * @var string
	 */
	private $html = "";

	/**
	 * @var string
	 */
	private $content = "";

	/**
	 * @var string
	 */
	private $contentType = "";

	/**
	 * @var array
	 */
	private $comments = [];

	/**
	 * @var string
	 */
	public $useSecondHandler = false;

	/**
	 * @param string			$url
	 * @param \Phx\NewsScraper	$newsScraper
	 * @return void
	 * 
	 * Constructor.
	 */
	public function __construct(string $url, NewsScraper $newsScraper)
	{
		$this->url = $url;
		$this->newsScraper = $newsScraper;
	}

	/**
	 * @return bool
	 */
	public function run(): bool
	{
		return $this->useSecondHandler ? $this->secondHandler() :$this->firstHandler();
	}

	/**
	 * @return bool
	 */
	private function secondHandler(): bool
	{
		icelog("Scraping {$this->url}...");

		$l = $this->newsScraper->exec($this->url);
		if (isset($l["error"]) && $l["error"]) {
			icelog("An error occured when scraping {$this->url}: {$l['errno']} {$l['error']}");
			return false;
		}

		$this->html = $l["out"];
		icelog("Identifying page...");
		if (preg_match("/(?:<title>)(.*)(?:<\/title>)/Usi", $l["out"], $m)) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$this->title = explode("-", strrev($this->title), 2);
			if (count($this->title) === 2) {
				$this->title = strrev(trim($this->title[1]));
			} else {
				$this->title = strrev($this->title[0]);
			}
			icelog("Got title: ".$this->title);

			if (substr($this->title, 0, 12) === "Galeri Foto:") {
				icelog("Skipping...");
				return false;
			}

			/**
			 * Get date and time.
			 */
			if (preg_match(
				"/(?:<time( class=\"grey\" style=\"display:inline-block;\")?>)(.*)(?:<\/time>)/Usi",
				$l["out"],
				$m
			)) {
				$this->datetime = trim(html_entity_decode($m[2], ENT_QUOTES, "UTF-8"));
				icelog("Got date and time: {$this->datetime}");
			} else {
				icelog("Could not get the date and time");
			}

			/**
			 * Get tags
			 */
			if (preg_match_all(
				"/(?:<h5 class.+<a.+>)(.*)(?:<\/a><\/h5>)/Usi",
				$l["out"],
				$m
			)) {
				foreach ($m[1] as $key => $tag) {
					$this->tags[] = trim(html_entity_decode($tag, ENT_QUOTES, "UTF-8"));
				}
				icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
			} else {
				icelog("Could not find the tag");
			}


			/**
			 * Get images, content, author and regional.
			 */
			if (preg_match(
				"/(?:<script type=\"application\/ld\+json\">)(.*)(?:<\/script>)/Usi",
				$l["out"],
				$m
			)) {
				$json = json_decode($m[1], true);
				if (isset($json["description"], $json["image"]["url"])) {
					$this->images[] = [
						"url" => $json["image"]["url"],
						"description" => $json["description"]
					];
					icelog("Got ".count($this->images)." image(s)");
				} else {
					icelog("Could not find the image");
				}

				if (isset($json["@type"])) {
					$this->contentType = $json["@type"] === "NewsArticle" ? "news" : $json["@type"];
					icelog("Got content type: ".$this->contentType);
				} else {
					$this->contentType = "news";
					icelog("Could not find the content type, back to default type (news)");
				}

				if (isset($json["author"]["name"])) {
					$this->authors[] = $json["author"]["name"];
					icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));
				} else {
					icelog("Could not find the author");
				}

				if (isset($json["publisher"]["name"])) {
					$reg = explode(" ", $json["publisher"]["name"], 2);
					if (count($reg) > 1) {
						$this->regional = trim($reg[1]);
						icelog("Got regional: ".$this->regional);
					} else {
						icelog("Could not find the regional");
					}
				}
			}


			/**
			 * Get max page.
			 */
			icelog("Getting max page");
			$max = null;
			$this->content = "";
			if (preg_match_all(
				"/\?page=(\d+)\"/Usi",
				$l["out"],
				$m
			)) {
				$max = (int)max($m[1]);
				if ($max < 1) {
					$max = null;
				}
			} else {
				icelog("Could not find the max page");
			}

			if (isset($max)) {
				for ($i=1; $i < $max; $i++) {
					icelog("Getting page {$i}");
					
					$ll = $this->newsScraper->exec($this->url."?page={$i}");
					if (isset($ll["error"]) && $ll["error"]) {
						icelog("An error occured when scraping {$this->url}?page={$i}: {$ll['errno']} {$ll['error']}");
						icelog("Skipping...");
						continue;
					}

					if (preg_match(
						"/(?:<div class=\"side-article txt-article\"\s?>)(.*)(?:<div class=\"side-article mb5\")/Usi",
						$l["out"],
						$m
					)) {
						$m[1] = trim(html_entity_decode(strip_tags(preg_replace(
							[
								"/<script.+<\/script>/Usi",
								"/<strong>Baca\:.+<\/script>/Usi",
							],
							"",
							$m[1])), ENT_QUOTES, "UTF-8"));
						do {
							$m[1] = str_replace("\n\n", "\n", $m[1], $n);
						} while ($n);
						$this->content .= $m[1];
						$contentOk = true;
						icelog("Got page {$i} with ".strlen($m[1])." characters");
					}
				}
			} else {
				if (preg_match(
					"/(?:<div class=\"side-article txt-article\"\s?>)(.*)(?:<div class=\"side-article mb5\")/Usi",
					$l["out"],
					$m
				)) {
					$m[1] = trim(html_entity_decode(strip_tags(preg_replace(
							[
								"/<script.+<\/script>/Usi",
								"/<strong>Baca\:.+<\/script>/Usi",
							],
							"",
							$m[1])), ENT_QUOTES, "UTF-8"));
					do {
						$m[1] = str_replace("\n\n", "\n", $m[1], $n);
					} while ($n);
					$this->content .= $m[1];
					$contentOk = true;
					icelog("Got page 1 with ".strlen($m[1])." characters");
				} else {
					$contentOk = false;
					icelog("Could not find the content");
				}
			}
			$contentOk and icelog("Finished get all contents, total characters: ".strlen($this->content));

			/**
			 * Get category.
			 */
			if (preg_match(
				"/(?:'content_category'.+:.+')(.*)(?:')/Usi",
				$l["out"],
				$m
			)) {
				$this->category[] = $m[1];
				icelog("Got category: ".json_encode($this->category));
			} else {
				icelog("Could not find the category");
			}

			icelog("Get comment is not available for this thread due to extended javascript environment");
			
			return true;
		}
		icelog("Could not find the title");
		icelog("Skipping...");
		return false;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getDateAndTime(): string
	{
		return $this->datetime;
	}

	/**
	 * @param string $tag
	 */
	public function setTag(string $tag): void
	{
		$this->tags[] = $tag;
	}

	/**
	 * @return string
	 */
	public function getRegional(): string
	{
		return $this->regional;
	}

	/**
	 * @return void
	 */
	public function setRegional(string $regional): void
	{
		$this->regional = $regional;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return array
	 */
	public function getAuthor(): array
	{
		return $this->authors;
	}

	/**
	 * @return array
	 */
	public function getImages(): array
	{
		return $this->images;
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->content;
	}

	/**
	 * @return string
	 */
	public function getContentType(): string
	{
		return $this->contentType;
	}

	/**
	 * @return array
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	/**
	 * @return array
	 */
	public function getCategory(): array
	{
		return $this->category;
	}

	/**
	 * @return array
	 */
	public function getComments(): array
	{
		return $this->comments;
	}

	/**
	 * @return string
	 */
	public function getHTML(): string
	{
		return $this->html;
	}

	/**
	 * @return bool
	 */
	private function firstHandler(): bool
	{
		icelog("Scraping {$this->url}...");

		$l = $this->newsScraper->exec($this->url);
		if (isset($l["error"]) && $l["error"]) {
			icelog("An error occured when scraping {$this->url}: {$l['errno']} {$l['error']}");
			return false;
		}

		$this->html = $l["out"];
		icelog("Identifying page...");
		if (preg_match("/(?:<title>)(.*)(?:<\/title>)/Usi", $l["out"], $m)) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$this->title = explode("-", strrev($this->title), 2);
			if (count($this->title) === 2) {
				$this->title = strrev(trim($this->title[1]));
			} else {
				$this->title = strrev($this->title[0]);
			}
			icelog("Got title: ".$this->title);

			/**
			 * Get date and time.
			 */
			if (preg_match("/(?:<time(\s.+)?>)(.*)(?:<\/time>)/Usi", $l["out"], $m)) {
				$this->datetime = trim(html_entity_decode($m[2], ENT_QUOTES, "UTF-8"));
				icelog("Got date and time: {$this->datetime}");
			} else {
				icelog("Could not get the date and time");
			}

			/**
			 * Get tags
			 */
			if (preg_match_all(
				"/(?:<h5 class.+<a.+>)(.*)(?:<\/a><\/h5>)/Usi",
				$l["out"],
				$m
			)) {
				foreach ($m[1] as $key => $tag) {
					$this->tags[] = trim(html_entity_decode($tag, ENT_QUOTES, "UTF-8"));
				}
				icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
			} else {
				icelog("Could not find the tag");
			}

			/**
			 * Get images, content, author and regional.
			 */
			if (preg_match(
				"/(?:<script type=\"application\/ld\+json\">)(.*)(?:<\/script>)/Usi",
				$l["out"],
				$m
			)) {
				$json = json_decode($m[1], true);
				if (isset($json["description"], $json["image"]["url"])) {
					$this->images[] = [
						"url" => $json["image"]["url"],
						"description" => $json["description"]
					];
					icelog("Got ".count($this->images)." image(s)");
				} else {
					icelog("Could not find the image");
				}

				if (isset($json["@type"])) {
					$this->contentType = $json["@type"] === "NewsArticle" ? "news" : $json["@type"];
					icelog("Got content type: ".$this->contentType);
				} else {
					$this->contentType = "news";
					icelog("Could not find the content type, back to default type (news)");
				}

				if (isset($json["author"]["name"])) {
					$this->authors[] = $json["author"]["name"];
					icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));
				} else {
					icelog("Could not find the author");
				}

				if (isset($json["publisher"]["name"])) {
					$reg = explode(" ", $json["publisher"]["name"], 2);
					if (count($reg) > 1) {
						$this->regional = trim($reg[1]);
						icelog("Got regional: ".$this->regional);
					} else {
						icelog("Could not find the regional");
					}
				}
			}


			/**
			 * Get max page.
			 */
			icelog("Getting max page");
			$this->content = "";
			if (preg_match_all(
				"/(?:<a href=\".+\?page=)(\d{1,2})(?:\")/Usi", 
				$l["out"], 
				$m
			)) {
				$maxPage = max($m[1]);
				icelog("Got max page: {$maxPage}");
				for ($i=1; $i <= $maxPage; $i++) {
					if ($i === 1) {
						icelog("Got page 1");
						$out = $l["out"];
					} else {
						icelog("Scraping ".$this->url."?page={$i}...");
						$o = $this->newsScraper->exec($this->url."?page={$i}");
						if (isset($o["error"]) && $o["error"]) {
							icelog("An error occured when scraping {$this->url}: {$o['errno']} {$o['error']}");
						}
						$out = $o["out"];
					}
					if (preg_match(
						"/<div class=\"side-article txt-article\">(.*)<div class=\"side-article mb5\">/Usi",
						$out,
						$m
					)) {
						$i===1 or icelog("Got page {$i}");
						$m[1] = trim(html_entity_decode(strip_tags(preg_replace(
							[
								"/<script.+<\/script>/Usi",
								"/<strong>Baca: .+<\/strong>/Usi"
							],
							"",
							$m[1]
						)), ENT_QUOTES, "UTF-8"));

						do {
							$m[1] = str_replace("\n\n", "\n", $m[1], $n);
						} while ($n);
						icelog("Got page {$i} with ".strlen($m[1])." characters");
						$this->content .= $m[1];
					}
				}
				icelog("Finished get all contents, total characters: ".strlen($this->content));
			} else {
				if (preg_match(
					"/<div class=\"side-article txt-article\">(.*)<div class=\"side-article mb5\">/Usi",
					$l["out"],
					$m
				)) {
					icelog("Got page 1");
					$m[1] = trim(html_entity_decode(strip_tags(preg_replace(
						[
							"/<script.+<\/script>/Usi",
							"/<strong>Baca: .+<\/strong>/Usi"
						],
						"",
						$m[1]
					)), ENT_QUOTES, "UTF-8"));

					do {
						$m[1] = str_replace("\n\n", "\n", $m[1], $n);
					} while ($n);
					$this->content = $m[1];
					icelog("Finished get all contents, total characters: ".strlen($this->content));
				} else {
					icelog("Could not find the content");
				}
			}

			/**
			 * Get category.
			 */
			if (preg_match(
				"/(?:'content_category'.+:.+')(.*)(?:')/Usi",
				$l["out"],
				$m
			)) {
				$this->category[] = $m[1];
				icelog("Got category: ".json_encode($this->category));
			} else {
				icelog("Could not find the category");
			}

			icelog("Get comment is not available for this thread due to extended javascript environment");
		} else {
			icelog("Could not get the title");
			icelog("Skipping...");
			return false;
		}
		return true;
	}
}
