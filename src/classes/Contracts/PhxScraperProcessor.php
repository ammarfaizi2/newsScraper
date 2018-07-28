<?php

namespace Contracts;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Contracts
 * @license MIT
 * @version 0.0.1
 */
interface PhxScraperProcessor
{
	/**
	 * @return string
	 */
	public function getRegional();

	/**
	 * @return string
	 */
	public function getTitle();

	/**
	 * @return string
	 */
	public function getAuthor();

	/**
	 * @return array
	 */
	public function images();

	/**
	 * @return string
	 */
	public function getContent();

	/**
	 * @return array
	 */
	public function getTags();

	/**
	 * @return array
	 */
	public function getCategory();

	/**
	 * @return array
	 */
	public function getComments();

	/**
	 * @return string
	 */
	public function getHTML();
}
