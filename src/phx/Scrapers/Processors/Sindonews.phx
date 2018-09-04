<?php

namespace Phx\Scrapers\Processors;

use Phx\NewsScraper;
use Contracts\PhxScraperProcessor;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Processors
 * @license MIT
 * @version 0.0.1
 */
final class Sindonews implements PhxScraperProcessor
{
	/**
	 * @var string
	 */
	private $url = "";

	/**
	 * @var \Phx\NewsScraper
	 */
	private $newsScraper = "";

	/**
	 * @var arry
	 */
	private $tags = [];

	/**
	 * @var string
	 */
	private $title = "";

	/**
	 * @var array
	 */
	private $images = [];

	/**
	 * @var string
	 */
	private $datetime = "";

	/**
	 * @var array
	 */
	private $authors = [];

	/**
	 * @var string
	 */
	private $regional = "";

	/**
	 * @var array
	 */
	private $category = [];

	/**
	 * @var bool
	 */
	private $imagesOnly = false;

	/**
	 * @var string
	 */
	private $html = "";

	/**
	 * @var string
	 */
	private $content = "";

	/**
	 * @var string
	 */
	private $contentType = "";

	/**
	 * @var string
	 */
	private $string;

	/**
	 * @var array
	 */
	private $comments = [];

	/**
	 * @param string			$url
	 * @param \Phx\NewsScraper	$newsScraper
	 * @return void
	 * 
	 * Constructor.
	 */
	public function __construct(string $url, NewsScraper $newsScraper)
	{
		$this->url = $url;
		$this->newsScraper = $newsScraper;
	}

	/**
	 * @return bool
	 */
	public function run(): bool
	{
		icelog("Scraping {$this->url}...");

		$l = $this->newsScraper->exec($this->url);
		if (isset($l["error"]) && $l["error"]) {
			icelog("An error occured when scraping {$this->url}: {$l['errno']} {$l['error']}");
			return false;
		}

		$this->html = $l["out"];
		icelog("Identifying page...");


        /*
         * Get Title
         */
		if ( preg_match("/<h1>(.*?)<\/h1>/", $l["out"], $m) ) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got title: ".$this->title);
        }

        /*
         * Get Authors
         */
		if ( preg_match("/<a rel=\"author\" href=\"\S+\">(.*?)<\/a>/", $l["out"], $m) ) {
            $author = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$this->authors[] = $author;
			icelog("Got author: " . $author);
        }

        /*
         * Get Images
         */
        if ( preg_match("/<img width=\"620\" src=\"(\S*?)\" alt=/", $l["out"], $m) ) {

            $img = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			// $this->authors[] = $author;
			icelog("Got image: " . $img);
            $des = "";
            if ( preg_match("/<figcaption>(.*?)<\/figcaption>/", $l["out"], $m) ) {
                $des = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
                // $this->authors[] = $author;
                icelog("Got description: " . $des);
            }

            $this->images[] = array(
                'url' => $img,
                'description' => $des
            );

        }

        if ( preg_match("/\"datePublished\"\: \"(.*?)\+/", $l["out"], $m) ) {
            $datetime = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
            $datetime = str_replace('T', ' ', $datetime);
            // $this->authors[] = $author;
            icelog("Got date and time: " . $datetime);
            $this->datetime = $datetime;
        }

        if ( preg_match("/<meta name=\"news_keywords\" content=\"(.*?)\">/", $l["out"], $m) ) {
            $keywords = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));

            $exp_keywords = explode(',', $keywords);

            foreach ($exp_keywords AS $key => $keyword) {
                $this->tags[] = ucwords($keyword);
                icelog("Got keyword: " . ucwords($keyword));
            }

            // $this->authors[] = $author;
        }

		$this->string = $l["out"];

		do {
			$this->string = $this->doContent($this->string);
		} while($this->string != false);

        
		return true;
	}

	private function doContent(string $string)
	{
		if ( preg_match("/<div id=\"content\">(.*?)<div class=\"editor\">[\S\(\)]+<\/div> <\/div>/", $string, $m) ) {
            $rawContent = $m[1];
            if ( preg_match_all("/src=\"(.*?)\" alt=\"(.*?)\"/", $m[1], $n) ) {

                foreach($n[1] AS $index => $image) {
                    $this->image[] = array(
                        'url' => trim(html_entity_decode($image, ENT_QUOTES, "UTF-8")),
                        'description' => ( isset( $n[2][$index] ) ) ? trim(html_entity_decode($n[2][$index], ENT_QUOTES, "UTF-8")) : ""
                    );
                }
            }
            $removeDivContent = preg_replace("/<div [\S\s\=\"\(\)]*>(.*?)<\/div>/", "", $rawContent);
            $replaceBrContent = preg_replace("/<br[\s]*>/", "\n", $removeDivContent);
            $content = strip_tags($replaceBrContent);
            $this->contentType = "news";
            $this->content .= trim(html_entity_decode($content, ENT_QUOTES, "UTF-8"));
            icelog("Got content ");
        }

		if ( preg_match("/<li class=\"article-next\"><a href=\"(.*?)\"><i class=\"fa fa-caret-right\">/", $string, $m) ) {

			$l = $this->newsScraper->exec($m[1]);

			if (isset($l["error"]) && $l["error"]) {
				return false;
			} else {
				$next_content = $l["out"];
				return $next_content;
			}
		} else {
			return false;
		}
	}

	/**
	 * @param string $regional
	 * @return void
	 */
	public function setRegional(string $regional)
	{
		$this->regional = $regional;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getDateAndTime(): string
	{
		return $this->datetime;
	}

	/**
	 * @param string $tag
	 */
	public function setTag(string $tag): void
	{
		$this->tags[] = $tag;
	}

	/**
	 * @return string
	 */
	public function getRegional(): string
	{
		return $this->regional;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return array
	 */
	public function getAuthor(): array
	{
		return $this->authors;
	}

	/**
	 * @return array
	 */
	public function getImages(): array
	{
		return $this->images;
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->content;
	}

	/**
	 * @return string
	 */
	public function getContentType(): string
	{
		return $this->contentType;
	}

	/**
	 * @return array
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	/**
	 * @return array
	 */
	public function getCategory(): array
	{
		return $this->category;
	}

	/**
	 * @return array
	 */
	public function getComments(): array
	{
		return $this->comments;
	}

	/**
	 * @return string
	 */
	public function getHTML(): string
	{
		return $this->html;
	}
}
