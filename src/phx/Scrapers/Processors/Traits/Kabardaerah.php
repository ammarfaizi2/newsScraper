<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Kabardaerah
{
	/**
	 * @return bool
	 */
	private function metaHandler(): bool
	{
		/**
		 * Get title.
		 */
		if (preg_match(
			"/(?:<meta property=\"og:title\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$this->title = explode("|", $this->title, 2);
			$this->title = $this->title[0];
			icelog("Got title: {$this->title}");
		} else {
			/**
			 * Get title (alternative way).
			 */
			if (preg_match(
				"/(?:<title>)(.*)(?:<\/title>)/Usi",
				$this->html,
				$m
			)) {
				$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$this->title = explode("|", $this->title, 2);
				$this->title = $this->title[0];
				icelog("Got title: {$this->title}");
			} else {
				icelog("Could not get the title");
				icelog("Skipping...");
				return false;
			}
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
		}		

		/**
		 * Get tags
		 */
		if (preg_match_all(
			"/(?:<meta property=\"article\:tag\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$tagsPointer = &$this->tags;

			array_walk($m[1], function ($tag) use (&$tagsPointer) {
				$tag = trim(html_entity_decode($tag, ENT_QUOTES, "UTF-8"));
				if (!empty($tag)) {
					$tagsPointer[] = $tag;
				}
			});

			unset($tagsPointer);

			$c = count($this->tags);
			if ($c > 0) {
				icelog("Got {$c} tag(s): ".json_encode($this->tags));
			} else {
				icelog("Could not get the tags");
			}
		} else {
			icelog("Could not get the tags");
		}


		/**
		 * Get author.
		 */
		if (preg_match(
			"/(?:<div class=\"td-author-name vcard author\".+<a href=.+>)(.*)(?:<)/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));
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
				"url" => $url,
				"description" => $desc
			];

			icelog("Got ".count($this->images)." image(s)");
		} else {
			/**
			 * Get image (alternative way).
			 */
			if (preg_match(
				"/(?:<meta name=\"twitter\:image\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$desc = "";

				if (preg_match(
					"/(?:<meta name=\"twitter\:description\" content=\")(.*)(?:\")/Usi",
					$this->html,
					$m
				)) {
					$desc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				}

				$this->images[] = [
					"url" => $url,
					"description" => $desc
				];

				icelog("Got ".count($this->images)." image(s)");
			} else {
				icelog("Could not get the image");
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function runThreadScraper(): bool
	{
		if ($this->metaHandler()) {
			if (preg_match(
				"/(?:<div class=\"td-post-content td-pb-padding-side\">)(.*)(?:<footer>)/Usi",
				$this->html,
				$m
			)) {
				$contentPointer = &$this->content;

				$contentPointer = trim(html_entity_decode(strip_tags(preg_replace(
					[
						"/<script.+<\/script>/Usi"
					],
					[
						""
					],
					$m[1]
				)), ENT_QUOTES, "UTF-8"));

				$contentPointer = str_replace("\r\n", "\n", $contentPointer);

				do {
					$contentPointer = str_replace("\n\n", "\n", $contentPointer, $n);
				} while ($n);

				unset($contentPointer);

				$len = strlen($this->content);

				if ($len < 20) {
					icelog("Could not get the content");
					return false;
				}

				$this->contentType = "news";

				icelog("Got content with %d characters", $len);
				return true;
			}
		}
		return false;
	}
}
