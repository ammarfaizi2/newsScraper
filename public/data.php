<?php

$page = isset($_GET["page"]) ? $_GET["page"] : 1;
if (! is_numeric($page)) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Error!</title>
	<script type="text/javascript">alert("Page parameter must be numeric!");window.location="?";</script>
</head>
<body>
</body>
</html><?php exit();
}

$page = (int) $page;

function tq($str, $max = 32)
{
	if (strlen($str) > $max) {
		return substr($str, 0, $max)."...";
	}
	return $str;
}

function es($str)
{
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

?><!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		* {
			font-family: Arial;
			font-size: 13px;
		}
		.dd {
			display: inline-block;
			border: 1px solid #000;
			padding: 10px;
			height: 600px;
			margin-bottom: 40px;
		}
		th {
			padding: 5px;
		}
		td {
			padding: 5px;
		}
		table {
			border-collapse:collapse;
		}
		button {
			cursor: pointer;
			padding: 4px 10px 4px 10px;
		}
	</style>
	<title>Scraped Data</title>
</head>
<body>
	<center>
		<h1 style="font-size: 32px;">Scraped Data</h1>
		<table border="1">
			<thead>
				<tr>
					<th>No.</th><th>Title</th><th>URL</th><th>Datetime</th><th>Authors</th><th>Content Type</th><th>Content</th><th>Regional</th><th>Scraped At</th><th>Action</th>
				</tr>
			</thead>
			<tbody>
<?php
$pdo = DB::pdo();
$limit = 25;
$offset = $page === 1 ? 0 : $limit * ($page-1);
$i = $page === 1 ? 1 : $limit * ($page-1);
$stq = $pdo->prepare(
	"SELECT `id`,`title`,`url`,`datetime`,`content_type`,`text` AS `content`,`regional`,`scraped_at` FROM `news` ORDER BY `scraped_at` DESC LIMIT {$limit} OFFSET {$offset};"
);
$stq->execute();
while ($rr = $stq->fetch(PDO::FETCH_ASSOC)) {
	foreach ($rr as $k => &$v) {
		switch ($k) {
			case 'title':
				$v = es(tq($v, 100));
				break;
			case 'url':
				$v = "<a href=\"{$v}\" target=\"_blank\">".es(tq($v, 200))."</a>";
				break;
			default:
				break;
		}
	}
	?>
				<tr>
					<td align="center"><?php print $i++; ?>.</td>
					<td align="center"><?php print $rr["title"]; ?></td>
					<td align="center"><?php print $rr["url"]; ?></td>
					<td align="center"><?php print $rr["datetime"]; ?></td>
	<?php
	$rr["authors"] = "";
	$st = $pdo->prepare("SELECT `author_name` FROM `authors` WHERE `news_id`=:news_id;");
	$st->execute([":news_id" => $rr["id"]]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$rr["authors"] .= $r[0].",";
	}
	?>
				<td align="center"><?php print es(trim($rr["authors"], ",")); ?></td>
				<td align="center"><?php print es(ucwords($rr["content_type"])); ?></td>
				<td align="center"><?php print es(tq($rr["content"], 200)); ?></td>
				<td align="center"><?php print es($rr["regional"]); ?></td>
				<td align="center"><?php print es($rr["scraped_at"]); ?></td>
				<td align="center"><a href="details.php?id=<?php print $rr["id"]; ?>" target="_blank"><button>Show Details</button></a></td>
	<?php

	// $st = $pdo->prepare("SELECT `category_name` FROM `categories` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $r["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_NUM)) {
	// }

	// $st = $pdo->prepare("SELECT `tag_name` FROM `tags` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $rr["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_NUM)) {
	// }

	// $st = $pdo->prepare("SELECT `author`,`content`,`datetime` FROM `comments` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $rr["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	// }

	// $st = $pdo->prepare("SELECT `image_url`,`description` FROM `images` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $rr["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	// }
	?>			</tr><?php
}
?>
		
			</tbody>
		</table>
	</center>
</body>
</html>