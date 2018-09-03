<?php

if ((!isset($_GET["id"])) || (!is_numeric($_GET["id"]))) {
	if (isset($_SERVER["HTTP_REFERER"])) {
		header("Location: ".$_SERVER["HTTP_REFERER"]);
	} else {
		header("Location: data.php");
	}
	exit();
}

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `id`,`title`,`url`,`datetime`,`content_type`,`regional`,`text`,`scraped_at` FROM `news` WHERE `id` = :id LIMIT 1;");
$st->execute([":id" => $_GET["id"]]);
if ($rr = $st->fetch(PDO::FETCH_ASSOC)) {
	$rr["regional_id"] = 0;
	$rr["authors"] = [];
	$rr["tags"] = [];
	$rr["categories"] = [];
	$rr["images"] = [];
	$rr["comments"] = [];


	$st = $pdo->prepare("SELECT `id` FROM `regional` WHERE `regional`=:regional LIMIT 1;");
	$st->execute([":regional" => $rr["regional"]]);
	$st = $st->fetch(PDO::FETCH_NUM)[0];
	$rr["regional_id"] = $st;

	$st = $pdo->prepare("SELECT `author_name` FROM `authors` WHERE `news_id` = :news_id;");
	$st->execute([":news_id" => $_GET["id"]]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$rr["authors"][] = $r[0];
	}

	$st = $pdo->prepare("SELECT `tag_name` FROM `tags` WHERE `news_id` = :news_id;");
	$st->execute([":news_id" => $_GET["id"]]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$rr["tags"][] = $r[0];
	}

	$st = $pdo->prepare("SELECT `category_name` FROM `categories` WHERE `news_id` = :news_id;");
	$st->execute([":news_id" => $_GET["id"]]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$rr["tags"][] = $r[0];
	}

	$st = $pdo->prepare("SELECT `image_url`,`description` FROM `images` WHERE `news_id` = :news_id;");
	$st->execute([":news_id" => $_GET["id"]]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$rr["images"][] = [
			"url" => $r[0],
			"description" => $r[1]
		];
	}

	$st = $pdo->prepare("SELECT `author`,`content` FROM `comments` WHERE `news_id` = :news_id;");
	$st->execute([":news_id" => $_GET["id"]]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$rr["comments"][] = [
			"author" => $r[0],
			"content" => $r[1]
		];
	}

	if (isset($_GET["json"])) {
		header("Content-Type: application/json");
		exit(json_encode($rr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}

	unset($st, $r);
	$rr = str_replace(
		["\n", "  "],
		["<br/>", "&nbsp;"],
		htmlspecialchars(print_r($rr, true), ENT_QUOTES, "UTF-8")
	);
} else {
	?><!DOCTYPE html>
	<html>
	<head>
		<title>Not Found</title>
	</head>
	<body>
		<script type="text/javascript">
			alert("ID <?php print $_GET["id"]; ?> not found!");
			window.location = "data.php";
		</script>
	</body>
	</html><?php
	exit();
}

?><!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<center>
		<a href="index.php"><button style="cursor: pointer;">Back to Home</button></a>
		<a target="_blank" href="?id=<?php print $_GET["id"] ?>&amp;json=1"><button style="cursor: pointer;">Show as JSON</button></a>
	</center>
	<div>
<pre>
<?php print $rr; ?>
</pre>
	</div>
</body>
</html>