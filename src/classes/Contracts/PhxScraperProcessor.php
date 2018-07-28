<?php

namespace Contracts;

use Phx\NewsScraper;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Contracts
 * @license MIT
 * @version 0.0.1
 */
interface PhxScraperProcessor
{
	/**
	 * @param string			$url
	 * @param Phx\NewsScraper	$newsScrape
	 * @return void
	 * 
	 * Constructor.
	 */
	public function __construct(string $url, NewsScraper $newsScraper);

	/**
	 * @return bool
	 */
	public function run(): bool;

	/**
	 * @return string
	 */
	public function getUrl(): string;

	/**
	 * @return string
	 */
	public function getRegional(): string;

	/**
	 * @return string
	 */
	public function getTitle(): string;

	/**
	 * @return array
	 */
	public function getAuthor(): array;

	/**
	 * @return array
	 */
	public function getImages(): array;

	/**
	 * @return string
	 */
	public function getContent(): string;

	/**
	 * @return array
	 */
	public function getTags(): array;

	/**
	 * @return array
	 */
	public function getCategory(): array;

	/**
	 * @return array
	 */
	public function getComments(): array;

	/**
	 * @return string
	 */
	public function getHTML(): string;

	/**
	 * @return string
	 */
	public function getDateAndTime(): string;
}
