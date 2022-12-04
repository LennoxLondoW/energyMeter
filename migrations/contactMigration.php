<?php
session_start();
/*main operation file*/
require_once '../app/extensions/app.migration.php';

/*creting migrations*/
$migration = new Migration();




/* adjust tables*/
$migration->tables = [
  "lentec_contact" => [
    "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "section_id varchar(255) NOT NULL UNIQUE KEY",
    "section_title longtext NOT NULL"
  ],
  "lentec_email_settings" => [
    "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "section_id varchar(255) NOT NULL UNIQUE KEY",
    "section_title longtext NOT NULL"
  ],
  "lentec_ask" => [
    "id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
    "ask_name varchar(255) NOT NULL",
    "ask_email varchar(255) NOT NULL",
    "ask_tel varchar(255) NOT NULL",
    "ask_subject varchar(255) NOT NULL",
    "ask_date varchar(255) NOT NULL",
    "ask_message longtext NOT NULL"
  ]
];


/*set migration sections*/
$migration->insertData = [
  /*Page meta*/
  [
    "section_id" => "active",
    "section_title" => "contact"
  ],
  [
    "section_id" => "page_title",
    "section_title" => "Contact Us"
  ],
  [
    "section_id" => "page_desc",
    "section_title" => "Get in contact with us"
  ],
  [
    "section_id" => "page_keywords",
    "section_title" => "Web design, web developer"
  ],
  [
    "section_id" => "page_image",
    "section_title" => "images/images/don.png"
  ],
  /*/Page meta*/
  [
    "section_id" => "company_location",
    "section_title" => "2969 Transview, Lulu Road, Athiriver Senior Staff, Off Namanga "
  ],

  [
    "section_id" => "com_im_1",
    "section_title" => "images/images/con1.png"
  ],
  [
    "section_id" => "com_im_2",
    "section_title" => "images/images/con2.svg"
  ],
  [
    "section_id" => "con_t_1",
    "section_title" => "Contact Us"
  ],
  [
    "section_id" => "con_t_2",
    "section_title" => "Have Questions?"
  ],
  [
    "section_id" => "con_t_3",
    "section_title" => "Get in Touch!"
  ],

];

/*set table name*/
$migration->activeTable = "lentec_contact";

/*migrate the tables */
echo $migration->migrate(false);
/*save data into the migrated table*/
echo $migration->saveData();


/*email settings*/
$migration->activeTable = "lentec_email_settings";
$migration->insertData = [

  [
    "section_id" => "email_mail",
    "section_title" => "info@anchortrends.com"
  ],
  [
    "section_id" => "email_password",
    "section_title" => "z;PKB=Jl.Z@w"
  ],

  [
    "section_id" => "email_domain",
    "section_title" => "mail.anchortrends.com"
  ],
  [
    "section_id" => "email_SMTPSecure",
    "section_title" => "tsl"
  ],
  [
    "section_id" => "email_port",
    "section_title" => "587"
  ],

  [
    "section_id" => "email_site_name",
    "section_title" => "LennTech"
  ],
  [
    "section_id" => "email_company_logo",
    "section_title" => "images/ll.png"
  ],
  [
    "section_id" => "email_company_location",
    "section_title" => "Bungoma, Kenya"
  ],
  [
    "section_id" => "email_phone",
    "section_title" => "+254708889764"
  ],


  [
    "section_id" => "email_heading_background",
    "section_title" => "#181818"
  ],
  [
    "section_id" => "email_body_background",
    "section_title" => "#464545"
  ],
  [
    "section_id" => "email_footer_background",
    "section_title" => "#181818"
  ],
  [
    "section_id" => "email_entire_page_background",
    "section_title" => "#dbdbdb"
  ],
  [
    "section_id" => "email_body_color",
    "section_title" => "#ffffff"
  ],
  [
    "section_id" => "email_heading_color",
    "section_title" => "#ffffff"
  ],
  [
    "section_id" => "email_footer_color",
    "section_title" => "#d1d1d1"
  ],
  [
    "section_id" => "email_link_color",
    "section_title" => "#FF8D1B"
  ],
  [
    "section_id" => "email_button_background",
    "section_title" => "#FF8D1B"
  ],
  [
    "section_id" => "email_button_color",
    "section_title" => "#ffffff"
  ],
  [
    "section_id" => "email_success",
    "section_title" => "Thank you. We will get back to you."
  ]

];

echo $migration->saveData();

session_unset();
session_destroy();
