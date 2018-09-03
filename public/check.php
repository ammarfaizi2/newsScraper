<?php
header("Content-Type: text/plain");
print shell_exec("ps aux | grep icetea_worker 2>&1");
