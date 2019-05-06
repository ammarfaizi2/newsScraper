<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Kompas
{
	/**
	 * @return void
	 */
	private function metaHandler(): void
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
		}

		if (preg_match(
			"/(?:<meta name=\"content_PublishedDate\" content=\")(.*)(?:\")/Usi",
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
			$this->tags = explode(",", trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8")));
			$tagsPointer = &$this->tags;
			array_walk($this->tags, function (&$tag, $i) use (&$tagsPointer) {
				if (empty($tag = trim($tag))) {
					unset($tagsPointer[$i]);
				}
			});

			if (count($this->tags) === 0) {
				if (preg_match(
					"/(?:<meta name=\"content_tag\" content=\")(.*)(?:\")/Usi",
					$this->html,
					$m
				)) {
					$this->tags = explode(",", trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8")));
					$tagsPointer = &$this->tags;
					array_walk($this->tags, function (&$tag, $i) use (&$tagsPointer) {
						if (empty($tag = trim($tag))) {
							unset($tagsPointer[$i]);
						}
					});

					if (count($this->tags) === 0) {
						icelog("Count not get tags");
					} else {
						icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
					}
				}
			} else {
				icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
			}

		} else {
			icelog("Could not get tags");
		}

		if (preg_match(
			"/(?:<meta name=\"content_author\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$author = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			if (empty($author)) {
				if (preg_match(
					"/(?:<meta name=\"content_editor\" content=\")(.*)(?:\")/Usi",
					$this->html,
					$m
				)) {
					$this->authors[] = $author;
					icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));
				} else {
					icelog("Could not get authors");		
				}
			} else {
				$this->authors[] = $author;
				icelog("Got ".count($this->authors)." author(s): ".json_encode($this->authors));
			}
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
	}

	/**
	 * @return bool
	 */
	private function nasionalSubdomain(): bool
	{
		$this->metaHandler();
		if (substr($this->title, 0, 5) === "Foto:") {
			icelog("Images only, skipping...");
			return false;
		}
		if (preg_match(
			"/(?:<div class=\"read__content\">)(.*)(?:<div class=\"subscribe__wrap clearfix\">)/Usi",
			$this->html,
			$m
		)) {
			$m = trim(html_entity_decode(strip_tags(preg_replace(
				[
					"/<div.+class=\"video\">.+<\/div>/Usi",
					"/<script.+>.+<\/script>/Usi",
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
			$m = implode("\n", $n);
			$this->content = $m;
			$this->contentType = "news";
			return true;
		}
		icelog("Could not get the content");
		icelog("Skipping...");
		return false;
	}


	/**
	 * @return bool
	 */
	public function regionalSubdomain(): bool
	{
		return $this->nasionalSubdomain();
	}

	/**
	 * @return bool
	 */
	public function travelSubdomain(): bool
	{
		return $this->nasionalSubdomain();
	}

	/**
	 * @return bool
	 */
	public function internasionalSubdomain(): bool
	{
		return $this->nasionalSubdomain();
	}
}
