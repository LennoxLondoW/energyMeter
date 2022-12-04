<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();

/* adjust tables*/
$migration ->tables = [
          "lentec_entries_meter" => [
          "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
          "section_id varchar(255) NOT NULL UNIQUE KEY",
          "section_title longtext NOT NULL"
          ],
          //single meter analytics
          "lentec_single_meter_daily" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "unique_id varchar(255) NOT NULL UNIQUE KEY", //has id and date concatinated
            "meter_serial_number varchar(255) NOT NULL",
            "period_date varchar(255) NOT NULL",
            "unit_measurement double(20,10) NOT NULL",
          ],
          "lentec_single_meter_monthly" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "unique_id varchar(255) NOT NULL UNIQUE KEY", //has id and date concatinated
            "meter_serial_number varchar(255) NOT NULL",
            "period_date varchar(255) NOT NULL",
            "unit_measurement double(20,10) NOT NULL",
            "days_calculated int(255) NOT NULL",
          ],
          "lentec_single_meter_annually" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "unique_id varchar(255) NOT NULL UNIQUE KEY", //has id and date concatinated
            "meter_serial_number varchar(255) NOT NULL",
            "period_date varchar(255) NOT NULL",
            "unit_measurement double(20,10) NOT NULL",
            "months_calculated int(255) NOT NULL",
          ], 
          //jointed meter readings
          "lentec_total_meter_daily" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "period_date varchar(255) NOT NULL UNIQUE KEY",
            "number_calculated int(255) NOT NULL",
            "unit_measurement double(20,10) NOT NULL",
          ],
          "lentec_total_meter_monthly" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "period_date varchar(255) NOT NULL UNIQUE KEY",
            "days_calculated int(255) NOT NULL",
            "unit_measurement double(20,10) NOT NULL",
          ],
          "lentec_total_meter_annually" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "period_date varchar(255) NOT NULL UNIQUE KEY",
            "months_calculated int(255) NOT NULL",
            "unit_measurement double(20,10) NOT NULL",
          ],
      ];

/*migrate the tables */
echo $migration->migrate($delete=false);

session_unset();
session_destroy();
?>