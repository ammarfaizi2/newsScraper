<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$regional_id = NULL;

if (isset($_GET["regional_id"])) {
    if (! is_string($_GET["regional_id"]) && ! is_numeric($_GET["regional_id"])) {
        exit(json_encode(
            [
                "status" => "error",
                "error_msg" => "regional_id must be a string or integer"
            ]
        ));
        $regional_id = $_GET["regional_id"];
    }
}

$start_date = time()-(3600*24*7);
$end_date   = time();

if (isset($_GET["start_date"])) {
    if (! is_string($_GET["start_date"])) {
        exit(json_encode([
            "status" => "error",
            "error_msg" => "Start date must be a string"
        ]));
    }
    $start_date = $_GET["start_date"];
    if (! is_numeric($_GET["start_date"])) {
        $start_date = strtotime($_GET["start_date"]);
    }
}

if (isset($_GET["end_date"])) {
    if (! is_string($_GET["end_date"])) {
        exit(json_encode([
            "status" => "error",
            "error_msg" => "End date must be a string"
        ]));
    }
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

$query = <<<QUERY
SELECT `tags`.`tag_name`, COUNT(1) AS `count`
FROM `tags`
INNER JOIN `news` ON `tags`.`news_id` = `news`.`id`
INNER JOIN `regional` ON `regional`.`regional` = `news`.`regional`
WHERE `news`.`datetime` >= :start_date AND `news`.`datetime` <= :end_date
QUERY;

if(!is_null($regional_id)) {
    $query .= "AND `regional`.`id` = :regional_id";
}

$query .= <<<QUERY
GROUP BY `tag_name`
ORDER BY `count` desc;
QUERY;

$st = $pdo->prepare($query);
$root = [];

$dataset = [];

$zeroDay = date("Y-m-d", $start_date);
$endDay = date("Y-m-d", $end_date);

$execute_param = [
    ":start_date" => $zeroDay,
    ":end_date" => $endDay,
];

if(!is_null($regional_id)) {
    $execute_param[":regional_id"] = $regional_id;
}

$wordcloud = "";

$st->execute($execute_param);
while($r = $st->fetch(PDO::FETCH_ASSOC)) {
    for($i = 0; $i < $r["count"]; $i++) {
        $wordcloud .= $r["tag_name"] . " ";
    }
}


print json_encode(
    [
        "status" => "ok",
        "result" => $wordcloud
    ]
);
