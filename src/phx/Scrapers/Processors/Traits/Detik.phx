<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Detik
{
	/**
	 * @return bool
	 */
	private function metaHandler(): bool
	{
		if (preg_match(
			"/(?:<meta property=\"og:title\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got title: ".$this->title);
		} else {
			icelog("Could not get title");
			icelog("Skipping...");
			return false;
		}

		if (preg_match(
			"/(?:<meta name=\"publishdate\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got datetime: ".$this->datetime);
		} else {
			icelog("Could not get datetime");
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
			"/(?:<meta name=\"author\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));
		} else {
			icelog("Could not get authors");
		}

		if (preg_match(
			"/(?:<meta property=\"og:image\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$url = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$desc = "";
			if (preg_match(
				"/(?:<meta property=\"og:description\" content=\")(.*)(?:\")/Usi",
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
			icelog("Could not get images");
		}
		return true;
	}

	/**
	 * @return bool
	 */
	private function newsSubdomain(): bool
	{
		if ($this->metaHandler()) {
			if (substr($this->title, 0, 5) === "Foto:") {
				icelog("Images only, skipping...");
				return false;
			}
			if (preg_match(
				"/(?:<div class=\".+\" id=\"detikdetailtext\"\s?>)(.*)(?:<div class=\"detail_tag\">)/Usi",
				$this->html,
				$m
			)) {
				$contentPointer = &$this->content;

				$m = trim(html_entity_decode(strip_tags(preg_replace(
					[
						"/<script>.+<\/script>/Usi",
						"/<strong>Baca juga:.+<\/a>/Usi"
					],
					"",
					$m[1]
				)), ENT_QUOTES, "UTF-8"));

				do {
					$m = str_replace("\n\n", "\n", $m, $n);
				} while ($n);

				$n = explode("\n", $m);

				foreach ($n as &$tmp) {
					$tmp = trim($tmp);
				}

				$contentPointer = implode("\n", $n);

				unset($contentPointer, $m);

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

	/**
	 * @return bool
	 */
	public function healthSubdomain(): bool
	{
		return $this->newsSubdomain();
	}

	/**
	 * @return bool
	 */
	public function travelSubdomain(): bool
	{
		return $this->newsSubdomain();
	}

	/**
	 * @return bool
	 */
	public function financeSubdomain(): bool
	{
		return $this->newsSubdomain();
	}
}
