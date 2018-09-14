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
final class Kabarsumbar implements PhxScraperProcessor
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
		if ( preg_match("/og\:title\" content=\"(.*?)\" \//", $l["out"], $m) ) {
			$this->title = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got title: ".$this->title);
        }

        /*
         * Get Authors
         */
        if ( preg_match("/Editor \: (.*?)<\/span>/", $l["out"], $m) ) {
            $author = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$this->authors[] = $author;
			icelog("Got author: " . $author);
        }
		/* if ( preg_match("/<span style=\"color\: \#808080\;\">\((.*?)\)<\/span>/", $l["out"], $m) ) {
            $author = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			$this->authors[] = $author;
			icelog("Got author: " . $author);
        } */

        /*
         * Get Images
         */
        if ( preg_match("/og\:image\" content=\"(.*?)\?fit \/>/", $l["out"], $m) ) {

            $img = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got image: " . $img);
            $des = "";

            if( preg_match("/class=\"wp-caption-text\">(.*?)<\/figcaption>/", $l["out"], $m) ) {
                $des = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
                icelog("Got description: " . $des);
            }
            
            $this->images[] = array(
                'url' => $img,
                'description' => $des
            );

        }

        // Get date and time
        if ( preg_match("/published\_time\" content=\"(.*?)\+/", $l["out"], $m) ) {
            icelog("Got date and time: " . $m[1]);
            $time = str_replace('T', ' ', $m[1]);
            $this->datetime = $time;
        }

        // Get Tags 
        /* if ( preg_match("/<p class=\"post\-tag\">Tags (.*?)<\/p>/", $l["out"], $m) ) {
            $contentTags = $m[1]; */

            if( preg_match_all("/<meta property=\"article\:tag\" content=\"(.*?)\" \/>/", $l["out"], $n) ) {
                foreach ($n[1] AS $key => $tag) {
                    $this->tags[] = ucwords($tag);
                    icelog("Got tag: " . ucwords($tag));
                }
            }
            
        /* } */


        /*
         * Get post type
         */
        if( preg_match("/\"og\:type\" content=\"(.*?)\" \/>/", $l["out"], $m) ) {
            $this->contentType = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
        } else {
            $this->contentType = "news";
        }

        /*
         * Get kategori
         */

         /* if( preg_match("/itemprop=\"url\">(.*?)<\/a><\/li><li class=\"breadcrumb-item active\">/", $l["out"], $m) ) {
             $kategori = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
         } */

		$this->string = $l["out"];
		do {
			$this->string = $this->doContent($this->string);
		} while($this->string != false);

        
		return true;
	}

	private function doContent(string $string)
	{
        if( preg_match("/<div class=\"entry-content clearfix single-post-content\">([\s|\S]*?)<div class=\"bs-irp left bs-irp-text-3\"/", $string, $m) ) {
            if ( preg_match_all("/<p[ dir=\"ltr\"]*>(.*?)<\/p>/", $m[1], $n) ) {
                foreach($n[1] as $k => $c) {
                    $this->content .= trim(html_entity_decode(strip_tags($c), ENT_QUOTES, "UTF-8"));
                }
                icelog("Got content ");
            }
        }

        if( preg_match("/<div class=\"entry-content clearfix single-post-content\">([\s|\S]*?)<div class=\"bsac bsac-clearfix bsac-post-bottom bsac-float-center bsac-align-center bsac-column-1\"/", $string, $m) ) {
            if ( preg_match_all("/<p[ dir=\"ltr\"]*>(.*?)<\/p>/", $m[1], $n) ) {
                foreach($n[1] as $k => $c) {
                    $this->content .= trim(html_entity_decode(strip_tags($c), ENT_QUOTES, "UTF-8"));
                }
                icelog("Got content ");
            }
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
