<?php

namespace Contract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Contract
 * @license MIT
 * @version 0.0.1
 */
interface PhxScraperContract
{
	/**
	 * @return void
	 */
	public function run();

	/**
	 * @return array
	 */
	public function getData();
}
