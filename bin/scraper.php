#!/usr/bin/env php
<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

use Phx\NewsScraper;
use Phx\Scrapers\Jpnn;
use Phx\Scrapers\Viva;
use Phx\Scrapers\Suara;
use Phx\Scrapers\Detik;
use Phx\Scrapers\Gonews;
use Phx\Scrapers\Kompas;
use Phx\Scrapers\Liputan6;
use Phx\Scrapers\Tribunnews;
use Phx\Scrapers\Antaranews;
use Phx\Scrapers\Kabardaerah;
use Phx\Scrapers\Indonesiatimur;
use Phx\Scrapers\Sindonews;
use Phx\Scrapers\Sumutpos;
use Phx\Scrapers\Kabarmedan;
use Phx\Scrapers\Medansatu;
use Phx\Scrapers\Kabarsumut;
use Phx\Scrapers\Goaceh;
use Phx\Scrapers\Prohaba;
use Phx\Scrapers\Modusaceh;
use Phx\Scrapers\Ajnn;
use Phx\Scrapers\Matatelinga;
use Phx\Scrapers\Portalsatu;
use Phx\Scrapers\Banteninfo;
use Phx\Scrapers\Beritasumut;
use Phx\Scrapers\Gosumut;
use Phx\Scrapers\Waspada;
use Phx\Scrapers\Hargasumut;
use Phx\Scrapers\Kabarsumbar;
use Phx\Scrapers\Hariansinggalang;
use Phx\Scrapers\Minangkabaunews;
use Phx\Scrapers\Kabarpadang;
use Phx\Scrapers\Gosumbar;
use Phx\Scrapers\Harianhaluan;
use Phx\Scrapers\Infosumbar;
use Phx\Scrapers\Beritasumbar;
use Phx\Scrapers\Redaksisumbar;
use Phx\Scrapers\Riauterkini;
use Phx\Scrapers\Goriau;
use Phx\Scrapers\Riaugreen;
use Phx\Scrapers\Beritariau;
use Phx\Scrapers\Riausky;
use Phx\Scrapers\Inforiauco;
use Phx\Scrapers\Infoinhil;
use Phx\Scrapers\Sijorikepri;
use Phx\Scrapers\Lendoot;
use Phx\Scrapers\Tanjungpinangpos;
use Phx\Scrapers\Batamtoday;
use Phx\Scrapers\Batampos;
use Phx\Scrapers\Lintaskepri;
use Phx\Scrapers\Visitkepri;
use Phx\Scrapers\Kepricoid;
use Phx\Scrapers\Infokepri;
use Phx\Scrapers\Keprionline;
use Phx\Scrapers\Kepriinfo;
use Phx\Scrapers\Infojambi;
use Phx\Scrapers\Jambiupdate;
use Phx\Scrapers\Metrojambi;
use Phx\Scrapers\Jambiindependent;
use Phx\Scrapers\Fokusjambi;
use Phx\Scrapers\Jambiekspres;
use Phx\Scrapers\Beritajambi;
use Phx\Scrapers\Detiksumsel;
use Phx\Scrapers\Palembangpos;
use Phx\Scrapers\Rmolsumsel;
use Phx\Scrapers\Beritapagi;
use Phx\Scrapers\Kabarsumatera;
use Phx\Scrapers\Inradiofm;
use Phx\Scrapers\Kabarbangka;
use Phx\Scrapers\Radarbangka;
use Phx\Scrapers\Rakyatpos;
use Phx\Scrapers\Kabarbabel;
use Phx\Scrapers\Reportasebangka;
use Phx\Scrapers\Beritabangka;
use Phx\Scrapers\Beritababel;
use Phx\Scrapers\Rmolbabel;
use Phx\Scrapers\Rbtv;
use Phx\Scrapers\Harianrakyatbengkulu;
use Phx\Scrapers\Radarbengkuluonline;
use Phx\Scrapers\Liputanbengkulu;
use Phx\Scrapers\Bengkuluekspress;
use Phx\Scrapers\Bengkulutoday;
use Phx\Scrapers\Rmolbengkulu;
use Phx\Scrapers\Pedomanbengkulu;
use Phx\Scrapers\Radarlampung;
use Phx\Scrapers\Infolampung;
use Phx\Scrapers\Lampost;
use Phx\Scrapers\Kupastuntas;
use Phx\Scrapers\Infojakarta;
use Phx\Scrapers\Beritajakarta;
use Phx\Scrapers\Jakartakita;
use Phx\Scrapers\Kabarjakarta;
use Phx\Scrapers\Aktual;
use Phx\Scrapers\Pikiranrakyat;
use Phx\Scrapers\Bandungberita;
use Phx\Scrapers\Republika;
use Phx\Scrapers\Kabarjabar;
use Phx\Scrapers\Postkeadilan;
use Phx\Scrapers\Jabarekspres;
use Phx\Scrapers\Jabarnews;
use Phx\Scrapers\Radarbanten;
use Phx\Scrapers\Kabarbanten;
use Phx\Scrapers\Infobanten;
use Phx\Scrapers\Bantennews;
use Phx\Scrapers\Kabartangsel;
use Phx\Scrapers\Beritajateng;
use Phx\Scrapers\Metrojateng;
use Phx\Scrapers\Kabarjateng;
use Phx\Scrapers\Jogja;
use Phx\Scrapers\Kotajogja;
use Phx\Scrapers\Infojogja;
use Phx\Scrapers\Krjogja;
use Phx\Scrapers\Beritajatim;
use Phx\Scrapers\Infojatim;
use Phx\Scrapers\Kabarjatim;
use Phx\Scrapers\Bisnis;
use Phx\Scrapers\Kabarjawatimur;
use Phx\Scrapers\Beritabali;
use Phx\Scrapers\Balipost;
use Phx\Scrapers\Nusabali;
use Phx\Scrapers\Suarantb;
use Phx\Scrapers\Mataramnews;
use Phx\Scrapers\Wartantb;
use Phx\Scrapers\Hariannusa;
use Phx\Scrapers\Lombokita;
use Phx\Scrapers\Voxntt;
use Phx\Scrapers\Kalbaronline;
use Phx\Scrapers\Kilaskalbar;
use Phx\Scrapers\Rmolkalbar;
use Phx\Scrapers\Prokal;
use Phx\Scrapers\Borneonews;

if (! isset($argv[1])) {
	print "\$argv[1] is not defined!\n";
	exit(1);
}

$noend = false;
if (isset($argv[2])) {
	if ($argv[2] === "--while-true") {
		$noend = true;
	} else {
		print "Invalid parameter: \"{$argv[2]}\"\n";
		exit(1);
	}
}

switch ($argv[1]) {
	case 'suara':
		$st = new Suara;
		#define("LOG_FILE", "suara.log");
		break;
		
	case 'detik':
		$st = new Detik;
		#define("LOG_FILE", "detik.log");
		break;

	case 'liputan6':
		$st = new Liputan6;
		#define("LOG_FILE", "liputan6.log");
		break;
	
	case 'tribunnews':
		$st = new Tribunnews;
		#define("LOG_FILE", "tribunnews.log");
		break;

	case 'kompas':
		$st = new Kompas;
		#define("LOG_FILE", "kompas.log");
		break;

	case 'antaranews':
		$st = new Antaranews;
		#define("LOG_FILE", "antaranews.log");
		break;

	case 'viva':
		$st = new Viva;
		#define("LOG_FILE", "viva.log");
		break;

	case 'kabardaerah':
		$st = new Kabardaerah;
		#define("LOG_FILE", "kabardaerah.log");
		break;

	case 'jpnn':
		$st = new Jpnn;
		#define("LOG_FILE", "jpnn.log");
		break;

	case 'indonesiatimur':
		$st = new Indonesiatimur;
		#define("LOG_FILE", "indonesiatimur.log");
		break;

	case 'sindonews':
		$st = new Sindonews;
		#define("LOG_FILE", "sindonews.log");
		break;

	case 'gonews':
		$st = new Gonews;
		#define("LOG_FILE", "gonews.log");
		break;

	case 'sumutpos':
		$st = new Sumutpos;
		break;

	case 'kabarmedan':
		$st = new Kabarmedan;
		break;

	case 'medansatu':
		$st = new Medansatu;
		break;

	case 'kabarsumut':
		$st = new Kabarsumut;
		break;

	case 'goaceh':
		$st = new Goaceh;
		break;

	case 'prohaba':
		$st = new Prohaba;
		break;

	case 'modusaceh':
		$st = new Modusaceh;
		break;
	
	case 'ajnn':
		$st = new Ajnn;
		break;

	case 'portalsatu':
		$st = new Portalsatu;
		break;

	case 'gonews':
		$st = new Gonews;
		#define("LOG_FILE", "gonews.log");
		break;

	case 'banteninfo':
		$st = new Banteninfo;
		#define("LOG_FILE", "bateninfo.log");
		break;
	
	case 'matatelinga':
		$st = new Matatelinga;
		break;

	case 'beritasumut':
		$st = new Beritasumut;
		break;

	case 'gosumut':
		$st = new Gosumut;
		break;

	case 'waspada':
		$st = new Waspada;
		break;

	case 'hargasumut':
		$st = new Hargasumut;
		break;
		
	case 'kabarsumbar':
		$st = new Kabarsumbar;
		break;

	case 'hariansinggalang':
		$st = new Hariansinggalang;
		break;

	case 'minangkabaunews':
		$st = new Minangkabaunews;
		break;

	case 'kabarpadang':
		$st = new Kabarpadang;
		break;

	case 'gosumbar':
		$st = new Gosumbar;
		break;

	case 'harianhaluan':
		$st = new Harianhaluan;
		break;

	case 'infosumbar':
		$st = new Infosumbar;
		break;

	case 'beritasumbar':
		$st = new Beritasumbar;
		break;

	case 'redaksisumbar':
		$st = new Redaksisumbar;
		break;

	case 'riauterkini':
		$st = new Riauterkini;
		break;

	case 'goriau':
		$st = new Goriau;
		break;

	case 'riaugreen':
		$st = new Riaugreen;
		break;

	case 'beritariau':
		$st = new Beritariau;
		break;

	case 'riausky':
		$st = new Riausky;
		break;

	case 'inforiauco':
		$st = new Inforiauco;
		break;

	case 'infoinhil':
		$st = new Infoinhil;
		break;

	case 'sijorikepri':
		$st = new Sijorikepri;
		break;

	case 'lendoot':
		$st = new Lendoot;
		break;

	case 'tanjungpinangpos':
		$st = new Tanjungpinangpos;
		break;

	case 'batamtoday':
		$st = new Batamtoday;
		break;

	case 'batampos':
		$st = new Batampos;
		break;

	case 'lintaskepri':
		$st = new Lintaskepri;
		break;

	case 'visitkepri':
		$st = new Visitkepri;
		break;

	case 'kepricoid':
		$st = new Kepricoid;
		break;

	case 'infokepri':
		$st = new Infokepri;
		break;

	case 'keprionline':
		$st = new Keprionline;
		break;

	case 'kepriinfo':
		$st = new Kepriinfo;
		break;

	case 'infojambi':
		$st = new Infojambi;
		break;

	case 'jambiupdate':
		$st = new Jambiupdate;
		break;

	case 'metrojambi':
		$st = new Metrojambi;
		break;

	case 'jambiindependent':
		$st = new Jambiindependent;
		break;

	case 'fokusjambi':
		$st = new Fokusjambi;
		break;

	case 'jambiekspres':
		$st = new Jambiekspres;
		break;

	case 'beritajambi':
		$st = new Beritajambi;
		break;

	case 'detiksumsel':
		$st = new Detiksumsel;
		break;

	case 'palembangpos':
		$st = new Palembangpos;
		break;

	case 'rmolsumsel':
		$st = new Rmolsumsel;
		break;

	case 'beritapagi':
		$st = new Beritapagi;
		break;

	case 'kabarsumatera':
		$st = new Kabarsumatera;
		break;

	case 'inradiofm':
		$st = new Inradiofm;
		break;

	case 'kabarbangka':
		$st = new Kabarbangka;
		break;

	case 'radarbangka':
		$st = new Radarbangka;
		break;

	case 'rakyatpos':
		$st = new Rakyatpos;
		break;

	case 'kabarbabel':
		$st = new Kabarbabel;
		break;

	case 'reportasebangka':
		$st = new Reportasebangka;
		break;

	case 'beritabangka':
		$st = new Beritabangka;
		break;

	case 'beritababel':
		$st = new Beritababel;
		break;

	case 'rmolbabel':
		$st = new Rmolbabel;
		break;

	case 'rbtv':
		$st = new Rbtv;
		break;
	
	case 'harianrakyatbengkulu':
		$st = new Harianrakyatbengkulu;
		break;
	
	case 'radarbengkuluonline':
		$st = new Radarbengkuluonline;
		break;

	case 'liputanbengkulu':
		$st = new Liputanbengkulu;
		break;

	case 'bengkuluekspress':
		$st = new Bengkuluekspress;
		break;

	case 'bengkulutoday':
		$st = new Bengkulutoday;
		break;

	case 'rmolbengkulu':
		$st = new Rmolbengkulu;
		break;

	case 'pedomanbengkulu':
		$st = new Pedomanbengkulu;
		break;

	case 'radarlampung':
		$st = new Radarlampung;
		break;

	case 'infolampung':
		$st = new Infolampung;
		break;

	case 'lampost':
		$st = new Lampost;
		break;

	case 'kupastuntas':
		$st = new Kupastuntas;
		break;

	case 'infojakarta':
		$st = new Infojakarta;
		break;

	case 'beritajakarta':
		$st = new Beritajakarta;
		break;

	case 'jakartakita':
		$st = new Jakartakita;
		break;

	case 'kabarjakarta':
		$st = new Kabarjakarta;
		break;

	case 'aktual':
		$st = new Aktual;
		break;

	case 'pikiranrakyat':
		$st = new Pikiranrakyat;
		break;

	case 'bandungberita':
		$st = new Bandungberita;
		break;

	case 'republika':
		$st = new Republika;
		break;

	case 'kabarjabar':
		$st = new Kabarjabar;
		break;

	case 'postkeadilan':
		$st = new Postkeadilan;
		break;

	case 'jabarekspres':
		$st = new Jabarekspres;
		break;
		
	case 'jabarnews':
		$st = new Jabarnews;
		break;

	case 'radarbanten':
		$st = new Radarbanten;
		break;

	case 'kabarbanten':
		$st = new Kabarbanten;
		break;

	case 'infobanten':
		$st = new Infobanten;
		break;

	case 'bantennews':
		$st = new Bantennews;
		break;

	case 'kabartangsel':
		$st = new Kabartangsel;
		break;

	case 'beritajateng':
		$st = new Beritajateng;
		break;

	case 'metrojateng':
		$st = new Metrojateng;
		break;

	case 'kabarjateng':
		$st = new Kabarjateng;
		break;

	case 'jogja':
		$st = new Jogja;
		break;

	case 'kotajogja':
		$st = new Kotajogja;
		break;
		
	case 'infojogja':
		$st = new Infojogja;
		break;

	case 'krjogja':
		$st = new Krjogja;
		break;

	case 'beritajatim':
		$st = new Beritajatim;
		break;

	case 'infojatim':
		$st = new Infojatim;
		break;

	case 'kabarjatim':
		$st = new Kabarjatim;
		break;

	case 'bisnis':
		$st = new Bisnis;
		break;

	case 'kabarjawatimur':
		$st = new Kabarjawatimur;
		break;

	case 'beritabali':
		$st = new Beritabali;
		break;

	case 'balipost':
		$st = new Balipost;
		break;

	case 'nusabali':
		$st = new Nusabali;
		break;

	case 'suarantb':
		$st = new Suarantb;
		break;

	case 'mataramnews':
		$st = new Mataramnews;
		break;

	case 'wartantb':
		$st = new Wartantb;
		break;

	case 'hariannusa':
		$st = new Hariannusa;
		break;

	case 'lombokita':
		$st = new Lombokita;
		break;

	case 'voxntt':
		$st = new Voxntt;
		break;	

	case 'kalbaronline':
		$st = new Kalbaronline;
		break;	

	case 'kilaskalbar':
		$st = new Kilaskalbar;
		break;	

	case 'rmolkalbar':
		$st = new Rmolkalbar;
		break;	

	case 'prokal':
		$st = new Prokal;
		break;	

	case 'borneonews':
		$st = new Borneonews;
		break;	

	default:
		print "Invalid argument \"{$argv[1]}\"";
		exit(1);
		break;
}

if (!($st instanceof NewsScraper)) {
	print "Error: ".get_class($st)." is not instanceof ".NewsScraper::class."!\n";
	exit(1);
}

do {
	$st->run();
	$st->getData();
	if ($noend) {
		icelog("Sleeping 500 seconds...");
		sleep(500);
	}
} while ($noend);
