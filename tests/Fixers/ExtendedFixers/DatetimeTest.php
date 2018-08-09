<?php

namespace test\Fixers\ExtendedFixers;

use PHPUnit\Framework\TestCase;
use Phx\Scrapers\Fixers\ExtendedFixers\DatetimeConverter;

class DatetimeTest extends TestCase
{

	public function testFixer1()
	{
		foreach(
			[
				"Senin, 6 Agustus 2018 11:16",
				"2016-04-01 22:40:00",
				"2018-07-02 12:52:35",
				"2018-01-03 21:00:00",
				"2017-02-24 07:10:00",
				"Kamis, 2 Agustus 2018 18:39",
				"2014-03-04 14:38:34",
				"01 Agu 2018, 12:45 WIB",
				"2018-02-12 22:15:00",
				"Sabtu, 17 Desember 2016 17:42 WIB",
			] as $epoch => $date
		) {
			$fixer = new DatetimeConverter($date);
			$this->assertEquals(
				$fixer->toUnix($epoch), $epoch
			);
		}
	}
}
