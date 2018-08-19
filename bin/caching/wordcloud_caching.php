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
	ORDER BY `count` DESC;
");
$st = $pdo->prepare(
	"INSERT INTO `title_wordcloud_regional_caching` (`regional`, `count`, `words`, `n`, `hash`, `created_at`)
VALUES (:regional, :count, :words, :n, :hash, :created_at);"
);

$stu = $pdo->prepare(
	"UPDATE `title_wordcloud_regional_caching` SET `updated_at` = :updated_at,
	`count`=:count WHERE `hash` = :hash LIMIT 1;
	;"
);

for ($i=1; $i <= 4; $i++) { 
	$wc->execute(
		[
			":n" => $i,
			":regional" => $argv[1]
		]
	);
	while ($r = $wc->fetch(PDO::FETCH_ASSOC)) {
		icelog("Inserting %s", json_encode($r));
		$hash = sha1($argv[1].$argv[2].$r["words"].$i)."_".md5($argv[1].$argv[2].$r["words"].$i);
		try {
			$st->execute(
				[
					":regional" => $argv[2],
					":count" => $r["count"],
					":words" => $r["words"],
					":n" => $i,
					":hash" => $hash,
					":created_at" => date("Y-m-d H:i:s")
				]
			);	
		} catch (PDOException $e) {
			icelog("Duplicate");
			icelog("Updating data where hash = :%s", $hash."...");
			$stu->execute(
				[
					":updated_at" => date("Y-m-d H:i:s"),
					":hash" => $hash,
					":count" => $r["count"]
				]
			);
		}
	}
}
unset($pdo, $st);