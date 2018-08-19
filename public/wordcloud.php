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
	$limit = 15;
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
		* {
			font-family: Arial;
		}
		.cage {
			border: 1px solid #000;
			width: 400px;
			height: 500px;
			overflow-y: scroll;
			overflow-x: scroll;
			display: inline-block;
		}
		table {
			border-collapse: collapse;
			width: 360px;
		}
		.pgg {
			font-size: 20px;
		}
		.rqq {
			color: blue;
			cursor: pointer;
		}
	</style>
</head>
<body>
	<center>
	<h2>Top <?php print $n !== 1 ? $n : ""; $ii = 1; ?> Word<?php print $n === 1 ? "" : "s"; ?> Frequency</h2>
	<h4 class="pgg">Change n value: &nbsp;<?php for ($i=1; $i <= 4; $i++) { 
		?><a class="rqq" href="?n=<?php print $i; ?>"><button class="rqq" <?php print $i === $n ? "disabled":"";?>><span class="pgg"><?php print $i ?></span></button></a>&nbsp;&nbsp;&nbsp;<?php
	} ?></h4>
	<?php $jj = 0; while ($r = $st->fetch(PDO::FETCH_NUM)): $wc->execute([":n" => $n, ":regional" => $r[1]]); ?>
		<div class="cage">
			<p><?php print $ii++; ?></p>
			<div>
				<h3><?php print $r[0]; ?></h3>
				<p>Regional Code: <?php print $r[1]; ?></p>
			</div>
			<table border="1">
				<thead>
						<tr><th align="center">No.</th><th align="center">Words</th><th align="center">Amount</th></tr>
				</thead>
				<tbody>
					<?php $i = 1; while ($rr = $wc->fetch(PDO::FETCH_ASSOC)): ?>
						<tr><td align="center"><?php print $i++; ?></td><td><?php print htmlspecialchars($rr["words"], ENT_QUOTES, "UTF-8"); ?></td><td><?php print $r["count"]; ?></td></tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	<?php endwhile; ?>
	</center>
</body>
</html><?php unset($pdo); ?>