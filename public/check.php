<?php
header("Content-Type: text/plain");
print shell_exec("ps aux | grep scraper.php 2>&1");
