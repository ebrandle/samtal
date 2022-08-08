<?php
   class MyDB extends SQLite3 {
      function __construct() {
         $this->open('samtal.db');
      }
   }

   $db = new MyDB();
   if(!$db) {
      echo $db->lastErrorMsg();
   }
   
   /*else {
      echo "Opened database successfully\n";
   }*/

   /*   $sql =<<<EOF
      SELECT * from words;
EOF;*/
   
   //echo "Operation done successfully\n";

   
   function listAllWords() {
      global $db;
      $sql = "SELECT * from words;";

      $ret = $db->query($sql);
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
         echo "<h3>". $row['samtal'] ."</h3>";
         echo "English: ". $row['english'] ."<br>\n";
         echo "English 2: ". $row['eng_def_2'] ."<br>\n";
         echo "<br>";
      }
   }
   
   function listAllCats() {
      global $db;
      $sql = "SELECT * from categories;";

      $ret = $db->query($sql);
      echo "<h3>Categories". $row['cat'] ."</h3>";
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
         echo $row['cat'] ."<br>\n";
      }
   }
   
   function listWordsByCat($cat) {
      global $db;
      //$sql = "SELECT * FROM link_words_cat WHERE cat_link='". $cat ."';";
      $sql = "SELECT samtal_link FROM link_words_cat WHERE cat_link='". $cat ."';";
      
      // error: thinks $cat is a column, not column entry
      /* Warning: SQLite3::query():
       * Unable to prepare statement: 1,
       * no such column: noun in
       * ~/.../samtal.php on line 52
       **/
      $ret = $db->query($sql);
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
         echo $row['cat_link'] ."<br>\n";
      }
   }
   
   if ($_GET["op"] && $_GET["op"]=="listAllWords"){
      listAllWords();
   }
   if ($_GET["op"] && $_GET["op"]=="listAllCats"){
      listAllCats();
   }
   if ($_GET["op"] && $_GET["op"]=="listWordsByCat"){
      listWordsByCat($_GET["cat"]);
   }
   
   //listAllWords();
   $db->close();
?>