<?php

namespace test\WordCloud;

use WordCloud;
use PHPUnit\Framework\TestCase;

class WordCloudTest extends TestCase
{
	public function testN2()
	{
		$sentence = "Paman pergi ke rumah teman bersama Jokowi";
		$st = new WordCloud($sentence, 2);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"paman pergi",
				"pergi rumah",
				"rumah teman",
				"teman jokowi"
			]
		);

		$sentence = "Septian sedang makan di rumah teman bersama-sama dengan timnya";
		$st = new WordCloud($sentence, 2);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"septian makan",
				"makan rumah",
				"rumah teman",
				"teman timnya"
			]
		);

		$sentence = "Bupati Simeulue Doyan Makan Memek";
		$st = new WordCloud($sentence, 2);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"bupati simeulue",
				"simeulue doyan",
				"doyan makan",
				"makan memek"
			]
		);
	}

	public function testN3()
	{
		$sentence = "Paman pergi ke rumah teman bersama Jokowi";
		$st = new WordCloud($sentence, 3);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"paman pergi rumah",
				"rumah teman jokowi",
			]
		);

		$sentence = "Septian sedang makan di rumah teman bersama-sama dengan timnya";
		$st = new WordCloud($sentence, 3);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"septian makan rumah",
				"rumah teman timnya",
			]
		);

		$sentence = "Bupati Simeulue Doyan Makan Memek";
		$st = new WordCloud($sentence, 3);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"bupati simeulue doyan",
				"doyan makan memek"
			]
		);
	}

	public function testN4()
	{
		$sentence = "Paman pergi ke rumah teman bersama Jokowi";
		$st = new WordCloud($sentence, 4);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"paman pergi rumah teman",
				"teman jokowi",
			]
		);

		$sentence = "Septian sedang makan di rumah teman bersama-sama dengan timnya";
		$st = new WordCloud($sentence, 4);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"septian makan rumah teman",
				"teman timnya",
			]
		);

		$sentence = "Bupati Simeulue Doyan Makan Memek";
		$st = new WordCloud($sentence, 4);
		$st->toLower();
		$this->assertEquals(
			$st->get(),
			[
				"bupati simeulue doyan makan",
				"makan memek"
			]
		);
	}
}
