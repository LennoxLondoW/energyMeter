<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();

/* adjust tables*/
$migration ->tables = [
          "lentec_Equipment" => [
          "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
          "section_id varchar(255) NOT NULL UNIQUE KEY",
          "section_title longtext NOT NULL"
          ],

          "lentec_equipment_data" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "equipment_name varchar(255) NOT NULL UNIQUE KEY",
            "equipment_rating double(10,2) NOT NULL",
            "equipment_quantity int(255) NOT NULL",
            "file varchar(255) NOT NULL",

           ]
      ];

/*migrate the tables */
echo $migration->migrate($delete=false);

session_unset();
session_destroy();
?>