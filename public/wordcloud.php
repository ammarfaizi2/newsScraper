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
$pdo->exec("set sql_mode=''");
$st = $pdo->prepare("SELECT `regional`,`id` FROM `regional`;");
$st->execute();
$wc = $pdo->prepare("SELECT `a`.`count`,`a`.`words` FROM `title_wordcloud_regional_caching` AS `a`
	INNER JOIN `regional`
	ON `regional`.`id` = `a`.`regional`
	WHERE `a`.`n` = :n AND `regional`.`regional` = :regional
	GROUP BY `words`
	ORDER BY `count`
	DESC LIMIT {$limit};
");
?><!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		.x {
			display: inline-block;
		}
	</style>
</head>
<body>
	<center>
		<?php
			while ($r = $st->fetch(PDO::FETCH_NUM)) {
				$wc->execute([":n" => $n,":regional" => $r[0]]);
?><div class="x">
	<div><h3><?php print $r[0]; ?></h3></div>
	<div><h4>(regional code: <?php print $r[1]; ?>)</h4></div>
	<table border="1" style="border-collapse: collapse;">
		<tr><td align="center">No.</td><td align="center">Words</td><td align="center">Amount</td></tr>
<?php $i = 1;
				while ($rr = $wc->fetch(PDO::FETCH_ASSOC)) {
?><tr><td align="center"><?php print $i++; ?>.</td><td align="center"><?php print htmlspecialchars($rr["words"]); ?></td><td align="center"><?php print $rr["count"] ?></td></tr>
<?php
				}
?>	</table>
</div><?php
			}
		?>
	</center>
</body>
</html><?php unset($pdo); ?>