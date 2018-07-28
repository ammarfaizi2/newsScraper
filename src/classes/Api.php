<?php


/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx\Scrapers
 * @license MIT
 * @version 0.0.1
 */
final class Api
{
	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @return void
	 *
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		$this->showData();
	}

	private function showData(): void
	{
		header("Content-Type: application/json");
		$result = [];
		$offset = 0;
		$limit  = 25;

		if (isset($_GET["limit"])) {
			if (! is_numeric($_GET["limit"])) {
				$this->err("\"limit\" must be an integer!");
			}
			$limit = (int)$_GET["limit"];
		}

		if (isset($_GET["offset"])) {
			if (! is_numeric($_GET["offset"])) {
				$this->err("\"offset\" must be an integer!");
			}
			$offset = (int)$_GET["offset"];
		}

		$this->pdo = DB::pdo();
		$query = "SELECT `id`,`title`,`url`,`datetime`,`content`,`regional`,`scraped_at` FROM `news` ORDER BY `scraped_at` DESC LIMIT {$limit} OFFSET {$offset};";
		$st = $this->pdo->prepare($query);
		$st->execute();
		while($r = $st->fetch(PDO::FETCH_ASSOC)) {
			$result = $r;
			$result["authors"] = [];
			$result["categories"] = [];
			$result["tags"] = [];
			$result["images"] = [];

			$st = $this->pdo->prepare("SELECT `author_name` FROM `authors` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $r["id"]]);
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$result["authors"][] = $r[0]
			}

			$st = $this->pdo->prepare("SELECT `category_name` FROM `categories` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $r["id"]]);
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$result["categories"][] = $r[0]
			}

			$st = $this->pdo->prepare("SELECT `tag_name` FROM `tags` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $r["id"]]);
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$result["tags"][] = $r[0]
			}

			$st = $this->pdo->prepare("SELECT `image_url`,`description` FROM `images` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $r["id"]]);
			while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
				$result["categories"][] = $r;
			}

		}

		print json_encode(
			[
				[
					"status" => "success",
					"code" => 200,
					"message" => $result
				]
			],
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
		);
	}

	private function err(string $msg): void
	{
		http_response_code(400);
		print json_encode(
			[
				"status" => "error",
				"code" => 400,
				"message" => $msg
			]
		);
		exit();
	}
}
