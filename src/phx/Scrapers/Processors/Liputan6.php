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
final class Liputan6 implements PhxScraperProcessor
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
		icelog("Scraping {$this->url}...");

		$l = $this->newsScraper->exec($this->url);
		if (isset($l["error"]) && $l["error"]) {
			icelog("An error occured when scraping {$this->url}: {$l['errno']} {$l['error']}");
			return false;
		}

		$this->html = $l["out"];
		icelog("Identifying page...");
		if (preg_match("/<title>(.*)<\/title>/Usi", $l["out"], $m)) {
			$this->title = trim(str_replace("- Regional Liputan6.com", "", html_entity_decode($m[1], ENT_QUOTES, "UTF-8")));
			icelog("Got title: ".$this->title);
			if (substr($this->title, 0, 5) === "FOTO:") {
				$this->imagesOnly = true;
				icelog("This page might contains images only (image without news explanation)");
				icelog("Finding image slider...");
				if (preg_match_all(
					"/(?:<figure class=\"read-page--photo-tag--slider__top__item.+data-image=\")(.*)\".+(?:data-description=\")(.*)\".+>/Usi",
					$l["out"],
					$m
				)) {

					/**
					 * Get all images from slider.
					 */
					foreach ($m[1] as $key => $value) {
						$this->images[] = [
							"url" => trim(html_entity_decode($value, ENT_QUOTES, "UTF-8")),
							"description" => trim(html_entity_decode($m[2][$key], ENT_QUOTES, "UTF-8")),
						];
					}
					icelog(count($this->images)." image(s) found!");

					/**
					 * Get authors.
					 */
					if (preg_match_all(
						"/<div class=\"read-page--photo-tag--header__credits-user\">(.*)(?:<)/Usi",
						$l["out"],
						$m
					)) {
						foreach ($m[1] as $key => $author) {
							$this->authors[] = trim(html_entity_decode($author, ENT_QUOTES, "UTF-8"));
						}
						icelog("Got author(s): ".json_encode($this->authors));
					} else {
						icelog("Could not find the author");
					}

					/**
					 * Get date and time.
					 */
					if (preg_match(
						"/(?:<p class=\"read-page--photo-tag--header__datetime-wrapper\"><time .+>)(.*)(?:<)/Usi",
						$l["out"],
						$m
					)) {
						$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
						icelog("Got date and time: \"{$m[1]}\"");
					} else {
						icelog("Could not find the date and time");
					}

					/**
					 * Get tags.
					 */
					if (preg_match_all(
						"/(?:<a class=\"tags--snippet__link js-tags-link\".+><span.+>)(.*)(?:<)/Usi",
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
					 * Get category
					 */
					if (preg_match(
						"/(?:\"category\":\")(.*)(?:\")/Usi",
						$l["out"],
						$m
					)) {
						$this->category[] = ucwords(strtolower(trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"))));
						icelog("Got category: ".json_encode($this->category));
					} else {
						icelog("Could not find the category");
					}


					/**
					 * Get regional.
					 */
					if (preg_match(
						"/(?:data-component-name=\"desktop\:read-page\:article-content-body\:section\:text\"><p><b>.+\,)(.*)(?:<\/b>)/Usi",
						$l["out"],
						$m
					)) {
						$this->regional = ucwords(strtolower(trim(html_entity_decode(preg_replace("/[^a-zA-Z\s]/Usi", "", $m[1]), ENT_QUOTES, "UTF-8"))));
						icelog("Got regional: ".$this->regional);
					} else {
						if (preg_match(
							"/(?:<meta name=\"adx:sections\" content=\"regional\/)(.*)\">/Usi",
							$l["out"],
							$m
						)) {
							$this->regional = ucwords(strtolower(trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"))));
							icelog("Got regional: ".$this->regional);
						} else {
							icelog("Could not find the regional");
						}
					}

					/**
					 * Get comments
					 */
					$id = explode(
							"/",
							explode(
								"/read/", 
								$this->url,
								2
							)[1],
							2
						)[0];
					if ($id) {
						icelog("Getting thread comments...");
						$ll = $this->newsScraper->exec(
							$url = "https://www.liputan6.com/widgets-nocache/articles/{$id}/conversation"
						);
						if (isset($l["error"]) && $l["error"]) {
							icelog("An error occured when getting the comments {$this->url}: {$l['errno']} {$l['error']}");
						} else {
							if (preg_match_all(
								"/(?:<div class=\"conversation--list--item__header\">.+<span class=\"conversation--list--item__author\"><a href.+>)(.*)(?:<\/a>.+<time class=\".+>)(.*)(?:<\/time>.+<div class=\"conversation--list--item__content\">.+>)(.*)<\/p>/Usi", 
								$l["out"], 
								$m
							)) {
								$this->comments = [];
								foreach ($m[1] as $key => $v) {
									$this->comments[] = [
										"author" => trim(html_entity_decode($v, ENT_QUOTES, "UTF-8")),
										"datetime" => trim(html_entity_decode($m[2][$key], ENT_QUOTES, "UTF-8")),
										"content" => trim(html_entity_decode($m[3][$key], ENT_QUOTES, "UTF-8"))
									];
								}
								icelog("Got ".count($this->comments)." comment(s)");
							} else {
								icelog("There is no comment for this thread");
							}
						}
					} else {
						icelog("Could not find the comment");
					}

					$this->contentType = "images only";
				} else {
					icelog("Could not find any image");
					return false;
				}
			} elseif (substr($this->title, 0, 6) === "VIDEO:") {
				icelog("Skipping video content...");
				return false;
			} else {

				/**
				 * Get content
				 */
				if (preg_match_all(
					"/(?:data-component-name=\"desktop:read-page:article-content-body:section:text\">)(.*)(?:<\/div><div)/Usi",
					$l["out"],
					$m
				)) {
					unset($m[0]);
					foreach ($m[1] as &$v) {
						$v = strip_tags(preg_replace("/<div class=\"baca-juga\">.*<\/div>/Usi", "", $v));
						do {
							$v = str_replace("\n\n", "\n", $v, $n);
						} while ($n);
						$v = trim(html_entity_decode($v, ENT_QUOTES, "UTF-8"));
					}
					unset($v);
					$m = implode($m[1], "\n\n");
					$this->content = $m;
					icelog("Got content with ".strlen($this->content)." characters");
				} else {
					icelog("Could not find the content");
				}

				/**
				 * Get author
				 */
				if (preg_match(
					"/(?:<span class=\"read-page--header--author__name fn\" itemprop=\"name\">)(.*)(?:<)/Usi",
					$l["out"],
					$m
				)) {
					$this->authors[] = $m[1];
					icelog("Got author(s): ".json_encode($this->authors));
				} else {
					icelog("Could not find the author");
				}

				/**
				 * Get date and time.
				 */
				if (preg_match(
					"/(?:<time class=\"read-page--header--author__datetime updated\".+>)(.*)(?:<\/time>)/Usi",
					$l["out"],
					$m
				)) {
					$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
					icelog("Got date and time: \"{$m[1]}\"");
				} else {
					icelog("Could not find the date and time");
				}

				/**
				 * Get tags.
				 */
				if (preg_match_all(
					"/(?:<span class=\"tags--snippet__name\">)(.*)(?:<\/span>)/Usi",
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
				 * Get images.
				 */
				if (preg_match(
					"/(?:<figure class=\"read-page--photo-gallery--item\".+data-image=\")(.*)(?:\".+data-description=\")(.+)(?:\")/Usi",
					$l["out"],
					$m
				)) {
					$this->images[] = [
						"url" => trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8")),
						"description" => trim(html_entity_decode($m[2], ENT_QUOTES, "UTF-8")),
					];
				} else {
					icelog("Could not find the image");
				}

				/**
				 * Get regional.
				 */
				if (preg_match(
					"/(?:data-component-name=\"desktop\:read-page\:article-content-body\:section\:text\"><p><b>.+\,)(.*)(?:<\/b>)/Usi",
					$l["out"],
					$m
				)) {
					$this->regional = ucwords(strtolower(trim(html_entity_decode(preg_replace("/[^a-zA-Z\s]/Usi", "", $m[1]), ENT_QUOTES, "UTF-8"))));
					icelog("Got regional: ".$this->regional);
				} else {
					if (preg_match(
						"/(?:<meta name=\"adx:sections\" content=\"regional\/)(.*)\">/Usi",
						$l["out"],
						$m
					)) {
						$this->regional = ucwords(strtolower(trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"))));
						icelog("Got regional: ".$this->regional);
					} else {
						icelog("Could not find the regional");
					}
				}

				/**
				 * Get category
				 */
				if (preg_match(
					"/(?:\"category\":\")(.*)(?:\")/Usi",
					$l["out"],
					$m
				)) {
					$this->category[] = ucwords(strtolower(trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"))));
					icelog("Got category: ".json_encode($this->category));
				} else {
					icelog("Could not find the category");
				}

				/**
				 * Get comments
				 */
				$id = explode(
						"/",
						explode(
							"/read/", 
							$this->url,
							2
						)[1],
						2
					)[0];
				if ($id) {
					icelog("Getting thread comments...");
					$ll = $this->newsScraper->exec(
						$url = "https://www.liputan6.com/widgets-nocache/articles/{$id}/conversation"
					);
					if (isset($l["error"]) && $l["error"]) {
						icelog("An error occured when getting the comments {$this->url}: {$l['errno']} {$l['error']}");
					} else {
						if (preg_match_all(
							"/(?:<div class=\"conversation--list--item__header\">.+<span class=\"conversation--list--item__author\"><a href.+>)(.*)(?:<\/a>.+<time class=\".+>)(.*)(?:<\/time>.+<div class=\"conversation--list--item__content\">.+>)(.*)<\/p>/Usi", 
							$l["out"], 
							$m
						)) {
							$this->comments = [];
							foreach ($m[1] as $key => $v) {
								$this->comments[] = [
									"author" => trim(html_entity_decode($v, ENT_QUOTES, "UTF-8")),
									"datetime" => trim(html_entity_decode($m[2][$key], ENT_QUOTES, "UTF-8")),
									"content" => trim(html_entity_decode($m[3][$key], ENT_QUOTES, "UTF-8"))
								];
							}
							icelog("Got ".count($this->comments)." comment(s)");
						} else {
							icelog("There is no comment for this thread");
						}
					}
				} else {
					icelog("Could not find the comment");
				}

				$this->contentType = "news";
			}
		} else {
			icelog("Could not find the title tag on ".$this->url);
		}

		return true;
	}

	/**
	 * @param string $regional
	 * @return void
	 */
	public function setRegional(string $regional)
	{
		$this->regional = $regional;
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
}
