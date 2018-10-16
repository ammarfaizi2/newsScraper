#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";

function reg(&$a, Closure $c) {
	$a[] = $c;
}

$whileTrue = true;
$param = [
	"suara",
	"liputan6",
	"tribunnews",
	"detik",
	"kompas",
	"antaranews",
	"viva",
	"jpnn",
	"kabardaerah",
	"indonesiatimur",
	"sindonews",
	"sumutpos",
	"kabarmedan",
	"medansatu",
	"kabarsumut",
	"goaceh",
	"prohaba",
	"modusaceh",
	"ajnn",
	"portalsatu",
	"gonews",
	"matatelinga",
	"beritasumut",
	"gosumut",
	"waspada",
	"hargasumut",
	"kabarsumbar",
	"hariansinggalang",
	"minangkabaunews",
	"kabarpadang",
	"gosumbar",
	"harianhaluan",
	"infosumbar",
	"beritasumbar",
	"redaksisumbar",
	"riauterkini",
	"goriau",
	"riaugreen",
	"beritariau",
	"riausky",
	"inforiauco",
	"infoinhil",
	"sijorikepri",
	"lendoot",
	"tanjungpinangpos",
	"batamtoday",
	"batampos",
	"lintaskepri",
	"visitkepri",
	"kepricoid",
	"infokepri",
	"keprionline",
	"kepriinfo",
	"infojambi",
	"jambiupdate",
	"metrojambi",
	"jambiindependent",
	"fokusjambi",
	"jambiekspres",
	"beritajambi",
	"detiksumsel",
	"palembangpos",
	"rmolsumsel",
	"beritapagi",
	"kabarsumatera",
	"inradiofm",
	"kabarbangka",
	"radarbangka",
	"rakyatpos",
	"kabarbabel",
	"reportasebangka",
	"beritabangka",
	"beritababel",
	"rmolbabel",
	"rbtv",
	"harianrakyatbengkulu",
	"radarbengkuluonline",
	"liputanbengkulu",
	"bengkuluekspress",
	"bengkulutoday",
	"rmolbengkulu",
	"pedomanbengkulu",
	"radarlampung",
	"infolampung",
	"lampost",
	"kupastuntas",
	"infojakarta",
	"beritajakarta",
	"jakartakita",
	"kabarjakarta",
	"aktual",
	"pikiranrakyat",
	"bandungberita",
	"republika",
	"kabarjabar",
	"postkeadilan",
	"jabarekspres",
	"jabarnews",
	"radarbanten",
	"kabarbanten",
	"infobanten",
	"banteninfo",
	"bantennews",
	"kabartangsel",
	"beritajateng",
	"metrojateng",
	"kabarjateng",
	"jogja",
	"kotajogja",
	"infojogja",
	"krjogja",
	"beritajatim",
	"infojatim",
	"kabarjatim",
	"bisnis",
	"kabarjawatimur",
	"beritabali",
	"balipost",
	"nusabali",
	"suarantb",
	"mataramnews",
	"wartantb",
	"hariannusa",
	"lombokita",
	"voxntt",
	"kalbaronline",
	"kilaskalbar",
	"rmolkalbar",
	"prokal",
	"borneonews",
	"beritasampit",
	"kaltengnews",
	"beritakalteng",
	"gerakkalteng",
	"kliksamarinda",
	"korankaltim",
	"beritamanado",
	"sulutdaily",
	"manadoline",
	"suaramanado",
	"rmol",
	"sulutonline",
	"cybersulutnews",
	"pilarsulut",
	"sulutpost",
	"fajar",
	"suryatopnews",
	"fajaronline",
	"sulbarkita",
	"jurnalsulbar",
	"mediasulbar",
	"radarsultengonline",
	"kabarsulteng",
	"tempo",
	"satusulteng",
	"beritasulteng",
	"zonasultra",
	"sultrakini",
	"kabarmakassar",
	"terkini",
	"beritasulsel",
	"okezone",
	"merdeka",
	"radargorontalo",
	"mimoza",
	"vifapost",
	"tribunmaluku",
	"rakyatmaluku",
	"detikmaluku",
	"tajuktimur",
	"terasmaluku",
	"beritamalutco",
	"beritamalutcom",
	"beritamalukuonline",
	"deliknews",
	"indonesiaberita",
	"beritapapua",
];


$noend = "";
if ($whileTrue) {
	$noend = "--while-true";
}

$a = [];

foreach ($param as $key => $value) {
	reg($a, function() use ($value, $noend) {
		cli_set_process_title("icetea_worker --module=icetea_scraper.so --target=$value");
		shell_exec(
			"nohup ".PHP_BINARY." ".__DIR__."/scraper.php {$value} {$noend} >> ".LOG_DIR."/{$value}.log 2>&1"
		);
	});
}

foreach ($a as $v) {
	if (!isset($pid) || $pid !== 0) {
		$pid = pcntl_fork();
	}
	if (!$pid) {
		while(true) {
			$v();
		}
	}
}

while (true) {
	sleep(1000);
}
