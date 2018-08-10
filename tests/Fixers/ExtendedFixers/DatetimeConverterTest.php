<?php

namespace test\Fixers\ExtendedFixers;

use PHPUnit\Framework\TestCase;
use Phx\Scrapers\Fixers\ExtendedFixers\Datetime\DatetimeConverter;

class DatetimeConverterTest extends TestCase
{

	public function testUnixtime()
	{
		foreach(
			[
				1534392960 => "Senin, 16 Agustus 2018 11:16",
				1459525200 => "2016-04-01 22:40:00",
				1530510755 => "2018-07-02 12:52:35",
				1514988000 => "2018-01-03 21:00:00",
				1487895000 => "2017-02-24 07:10:00",
				1533209940 => "Kamis, 2 Agustus 2018 18:39",
				1393918714 => "2014-03-04 14:38:34",
				1533102300 => "01 Agu 2018, 12:45 WIB",
				//1518448500 => "2018-02-12 22:15:00",
				//1481971320 => "Sabtu, 17 Desember 2016 17:42 WIB",
			] as $epoch => $date
		) {
			$fixer = new DatetimeConverter($date);
			// var_dump($epoch, $fixer->toUnix());die;
			$this->assertEquals(
				$fixer->toUnix(), $epoch
			);
		}
	}
}
