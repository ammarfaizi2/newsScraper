<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

?><!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		* {
			font-family: Arial;
		}
		.dd {
			display: inline-block;
			border: 1px solid #000;
			padding: 10px;
		}
		th {
			padding: 5px;
		}
		td {
			padding: 2px;
		}
		table {
			border-collapse:collapse;
			width: 400px;
		}
	</style>
	<title>News Scraper</title>
</head>
<body>
	<center>
		<h1>News Scraper Data Statistics</h1>
		<div class="dd">
			<h2>Regional Statistic</h2>
			<table border="1">
				<thead>
					<tr>
						<th>No.</th><th>Regional</th><th>Amount of Data</th>
					</tr>
				</thead>
				<tbody>
<?php 

$pdo = DB::pdo();
$st = $pdo->prepare(
	"SELECT `regional`,COUNT(`regional`) AS `total` FROM `news` GROUP BY `regional` ORDER BY `total` DESC;"
);
$st->execute();
$i = 1;
$total = 0;
while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	$total += $r["total"];
	?>
					<tr><td align="center"><?php print $i++; ?>.</td><td align="center"><?php print $r["regional"]; ?></td><td align="center"><?php print $r["total"]; ?></td></tr>
	<?php
}
?>
			</tbody>
			<tfoot>
				<tr><td colspan="2" align="center">Total Data</td><td align="center"><?php print $total; ?></td></tr>
			</tfoot>
			</table>
		</div>
		<div class="dd">
			<h2>Sites Statistic</h2>
			<table border="1">
				<thead>
					<tr>
						<th>No.</th><th>Regional</th><th>Amount of Data</th>
					</tr>
				</thead>
				<tbody>
<?php

$sites = [
	"Tribunnews" => "tribunnews",
	"Liputan6" => "liputan6"
];
$i = 1;
$total = 0;
foreach ($sites as $key => $site) {
	$pdo = DB::pdo();
	$st = $pdo->prepare(
		"SELECT COUNT(`url`) AS `total` FROM `news` WHERE `url` LIKE :site ORDER BY `total` DESC;"
	);
	$st->execute([":site" => "%".$site."%"]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$total += $r[0];
		?>
					<tr><td align="center"><?php print $i++; ?>.</td><td align="center"><?php print $key; ?></td><td align="center"><?php print $r[0]; ?></td></tr>
<?php
	}
}
?>
			</tbody>
			<tfoot>
				<tr><td colspan="2" align="center">Total Data</td><td align="center"><?php print $total; ?></td></tr>
			</tfoot>
			</table>
		</div>
	</center>
</body>
</html>