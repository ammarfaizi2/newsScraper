<?php  

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

if (isset($_GET["limit"])) {
	if (!is_numeric($_GET["limit"])) {
?><!DOCTYPE html>
<html>
<head>
	<title>Error</title>
	<script type="text/javascript">
		alert("limit parameter must be numeric!");
		window.location = "?";
	</script>
</head>
<body>

</body>
</html><?php
		exit();
	}

	$limit = abs((int)$_GET["limit"]);
} else {
	$limit = 10;
}

if (isset($_GET["n"])) {
	if (!in_array($_GET["n"], ["1", "2", "3", "4"])) {
?><!DOCTYPE html>
<html>
<head>
	<title>Error</title>
	<script type="text/javascript">
		alert("n parameter must in range 1 - 4");
		window.location = "?";
	</script>
</head>
<body>

</body>
</html><?php 
		exit();
	}
	$n = (int) $_GET["n"];
} else {
	$n = 2;
}

$pdo = DB::pdo();
$st = $pdo->prepare("SELECT `regional`,`id` FROM `regional`;");
$st->execute();
$wc = $pdo->prepare("SELECT COUNT(`words`) AS `count`,`words` FROM `title_wordcloud`
	INNER JOIN `news`
	ON `news`.`id` = `title_wordcloud`.`news_id`
	WHERE `title_wordcloud`.`n` = :n AND `news`.`regional` = :regional
	GROUP BY `words`
	ORDER BY `count`
	DESC LIMIT {$limit};
");
?><!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<center>
		<?php
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$wc->execute([":n" => $n,":regional" => $r[0]]);
?><div>
	<h3><?php print $r[0]; ?> (regional code: <?php print $r[1]; ?>)</h3>
	<table border="1" style="border-collapse: collapse;">
		<tr><td>No.</td><td>Words</td><td>Amount</td></tr>
<?php $i = 1;
				while ($rr = $wc->fetch(PDO::FETCH_ASSOC)) {
?><tr><td><?php print $i++; ?>.</td><td><?php print htmlspecialchars($rr["words"]); ?></td><td><?php print $rr["count"] ?></td></tr>
<?php
				}
?>	</table>
</div><?php
			}
		?>
	</center>
</body>
</html><?php unset($pdo); ?>