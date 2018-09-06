<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Gonews
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
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got title: %s", $this->title);
		} else {
			icelog("Could not get the content");
			icelog("Skipping...");
			return false;
		}

		if (preg_match(
			"/(?:<meta property=\"article:published_time\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got date and time: %s", $this->datetime);
		} else {
			icelog("Could not get date and time");
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
				if ($tag !== "") {
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
			"/(?:<meta property=\"dable:author\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got %d author(s): %s", count($this->authors), json_encode($this->authors));
		} else {
			icelog("Could not get the author");
		}

		if (preg_match(
			"/(?:<meta property=\"og:image\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$imgUrl = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF"));
			$desc = "";
			if (preg_match(
				"/(?:<meta property=\"og:description\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$desc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			}

			$this->images[] = [
				"url" => $imgUrl,
				"description" => $desc
			];
		} else {
			if (preg_match(
				"/(?:<meta name=\"twitter:image:src\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$imgUrl = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$desc = "";
				if (preg_match(
					"/(?:<meta name=\"twitter:description\" content=\")(.*)(?:\")/Usi",
					$this->html,
					$m
				)) {
					$desc = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				}

				$this->images[] = [
					"url" => $imgUrl,
					"description" => $desc
				];
			} else {
				icelog("Could not get the image");
			}
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
				"/(?:<div class=\"news-content\">)(.*<p>.*)(?:<\/div>)/Usi",
				$this->html,
				$m
			)) {

				$contentPointer = &$this->content;
				$contentPointer = trim(html_entity_decode(strip_tags(str_replace(
					["<p>", "</p>", "</P>", "<P>"],
					"\n",
					$m[1]
				))));

				do {
					$contentPointer = str_replace("\n\n", "\n", $contentPointer, $nnn);
				} while ($nnn);

				unset($m[1], $contentPointer);

				$contentLength = strlen($this->content);

				if ($contentLength < 50) {
					icelog("Could not get the content");
					icelog("Skipping...");
					return false;
				}

				icelog("Got content with %d characters length", $contentLength);

				return true;
			}
			
			return true;
		}
		return false;
	}
}
