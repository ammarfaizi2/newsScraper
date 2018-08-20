<?php

$page = isset($_GET["page"]) ? $_GET["page"] : 1;
if (! is_numeric($page)) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Error!</title>
	<script type="text/javascript">alert("Page parameter must be numeric!");window.location="?";</script>
</head>
<body>
</body>
</html><?php exit();
}

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

$page = (int) $page;

$queryString = ""; $qqqq=false;
foreach ($_GET as $key => $value) {
	if ($value !== "" && $key !== "page") {
		$qqqq = true;
		$queryString .= "&".urlencode($key)."=".urlencode($value);
	}
}
$queryString = trim($queryString, "&");
if ($qqqq) {
	$queryString = "&".$queryString;
}


function tq($str, $max = 32)
{
	if (strlen($str) > $max) {
		return substr($str, 0, $max)."...";
	}
	return $str;
}

function es($str)
{
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

function gptz($n)
{
	switch ($n) {
		case 1:
			return "=";
		case 2:
			return "!=";
		case 3:
			return ">";
		case 4:
			return "<";
		case 5:
			return ">=";
		case 6:
			return "<=";
		case 7:
		case 8:
		case 9:
		case 10;
			return "LIKE";
		default:
			return "=";
	}
}

$pdo = DB::pdo();
$limit = 50;
$offset = $page === 1 ? 0 : $limit * ($page-1);

$opt = '<option value="1">=</option><option value="2">!=</option><option value="3">&gt;</option><option value="4">&lt;</option><option value="5">&gt;=</option><option value="6">&lt;=</option><option value="7">LIKE</option><option value="8">%LIKE</option><option value="9">LIKE%</option><option value="10">%LIKE%</option>';

$stq = $pdo->prepare(
	"SELECT `regional` FROM `news` GROUP BY `regional` ORDER BY `regional` ASC;"
);
$stq->execute();
$stq = $stq->fetchAll(PDO::FETCH_NUM);
$optr = "<option value=\"all\">All</option>";
foreach ($stq as $v) {
	$optr .= "<option value=\"".htmlspecialchars($v[0], ENT_QUOTES, "UTF-8")."\">".htmlspecialchars($v[0], ENT_QUOTES, "UTF-8")."</option>";
}
$bind = [];
$where = "";
if (isset($_GET["regional"]) && $_GET["regional"] !== "" && is_string($_GET["regional"]) && strtolower($_GET["regional"]) !== "all") {
	$where .= "`regional` = :regional AND";
	$bind[":regional"] = trim($_GET["regional"]);
}

if (isset($_GET["title"]) && $_GET["title"] !== "" && is_string($_GET["title"])) {
	if (isset($_GET["title_op"])) {
		$oz = trim($_GET["title_op"]);
		switch ($oz) {
			case 8:
				$_GET["title"] = "%".$_GET["title"];
			case 9:
				$_GET["title"] = $_GET["title"]."%";
				break;
			case 10:
				$_GET["title"] = "%".$_GET["title"]."%";
				break;
			default:
				break;
		}
	}
	$where .= "`title` ".gptz($oz)." :title AND";
	$bind[":title"] = $_GET["title"];
}

if (isset($_GET["url"]) && $_GET["url"] !== "" && is_string($_GET["url"])) {
	if (isset($_GET["url_op"])) {
		$oz = trim($_GET["url_op"]);
		switch ($oz) {
			case 8:
				$_GET["url"] = "%".$_GET["url"];
			case 9:
				$_GET["url"] = $_GET["url"]."%";
				break;
			case 10:
				$_GET["url"] = "%".$_GET["url"]."%";
				break;
			default:
				break;
		}
	}
	$where .= "`url` ".gptz($oz)." :url AND";
	$bind[":url"] = $_GET["url"];
}

if (isset($_GET["datetime"]) && $_GET["datetime"] !== "" && is_string($_GET["datetime"])) {
	if (isset($_GET["datetime_op"])) {
		$oz = trim($_GET["datetime_op"]);
		switch ($oz) {
			case 8:
				$_GET["datetime"] = "%".$_GET["datetime"];
			case 9:
				$_GET["datetime"] = $_GET["datetime"]."%";
				break;
			case 10:
				$_GET["datetime"] = "%".$_GET["datetime"]."%";
				break;
			default:
				break;
		}
	}
	$where .= "`datetime` ".gptz($oz)." :datetime";
	$bind[":datetime"] = $_GET["datetime"];
}

if (! empty($where)) {
	$where = "WHERE ".trim(trim($where, "AND"));
}

$st = $pdo->prepare(
	"SELECT COUNT(*) FROM `news` {$where};"
);
$st->execute($bind);
$st = $st->fetch(PDO::FETCH_NUM);
if ($st !== false) {
	$mx = ceil($st[0]/$limit);
}

$stq = $pdo->prepare(
	$myquery = "SELECT `id`,`title`,`url`,`datetime`,`content_type`,`text` AS `content`,`regional`,`scraped_at` FROM `news` {$where} ORDER BY `scraped_at` DESC LIMIT {$limit} OFFSET {$offset};"
);

?><!DOCTYPE html>
<html>
<head>
	<title>Scraped Data</title>
		<style type="text/css">
		* {
			font-family: Arial;
			font-size: 13px;
		}
		.dd {
			display: inline-block;
			border: 1px solid #000;
			padding: 10px;
			height: 600px;
			margin-bottom: 40px;
		}
		th {
			padding: 5px;
		}
		td {
			padding: 5px;
		}
		.qq {
			width: 1230px;
			margin-bottom: 30px;
		}
		table {
			border-collapse:collapse;
		}
		button {
			cursor: pointer;
			padding: 4px 10px 4px 10px;
		}
		.pgg {
			font-size: 20px;
		}
		.sc {
			border: 1px solid #000;
			margin-bottom: 10px;
		}
		.scg {
			margin-bottom: 20px;
		}
		.wr {
			font-size: 18px;
		}
		.wrq {
			font-size: 15px;
			margin-left: 10px;
			margin-right: 10px;
			color: red;
		}
		#scg_1 {
			margin-bottom: 30px;
		}
		#scg_1_ {
			margin-top: 10px;
			margin-bottom: 10px;
		}
		.fqq {
			margin-top: 10px;
		}
	</style>
	<script type="text/javascript">
		function filterOpen()
		{
			document.getElementById("scg_1").style.display = "";
			document.getElementById("scg_1__").style.display = "";
			document.getElementById("scg_1_").style.display = "none";
		}
		function filterClose()
		{
			document.getElementById("scg_1").style.display = "none";
			document.getElementById("scg_1_").style.display = "";
			document.getElementById("scg_1__").style.display = "none";
		}
	</script>
</head>
<body>
	<center>
		<div>
			<a href="index.php"><button>Back to Home</button></a>
		</div>
		<h1 style="font-size: 32px;">Scraped Data</h1>
		<div class="sc">
			<div class="fqq">
				<a href="api.php?limit=25<?php print $queryString; ?>" target="_blank"><button>Get API URL by This Filter</button></a>
				<button id="scg_1_" onclick="filterOpen();">Filter Data</button>
				<button style="display:none;" id="scg_1__" onclick="filterClose();">Close Filter</button>
			</div>
			<div id="scg_1" style="display: none;">
				<h3 style="font-size: 18px;">Filter Data</h3>
				<form method="get" action="" id="filter">
					<table border="1">
						<tbody>
							<tr>
								<td><span class="wr">Regional</span></td>
								<td><select name="regional_op"><option value="1">=&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option></select></td>
								<td><select name="regional"><?php print $optr; ?></select></td>
							</tr>
							<tr>
								<td><span class="wr">Title</span></td>
								<td><select name="title_op"><?php print $opt; ?></select></td>
								<td><input type="text" name="title"></td>
							</tr>
							<tr>
								<td><span class="wr">URL</span></td>
								<td><select name="url_op"><?php print $opt; ?></select></td>
								<td><input type="text" name="url"></td>
							</tr>
							<tr>
								<td><span class="wr">Datetime</span></td>
								<td><select name="datetime_op"><?php print $opt; ?></select></td>
								<td><input type="text" name="datetime"></td>
							</tr>	
							<tr>
								<td><span class="wr">Content Type</span></td>
								<td><select name="content_type_op"><option value="1">=&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option></select></td>
								<td><select name="content_type"><option value="News">News</option></select></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" align="center">
									<button>Search</button>
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
			</div>
			<div>
				<div>
					<h3 style="font-size: 18px;">Current Query:</h3>
					<p style="font-size: 16px;"><?php print htmlspecialchars($myquery, ENT_QUOTES, "UTF-8");?></p>
					<h3 style="font-size: 18px;">Binding Data:</h3>
					<p><?php print htmlspecialchars(json_encode($bind), ENT_QUOTES, "UTF-8");?></p>
				</div>
			</div>
		</div>
		<table class="qq" border="1">
			<thead>
				<tr>
					<td colspan="10"><div style="width: 1230px; word-wrap: break-word;"><?php for ($i=1; $i <= $mx; $i++) { 
						if ($i === $page) {
							?><strong><a href="?page=<?php print $i; ?><?php print htmlspecialchars($queryString); ?>"><span class="pgg"><?php print $i; ?></span></a></strong>&nbsp;&nbsp;<?php	
						} else {
							?><a href="?page=<?php print $i; ?><?php print htmlspecialchars($queryString); ?>"><span class="pgg"><?php print $i; ?></span></a>&nbsp;&nbsp;<?php
						}
					}
					?></div></td>
				</tr>
				<tr>
					<th>No.</th><th>Title</th><th>URL</th><th>Datetime</th><th>Authors</th><th>Content Type</th><th>Content</th><th>Regional</th><th>Scraped At</th><th>Action</th>
				</tr>
			</thead>
			<tbody>
<?php
$i = $iia = $page === 1 ? 1 : $limit * ($page-1) + 1;
$stq->execute($bind);
while ($rr = $stq->fetch(PDO::FETCH_ASSOC)) {
	foreach ($rr as $k => &$v) {
		switch ($k) {
			case 'title':
				$v = es(tq($v, 100));
				break;
			case 'url':
				$v = "<a href=\"{$v}\" target=\"_blank\">".es(tq($v, 200))."</a>";
				break;
			default:
				break;
		}
	}
	?>
				
				<tr>
					<td align="center"><?php print $i++; ?>.</td>
					<td align="center"><?php print $rr["title"]; ?></td>
					<td align="center"><?php print $rr["url"]; ?></td>
					<td align="center"><?php print $rr["datetime"]; ?></td>
	<?php
	$rr["authors"] = "";
	$st = $pdo->prepare("SELECT `author_name` FROM `authors` WHERE `news_id`=:news_id;");
	$st->execute([":news_id" => $rr["id"]]);
	while ($r = $st->fetch(PDO::FETCH_NUM)) {
		$rr["authors"] .= $r[0].",";
	}
	?>
				<td align="center"><?php print es(trim($rr["authors"], ",")); ?></td>
					<td align="center"><?php print es(ucwords($rr["content_type"])); ?></td>
					<td align="center"><?php print es(tq($rr["content"], 200)); ?></td>
					<td align="center"><?php print es($rr["regional"]); ?></td>
					<td align="center"><?php print es($rr["scraped_at"]); ?></td>
					<td align="center"><a href="details.php?id=<?php print $rr["id"]; ?>"><button>Show Details</button></a></td>
	<?php

	// $st = $pdo->prepare("SELECT `category_name` FROM `categories` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $r["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_NUM)) {
	// }

	// $st = $pdo->prepare("SELECT `tag_name` FROM `tags` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $rr["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_NUM)) {
	// }

	// $st = $pdo->prepare("SELECT `author`,`content`,`datetime` FROM `comments` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $rr["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	// }

	// $st = $pdo->prepare("SELECT `image_url`,`description` FROM `images` WHERE `news_id`=:news_id;");
	// $st->execute([":news_id" => $rr["id"]]);
	// while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
	// }
	?>			</tr><?php
}
if ($i === $iia && !$rr) {
	?>
	<tr><td colspan="10" align="center"><h3 style="font-size: 32px;">Not Found</h3></td></tr>
	<?php
}
unset($rr, $stq);
?>
		
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10"><div style="width: 1230px; word-wrap: break-word;"><?php for ($i=1; $i <= $mx; $i++) { 
						if ($i === $page) {
							?><strong><a href="?page=<?php print $i; ?><?php print htmlspecialchars($queryString); ?>"><span class="pgg"><?php print $i; ?></span></a></strong>&nbsp;&nbsp;<?php	
						} else {
							?><a href="?page=<?php print $i; ?><?php print htmlspecialchars($queryString); ?>"><span class="pgg"><?php print $i; ?></span></a>&nbsp;&nbsp;<?php
						}
					}
					?></div></td>
				</tr>
			</tfoot>
		</table>
	</center>
</body>
</html>