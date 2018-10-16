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
final class Satusulteng implements PhxScraperProcessor
{
	/**
	 * @var string
	 */
	private $url = "";

	private $host = "http://www.satusulteng.com/";

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
        'may' => 5,
        'jun' => 6,
        'jul' => 7,
        'aug' => 8,
        'agu' => 8,
        'sep' => 9,
        'oct' => 10,
        'okt' => 10,
        'nov' => 11,
        'des' => 12,
        'dec' => 12
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
		if ( preg_match("/<h1 class=\"entry-title\" itemprop=\"headline\">(.*?)<\/h1>/", $l["out"], $m) ) {
			$this->title = trim(html_entity_decode( strip_tags($m[1]), ENT_QUOTES, "UTF-8"));
			icelog("Got title: ".$this->title);
        } else {
			icelog("Not a news page");
			return false;
		}

        /*
         * Get Authors
         */

        if ( preg_match("/<small itemprop=\"name\">(.*?)<\/small>/", $l["out"], $o) ) {
            $author = trim(html_entity_decode(strip_tags($o[1]), ENT_QUOTES, "UTF-8"));
            $this->authors[] = $author;
            icelog("Got author: " . $author);
        }
        
        //     $author = trim(html_entity_decode(strip_tags("Satusulteng"), ENT_QUOTES, "UTF-8"));
        //     $this->authors[] = $author;
        //     icelog("Got author: " . $author);
        // }

        /*
         * Get Images
         */
        if ( preg_match("/og\:image\"[\s]+content=\"([^\"]+)\" \/>/", $l["out"], $m) ) {

            $i = $m[1];

            $img = "";
            $des = "";

            $img = trim(html_entity_decode( $i, ENT_QUOTES, "UTF-8"));
            icelog("Got image: " . $img);

            if( preg_match("/<figcaption[^\>]*>[\s\t\n]+(.*?)[\s\t\n]+<\/figcaption>/", $l["out"], $n) ) {
                $des = trim(html_entity_decode(strip_tags($n[1]), ENT_QUOTES, "UTF-8"));
                icelog("Got description: " . $des);
            }
            
            $this->images[] = array(
                'url' => $img,
                'description' => $des
            );

        }

		// Get date and time

		// if ( preg_match("/<span class=\"meta_date\">([^\,]+)\, ([0-9]+)<\/span>/", $l["out"], $m) ) {

        //     $date           = $m[1];
        //     $year           = $m[2];
        //     $detail_time    = '00:00:00';

        //     $exp    = explode(" ", $date);
			
        //     // $detail_time = "00:00:00";

        //     $time   = $date . $year ;
            
        //     if( count($exp) > 1) {
        //         $mon = strtolower(substr($exp[0], 0, 3));
        //         if( isset($this->months[$mon]) ) {
        //             $real_mon = sprintf("%02d", $this->months[$mon]);

        //             // $detail_time = $exp[3];

        //             $time = $year . '/' . $real_mon . '/' . sprintf("%02d", $exp[1]) . ' ' . $detail_time;
        //         }
        //     }

		// 	// $time 	= $this->toWIB($date . ' ' . $detail_time);
            
        //     $this->datetime = $this->toWIB($time);
		// 	icelog("Got time: " . $time);
        // }

        // if ( preg_match("/<\/a> on (.*?)<\/span>/", $l["out"], $m) ) {

        //     $time   = $m[1];
        //     $detail = "00:00:00";
            
        //     $exp    = explode('/', $time);
        //     if( count($exp) > 1 ) {
        //         $time = $exp[2] . '/' . $exp[1] . '/' . $exp[0];
        //     }
            
        //     $this->datetime = $time . " " . $detail;
		// 	icelog("Got time: " . $time);
        // }

        if ( preg_match_all("/published_time\" content=\"([^\+]+)\+/", $l["out"], $m) ) {

            $time   = $m[1][0];

            $time   = str_replace("T", " ", $time);
            
            // $this->datetime = $this->toWIB($time);
            $this->datetime = $time;
			icelog("Got time: " . $time);
        }

        // if ( preg_match("/\"datePublished\"\:\"([^\+]+)\+/", $l["out"], $m) ) {

        //     $time   = $m[1];

        //     $time   = str_replace("T", " ", $time);
            
        //     $this->datetime = $this->toWIB($time);
        //     // $this->datetime = $time;
		// 	icelog("Got time: " . $time);
        // }
        

        // Get Tags 
        // if ( preg_match("/<ul class=\"tag\">(.*?)<\/ul>/", $l["out"], $m) ) {
        //     $contentTags = $m[1];

        //     if( preg_match_all("/<li><a href=\"[^\"]+\">(.*?)\,/", $contentTags, $n) ) {
        //         foreach ($n[1] AS $key => $tag) {
        //             $this->tags[] = ucwords($tag);
        //             icelog("Got tag: " . ucwords($tag));
        //         }
        //     }
            
        // }

        // if( preg_match_all("/itemprop=\"keywords\" class=\"[^\"]+\">(.*?)<\/a>/", $l["out"], $n) ) {
        //     foreach ($n[1] AS $key => $tag) {
        //         $this->tags[] = ucwords($tag);
        //         icelog("Got tag: " . ucwords($tag));
        //     }
        // }


        /*
         * Get post type
         */
        if( preg_match("/og\:type\"[\s\t]+content=\"([^\"]+)\" \//", $l["out"], $m) ) {
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
		if( preg_match("/<\!-- kk-star-ratings -->([\s|\S]*?)<div id=\"wpdevar_comment_1\"/", $string, $m) ) {
			$content = $m[1];

			if ( preg_match_all("/<img([^\>]+)>/", $content, $m) ) {

				foreach ($m[1] as $key => $value) {

					$img = "";
					$des = "";

					if( preg_match("/src=\"([^\"]+)\"/", $value, $im) ) {
						$img = trim(html_entity_decode(strip_tags($im[1]), ENT_QUOTES, "UTF-8"));
						icelog("Got image: " . $img);
					}

					if( preg_match("/alt=\"([^\"]+)\"/", $value, $de) ) {
						$des = trim(html_entity_decode(strip_tags($de[1]), ENT_QUOTES, "UTF-8"));
						icelog("Got description: " . $des);
					}

					if( $img != "" ) {

						$this->images[] = array(
							'url' => $img,
							'description' => $des
						); 
					} 
					
				}

			}

			// if( preg_match("/<h2[^\>]*>(.*?)<\/h2>/", $content, $o) ) {
			// 	$this->content .= trim(html_entity_decode(strip_tags($o[1]) . "\n", ENT_QUOTES, "UTF-8"));
			// 	icelog("Got content");
			// }

			if( preg_match_all("/<p[^\>]*>([\s\S]*?)<\/p>/", $content, $o) ) {
				foreach ($o[1] as $key => $value) {
                    $next = $key + 1;
					// $value = str_replace(" Tinggalkan Komentar anda", "", $value);
					$this->content .= trim(html_entity_decode(strip_tags($value) . "\n", ENT_QUOTES, "UTF-8"));
                    icelog("Got content");
                    // if( ! isset($o[1][$next]) ) {
                    //     if ( preg_match("/\(([^\<]+)\)/", $value, $p) ) {
                    //         $author = trim(html_entity_decode(strip_tags($p[1]), ENT_QUOTES, "UTF-8"));
                    //         $this->authors[] = $author;
                    //         icelog("Got author: " . $author);
                    //     }                
                    // }
				}
            }
            

			// $content = preg_replace("/<script[^>]*>([\s\S]*?)<\/script>/", "", $content);
            // $content = preg_replace("/<\!--[\s\t\n]+(.*?)[\s\t\n]+-->/", "", $content);
            // // $content = preg_replace("/<em><strong>Baca juga:([\s\S]+)<\/strong><\/em>/", "", $content);
            // $content = preg_replace("/<([^\>]+)>/", "", $content);
            // $content = preg_replace("/([\s]+[\s]+)/", "", $content);

			// $this->content .= trim(html_entity_decode(strip_tags($content) . "\n", ENT_QUOTES, "UTF-8"));
			// icelog("Got content");
        }
		

        // $this->doComment($string);

		if ( preg_match("/rel=\"next\" href=\"([^\"]+)\"/", $string, $m) ) {

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

    private function toWIB($date = null) {
        $time = strtotime($date);
        if( $time ) {
            $WIBtime = $time - 3600;
            return date('Y-m-d H:i:s', $WIBtime);
        }
        return $date;
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
