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

$startdd = $start_date;
$enddd = $end_date;

$end_date += (3600*24);

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

$st = $pdo->prepare("SELECT COUNT(`news`.`url`) AS `count`,`news`.`regional`,`regional`.`id` FROM `news` INNER JOIN `regional` ON `regional`.`regional` = `news`.`regional` WHERE `news`.`datetime` >= :start_date AND `news`.`datetime` <= :end_date GROUP BY `regional`;");

$labels = [];
$dataset = [];
while ($start_date <= $end_date) {
    $zeroDay = strtotime(date("Y-m-d", $start_date)." 00:00:00");
    $labels[] = date("d F Y", $zeroDay);
    $st->execute(
        [
            ":start_date" => $zeroDay,
            ":end_date" => $zeroDay+(3600*24)
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
/*{
label: 'My First dataset',
backgroundColor: "black",
borderColor: "black",
data: [
    
],
fill: false,*/

?><!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
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
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print isset($_GET["start_date"]) && ($i < 10 ? "0".$i : $i) == date("d", $startdd) ? "selected" : ""; ?>><?php print $i; ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select id="start_month">
                            <option></option>
                            <?php $t = 0;
                                for ($i=1; $i <= 12; $i++) { 
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print isset($_GET["start_date"]) && ($i < 10 ? "0".$i : $i) == date("m", $startdd) ? "selected" : ""; ?>><?php print date("F", strtotime(date("Y-m-d H:i:s", 0)."+".($i-1)." month")); ?></option><?php
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
                                    ?><option value="<?php print $i; ?>" <?php print isset($_GET["start_date"]) && $i == date("Y", $startdd) ? "selected" : ""; ?>><?php print $i; ?></option><?php
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
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print isset($_GET["end_date"]) && ($i < 10 ? "0".$i : $i) == date("d", $enddd) ? "selected" : ( $i == $d && !isset($_GET["end_date"])? "selected" : ""); ?>><?php print $i; ?></option><?php
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
                                    ?><option value="<?php print $i < 10 ? "0".$i : $i; ?>" <?php print isset($_GET["end_date"]) && ($i < 10 ? "0".$i : $i) == date("m", $enddd) ? "selected" : ( $d == $i && !isset($_GET["end_date"]) ? "selected"  :  "");?>><?php print $mm; ?></option><?php
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
                                    ?><option value="<?php print $i; ?>" <?php print isset($_GET["end_date"]) && $i == date("Y", $enddd) ? "selected" : ($i == $t && !isset($_GET["end_date"])? "selected" : "") ?>><?php print $i; ?></option><?php
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" align="center">
                        <button id="submit_me" style="cursor:pointer;">Submit</button>
                    </td>
                </tr>
            </table>
            <h3>Current range:</h3>
            <h4><?php print date("d F Y", $startdd); ?> to <?php print date("d F Y", $enddd); ?></h4>
        </div>
        <div style="width:100%;padding-top: 10px;margin-bottom: 50px;">
            <canvas id="myChart"></canvas>
        </div>
    </center>
    <script>
    var ctx = document.getElementById("myChart").getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
                labels: <?php print json_encode($labels); ?>,
                datasets: <?php print json_encode($datasets); ?>
            },
        options: {}
    });
    $("#submit_me")[0].addEventListener("click", function () {
            var param = "",
                paramOk = 1,
                d = $("#start_day").val(),
                m = $("#start_month").val(),
                y = $("#start_year").val();

            if (d != "" || m != "" || y != "") {
                if (d == "") {
                    paramOk = false;
                    paramError = "You need to complete the start date or left them blank!";
                }
                if (m == "") {
                    paramOk = false;
                    paramError = "You need to complete the start date or left them blank!";
                }
                if (y == "") {
                    paramOk = false;
                    paramError = "You need to complete the start date or left them blank!";
                }
                param += "start_date="+(encodeURIComponent(
                    y+"-"+m+"-"+d+" 00:00:00"
                ))+"&";
            }

            var d = $("#end_day").val(),
                m = $("#end_month").val(),
                y = $("#end_year").val();
                console.log(d,m,y);
            if (d != "" || m != "" || y != "") {
                if (d == "") {
                    paramOk = false;
                    paramError = "You need to complete the end date or left them blank!";
                }
                if (m == "") {
                    paramOk = false;
                    paramError = "You need to complete the end date or left them blank!";
                }
                if (y == "") {
                    paramOk = false;
                    paramError = "You need to complete the end date or left them blank!";
                }
                param += "end_date="+(encodeURIComponent(
                    y+"-"+m+"-"+d+" 00:00:00"
                ))+"&";
            }

            if (paramOk) {
                window.location = "?"+param;
            } else {
                alert(paramError)
            }
    });
    </script>
</body>
</html>
