<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

if (isset($_GET["page"])) {
	$st = new Statistics;
	switch ($_GET["page"]) {
		case 'regional':
			goto regional;
			break;
		
		default:
			# code...
			break;
	}

	exit();
}
?><!DOCTYPE html>
<html>
<head>
	<title>Data Statistics</title>
</head>
<body>

</body>
</html><?php

regional:


exit();