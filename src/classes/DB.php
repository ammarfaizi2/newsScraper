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
	 * Constructor
	 */
	public function __construct()
	{
		var_dump(123);
		$this->pdo = new PDO(
			"mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME,
			DB_USER,
			DB_PASS,
			[
				PDO::ATTR_CASE => PDO::CASE_NATURAL,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT => true
			]
		);
	}

	/**
	 * @return \PDO
	 */
	public static function pdo(): PDO
	{
		return self::getInstance()->pdo;
	}

	public static function getInstance()
	{
		if (!(self::$self instanceof DB)) {
			self::$self = new self;
		}
		return self::$self;
	}

	/**
	 * @return void
	 *
	 * Destructor.
	 */
	public function __destruct()
	{
		unset($this->pdo);
	}
}
