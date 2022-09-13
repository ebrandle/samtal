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
   
   /***** LIST STUFF *****/
   
   // list all words
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
   
   // list all categories
   function listAllCats() {
      global $db;
      $sql = "SELECT * from categories;";
      $ret = $db->query($sql);
      
      echo "<h3>categories". $row['cat'] ."</h3>";
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
         echo $row['cat'] ."<br>\n";
      }
   }
   
   // list all words in specified category
   function listWordsInCat($cat) {
      global $db;
      $sql = "SELECT * FROM link_words_cat WHERE cat_link='". $cat ."';";
      $ret = $db->query($sql);
      
      $verify_cat_sql = "SELECT * FROM categories WHERE cat='". $cat ."';";
      $verify_cat_ret = $db->query($verify_cat_sql);
      $verify_cat_val = $verify_cat_ret->fetchArray(SQLITE3_ASSOC);
      if (doesWordExist($verify_cat_val) == False) return;
      
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
   
   // list all words, sorted by category
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
   
   
   
   /***** TRANSLATE STUFF *****/
   
   // samtal -> english
   function translateWordFromSamtal($samtal) {
      global $db;
      $sql = "SELECT english, eng_def_2 FROM words WHERE samtal='". $samtal ."';";
      $ret = $db->query($sql);
      $vals = $ret->fetchArray(SQLITE3_ASSOC);
      
      if (doesWordExist($vals) == False) return;
      echo "<h3>".$samtal."</h3>\n";
      echo $vals['english'];
      if ($vals['eng_def_2'] != "") {
         echo ", ". $vals['eng_def_2'];
      }
      echo "<br>";
   }
   
   // english -> samtal
   function translateWordFromEnglish($english) {
      global $db;
      $sql = "SELECT * FROM words WHERE english='". $english ."' OR eng_def_2='". $english ."';";
      $ret = $db->query($sql);
      $vals = $ret->fetchArray(SQLITE3_ASSOC);
      
      if (doesWordExist($vals) == False) return;
      
      echo "<h3>".$vals['samtal']."</h3>\n";
      echo $vals['english'];
      if ($vals['eng_def_2'] != "") {
         echo ", ". $vals['eng_def_2'];
      }
      echo "<br>";
   }
   
   
   
   /***** CATEGORIZE STUFF *****/
   
   // add word to category
   function orderWord($samtal,$cat) {
      global $db;
      
      // check samtal
      $sql = "SELECT * FROM words WHERE samtal='". $samtal ."';";
      $ret = $db->query($sql);
      $vals = $ret->fetchArray(SQLITE3_ASSOC);
      if (doesWordExist($vals) == False) return;
      
      // check category
      $sql = "SELECT * FROM categories WHERE cat='". $cat ."';";
      $ret = $db->query($sql);
      $vals = $ret->fetchArray(SQLITE3_ASSOC);
      if (doesWordExist($vals) == False) return;
      
      $vals = [$samtal,$cat];
      $sql = "INSERT INTO link_words_cat (samtal_link, cat_link) VALUES ('".$samtal."', '".$cat."');";
      $ret = $db->query($sql);

      echo "yay<br>";
   }



   /***** DELETE STUFF *****/

   // delete word
   function deleteWord($samtal) {
      global $db;

      // check if word exists
      $sql = "SELECT * FROM words WHERE samtal='". $samtal ."';";
      $ret = $db->query($sql);
      $vals = $ret->fetchArray(SQLITE3_ASSOC);
      if (doesWordExist($vals) == False) return;

      $sql = "DELETE FROM words WHERE samtal='". $samtal ."';";
      $ret = $db->query($sql);
      $sql = "DELETE FROM link_words_cat WHERE samtal_link='". $samtal ."';";
      $ret = $db->query($sql);

      echo "Word deleted<br>";
   }

   function deleteCategory($cat) {
      global $db;

      // check if word exists
      $sql = "SELECT * FROM categories WHERE cat='". $cat ."';";
      $ret = $db->query($sql);
      $vals = $ret->fetchArray(SQLITE3_ASSOC);
      if (doesWordExist($vals) == False) return;

      $sql = "DELETE FROM categories WHERE cat='". $cat ."';";
      $ret = $db->query($sql);
      $sql = "DELETE FROM link_words_cat WHERE cat_link='". $cat ."';";
      $ret = $db->query($sql);

      echo "Category deleted<br>";
   }


   
   /***** VALIDATION *****/
   
   // basic validator
   function doesWordExist($vals) {
      if ($vals == []) {
         echo "<em>The specified word does not exist. We apologize for the inconvenience.</em><br>";
         return False;
      }
      return True;
   }
   
   
   
   /***** FUNCTION CALLS *****/
   
   // list words
   if ($_GET["op"] && $_GET["op"]=="listAllWords"){
      listAllWords();
   }
   // list categories
   if ($_GET["op"] && $_GET["op"]=="listAllCats"){
      listAllCats();
   }
   // list words in specified category
   if ($_GET["op"] && $_GET["op"]=="listWordsInCat"){
      listWordsInCat($_GET["cat"]);
   }
   // list words, sorted by category
   if ($_GET["op"] && $_GET["op"]=="listAllWordsByCat"){
      listAllWordsByCat();
   }
   
   // translate samtal to english
   if ($_GET["op"] && $_GET["op"]=="translateWordFromSamtal"){
      translateWordFromSamtal($_GET["samtal"]);
   }
   // translate english to samtal
   if ($_GET["op"] && $_GET["op"]=="translateWordFromEnglish"){
      translateWordFromEnglish($_GET["english"]);
   }
   
   // put a word in a category
   if ($_GET["op"] && $_GET["op"]=="orderWord"){
      orderWord($_GET["samtal"],$_GET["cat"]);
   }

   // delete word
   if ($_GET["op"] && $_GET["op"]=="deleteWord"){
      deleteWord($_GET["word"]);
   }
   if ($_GET["op"] && $_GET["op"]=="deleteCategory"){
      deleteCategory($_GET["cat"]);
   }
   
   //listAllWords();
   $db->close();
?>