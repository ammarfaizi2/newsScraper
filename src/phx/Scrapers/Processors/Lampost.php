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
final class Lampost implements PhxScraperProcessor
{
	/**
	 * @var string
	 */
	private $url = "";

	private $host = "http://www.lampost.co/";

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

    private $months = array(
        'jan' => 1,
        'feb' => 2,
        'mar' => 3,
        'apr' => 4,
        'mei' => 5,
        'jun' => 6,
        'jul' => 7,
        'agu' => 8,
        'sep' => 9,
        'okt' => 10,
        'nov' => 11,
        'des' => 12
    );

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
			$this->title = trim(html_entity_decode( strip_tags($m[1]), ENT_QUOTES, "UTF-8"));
			icelog("Got title: ".$this->title);
        } else {
			icelog("Not a news page");
			return false;
		}

        /*
         * Get Authors
         */

        if ( preg_match("/class=\"fa fa-user\"><\/span> (.*?) <span/", $l["out"], $o) ) {
            $author = trim(html_entity_decode(strip_tags($o[1]), ENT_QUOTES, "UTF-8"));
            $this->authors[] = $author;
            icelog("Got author: " . $author);
        }
        
        if ( preg_match("/<h4>EDITOR<\/h4>[\s\t\n]+<p>(.*?)<\/p>/", $l["out"], $o) ) {
            $author = trim(html_entity_decode(strip_tags($o[1]), ENT_QUOTES, "UTF-8"));
            $this->authors[] = $author;
            icelog("Got author: " . $author);
        }


        /*
         * Get Images
         */
        if ( preg_match("/og:image\" content=\"([\"]+)\"\/>/", $l["out"], $m) ) {

            $img = trim(html_entity_decode($m[1], ENT_QUOTES, "UTF-8"));
			icelog("Got image: " . $img);
            $des = "";

            if( preg_match("/<figcaption[^>]*>(.*?)<\/figcaption>/", $l["out"], $m) ) {
                $des = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, "UTF-8"));
                icelog("Got description: " . $des);
            }
            
            $this->images[] = array(
                'url' => $img,
                'description' => $des
            );

        }

        // Get date and time
        if ( preg_match("/published_time\" content=\"(.*?)\+/", $l["out"], $m) ) {

            $time   = $m[1];
            $time   = str_replace("T", " ", $time);

            $this->datetime = $time;
			icelog("Got time: " . $time);
        }

        // Get Tags 
        // if ( preg_match("/<span class=\"tags\">(.*?)<\/span>/", $l["out"], $m) ) {
        //     $contentTags = $m[1];

        //     if( preg_match_all("/rel=\"tag\">(.*?)<\/a>/", $contentTags, $n) ) {
        //         foreach ($n[1] AS $key => $tag) {
        //             $this->tags[] = ucwords($tag);
        //             icelog("Got tag: " . ucwords($tag));
        //         }
        //     }
            
        // }

        if( preg_match_all("/class=\"tag\">\#(.*?)<\/a>/", $l["out"], $n) ) {
            foreach ($n[1] AS $key => $tag) {
                $this->tags[] = ucwords($tag);
                icelog("Got tag: " . ucwords($tag));
            }
        }


        /*
         * Get post type
         */
        if( preg_match("/og\:type'[\s\t]+content='([^\']+)'/", $l["out"], $m) ) {
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
		if( preg_match("/<div itemprop=\"articleBody\"([\s|\S]*?)ADVERTNATIVE ADS/", $string, $m) ) {
            $content = $m[1];

            if( preg_match_all("/<p[^>]*>([\s\S]*?)<\/p>/", $content, $n) ) {
                foreach ($n[1] as $k => $v) {
                    $next = $k + 1;
                    $v = preg_replace("/<script[^>]*>(.*?)<\/script>/", "", $v);
                    $this->content .= trim(html_entity_decode(strip_tags($v) . "\n", ENT_QUOTES, "UTF-8"));
                    icelog("Got content");
                }
            }
        }
		
		if( preg_match("/<iframe src=\"(https\:\/\/www\.youtube\.com\/[^\"]+)\"/", $string, $m) ) {
			$this->content .= trim(html_entity_decode(strip_tags($m[1]) . "\n", ENT_QUOTES, "UTF-8"));
			icelog("Got videos");
		}

        // $this->doComment($string);

		if ( preg_match("/<a href=\"([a-zA-Z0-9\/\-\.\_\:]+)\"><span class=\"prevnextpost\">Laman berikutnya/", $string, $m) ) {

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
    
    private function doComment(string $string)
    {
        if( preg_match_all("/<li id=\"comment-[0-9]+\">([\s|\S]+)<\/li><\!-- \#comment-\#\# -->/", $string, $m) ) {
            foreach ($m[1] as $k => $v) {
                $author = "";
                $content = "";
                $datetime = "";
                if( preg_match("/<cite class=\"fn\">(.*?)<\/cite>/", $v, $n) ) {
                    $author = trim(html_entity_decode(strip_tags($n[1]), ENT_QUOTES, "UTF-8"));
                }
                if( preg_match("/<div class=\"comment-content\">[\s|\t|\n]+(.*?)[\s|\t|\n]+<\/div>/", $v, $n) ) {
                    $content = trim(html_entity_decode(strip_tags($n[1]), ENT_QUOTES, "UTF-8"));
                }
                if( preg_match("/<div class=\"comment-meta commentmetadata\">(.*?)<\/div><\!-- \.comment-meta/", $v, $n) ) {
                    $time   = $n[1];
                    $datetime = trim(html_entity_decode(strip_tags($time), ENT_QUOTES, "UTF-8"));
                }
                $comment = array(
                    'author' => $author,
                    'content' => $content,
                    'datetime' => $datetime
                );
                $this->comments[] = $comment;
            }
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
