<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Banteninfo
{
	/**
	 * @return bool
	 */
	private function metaHandler(): bool
	{
		if (preg_match(
			"/(?:<title>)(.*)(?:<\/title>)/Usi",
			$this->html,
			$m
		)) {
			$this->title = str_replace("| Banteninfo.com", "", $m[1]);
			$this->title = trim(html_entity_decode($this->title, ENT_QUOTES, "UTF-8"));
			icelog("Got title: %s", $this->title);
		} else {
			icelog("Could not get the title");
			icelog("Skipping..");
			return false;
		}

		if (preg_match(
			"/(?:<meta itemprop=\"datePublished\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got date and time: %s", $this->datetime);
		} else {
			icelog("Could not get date and time");
		}

		if (preg_match(
			"/(?:<meta name=\"author\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got %d author(s): %s", count($this->authors), json_encode($this->authors));
		} else {
			icelog("Could not find author");
		}

		if (preg_match(
			"/(?:<meta property=\"og:image\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$desc = "";

			$this->images[] = [
				"url" => $url,
				"description" => $desc
			];

			icelog("Got %d image(s)", count($this->images));
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

			if (preg_match_all(
				"/(?:<p dir=\"ltr\" style=\"text-align: justify;\">)(.*)(?:<\/p>)/Usi",
				$this->html,
				$m
			)) {
				
				$contentPointer = &$this->content;
				array_walk($m[1], function ($segment) use (&$contentPointer) {
					$segment = trim(strip_tags(html_entity_decode($segment, ENT_QUOTES, "UTF-8")));
					if ($segment !== "") {
						$contentPointer .= $segment;
					}
				});

				$contentLength = strlen($this->content);

				if ($contentLength < 10) {
					icelog("Could not get the content");
					icelog("Skipping...");
					return false;
				}

				
				icelog("Got content with %d characters", $contentLength);

				$this->contentType = "news";

				return true;
			} elseif (preg_match(
				"/(?:<div class=\"td-post-content td-pb-padding-side\">)(.*)(?:<footer>)/Usi",
				$this->html,
				$m
			)) {
				$contentPointer = &$this->content;
				$m[1] = explode("\n", $m[1]);
				array_walk($m[1], function ($segment) use (&$contentPointer) {
					$segment = trim(strip_tags(html_entity_decode($segment, ENT_QUOTES, "UTF-8")));
					if ($segment !== "") {
						$contentPointer .= $segment;
					}
				});

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
