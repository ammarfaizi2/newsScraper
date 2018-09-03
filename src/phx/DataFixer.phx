<?php

namespace Phx;

defined("DATA_DIR") or die("Error: DATA_DIR is not defined yet!\n");
defined("HTML_DIR") or die("Error: HTML_DIR is not defined yet!\n");
defined("COOKIE_DIR") or die("Error: COOKIE_DIR is not defined yet!\n");
defined("HASH_CHECK_DIR") or die("Error: HASH_CHECK_DIR is not defined yet!\n");

use DB;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx
 * @license MIT
 * @version 0.0.1
 */
abstract class DataFixer
{
		/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var string
	 */
	protected $cookieFile = "";

	/**
	 * @var string
	 */
	protected $userAgent = "";

      /**
       * @var \PDO
       */
      protected $pdo;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->generateUserAgent();
		$this->cookieFile = COOKIE_DIR."/".str_replace("\\", "_", static::class).".cookie";
		$this->pdo = DB::pdo();
	}

	/**
	 * @return void
	 */
	protected function generateUserAgent(): void
	{
		$this->userAgent = NewsScraper::USERAGENT_COLLECTION[
			rand(0, count(NewsScraper::USERAGENT_COLLECTION) - 1)
		];
	}

	/**
	 * @param string $url
	 * @param array  $opt
	 * @return array
	 */
	public function exec(string $url, $opt = []): array
	{
		$ch = curl_init($url);
		$optf = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_COOKIEFILE => $this->cookieFile,
			CURLOPT_COOKIEJAR => $this->cookieFile,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_USERAGENT => $this->userAgent
		];
		foreach ($opt as $key => $value) {
			$optf[$key] = $value;
		}
		curl_setopt_array($ch, $optf);
		$out = curl_exec($ch);
		$err = curl_error($ch);
		$ern = curl_errno($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		return [
			"out" => $out,
			"error" => $err,
			"errno" => $ern,
			"info" => $info
		];
	}
}
