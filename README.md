# Requirements:
1. PHP 7.2
2. PHP Curl Extension.
3. PHP PDO + MySQL Extension.
4. MySQL Database
5. GNU/Linux Environment.

# Installation and Usage:

### Run the scraper:
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
5. The scrapers will running on the background process.
- You can check the background process by `ps` command.
```shell
ps aux | grep scraper.php
```

### Run the Web API:
1. Change the working directory to `public` folder.
2. Run PHP Server by this command
```shell
nohup php -S 0.0.0.0:4444 >> /dev/null 2>&1 &
```
3. Open the API from your browser, for example http://127.0.0.1:4444/index.php