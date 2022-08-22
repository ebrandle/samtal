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
         echo "English: ". $row['english'];// ."<br>\n";
         if ($row['eng_def_2'] != "") {
            echo ", ". $row['eng_def_2'] ."<br>\n";
         } else {echo "<br>";}
         echo "<br>";
      }
   }
   
   function listAllCats() {
      global $db;
      $sql = "SELECT * from categories;";
      $ret = $db->query($sql);
      
      echo "<h3>categories". $row['cat'] ."</h3>";
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
         echo $row['cat'] ."<br>\n";
      }
   }
   
   function listWordsInCat($cat) {
      global $db;
      $sql = "SELECT * FROM link_words_cat WHERE cat_link='". $cat ."';";
      $ret = $db->query($sql);
      
      echo "<h3>".$cat."s</h3>\n";
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
         //echo $row['samtal_link'] ."<br>\n";
         $samtal = $row['samtal_link'];
         $sql_eng = "SELECT * FROM words WHERE samtal='". $samtal ."';";
         $ret_eng = $db->query($sql_eng);
         
         echo $samtal .": ";
         while($row_eng = $ret_eng->fetchArray(SQLITE3_ASSOC) ) {               
            echo $row_eng['english'];
            if ($row_eng['eng_def_2'] != "") {
               echo ", ". $row_eng['eng_def_2'];
            }
            echo "<br>";
         }
      }
   }
   
   function listAllWordsByCat() {
      global $db;
      $sql_cat = "SELECT * from categories;";
      $ret_cat = $db->query($sql_cat);
      
      while($row_cat = $ret_cat->fetchArray(SQLITE3_ASSOC) ) {
         $cat = $row_cat['cat'];
         echo "<h3>". $cat ."s</h3>";
         
         $sql = "SELECT * FROM link_words_cat WHERE cat_link='". $cat ."';";
         $ret = $db->query($sql);
         
         while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
            $samtal = $row['samtal_link'];
            $sql_eng = "SELECT * FROM words WHERE samtal='". $samtal ."';";
            $ret_eng = $db->query($sql_eng);
            
            echo $samtal .": ";
            while($row_eng = $ret_eng->fetchArray(SQLITE3_ASSOC) ) {               
               echo $row_eng['english'];
               if ($row_eng['eng_def_2'] != "") {
                  echo ", ". $row_eng['eng_def_2'];
               }
               echo "<br>";
            }
         }
         echo "<br>";
      }
   }
   
   if ($_GET["op"] && $_GET["op"]=="listAllWords"){
      listAllWords();
   }
   if ($_GET["op"] && $_GET["op"]=="listAllCats"){
      listAllCats();
   }
   if ($_GET["op"] && $_GET["op"]=="listWordsInCat"){
      listWordsInCat($_GET["cat"]);
   }
   if ($_GET["op"] && $_GET["op"]=="listAllWordsByCat"){
      listAllWordsByCat();
   }
   
   //listAllWords();
   $db->close();
?>