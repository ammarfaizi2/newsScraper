<?php


if (! empty($_GET["q"])) {
	header("Content-Type: application/json");
	
	require __DIR__."/../vendor/autoload.php";

	

	$query = $_GET["q"];

	$st = new GoogleSearch\GoogleSearch($query);
	$out = $st->exec();

	print json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	exit();
}

?><!DOCTYPE html>
<html>
<head>
	<title>Google Search</title>
</head>
<body>
	<center>
		<form method="GET" action="<?php print $_SERVER["PHP_SELF"]; ?>">
			<h3>Enter your query</h3>
			<input type="text" name="q"><br/><br/>
			<button type="submit">Search</button>
		</form>
	</center>
</body>
</html>