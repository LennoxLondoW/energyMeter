<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();

/* adjust tables*/
$migration ->tables = [
          "lentec_entries_expense" => [
          "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
          "section_id varchar(255) NOT NULL UNIQUE KEY",
          "section_title longtext NOT NULL"
          ],
          "lentec_entries_monthly_expense" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "_date_ varchar(255) NOT NULL UNIQUE KEY",
            "amount double(20,10) NOT NULL"
          ],
          "lentec_entries_annual_expense" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "_date_ varchar(255) NOT NULL UNIQUE KEY",
            "months_calculated varchar(255) NOT NULL",
            "amount double(20,10) NOT NULL"
           ]
      ];

/*migrate the tables */
echo $migration->migrate($delete=false);

session_unset();
session_destroy();
