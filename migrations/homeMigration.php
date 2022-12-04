<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();

/* adjust tables*/
$migration ->tables = [
      "lentec_home" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "section_id varchar(255) NOT NULL UNIQUE KEY",
            "section_title longtext NOT NULL"
      ],
      "lentec_artists" => [
            "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
            "name varchar(255) NOT NULL UNIQUE KEY",
            "image varchar(255) NOT NULL",
            "fb varchar(255) NOT NULL",
            "twitter varchar(255) NOT NULL",
            "insta varchar(255) NOT NULL",
            "youtube varchar(255) NOT NULL",
            "web varchar(255) NOT NULL",
            "tiktok varchar(255) NOT NULL",
            "pint varchar(255) NOT NULL",
            "linkedIn varchar(255) NOT NULL",
            "reddit varchar(255) NOT NULL",
            "telegram varchar(255) NOT NULL",
            "whatsapp varchar(255) NOT NULL",
            "cart varchar(255) NOT NULL",
           ]
      ];

/*migrate the tables */
echo $migration->migrate($delete=true);

session_unset();
session_destroy();
