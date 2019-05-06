<?php

namespace Phx;

defined("DATA_DIR") or die("Error: DATA_DIR is not defined yet!\n");
defined("HTML_DIR") or die("Error: HTML_DIR is not defined yet!\n");
defined("COOKIE_DIR") or die("Error: COOKIE_DIR is not defined yet!\n");
defined("HASH_CHECK_DIR") or die("Error: HASH_CHECK_DIR is not defined yet!\n");

use DB;
use PDO;
use WordCloud as BaseWordCloud;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @package Phx
 * @license MIT
 * @version 0.0.1
 */
final class WordCloud
{
      /**
       * @var string
       */
      private $regional;

      /**
       * @var \PDO
       */
      private $pdo;

      /**
       * @param string $regional
       */
      public function __construct(string $regional)
      {
            $this->pdo = DB::pdo();
            $this->regional = $regional;
      }
      
      public function runTitleWordCloud()
      {
            $wc = $this->pdo->prepare("INSERT INTO `title_wordcloud` (`news_id`, `words`, `n`,`hash`,`created_at`) VALUES (:news_id, :words, :n, :hash, :created_at);");
            $st = $this->pdo->prepare("SELECT `id`,`title` FROM `news` WHERE `regional`=:regional");
            $st->execute([":regional" => $this->regional]);
            while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
                  $r["title"] = str_replace([ " - ","- ", " -"], ["-","-","-"], preg_replace("/[^a-z0-9\-\s]/", "", strtolower($r["title"])));
                  $this->fixer($r["title"]);
                  icelog("Processing \"{$r['title']}\"...");
                  for ($i=1; $i <= 4; $i++) {
                        $wcc = new BaseWordCloud($r["title"], $i);
                        $wcc->toLower();
                        foreach($wcc->get() as $wordcloud) {
                              $wordcloud = trim($wordcloud);
                              if (empty($wordcloud)) {
                                    continue;
                              }
                              icelog("Got \"{$wordcloud}\"");
                              icelog("Checking \"{$wordcloud}\"...");
                              $hash = sha1(json_encode([$wordcloud, $i, $r["id"]]))."_".md5(json_encode([$wordcloud, $i, $r["id"]]));
                              if ($this->check($r["id"], $wordcloud, $i, $hash)) {
                                    icelog("Inserting \"{$wordcloud}\"");
                                    $wc->execute(
                                          [
                                                ":news_id" => $r["id"],
                                                ":words" => $wordcloud,
                                                ":n" => $i,
                                                ":hash" => $hash,
                                                ":created_at" => date("Y-m-d H:i:s")
                                          ]
                                    );
                                    icelog("OK");
                              } else {
                                    icelog("\"{$wordcloud}\" with news_id = {$r['id']} and n = {$i} has already been saved...");
                                    icelog("Skipping...");
                              }
                        }
                  }
            }
      }

      private function check($id, $wordcloud, $n, $hash)
      {
            $st = $this->pdo->prepare("SELECT `news_id` FROM `title_wordcloud` WHERE `news_id`=:news_id AND `words`=:wordcloud AND `n`=:n AND `hash`=:hash LIMIT 1;");
            $st->execute(
                  [
                        ":news_id" => $id,
                        ":wordcloud" => $wordcloud,
                        ":n" => $n,
                        ":hash" => $hash
                  ]
            );

            return !$st->fetch(PDO::FETCH_NUM);
      }

      private function fixer(&$title)
      {
            $title = preg_replace(
                  [
                        "/\-antara\s?news.+$/Usi",
                        "/liputan6com/Usi",
                        "/bali\-kompascom/Usi",
                        "/\-\s?Kompas\.com/Usi",
                        "/halaman \d{1,3}-kompascom/Usi",
                        "/[a-z]{3,10}\s?[a-z]{3,10}-kompascom/Usi",
                        "/\-kompascom/Usi",
                  ],
                  [
                        "",
                        "",
                        "",
                        "",
                        "",
                        "",
                        ""
                  ],
                  $title
            );

            $title = trim(trim($title), "-");
      }
}
