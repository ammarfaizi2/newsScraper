<?php

namespace Phx;

defined("DATA_DIR") or die("Error: DATA_DIR is not defined yet!\n");
defined("HTML_DIR") or die("Error: HTML_DIR is not defined yet!\n");
defined("HASH_CHECK_DIR") or die("Error: HASH_CHECK_DIR is not defined yet!\n");

use Contract\PhxScraperContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx
 * @license MIT
 * @version 0.0.1
 */
abstract class NewsScraper implements PHXScraperContract
{
	/**
	 * @var array
	 */
	private $data = [];

	/**
	 * @param string $url
	 * @return bool
	 */
	public function hashCheck($url)
	{
		return file_exists(HASH_CHECK_DIR."/".sha1($url));
	}

	/**
	 * @return void
	 */
	abstract public function run();

	/**
	 * @return array
	 */
	abstract public function getData();
}
