<?php

namespace Phx\Scrapers\Fixers\ExtendedFixers\Datetime;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package hx\Scrapers\Fixers\ExtendedFixers\Datetime
 * @license MIT
 * @version 0.0.1
 */
final class DatetimeParser
{
	/**
	 * Unknown format
	 */
	const DF_UNKNOWN = -1;

	/**
	 * "Senin, 6 Agustus 2018 11:16"
	 */
	const DF0 = 10;

	/**
	 * "2016-04-01 22:40:00"
	 */
	const DF1 = 11;

	/**
	 * "01 Agu 2018, 12:45 WIB"
	 */
	const DF2 = 12;

	/**
	 * "Tue, 31 Jul 2018 14:44:40 +0700"
	 */
	const DF3 = 13;

	/**
	 * "2018-08-22T21:12:00+0700"
	 * "2018-08-04T22:49:21+07:00"
	 */
	const DF4 = 14;

	/**
	 * "1534581504"
	 */
	const DF5 = 15;

	/**
	 * "2018/07/08 18:06:58"
	 */
	const DF6 = 16;

	/**
	 * "2018-03-11T15:22:00Z"
	 */
	const DF7 = 17;

	/**
	 * "01", "10"
	 */
	const ZEROFILL_NUMBER = 20;

	/**
	 * "Januari", "Februari", ...
	 */
	const ALPHA_MONTH = 30;

	/**
	 * "Jan", "Feb", ..., "Des"
	 */
	const ALPHA_MONTH_2 = 31;

	/**
	 * "Jan", "Feb", ..., "Dec"
	 */
	const ALPHA_MONTH_3 = 32;

	/**
	 * @var \Phx\Scrapers\Fixers\ExtendedFixers\Datetime\DatetimeConverter
	 */
	private $datetimeConv;

	/**
	 * @var int
	 */
	private $formatType = -1;

	/**
	 * @var string
	 */
	private $input = "";

	/**
	 * @var array
	 */
	private $matches = [];

	/**
	 * @var string
	 */
	private $vv = "1970-01-01 07:00:00";

	/**
	 * @var string
	 */
	private $standardDatetime = "1970-01-01 07:00:00";

	/**
	 * Constructor.
	 *
	 * @param \Phx\Scrapers\Fixers\ExtendedFixers\Datetime\DatetimeConverter $datetimeConv
	 * @return void
	 */
	public function __construct(DatetimeConverter $datetimeConv)
	{
		$this->formatType = self::DF_UNKNOWN;
		$this->datetimeConv = $datetimeConv;
		$this->input = $this->datetimeConv->getInput();
		if ($this->parse()) {
			$this->next();
		}
	}

	/**
	 * @return int
	 */
	public function getType(): int
	{
		return $this->formatType;
	}

	/**
	 * @return int
	 */
	public function getUnix(): int
	{
		return strtotime($this->vv);
	}

	/**
	 * @return bool
	 */
	private function parse(): bool
	{
		if (preg_match(
			"/^(?:(Senin|Selasa|Rabu|Kamis|Jum'?at|Sabtu|Minggu),\s)(\d{1,2})(?:\s)(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)(?:\s)(\d{4})(?:\s)(\d{1,2})(?:\:)(\d{2})(?:\s(WIB|WIT|WITA))?$/Usi",
			$this->input,
			$m
		)) {
			$this->formatType = self::DF0;
			$this->matches = $m;
			return true;
		} elseif (preg_match(
			"/^\d{4}-\d{2}-\d{2}\s(\d{2}:){2}(\d{2})?$/",
			$this->input,
			$m
		)) {
			$this->formatType = self::DF1;
			return true;
		} elseif (preg_match(
			"/(\d{2})(?:\s)(Jan|Feb|Mar|Apr|Mei|Jun|Jul|Agu|Sep|Okt|Nov|Des)(?:\s)(\d{4})(?:,?\s)(\d{2})(?::)(\d{2})(?:\s(WIB|WIT|WITA))?$/Usi",
			$this->input,
			$m
		)) {
			$this->formatType = self::DF2;
			$this->matches = $m;
			return true;
		} elseif (preg_match(
			"/(?:Sun|Mon|Tue|Wed|Thu|Fri|Sat)(?:,?\s)(\d{1,2})(?:\s)(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)(?:\s)(\d{4})(?:\s)(\d{2})(?::)(\d{2})(?::)(\d{2})(?:\s\+\d{4})?$/Usi",
			$this->input,
			$m
		)) {
			$this->formatType = self::DF3;
			$this->matches = $m;
			return true;
		} elseif (preg_match(
			"/^\d{4}-\d{2}-\d{2}T(\d{2}:){2}(\d{2})\+(\d{2}:?\d{2})?$/",
			$this->input,
			$m
		)) {
			$this->formatType = self::DF4;
			return true;
		} elseif (preg_match(
			"/^\d{10}$/",
			$this->input
		)) {
			$this->formatType = self::DF5;
			return true;
		} elseif (preg_match(
			"/\d{4}\/\d{2}\/\d{2}\s\d{2}\:\d{2}\:\d{2}/Usi",
			$this->input,
			$m
		)) {
			$this->formatType = self::DF6;
			return true;
		} elseif (preg_match(
			"/^(\d{4}\-\d{2}-\d{2})(?:T)(\d{2}\:\d{2}\:\d{2})(?:Z)$/Usi",
			$this->input,
			$m
		)) {
			$this->matches = $m;
			$this->formatType = self::DF7;
			return true;
		}

		return false;
	}

	/**
	 * @return void
	 */
	private function next(): void
	{
		$m = &$this->matches;
		switch ($this->formatType) {
			case self::DF0:
					$this->vv = 
						$m[4]."-".$this->convert($m[3], self::ALPHA_MONTH, self::ZEROFILL_NUMBER)."-".$m[2]." ".$m[5].":".$m[6].":00";
				break;
			case self::DF1:
					$this->vv = $this->input;
				break;
			case self::DF2:
					$this->vv =
						$m[3]."-".$this->convert($m[2], self::ALPHA_MONTH_2, self::ZEROFILL_NUMBER)."-".$m[1]." ".$m[4].":".$m[5].":00";
				break;
			case self::DF3:
					$this->vv =
						$m[3]."-".$this->convert($m[2], self::ALPHA_MONTH_3, self::ZEROFILL_NUMBER)."-".$m[1]." ".$m[4].":".$m[5].":".$m[6];
				break;
			case self::DF4:
					$this->vv = date("Y-m-d H:i:s", strtotime($this->input));
				break;
			case self::DF5:
					$this->vv = date("Y-m-d H:i:s", $this->input);
				break;
			case self::DF6:
					$this->vv = date("Y-m-d H:i:s", strtotime($this->input));
				break;
			case self::DF7:
					$this->vv = date("Y-m-d H:i:s", strtotime($this->matches[1]." ".$this->matches[2]));
				break;
			default:
				break;
		}
	}

	/**
	 * @param mixed $input
	 * @param int   $from
	 * @param int   $to
	 * @return mixed
	 */
	private function convert($input, int $from, int $to)
	{
		switch ($from) {
			case self::ALPHA_MONTH:
					return $this->alphaMonthConverter($input, $to, $from);
			case self::ALPHA_MONTH_2:
					return $this->alphaMonthConverter($input, $to, $from);
			case self::ALPHA_MONTH_3:
					return $this->alphaMonthConverter($input, $to, $from);
		}
	}

	/**
	 * @param string $input
	 * @param int 	 $to
	 * @param int    
	 * @return mixed
	 */
	private function alphaMonthConverter(string $input, int $to, int $type = 1)
	{
		switch ($to) {
			case self::ZEROFILL_NUMBER:
					return $this->monthZerofillNumberFromAlpha($input, $type);
				break;
			default:
				break;
		}
	}

	/**
	 * @param string $input
	 * @param int 	 $type
	 * @return string
	 */
	private function monthZerofillNumberFromAlpha(string $input, int $type): string
	{		
		$input = strtolower($input);
		switch ($type) {
			case self::ALPHA_MONTH:
					switch ($input) {
						case "januari":
							return "01";
						case "februari":
							return "02";
						case "maret":
							return "03";
						case "april":
							return "04";
						case "mei":
							return "05";
						case "juni":
							return "06";
						case "juli":
							return "07";
						case "agustus":
							return "08";
						case "september":
							return "09";
						case "oktober":
							return "10";
						case "november":
							return "11";
						case "desember":
							return "12";
						default:
							return "01";
					}
				break;
			case self::ALPHA_MONTH_2:
					switch ($input) {
						case "jan":
							return "01";
						case "feb":
							return "02";
						case "mar":
							return "03";
						case "apr":
							return "04";
						case "mei":
							return "05";
						case "jun":
							return "06";
						case "jul":
							return "07";
						case "agu":
							return "08";
						case "sep":
							return "09";
						case "okt":
							return "10";
						case "nov":
							return "11";
						case "des":
							return "12";
						default:
							return "01";
					}
				break;
			case self::ALPHA_MONTH_3:
					switch ($input) {
						case "jan":
							return "01";
						case "feb":
							return "02";
						case "mar":
							return "03";
						case "apr":
							return "04";
						case "may":
							return "05";
						case "jun":
							return "06";
						case "jul":
							return "07";
						case "aug":
							return "08";
						case "sep":
							return "9";
						case "oct":
							return "10";
						case "nov":
							return "11";
						case "dec":
							return "12";
						default:
							return "01";
					}
				break;
		}
	}
}
