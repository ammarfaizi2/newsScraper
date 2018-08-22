<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

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

if ($end_date < $start_date) {
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript">
        alert("Start date cannot be higher than end date!");
        window.location = "?";
    </script>
</head>
<body>

</body>
</html>
<?php exit();
}

$pdo = DB::pdo();

$st = $pdo->prepare("SELECT COUNT(`news`.`url`) AS `count`,`news`.`regional`,`regional`.`id` FROM `news` INNER JOIN `regional` ON `regional`.`regional` = `news`.`regional` WHERE `news`.`datetime` >= :start_date AND `news`.`datetime` <= :end_date GROUP BY `regional` LIMIT 1;");

$dataset = [];
while ($start_date <= $end_date) {
    $zeroDay = strtotime(date("Y-m-d", $start_date)." 00:00:00");
    $st->execute(
        [
            ":start_date" => $zeroDay,
            ":end_date" => $zeroDay+(3600*24)
        ]
    );
    $r = $st->fetch(PDO::FETCH_NUM);
    $dataset[$r["regional"]] = [
        $r["count"]
    ]; 
}

var_dump($dataset);die;


?><!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script> -->
    <style type="text/css">
        * {
            font-family: Arial, Helvetica;
        }
    </style>
</head>
<body>
    <center>
        <div>
            <table>
                <tr>
                    <td>Start Date</td>
                    <td>:</td>
                    <td>
                        <select id="start_day">
                            <option></option>
                            <?php 
                                for ($i=1; $i <= 31; $i++) { 
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>"><?php print $i; ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select id="start_month">
                            <option></option>
                            <?php $t = 0;
                                for ($i=1; $i <= 12; $i++) { 
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>"><?php print date("F", strtotime(date("Y-m-d H:i:s", 0)."+".($i-1)." month")); ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select id="start_year">
                            <option></option>
                            <?php
                                $t = date("Y");
                                for ($i=$t; $i >= 2005; $i--) { 
                                    ?><option value="<?php print $i; ?>"><?php print $i; ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>:</td>
                    <td>
                        <select id="end_day">
                            <option></option>
                            <?php
                                $d = date("d");
                                for ($i=1; $i <= 31; $i++) { 
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print $i == $d ? "selected" : ""; ?>><?php print $i; ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select id="end_month">
                            <option></option>
                            <?php $t = 0;
                            $d = (int)date("m");
                                for ($i=1; $i <= 12; $i++) { 
                                    $mm = date("F", strtotime(date("Y-m-d H:i:s", 0)."+".($i-1)." month"));
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print $d == $i ? "selected"  :  "";?>><?php print $mm; ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select id="end_year">
                            <option></option>
                            <?php
                                $t = date("Y");
                                for ($i=$t; $i >= 2005; $i--) { 
                                    ?><option value="<?php print $i; ?>" <?php print $i == $t ? "selected" : ""; ?>><?php print $i; ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" align="center">
                        <button id="submit_me">Submit</button>
                    </td>
                </tr>
            </table>
            <h3>Current range:</h3>
        </div>
        <div style="width:80%;padding-top: 10px;">
            <canvas id="myChart"></canvas>
        </div>
    </center>
    <script>
    var ctx = document.getElementById("myChart").getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
                labels: <?php
                $a = [];
                $t = time()-(3600*24*14);
                for ($i=1; $i <= 14; $i++) { 
                    $a[] = date("d F Y", $t+(3600*24*$i));
                }
                print json_encode($a);
                ?>,
                datasets: [{
                    label: 'My First dataset',
                    backgroundColor: "black",
                    borderColor: "black",
                    data: [
                        
                    ],
                    fill: false,
                }, {
                    label: 'My Second dataset',
                    fill: false,
                    backgroundColor: "blue",
                    borderColor: "blue",
                    data: [
                        1
                    ],
                }]
            },
        options: {}
    });
    </script>
</body>
</html>