<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();

/* adjust tables*/
$migration->tables = [
  "lentec_navbar" => [
    "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "section_id varchar(255) NOT NULL UNIQUE KEY",
    "section_title longtext NOT NULL"
  ],
  "lentec_iptracker" => [
    "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "_ip varchar(255) NOT NULL UNIQUE KEY",
    "_date VARCHAR(255) NOT NULL",
    "_times INT(255) NOT NULL"
  ],

];

$migration->insertData = [
  /*Page meta*/
  [
    "section_id" => "nav_link_1",
    "section_title" => "sign_up"
  ],
  [
    "section_id" => "nav_link_2",
    "section_title" => "verify_account"
  ],
  [
    "section_id" => "nav_link_3",
    "section_title" => "sign_in"
  ],
  [
    "section_id" => "nav_link_4",
    "section_title" => "reset_password"
  ],
  [
    "section_id" => "nav_link_5",
    "section_title" => "change_password"
  ],

  [
    "section_id" => "nav_link_6",
    "section_title" => "home"
  ],

  [
    "section_id" => "nav_link_7",
    "section_title" => "privacy_policy"
  ],

  [
    "section_id" => "nav_link_8",
    "section_title" => "terms_and_conditions"
  ],

  [
    "section_id" => "nav_link_9",
    "section_title" => "admin"
  ],


  [
    "section_id" => "nav_link_12",
    "section_title" => "donate"
  ],

  [
    "section_id" => "nav_link_13",
    "section_title" => "contact"
  ],
  [
    "section_id" => "nav_link_14",
    "section_title" => "about"
  ],
 
 
 
  [
    "section_id" => "nav_link_18",
    "section_title" => "view_users"
  ],

  

  [
    "section_id" => "nav_link_19",
    "section_title" => "equipment"
  ],

  [
    "section_id" => "nav_link_20",
    "section_title" => "meters"
  ],

  [
    "section_id" => "nav_link_21",
    "section_title" => "analytics_all"
  ],

  [
    "section_id" => "nav_link_22",
    "section_title" => "analytics_equipment"
  ],

  [
    "section_id" => "nav_link_23",
    "section_title" => "analytics_meter"
  ],

  [
    "section_id" => "nav_link_24",
    "section_title" => "entries_meter"
  ],

  [
    "section_id" => "nav_link_25",
    "section_title" => "entries_equpment"
  ],
  [
    "section_id" => "nav_link_26",
    "section_title" => "entries_expense"
  ],

  [
    "section_id" => "nav_link_2y",
    "section_title" => "analytics_price"
  ],
 
  /*/Page meta*/

];

/*migrate the tables */
echo $migration->migrate($delete = false);
session_unset();
session_destroy();
