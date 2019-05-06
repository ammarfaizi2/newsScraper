<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Indonesiatimur
{
	/**
	 * @return bool
	 */
	public function metaHandler(): bool
	{

		if (preg_match(
			"/(?:<title>)(.*)(?:<\/title>)/Usi",
			$this->html,
			$m
		)) {
			$this->title = $m[1];
			$this->title = explode("|", $this->title, 2);
			$this->title = trim(html_entity_decode($this->title[0], ENT_QUOTES,"UTF-8"));
			if (strlen($this->title) < 8) {
				icelog("Could not get the title");
				icelog("Skipping...");
				return false;
			}
			icelog("Got title: %s", $this->title);
		} else {
			icelog("Could not get the title");
			icelog("Skipping...");
			return false;
		}

		if (preg_match(
			"/(?:<meta property=\"article\:published_time\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got date and time: %s", $this->datetime);
		} else {
			icelog("Could not get date and time.");
		}

		if (preg_match(
			"/(?:<meta name=\"keywords\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$tagsPointer = &$this->tags;
			$m[1] = explode(",", $m[1]);
			array_walk($m[1], function ($tag) use (&$tagsPointer) {
				$tag = trim(html_entity_decode($tag, ENT_QUOTES, "UTF-8"));
				if (! empty($tag)) {
					$tagsPointer[] = $tag;
				}
			});
			unset($tagsPointer);
			$tagsCount = count($this->tags);
			if ($tagsCount > 0) {
				icelog("Got %d tag(s): %s", $tagsCount, json_encode($this->tags));
			} else {
				icelog("Could not get the tags");
			}
		} else {
			icelog("Could not get the tags");
		}

		if (preg_match(
			"/(?:<meta property=\"article:author\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got %d author(s): %s", count($this->authors), json_encode($this->authors));
		} else {
			if (preg_match(
				"/(?:<meta name=\"author\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				icelog("Got %d author(s): %s", count($this->authors), json_encode($this->authors));
			} else {
				icelog("Could not get the author");
			}
		}

		if (preg_match(
			"/(?:<meta name=\"twitter:image\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$desc = "";

			if (preg_match(
				"/(?:<meta name=\"twitter:description\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$desc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			}

			$this->images[] = [
				"url" => $url,
				"description" => $desc
			];

			icelog("Got %s image(s)", count($this->images));

		} else {
			icelog("Could not get the image");
		}

		return true;
	}

	public function scrape(): bool
	{
		if ($this->metaHandler()) {
			if (preg_match(
				"/(?:<div class=\"entry\-content\">)(.*)(?:<div id=\"comments\" class=\"comments\-area\">)/Usi",
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

				$contentPointer = explode("\n", $contentPointer);
				array_walk($contentPointer, function (&$segment, $index) use (&$contentPointer) {
					$segment = trim($segment);
					if ($segment === "") {
						unset($contentPointer[$index]);
					}
				});

				$contentPointer = implode("\n", $contentPointer);
				$contentPointer = str_replace("\r\n", "\n", $contentPointer);

				do {
					$contentPointer = str_replace("\n\n", "\n", $contentPointer, $nnn);
				} while ($nnn);

				unset($contentPointer, $m, $nnn);

				$contentLength = strlen($this->content);

				$this->contentType = "news";				

				if ($contentLength > 10) {
					icelog("Got content with %d characters", $contentLength);
					return true;
				}

				icelog("Could not get the content");
				icelog("Skipping...");

				return false;
			}
		}
		return false;
	}
}
