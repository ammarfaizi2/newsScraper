<?php

require __DIR__."/../config/main.php";
require __DIR__."/../config/scraper.php";
require __DIR__."/../bootstrap/icetea_bootstrap.php";

$pdo = DB::pdo();

$st = $pdo->prepare("SELECT `regional` FROM `regional`;");
$st->execute();
$rrr = "";
while($rr = $st->fetch(PDO::FETCH_ASSOC)) {
    $username = str_replace(" ", "_", strtolower($rr["regional"]));
    print "db.users.insert(".json_encode(
        [
            "username" => $username,
            "password" => password_hash($password = $username."QWERTYUIOP", PASSWORD_BCRYPT)
        ]
    ).");\n";
    $rrr .= "Username: {$username}\nPassword: {$password}\n\n";
}
print "\n\n";
print $rrr;