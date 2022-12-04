<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();




/* adjust tables*/
$migration ->tables = [
          "lentec_about" => [
          "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
          "section_id varchar(255) NOT NULL UNIQUE KEY",
          "section_title longtext NOT NULL"
         ]
      ];

/*migrate the tables */
echo $migration->migrate();

session_unset();
session_destroy();

?>