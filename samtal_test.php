<?php
   class MyDB extends SQLite3 {
      function __construct() {
         $this->open('samtal.db');
      }
   }

   $db = new MyDB();
   if(!$db) {
      echo $db->lastErrorMsg();
   } else {
      echo "Opened database successfully\n";
   }

$sql =<<<EOF
      SELECT * from words;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
      echo "Samtal = ". $row['samtal'] . "<br>\n";
      echo "English = ". $row['english'] ."<br>\n";
      echo "English 2 = ". $row['eng_def_2'] ."<br>\n";
   }
   echo "Operation done successfully\n";
   $db->close();
?>