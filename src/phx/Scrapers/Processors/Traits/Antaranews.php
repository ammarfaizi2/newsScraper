<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Antaranews
{
	/**
	 * @return bool
	 */
	public function scrape(): bool
	{
		if ($this->metaHandler()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return bool
	 */
	private function metaHandler(): bool
	{
		/**
		 * Get title
		 */
		if (preg_match(
			"/(?:<title>)(.*)(?:<\/title>)/Usi",
			$this->html,
			$m
		)) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got title: {$this->title}");
		} else {
			icelog("Could not get the title");
			return false;
		}

		if (preg_match(
			"/gorontalo\.antaranews\.com/Usi",
			$this->url
		) && 
			preg_match(
			"/(?:<span class=\"article-date\"><i class=\"fa fa-clock-o\"><\/i>)(.*)(?:<\/span>)/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got date and time: {$this->datetime}");
		} else {
			/**
			 * Get date and time
			 */
			if (preg_match(
				"/(?:<meta itemprop=\"datePublished\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				icelog("Got date and time: {$this->datetime}");
			} else {
				icelog("Could not get date and time");
			}
		}

		/**
		 * Get tags
		 */
		if (preg_match_all(
			"/(?:<meta name=\"keywords\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$tagsPointer = &$this->tags;
			$m[1] = explode(",", $m[1][0]);

			array_walk($m[1], function ($tag) use (&$tagsPointer) {
				$tag = trim(html_entity_decode($tag, ENT_QUOTES, "UTF-8"));
				if (! empty($tag)) {
					$tagsPointer[] = $tag;
				}
			});

			unset($tagsPointer);

			if ($c = count($this->tags)) {
				icelog("Got {$c} tag(s): ".json_encode($this->tags));
			} else {
				icelog("Could not get tags");
			}

		} else {
			icelog("Could not get tags");
		}

		/**
		 * Get image
		 */
		if (preg_match(
			"/(?:<meta property=\"og:image\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {

			$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$imageDesc = "";

			if (preg_match(
				"/(?:<meta name=\"description\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$imageDesc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			}

			$this->images[] = [
				"url" => $url,
				"description" => $imageDesc
			];

		} else {
			/**
			 * Get image 2
			 */
			if (preg_match(
				"/(?:<meta name=\"twitter:image:src\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$imageDesc = "";

				if (preg_match(
					"/(?:<meta name=\"description\" content=\")(.*)(?:\")/Usi",
					$this->html,
					$m
				)) {
					$imageDesc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				}

				$this->images[] = [
					"url" => $url,
					"description" => $imageDesc
				];
			} else {
				icelog("Could not get image");
				return false;
			}
		}

		/**
		 * Get author 1
		 */
		if (preg_match(
			"/(?:<span><b>Pewarta :)(.*)(?:<\/span>)/Usi",
			$this->html,
			$m
		)) {
			$m[1] = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, "UTF-8"));
			$this->authors[] = $m[1];			
		}

		/**
		 * Get author 2
		 */
		if (preg_match(
			"/(?:Editor: )(.+)(?:<)/Usi",
			$this->html,
			$m
		)) {
			$m[1] = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, "UTF-8"));
			$this->authors[] = $m[1];
		}

		if ($c = count($this->authors)) {
			icelog("Got {$c} author(s): ".json_encode($this->authors));	
		} else {
			/**
			 * Get author 3
			 */
			if (preg_match(
				"/(?:<span itemprop=\"author\"><b>)(.*)(?:<\/b>)/Usi",
				$this->html,
				$m
			)) {
				$m[1] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$this->authors[] = $m[1];
				icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));	
			} else {

				/**
			 	 * Get author 4
			 	 */
				if (preg_match(
					"/(?:<span itemprop=\"author\">)(.*)(?:<\/span>)/Usi",
					$this->html,
					$m
				)) {
					$m[1] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
					$this->authors[] = $m[1];
					icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));
				} else {
					if (preg_match(
						"/(?:<meta property=\"article:author\" content=\")(.*)(?:\")/Usi",
						$this->html,
						$m
					)) {
						$m[1] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
						$this->authors[] = $m[1];
						icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));	
					} else {
						icelog("Could not get the authors");
					}
				}
			}
		}

		/**
		 * Get content.
		 */
		if (preg_match(
			"/(?:<div class=\"post-content.+>)(.*)(?:<\/div>)/Usi",
			$this->html,
			$m
		)) {
			$n = 0;
			$m[1] = trim(html_entity_decode(strip_tags(str_replace("<br />", "\n", $m[1])), ENT_QUOTES, "UTF-8"));

			do {
				$m[1] = str_replace("\r\n", "\n", $m[1], $n);
			} while ($n);

			do {
				$m[1] = str_replace("\n\n", "\n", $m[1], $n);
			} while ($n);

			if (($l = strlen($m[1])) < 30) {
				icelog("Could not get content");
				return false;
			}

			$this->content = $m[1];

			icelog("Got content with {$l} characters");
		} else {
			icelog("Could not get content");
			return false;
		}
		

		$this->contentType = "news";

		return true;
	}
}
