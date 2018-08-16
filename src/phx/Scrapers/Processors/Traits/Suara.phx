<?php

namespace Phx\Scrapers\Processors\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors\Traits
 * @license MIT
 * @version 0.0.1
 */
trait Suara
{
	/**
	 * @return bool
	 */
	public function metaHandler(): bool
	{
		/**
		 * Get title
		 */
		if (preg_match(
			"/(?:<meta name=\"title\" content=\")(.*)(?:\")/Usi",
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
				"/(?:<meta property=\"og:title\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
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
			"/(?:<meta name=\"content_PublishedDate\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got date and time: {$this->datetime}");
		} else {
			icelog("Could not get date and time");
		}

		/**
		 * Get keywords/tags
		 */
		if (preg_match(
			"/(?:<meta name=\"keywords\" content=\")(.*)(\")/Usi",
			$this->html,
			$m
		)) {
			$tags = explode(",", html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$tagsPointer = &$this->tags;

			array_walk($tags, function ($tag) use (&$tagsPointer) {
				$tag = trim($tag);
				if (! empty($tag)) {
					$tagsPointer[] = $tag;
				}
			});

			unset($tagsPointer, $tags);

			icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
		} else {
			/**
			 * Get keyword/tags (alternative way)
			 */
			if (preg_match(
				"/(?:<meta name=\"news_keywords\" content=\")(.*)(?:\")/Usi",
				$this->html,
				$m
			)) {
				$tags = explode(",", html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
				$tagsPointer = &$this->tags;

				array_walk($tags, function ($tag) use (&$tagsPointer) {
					$tag = trim($tag);
					if (! empty($tag)) {
						$tagsPointer[] = $tag;
					}
				});

				unset($tagsPointer, $tags);

				icelog("Got ".count($this->tags)." tag(s): ".json_encode($this->tags));
			} else {
				icelog("Could not get the tags or keywords");
			}
		}

		/**
		 * Get content author.
		 */
		if (preg_match(
			"/(?:<meta name=\"content_author\" content=\")(.*)(?:\")/Usi",
			$this->html,
			$m
		)) {
			$this->authors[] = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got author(s): ".json_encode($this->authors));
		} else {
			icelog("Could not get the author");
		}

		/**
		 * Get image.
		 */
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
		} else {
			icelog("Could not get the image");
		}

		return true;
	}

	public function newsPath(): bool
	{
		if ($this->metaHandler()) {
			if (preg_match(
				"/(?:<div class=\"content-article\">)(.*)(?:<\/article>)/Usi",
				$this->html,
				$m
			)) {
				$contentPointer = &$this->content;
				$contentPointer = trim(
					html_entity_decode(
						strip_tags(preg_replace(
							[
								"/Baca Juga :.+<\/a>/Usi",
								"/<script.+<\/script>/Usi"
							],
							"",
							$m[1]
						)),
						ENT_QUOTES,
						"UTF-8"
				));
				$contentPointer = str_replace("\r\n", "\n", $contentPointer, $n);
				do {
					$contentPointer = str_replace("\n\n", "\n", $contentPointer, $n);
				} while ($n);
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
				return false;
			}

			icelog("Got content with ".strlen($this->content)." characters");

			$this->contentType = "news";
			return true;
		}

		return false;
	}
}
