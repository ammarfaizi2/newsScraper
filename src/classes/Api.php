<?php


/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
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
		$bind = [];
		$where = "";
		if (isset($_GET["regional"]) && $_GET["regional"] !== "" && is_string($_GET["regional"]) && strtolower($_GET["regional"]) !== "all") {
			$where .= "`regional` = :regional AND";
			$bind[":regional"] = trim($_GET["regional"]);
		}

		if (isset($_GET["title"]) && $_GET["title"] !== "" && is_string($_GET["url"])) {
			if (isset($_GET["title_op"])) {
				$oz = trim($_GET["title_op"]);
				switch ($oz) {
					case 8:
						$_GET["title"] = "%".$_GET["title"];
					case 9:
						$_GET["title"] = $_GET["title"]."%";
						break;
					case 10:
						$_GET["title"] = "%".$_GET["title"]."%";
						break;
					default:
						break;
				}
			}
			$where .= "`title` ".gptz($oz)." :title AND";
			$bind[":title"] = $_GET["title"];
		}

		if (isset($_GET["url"]) && $_GET["url"] !== "" && is_string($_GET["url"])) {
			if (isset($_GET["url_op"])) {
				$oz = trim($_GET["url_op"]);
				switch ($oz) {
					case 8:
						$_GET["url"] = "%".$_GET["url"];
					case 9:
						$_GET["url"] = $_GET["url"]."%";
						break;
					case 10:
						$_GET["url"] = "%".$_GET["url"]."%";
						break;
					default:
						break;
				}
			}
			$where .= "`url` ".gptz($oz)." :url AND";
			$bind[":url"] = $_GET["url"];
		}

		if (isset($_GET["datetime"]) && $_GET["datetime"] !== "" && is_string($_GET["datetime"])) {
			if (isset($_GET["datetime_op"])) {
				$oz = trim($_GET["datetime_op"]);
				switch ($oz) {
					case 8:
						$_GET["datetime"] = "%".$_GET["datetime"];
					case 9:
						$_GET["datetime"] = $_GET["datetime"]."%";
						break;
					case 10:
						$_GET["datetime"] = "%".$_GET["datetime"]."%";
						break;
					default:
						break;
				}
			}
			$where .= "`datetime` ".gptz($oz)." :datetime";
			$bind[":datetime"] = $_GET["datetime"];
		}

		if (! empty($where)) {
			$where = "WHERE ".trim(trim($where, "AND"));
		}


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
		$query = "SELECT `id`,`title`,`url`,`datetime`,`content_type`,`text` AS `content`,`regional`,`scraped_at` FROM `news` {$where} ORDER BY `scraped_at` DESC LIMIT {$limit} OFFSET {$offset};";
		$stq = $this->pdo->prepare($query);
		$stq->execute($bind);
		$i = 0;
		while($rr = $stq->fetch(PDO::FETCH_ASSOC)) {		
			$result[$i] = $rr;
			$result[$i]["content"] = str_replace("\r\n", "\n", $result[$i]["content"]);
			$result[$i]["authors"] = [];
			$result[$i]["categories"] = [];
			$result[$i]["tags"] = [];
			$result[$i]["comments"] = [];
			$result[$i]["images"] = [];

			$st = $this->pdo->prepare("SELECT `author_name` FROM `authors` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $rr["id"]]);
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$result[$i]["authors"][] = $r[0];
			}

			$st = $this->pdo->prepare("SELECT `category_name` FROM `categories` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $rr["id"]]);
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$result[$i]["categories"][] = $r[0];
			}

			$st = $this->pdo->prepare("SELECT `tag_name` FROM `tags` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $rr["id"]]);
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$result[$i]["tags"][] = $r[0];
			}

			$st = $this->pdo->prepare("SELECT `author`,`content`,`datetime` FROM `comments` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $rr["id"]]);
			while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
				$result[$i]["comments"][] = $r;
			}

			$st = $this->pdo->prepare("SELECT `image_url`,`description` FROM `images` WHERE `news_id`=:news_id;");
			$st->execute([":news_id" => $rr["id"]]);
			while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
				$result[$i]["images"][] = $r;
			}

			$result[$i]["hash_file"] = "http://".$_SERVER["HTTP_HOST"]."/storage/scraper/hash/".($hash = sha1($rr["url"])."_".md5($rr["url"]));
			$result[$i]["scraped_html_file"] = "http://".$_SERVER["HTTP_HOST"]."/storage/scraper/html/".$hash.".html";
			
			$i++;
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
