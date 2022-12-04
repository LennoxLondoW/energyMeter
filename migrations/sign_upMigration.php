<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();

/* adjust tables*/
$migration ->tables = [
          "lentec_sign_up" => [
          "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
          "section_id varchar(255) NOT NULL UNIQUE KEY",
          "section_title longtext NOT NULL"
          ],
          "lentec_users" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "email varchar(255) NOT NULL UNIQUE KEY",
            "name varchar(255) NOT NULL",
            "password longtext NOT NULL",
            "verify longtext NOT NULL",
            "is_admin varchar(255) NOT NULL",
            "is_suspended varchar(255) NOT NULL",
            "keywords varchar(255) NOT NULL",
            "FULLTEXT(keywords)"
           ]
      ];

/*migrate the tables */
echo $migration->migrate($delete=true);

session_unset();
session_destroy();
?>