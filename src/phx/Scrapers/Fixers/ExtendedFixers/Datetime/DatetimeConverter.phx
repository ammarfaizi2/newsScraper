<?php

namespace Phx\Scrapers\Fixers\ExtendedFixers\Datetime;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers\Fixers\ExtendedFixers\Datetime
 * @license MIT
 * @version 0.0.1
 */
class DatetimeConverter
{
	/**
	 * @var string
	 */
	private $input = "";

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(string $input)
	{
		$this->input = $input;
		$this->parser = new DatetimeParser($this);
	}

	/**
	 * @return string
	 */
	public function getInput(): string
	{
		return $this->input;
	}

	/**
	 * @return int
	 */
	public function toUnix(): int
	{
		return $this->parser->getUnix();
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->parser->getType();
	}
}
