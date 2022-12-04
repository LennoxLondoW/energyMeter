<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();

/* adjust tables*/
$migration ->tables = [
          "lentec_meters" => [
          "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
          "section_id varchar(255) NOT NULL UNIQUE KEY",
          "section_title longtext NOT NULL"
          ],

          "lentec_meters_data" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "meter_serial_number varchar(255) NOT NULL UNIQUE KEY",
            "meter_name varchar(255) NOT NULL",
            "lat double(15,10) NOT NULL",
            "lng double(15,10) NOT NULL",
           ]
      ];

/*migrate the tables */
echo $migration->migrate($delete=false);

session_unset();
session_destroy();
?>