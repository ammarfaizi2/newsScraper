<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

header("Content-Type: application/json");

$pdo = DB::pdo();

$query = "SELECT `title` FROM `news` WHERE `title` != '' ";
$bind  = [];

if (isset($_GET["regional"])) {
	$regional = $_GET["regional"];
	$query .= "AND `regional` = :regional ";
	if (is_numeric($_GET["regional"])) {
		$st = $pdo->prepare("SELECT `regional` FROM `regional` WHERE `id` = :id LIMIT 1;");
		$st->execute([":id" => $_GET["regional"]]);
		if ($st = $st->fetch(PDO::FETCH_NUM)) {
			$regional = $st[0];
		}
		unset($st);
	}
	$bind[":regional"] = $regional;
	unset($regional);
}

if (isset($_GET["start_date"])) {
	$start_date = $_GET["start_date"];
	if (! is_numeric($_GET["start_date"])) {
		$start_date = strtotime($_GET["start_date"]);
	}
	$query .= "AND `datetime` >= :start_date ";
	$bind[":start_date"] = $start_date;
	unset($start_date);
}

if (isset($_GET["end_date"])) {
	$end_date = $_GET["end_date"];
	if (! is_numeric($_GET["end_date"])) {
		$end_date = strtotime($_GET["end_date"]);
	}
	$query .= "AND `datetime` <= :end_date ";
	$bind[":end_date"] = $end_date;
	unset($end_date);
}

$limit = 500;

if (isset($_GET["limit"])) {
	if (is_numeric($_GET["limit"])) {
		$limit = (int)$_GET["limit"];
	}
}

$query .= " ORDER BY `datetime` DESC LIMIT {$limit};";

$st = $pdo->prepare($query);
$st->execute($bind);

$result = "";

while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	$r["title"] = preg_replace(
	      [
	            "/\-antara\s?news.+$/Usi",
	            "/liputan6com/Usi",
	            "/bali\-kompascom/Usi",
	            "/\-\s?Kompas\.com/Usi",
	            "/halaman \d{1,3}-kompascom/Usi",
	            "/[a-z]{3,10}\s?[a-z]{3,10}-kompascom/Usi",
	            "/\-kompascom/Usi",
	      ],
	      [
	            "",
	            "",
	            "",
	            "",
	            "",
	            "",
	            "",
	      ],
	      $r["title"]
	);
	$r["title"] = trim(trim($r["title"]), "-");
	$result .= $r["title"];
}

unset($pdo, $st);

$result = preg_replace(
	[
		"/[^a-z0-9\-\s]/Usi",
		"/antara news [a-z]+(?:[\s]{1})/Usi",
	],
	[
		"",
		""
	],
	$result
);

print json_encode(["result" => strtolower($result)], JSON_UNESCAPED_SLASHES);
