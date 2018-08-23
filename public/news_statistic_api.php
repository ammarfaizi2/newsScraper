<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

if (! isset($_GET["regional_id"])) {
    exit(
        [
            "status" => "error",
            "error_msg" => "regional_id must be provided!"
    ]);
}

if (! is_string($_GET["regional_id"]) && ! is_numeric($_GET["regional_id"])) {
    exit(
        [
            "status" => "error",
            "error_msg" => "regional_id must be a string or integer"
        ]
    );
}

$regional_id = $_GET["regional_id"];


if (! is_string($_GET["start_date"])) {
    exit([
        "status" => "error",
        "error_msg" => "Start date must be a string"
    ]);
}

if (! is_string($_GET["end_date"])) {
    exit([
        "status" => "error",
        "error_msg" => "End date must be a string"
    ]);
}

$start_date = time()-(3600*24*7);
$end_date   = time();

if (isset($_GET["start_date"])) {
    $start_date = $_GET["start_date"];
    if (! is_numeric($_GET["start_date"])) {
        $start_date = strtotime($_GET["start_date"]);
    }
}

if (isset($_GET["end_date"])) {
    $end_date = $_GET["end_date"];
    if (! is_numeric($_GET["end_date"])) {
        $end_date = strtotime($_GET["end_date"]);
    }
}

$startdd = $start_date;
$enddd = $end_date;

$end_date += (3600*24);

if ($end_date < $start_date) {

    exit(json_encode(
    [
        "status" => "error",
        "error_msg" => "Start date cannot be higher than end date"
    ]));

}

$pdo = DB::pdo();

$st = $pdo->prepare("SELECT COUNT(`news`.`url`) AS `count`,`news`.`regional`,`regional`.`id` FROM `news` INNER JOIN `regional` ON `regional`.`regional` = `news`.`regional` WHERE `news`.`datetime` >= :start_date AND `news`.`datetime` <= :end_date AND `regional`.`id` = :regional_id GROUP BY `regional`;");
$labels = [];
$dataset = [];

while ($start_date <= $end_date) {
    $zeroDay = strtotime(date("Y-m-d", $start_date)." 00:00:00");
    $labels[] = date("d F Y", $zeroDay);
    $st->execute(
        [
            ":start_date" => $zeroDay,
            ":end_date" => $zeroDay+(3600*24),
            ":regional_id" => $regional_id
        ]
    );
    while($r = $st->fetch(PDO::FETCH_ASSOC)) {
        $dataset[$r["regional"]][] = $r["count"];
    }
    $start_date+=3600*24+1;
}
//$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
$colors = json_decode('{"Bali":"#2ee63d","Bangka Belitung":"#0c1df7","Banten":"#529fd5","Bengkulu":"#7df6f2","Daerah Istimewa Yogyakarta":"#e8260e","Gorontalo":"#107d23","Jakarta":"#2fe3a2","Jawa Barat":"#af27f2","Jawa Tengah":"#ba571f","Jawa Timur":"#30aa84","Kalimantan Barat":"#7d5b53","Kalimantan Selatan":"#13b3d5","Kalimantan Tengah":"#909b76","Kalimantan Timur":"#c4e226","Kalimantan Utara":"#bc64af","Kepulauan Riau":"#e1dec5","Lampung":"#099842","Maluku":"#861455","Nanggroe Aceh Darussalam":"#699ab7","Nusa Tenggara Barat":"#7860bf","Nusa Tenggara Timur":"#74699c","Papua":"#88fb44","Papua Barat":"#99d2bd","Sulawesi Barat":"#272beb","Sulawesi Selatan":"#60b8ae","Sulawesi Tengah":"#86814d","Sulawesi Tenggara":"#29c070","Sulawesi Utara":"#10828f","Sumatera Barat":"#f54f1f","Sumatera Selatan":"#c1f952","Sumatera Utara":"#4ec41e","Maluku Utara":"#fe0a4f","Riau":"#26b9cb","Jambi":"#f845d1"}', true);
$datasets = [];
foreach ($dataset as $key => $value) {
    $datasets[] = [
        "label" => $key,
        "backgroundColor" => $colors[$key],
        "borderColor" => $colors[$key],
        "data" => $value,
        "fill" => false
    ];
}


print json_encode(
    [
        "status" => "ok",
        "message" => [
            "labels" => $labels,
            "datasets" => $datasets
        ]
    ]
);
