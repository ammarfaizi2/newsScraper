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
	$st2 = $pdo->prepare("SELECT `author_name` FROM `authors` WHERE `news_id` = :news_id;");
	$st2->execute([":news_id" => $_GET["id"]]);
	var_dump($st2->fetch());
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
		
	</center>
</body>
</html>