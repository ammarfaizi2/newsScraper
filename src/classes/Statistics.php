<?php


/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
class Statistics
{
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->pdo = DB::pdo();
	}

	/**
	 * @return void
	 */
	public function regional()
	{
		$st = $this->pdo->prepare(
			"SELECT `regional`,COUNT(`regional`) AS `total` FROM `news` GROUP BY `regional` ORDER BY `total` DESC;"
		);
	}
}
