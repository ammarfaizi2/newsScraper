<?php


/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class DB
{
	/**
	 * @var self
	 */
	private static $self;

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @return void
	 *
	 * Constructor
	 */
	public function __construct()
	{
		// $this->pdo = new PDO("mysql");
	}
}
