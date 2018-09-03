<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Jpnn
{
	/**
	 * @return bool
	 */
	private function metaHandler(): bool
	{
		if (preg_match(
			"/(?:<title>)(.*)(<\/title>)/Usi",
			$this->html,
			$m
		)) {
			$m[1] = str_replace(" - Daerah JPNN.com", "", $m[1]);
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got title: %s", $this->title);
		} else {
			icelog("Could not get the title");
			icelog("Skipping...");
			return false;
		}

		if (preg_match(
			"/(?:<meta property=\"article:published_time\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got datetime: %s", $this->datetime);
		} else {
			icelog("Could not get the date and time");
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
				if (!empty($tag)) {
					$tagsPointer[] = $tag;
				}
			});

			unset($tagsPointer);

			$c = count($this->tags);
			if ($c > 0) {
				icelog("Got %d tag(s): %s", $c, json_encode($this->tags));
			} else {
				icelog("Could not get the tags");
			}			
		} else {
			icelog("Could not get tags");
		}

		if (preg_match(
			"/(?:<meta property=\"article:author\" content=\")(.*)(\")/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got %d author(s): %s", count($this->authors), json_encode($this->authors));
		} else {
			icelog("Could not get the author");
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

	/**
	 * @return bool
	 */
	private function scrape(): bool
	{
		if ($this->metaHandler()) {

			if (preg_match(
				"/(?:<div class=\"post\">)(.*)(?:<div class=\"pagination\">)/Usi",
				$this->html,
				$m
			)) {
				$contentPointer = &$this->content;
				$m[1] = explode("\n", trim(strip_tags($m[1])));
				array_walk($m[1], function (&$segment) {
					$segment = trim($segment);
				});
				$contentPointer = implode("\n", $m[1]);
				$contentPointer = str_replace("\r\n", "\n", $contentPointer);

				unset($m);

				do {
					$contentPointer = str_replace("\n\n", "\n", $contentPointer, $nn);
				} while ($nn);

				$contentPointer = trim(html_entity_decode($contentPointer, ENT_QUOTES, "UTF-8"));

				unset($contentPointer);

				$contentLength = strlen($this->content);

				if ($contentLength < 10) {
					icelog("Could not get the content");
					icelog("Skipping...");
					return false;
				}

				icelog("Got content with %d characters", $contentLength);

				$this->contentType = "news";
				
				return true;
			}

			icelog("Could not get the content");
			icelog("Skipping...");
		}
		return false;
	}
}
