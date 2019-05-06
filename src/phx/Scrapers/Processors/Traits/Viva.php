<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Viva
{
	/**
	 * @return bool
	 */
	private function metaHandler(): bool
	{
		/**
		 * Get title
		 */
		if (preg_match(
			"/(?:<meta property=\"og:title\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got title: {$this->title}");
		} else {
			/**
		 	 * Get title (alternative way)
		 	 */
			if (preg_match(
				"/(?:<title>)(.*)(?:<\/title>)/Usi",
				$this->html,
				$m
			)) {
				$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				icelog("Got title: {$this->title}");
				var_dump($this->title);
			} else {
				icelog("Could not get the title");
				icelog("Skipping...");
				return false;
			}
		}

		/**
		 * Get tags.
		 */
		if (preg_match(
			"/(?:<meta name=\"news_keywords\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$tagsPointer = &$this->tags;
			$m = explode(",", $m[1]);
			array_walk($m, function ($tag) use (&$tagsPointer) {
				$tag = trim($tag);
				if (!empty($tag)) {
					$tagsPointer[] = $tag;
				}
			});
			unset($tagsPointer);
		}

		if (!count($this->tags)) {
			/**
			 * Get tags.
			 */
			if (preg_match(
				"/(?:<meta name=\"keywords\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$tagsPointer = &$this->tags;
				$m = explode(",", $m[1]);
				array_walk($m, function ($tag) use (&$tagsPointer) {
					$tag = trim($tag);
					if (!empty($tag)) {
						$tagsPointer[] = $tag;
					}
				});
				unset($tagsPointer);

				if (count($this->tags) > 0) {
					icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
				} else {
					icelog("Cound not get the tags");
				}
			} else {
				icelog("Cound not get the tags");
			}
		} else {
			icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
		}

		/**
		 * Get date and time.
		 */
		if (preg_match(
			"/(?:<meta property=\"article\:published_time\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got date and time: {$this->datetime}");
		} else {
			icelog("Could not get date and time");
		}

		/**
		 * Get author
		 */
		if (preg_match(
			"/(?:<meta content=\")(.*)(?:\" name=\"author\"\/>)/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = html_entity_decode($m[1], ENT_QUOTES, "UTF-8");
			icelog("Got author(s): ".json_encode($this->authors));
		} else {
			icelog("Could not get the author");
		}

		/**
		 * Get image
		 */
		if (preg_match(
			"/(?:<meta property=\"og\:image\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$desc = "";

			if (preg_match(
				"/(?:<meta property=\"og\:description\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$desc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			}

			$this->images[] = [
				"description" => $desc,
				"url" => $url
			];

			icelog("Got ".count($this->images)." image(s)");
		} else {
			/**
		 	 * Get image (alternative way)
		 	 */
			if (preg_match(
				"/(?:<meta name=\"twitter\:image\:src\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$desc = "";

				if (preg_match(
					"/(?:<meta property=\"og\:description\" content=\")(.*)(?:\")/Usi",
					$this->html,
					$m
				)) {
					$desc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				}

				$this->images[] = [
					"description" => $desc,
					"url" => $url
				];

				icelog("Got ".count($this->images)." image(s)");
			} else {
				icelog("Could not get the image");
			}
		}


		$this->contentType = "news";


		return true;
	}

	/**
	 * @return bool
	 */
	private function beritaPath(): bool
	{
		if ($this->metaHandler()) {
			if (preg_match(
				"/(?:id=\"article-detail-content\".+>)(.*)(?:<div class=\"article-share-v2\">)/Usi",
				$this->html,
				$m
			)) {
				$contentPointer = &$this->content;

				$contentPointer = trim(html_entity_decode(strip_tags(preg_replace(
					[
						"/<script.+<\/script>/Usi",
						"/<b>Lihat Juga<\/b>.+<\!-- end middle terkait -->/Usi"
					],
					"",
					$m[1]
				)), ENT_QUOTES, "UTF-8"));
				
				unset($m[1]);

				$contentPointer = str_replace(["\r\n", "\t"], ["\n", " "], $contentPointer);

				do {
					$contentPointer = str_replace("\n\n", "\n", $contentPointer, $nnn);
				} while ($nnn);

				$contentPointer = explode("\n", $contentPointer);
				array_walk($contentPointer, function (&$content, $index) use (&$contentPointer) {
					$content = trim($content);
					if (empty($content)) {
						unset($contentPointer[$index]);
					}
				});
				$contentPointer = implode("\n", $contentPointer);
				unset($contentPointer);
			}

			if (strlen($this->content) < 10) {
				icelog("Could not get the content");
				icelog("Skipping");
				return false;
			}

			icelog("Got content with ".strlen($this->content)." characters");
			return true;
		}
	}
}
