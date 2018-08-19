<?php

require __DIR__."/../../config/main.php";
require __DIR__."/../../config/scraper.php";
require __DIR__."/../../bootstrap/icetea_bootstrap.php";

if (! isset($argv[1])) {
	print "\$argv[1] is not defined!\n";
	exit(1);
}

if (! isset($argv[2])) {
	print "\$argv[2] is not defined!\n";
	exit(1);
}

$pdo = DB::pdo();
$wc = $pdo->prepare("SELECT COUNT(`words`) AS `count`,`words` FROM `title_wordcloud`
	INNER JOIN `news`
	ON `news`.`id` = `title_wordcloud`.`news_id`
	WHERE `title_wordcloud`.`n` = :n AND `news`.`regional` = :regional
	GROUP BY `words`
	ORDER BY `count`;
");
$st = $pdo->prepare(
	"INSERT INTO `title_wordcloud_regional_caching` (`regional`, `words`, `n`, `hash`, `created_at`)
VALUES (:regional, :words, :n, :hash, :created_at);"
);
for ($i=1; $i <= 4; $i++) { 
	$wc->execute(
		[
			":n" => $i,
			":regional" => $argv[1]
		]
	);
	while ($r = $wc->fetch(PDO::FETCH_ASSOC)) {
		icelog("%s", json_encode($r));
		// icelog("Inserting %s...", $r["words"]);
		// $st->execute(
		// 	[
		// 		":"
		// 	]
		// );
	}
}
unset($pdo, $st);