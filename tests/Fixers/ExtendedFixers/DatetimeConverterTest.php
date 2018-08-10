<?php

namespace test\Fixers\ExtendedFixers;

use PHPUnit\Framework\TestCase;
use Phx\Scrapers\Fixers\ExtendedFixers\Datetime\DatetimeParser;
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
				1518448500 => "2018-02-12 22:15:00",
				1481971320 => "Sabtu, 17 Desember 2016 17:42 WIB",
				1423880771 => "2015-02-14 09:26:11",
				1511665620 => "2017-11-26 10:07:00",
				1519974960 => "02 Mar 2018, 14:16 WIB",
				1533023080 => "Tue, 31 Jul 2018 14:44:40 +0700",
				1533533040 => "Senin, 6 Agustus 2018 12:24",
				1478784000 => "2016-11-10 20:20:00",
				1533728040 => "Rabu, 8 Agustus 2018 18:34",
				1533523380 => "Senin, 6 Agustus 2018 09:43",
				1517181900 => "2018-01-29 06:25:00",
				1518015600 => "07 Feb 2018, 22:00 WIB",
			] as $epoch => $date
		) {
			$fixer = new DatetimeConverter($date);
			$this->assertEquals($fixer->toUnix(), $epoch);
		}
	}

	public function testGetType()
	{
		foreach (
			[
				"Senin, 16 Agustus 2018 11:16" => DatetimeParser::DF0,
				"2016-04-01 22:40:00" => DatetimeParser::DF1,
				"2018-07-02 12:52:35" => DatetimeParser::DF1,
				"2018-01-03 21:00:00" => DatetimeParser::DF1,
				"2017-02-24 07:10:00" => DatetimeParser::DF1,
				"Kamis, 2 Agustus 2018 18:39" => DatetimeParser::DF0,
				"2014-03-04 14:38:34" => DatetimeParser::DF1,
				"01 Agu 2018, 12:45 WIB" => DatetimeParser::DF2,
				"2018-02-12 22:15:00"  => DatetimeParser::DF1,
				"Sabtu, 17 Desember 2016 17:42 WIB" => DatetimeParser::DF0,
				"2015-02-14 09:26:11" => DatetimeParser::DF1,
				"2017-11-26 10:07:00" => DatetimeParser::DF1,
				"02 Mar 2018, 14:16 WIB" => DatetimeParser::DF2,
				"Tue, 31 Jul 2018 14:44:40 +0700" => DatetimeParser::DF3,
				"Senin, 6 Agustus 2018 12:24" => DatetimeParser::DF0,
				"2016-11-10 20:20:00" => DatetimeParser::DF1,
				"Rabu, 8 Agustus 2018 18:34" => DatetimeParser::DF0,
				"Senin, 6 Agustus 2018 09:43" => DatetimeParser::DF0,
				"2018-01-29 06:25:00" => DatetimeParser::DF1,
				"07 Feb 2018, 22:00 WIB" => DatetimeParser::DF2,
				"" => DatetimeParser::DF_UNKNOWN
			] as $date => $type
		) {
			$fixer = new DatetimeConverter($date);
			$this->assertEquals($fixer->getType(), $type);
		}
	}
}
