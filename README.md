# Requirements:
1. PHP 7.2
2. PHP Curl Extension.
3. PHP PDO + MySQL Extension.
4. MySQL Database
5. GNU/Linux Environment.

# Installation and Usage:
1. Clone the repository
```shell
git clone https://github.com/ammarfaizi2/newsScraper
cd newsScraper
cp -vf config/main.php.example config/main.php
cp -vf config/scraper.php.example config/scraper.php
```
2. Create a new MySQL database and import `database.sql`.
3. Edit config files and adjust the configuration with your environment.
4. Run `bin/run.php`.
