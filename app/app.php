<?php
if (!defined('__path__')) {
  define('__path__', isset($__path__) ? $__path__ : "../");
}
require_once __path__ . 'env/config.php';
/**
 *  App -- performs all crud operations
 */

class App
{
  /*database*/
  public $database;
  /*insert data*/
  public $insertData = [];
  /*active table*/
  public $activeTable;
  /*fetch comparisons */
  public $comparisons = [];
  /*fetch joiners*/
  public $joiners = [''];
  /*fetch order*/
  public $order = ' BY id DESC ';
  /*cols to fetch*/
  public $cols = ' * ';
  /*fetch limit*/
  public $limit = ' 10 ';
  /*fetch offset*/
  public $offset = 0;
  /*max_file_size*/
  public $max_file_size = 5000000;
  /*upload_file*/
  public $upload_file;
  /*used in updating*/
  public $update_data;
  /*pagination*/
  public $_PAGINATION = "";
  /*next*/
  public $_NEXT = "";
  /*prev*/
  public $_PREV = "";
  /*beggib offset for blog*/
  public $blog_offset = 0;
  /*beggib offset for blog*/
  public $sql_extra = "";


  // emails
  // dynamic
  public $email_username = "Customer";
  public $email_message = "";
  public $email_subject = "";
  public $email_to = "";
  public $email_cc = [];
  public $email_attachment = false;
  // set by admin

  public $email_company_logo = "images/ll.png";
  public $email_company_location = "Bungoma, Kenya";
  public $email_phone = "+254708889764";
  public $email_site_name = "Lennox Technologies";
  public $email_mail = "info@anchortrends.com";
  public $email_password = "z;PKB=Jl.Z@w";
  public $email_domain = "mail.anchortrends.com";
  public $email_SMTPSecure = "tsl";
  public $email_port = "587";
  public $email_SMTPAuth = true;
  public $IsHtml = true;
  public $email_error = false;
  public $email_success = "Thank you. We are working on your query! We will get back to you.";

  public $email_heading_background = "#181818";
  public $email_body_background = "#464545";
  public $email_footer_background = "#181818";
  public $email_entire_page_background = "#dbdbdb";
  public $email_button_background = "#FF8D1B";
  public $email_body_color = "#fff";
  public $email_heading_color = "#fff";
  public $email_footer_color = "rgba(255,255,255,.8)";
  public $email_link_color = "#FF8D1B";
  public $email_button_color = "#fff";




  /**
   *  setting connection to database
   *  requires no parameters
   */
  public function use_database()
  {
    /*constants set from config file*/
    $this->database = new mysqli(server, user, password, database);
    /*connection error*/
    if ($this->database->connect_errno) {
      die('<!doctype html>
      <title>Site Maintenance</title>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
      <style>
        html, body { padding: 0; margin: 0; width: 100%; height: 100%; }
        * {box-sizing: border-box;}
        body { text-align: center; padding: 0; background: #d6433b; color: #fff; font-family: Open Sans; }
        h1 { font-size: 50px; font-weight: 100; text-align: center;}
        body { font-family: Open Sans; font-weight: 100; font-size: 20px; color: #fff; text-align: center; display: -webkit-box; display: -ms-flexbox; display: flex; -webkit-box-pack: center; -ms-flex-pack: center; justify-content: center; -webkit-box-align: center; -ms-flex-align: center; align-items: center;}
        article { display: block; width: 700px; padding: 50px; margin: 0 auto; }
        a { color: #fff; font-weight: bold;}
        a:hover { text-decoration: none; }
        svg { width: 75px; margin-top: 1em; }
      </style>
      
      <article>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 202.24 202.24"><defs><style>.cls-1{fill:#fff;}</style></defs><title>Asset 3</title><g id="Layer_2" data-name="Layer 2"><g id="Capa_1" data-name="Capa 1"><path class="cls-1" d="M101.12,0A101.12,101.12,0,1,0,202.24,101.12,101.12,101.12,0,0,0,101.12,0ZM159,148.76H43.28a11.57,11.57,0,0,1-10-17.34L91.09,31.16a11.57,11.57,0,0,1,20.06,0L169,131.43a11.57,11.57,0,0,1-10,17.34Z"/><path class="cls-1" d="M101.12,36.93h0L43.27,137.21H159L101.13,36.94Zm0,88.7a7.71,7.71,0,1,1,7.71-7.71A7.71,7.71,0,0,1,101.12,125.63Zm7.71-50.13a7.56,7.56,0,0,1-.11,1.3l-3.8,22.49a3.86,3.86,0,0,1-7.61,0l-3.8-22.49a8,8,0,0,1-.11-1.3,7.71,7.71,0,1,1,15.43,0Z"/></g></g></svg>
          <h1>We&rsquo;ll be back soon!</h1>
          <div>
              <p>Sorry for the inconvenience. We&rsquo;re performing some maintenance at the moment. We&rsquo;ll be back up shortly!</p>
              <p>&mdash; Technical Team</p>
          </div>
      </article>');
    }
  }

  /**
   *  close connection to database
   */
  public function release_database()
  {
    /*Globals database param*/
    if (is_resource($this->database) && get_resource_type($this->database) === 'mysql link') {
      $this->database->close();
    }
    if (is_resource($this->database) && get_resource_type($this->database) === 'mysql link') {
      die("error");
    }
  }





  /**
   *  saving data to active table
   */
  public function saveData($duplicate_column = 'id')
  {
    /*no empty inserts*/
    if (count($this->insertData) === 0) {
      return 'Empty fields!!';
    }
    $add = $duplicate_column ? ($duplicate_column === 'id' ? "ON DUPLICATE KEY UPDATE id = id " : (is_array($duplicate_column) ?
      "ON DUPLICATE KEY UPDATE " . implode(", ", array_map(function ($col) {
        return  $col . " = values(" . $col . ") ";
      }, $duplicate_column))
      :
      "ON DUPLICATE KEY UPDATE " . $duplicate_column . " = values(" . $duplicate_column . ") "
    )) : "";
    /*connect to database*/
    $this->use_database();
    /*inserting columns*/
    $cols = implode(", ", array_keys(end($this->insertData)));
    /*inserting values*/
    $values = implode(", ", array_fill(0, count(end($this->insertData)), "?"));
    /*finalexecutable query*/
    $query = "INSERT INTO {$this->activeTable} ({$cols}) VALUES ({$values}) " . $add;
    /*if query fails*/
    if (!($stmt = $this->database->prepare($query))) {
      /*close database*/
      $this->release_database();
      return "Error!" . $this->database->error;
    }
    /*bind param and insert*/
    foreach ($this->insertData as  $value) {
      /*bind parameters*/
      $params = array_merge([implode("", array_fill(0, count($value), "s"))], array_values($value));
      /*if binding fails*/
      if (!$stmt->bind_param(...$params)) {
        /*close database*/
        $this->release_database();
        return $this->database->error;
      }
      /*execute statement*/
      if (!$stmt->execute()) {
        /*close database*/
        $this->release_database();
        return $this->database->error;
      }
      $this->insert_id = $stmt->insert_id;
    }
    return "success";
  }



  /*
     *  
      fetching function
          $comparisons are key, value, sign
      * e.g
      * [
      *    ['name','LIKE','%boy%'],
      *    ['age','>','4'],
      *    ['marks','<','6'],
      *    ['score','=','20']
      * ]

      * $joiners
      *  whether to use or/and 
      *  the first joiner should always be empty
      *  e.g,
      *  ['','&&','||']
        * 
  */
  public function getData($paginate = '<ul><li><a PREVLINK >Prev</a></li> <li><a NEXTLINK >Next</a></li> </ul>')
  {
    $table = $this->activeTable;
    $comparisons = $this->comparisons;
    $joiners = $this->joiners;
    $order = $this->order;
    $cols = $this->cols;
    $lim = $this->limit;
    $offset = $this->offset;
    /*upper limit*/
    $limit  = $lim + 1;
    /*conditions to satisfy*/
    $conditions = [];
    /*binding parameters */
    $parameters = [''];
    /*if internals are present*/
    $internals = false;
    foreach ($comparisons as $key => $value) {
      $internals = true;
      $conditions[] = " {$joiners[$key]} {$value['0']} {$value['1']} ?";
      $parameters[0] .= 's';
      $parameters[] = "{$value[2]}";
    }

    /*connect to database*/
    $this->use_database();

    /*build query*/
    $query = "SELECT {$cols} FROM {$table} ";
    //parameterized
    if ($internals) {
      $query .= (' WHERE ' . implode(' ', $conditions) . " ORDER " . $order . " LIMIT " . $limit . " OFFSET " . $offset);
      $stmt = $this->database->prepare($query);
      if (!$stmt) {
        $this->database_error = $this->database->error;
        return [];
      }
      $stmt->bind_param(...$parameters);
      $stmt->execute();
      $result = $stmt->get_result();
    }
    //plain with no parameters
    else {
      $query .= (" ORDER " . $order . " LIMIT " . $limit . " OFFSET " . $offset);
      $result = $this->database->query($query);
      if (!$result) {
        $this->database_error = $this->database->error;
        return [];
      }
    }

    $this->release_database();

    //pagination
    $this->paginate($result->num_rows, $paginate);

    return array_slice($result->fetch_all(MYSQLI_ASSOC), 0, $lim);
  }





  /*
      *   fetching function
          $comparisons are key, value, sign
      * e.g
      * [
      *    ['name','LIKE','%boy%'],
      *    ['age','>','4'],
      *    ['marks','<','6'],
      *    ['score','=','20']
      * ]

      * $joiners
      *  whether to use or/and 
      *  the first joiner should always be empty
      *  e.g,
      *  ['','&&','||']
        * 
  */
  public function deleteData()
  {
    $table = $this->activeTable;
    $comparisons = $this->comparisons;
    $joiners = $this->joiners;
    /*conditions to satisfy*/
    $conditions = [];
    /*binding parameters */
    $parameters = [''];
    /*if internals are present*/
    $internals = false;
    foreach ($comparisons as $key => $value) {
      $internals = true;
      $conditions[] = " {$joiners[$key]} {$value['0']} {$value['1']} ?";
      $parameters[0] .= 's';
      $parameters[] = "{$value[2]}";
    }

    /*connect to database*/
    $this->use_database();

    /*build query*/
    $query = "DELETE FROM {$table} ";
    //parameterized
    if ($internals) {
      $query .= (' WHERE ' . implode(' ', $conditions));
      $stmt = $this->database->prepare($query);
      $stmt->bind_param(...$parameters);
    }
    //plain with no parameters
    else {
      $stmt = $this->database->prepare($query);
    }

    if ($stmt->execute()) {
      $this->release_database();
      return true;
    }
    $this->database_error = $this->database->error;
    $this->release_database();
    return false;
  }







  /**   updating function
          comparisons are key, value, sign
   * e.g
   * [
   *    ['name','LIKE','%boy%'],
   *    ['age','>','4'],
   *    ['marks','<','6'],
   *    ['score','=','20']
   * ]

   * $joiners
   *  whether to use or/and 
   *  the first joiner should always be empty
   *  e.g,
   *  ['','&&','||']
   * 
   */
  public function updateData()
  {
    $table = $this->activeTable;
    $comparisons = $this->comparisons;
    $joiners = $this->joiners;
    $update_data = $this->update_data;
    /*conditions to satisfy*/
    $conditions = [];
    /*binding parameters */
    $parameters = [''];
    /*if internals are present*/
    $internals = false;
    /*build query*/
    $query = "UPDATE {$table} SET ";
    /*loop*/
    $loop = 0;
    /*concatinate all updates*/
    foreach ($update_data as $key => $value) {

      $query .= ((++$loop === 1 ? "" : ",") . " {$key} =  ?");
      $parameters[0] .= 's';
      $parameters[] = "{$value}";
    }
    /**create conditions to update*/
    foreach ($comparisons as $key => $value) {
      $internals = true;
      $conditions[] = " {$joiners[$key]} {$value['0']} {$value['1']} ?";
      $parameters[0] .= 's';
      $parameters[] = "{$value[2]}";
    }

    /*connect to database*/
    $this->use_database();
    //parameterized
    $query .= $internals ? (' WHERE ' . implode(' ', $conditions)) : "";

    if (!$stmt = $this->database->prepare($query)) {
      $this->database_error = $this->database->error;
      return false;
    }

    if (!$stmt->bind_param(...$parameters)) {
      $this->database_error = $this->database->error;
      return false;
    }

    if (!$stmt->execute()) {
      $this->database_error = $this->database->error;
      return false;
    }

    $result = $stmt->get_result();
    $this->release_database();
    return true;
  }


  //get if table exist
  public function is_table($table)
  {
    $this->use_database();
    $num_rows = $this->database->query("SHOW TABLES LIKE '" . $table . "'")->num_rows;
    $this->release_database();
    return  is_numeric($num_rows) && $num_rows > 0 ? true : false;
  }






  /*validate_image*/
  public function save_image()
  {

    $a = getimagesize($this->upload_file['tmp_name']);

    $image_type = isset($a[2]) ? $a[2] : "";

    if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP)) && $this->upload_file['size'] <= $this->max_file_size) {
      if (!is_dir(($dir = "../uploads"))) {
        mkdir($dir);
      }
      if (!is_dir(($dir = "../uploads/images"))) {
        mkdir($dir);
      }
      if (!is_dir(($dir = "../uploads/images/" . $this->activeTable . "/"))) {
        mkdir($dir);
      }

      $file = $dir . date("Y") . time() . uniqid() . uniqid() . uniqid() . ".jpg";

      if (!move_uploaded_file($this->upload_file['tmp_name'], $file)) {
        $this->upload_error = "Something unusual has happened. Please try again.";
        return false;
      }

      return str_replace("../", "", $file);
    }
    if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {
      $this->upload_error = "Image size should be less than or equal to" . ($this->max_file_size);
      return false;
    }
    $this->upload_error = "Enter a valid image";
    return false;
  }





  /*validate_image*/
  public function save_any_file()
  {


    if ($this->upload_file['size'] <= $this->max_file_size) {
      if (!is_dir(($dir = "../uploads"))) {
        mkdir($dir);
      }
      if (!is_dir(($dir = "../uploads/all_files"))) {
        mkdir($dir);
      }
      if (!is_dir(($dir = "../uploads/all_files/" . $this->activeTable . "/"))) {
        mkdir($dir);
      }

      $arr = explode(".", $this->upload_file['name']);

      $file = $dir . date("Y") . time() . uniqid() . uniqid() . uniqid() . "." . end($arr);

      if (!move_uploaded_file($this->upload_file['tmp_name'], $file)) {
        $this->upload_error = "Something unusual has happened. Please try again.";
        return false;
      }

      return str_replace("../", "", $file);
    }
    $this->upload_error = "This file is too large";
    return false;
  }


  /*pagination*/
  public function paginate($num_rows, $paginate = '<ul><li><a PREVLINK >Prev</a></li> <li><a NEXTLINK >Next</a></li> </ul>')
  {

    $next =
      $prev = "no";

    $offset = $this->offset;
    $lim  = $this->limit;


    if ($offset > 0 || $num_rows > $lim) {

      if ($offset > 0) {
        $prev_hold = ($x = $offset - $lim) < 0 ? 0 : $x;
        if ($prev_hold != $offset) {
          $prev = $prev_hold; //setting previous to the new value
        }
      }

      if ($num_rows > $lim) {
        $next_hold = $offset + $lim;
        $next = $next_hold;
      }
    }

    $this->_NEXT = $next;
    $this->_PREV = $prev;
    $page = explode("/", substr($_SERVER['REQUEST_URI'], strlen(base_path)))[0];
    $href = base_path . $page;
    foreach ($_GET as $key => $value) {
      if ($key != "offset") {
        $href .= "/" . $key . "/" . urlencode($value);
      }
    }
    $href .= "/offset/";
    $prev_link = (is_numeric($prev) ? 'href="' . $href . $prev . '"' : 'href="#none" class="text-muted" ');
    $next_link =  (is_numeric($next) ? 'href="' . $href . $next . '"' : 'href="#none" class="text-muted" ');
    $this->_PAGINATION = str_replace("PREVLINK", $prev_link, str_replace("NEXTLINK", $next_link, $paginate));
  }



  // sending email
  public function send_email()
  {

    //pick email settings from table
    $settings = new App();
    $settings->activeTable = "lentec_email_settings";
    $settings->comparisons = [];
    $settings->joiners = [''];
    $settings->order = " BY id DESC ";
    $settings->cols = "section_id, section_title";
    $settings->limit = 2000;
    $settings->offset = 0;

    foreach ($settings->getData() as $key => $value) {
      $this->{$value['section_id']} = $value['section_title'];
    }
    //pick email settings from table

    $body =
      '<html lang="en">
          <head>
              <title>' . $this->email_site_name . '</title>
              <meta charset="utf-8">
              <meta name="viewport" content="width=device-width">
              <style type="text/css">
                  @import url("https://fonts.googleapis.com/css?family=Montserrat&display=swap");
                  * {
                      box-sizing: border-box;
                      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                  }
                  body, table, td, a {
                    -webkit-text-size-adjust: 100%;
                    -ms-text-size-adjust: 100%;
                    font-family:montserrat;
                  }
                  
                  table, td { 
                    mso-table-lspace: 0pt;
                    mso-table-rspace:0pt;
                  }

                  
                  table, td{
                    color: inherit!important; 
                  }
        
                img { 
                  -ms-interpolation-mode: bicubic;
                }
                
                body { 
                  height: 100vh !important;
                  margin: 0;
                  padding: 0;
                  width: 100% !important;
                }
                
                img { 
                  border: 0;
                  height: auto;
                  line-height: 100%;
                  outline: none;
                  text-decoration: none;
                }
                
                table {
                  border-collapse: collapse !important;
                }

                
                
                .apple-links a { 
                  text-decoration: none;
                }

                
                
                @media screen and (max-width: 600px) {

                  td[class="logo"] img {
                    margin: 0 auto !important;
                  }

                  table[class="wrapper"] {
                    width: 100% !important;
                  } 

                  td[class="mobile-image-pad"] {
                    padding: 0 10px 0 10px !important;
                  }
                  
                  td[class="mobile-title-pad"] {
                    padding: 35px 20px 0px 20px !important;
                  }

                  td[class="mobile-text-pad"] {
                    padding: 20px!important;
                  }

                  td[class="mobile-column-right"] {
                    padding-top: 20px !important;
                  }

                  img[class="fluid-image"] {
                    width: 100% !important;
                    height: auto !important;
                  }

                  td[class="hide"] {
                    display: none !important;
                  } 

                  td[class="mobile-button"] {
                    padding: 12px 60px 12px 60px !important;
                  }
                
                  td[class="mobile-button"] a {
                    font-size: 24px !important;
                  }
                }
          
              </style>
          </head>
          <body style="margin: 0; padding: 0 1%; background:' . $this->email_entire_page_background . '!important;">
              <!-- CONTAINER TABLE -->
              <table border="0" cellpadding="0" cellspacing="0"  style="table-layout: fixed; max-width:98%;margin:auto;">
                <tr>
                  <td align="center"  style="padding: 0;">
                    <!-- WRAPPER TABLE -->
                    <table border="0" cellpadding="0" cellspacing="0" width="600" class="wrapper">
                      <tr>
                        <td>
                          <table border="0" cellpadding="0" cellspacing="0"  style="width:100%;">
                            <tr>
                              <td align="center" style="padding: 20px 5px 20px 5px; background:' . $this->email_heading_background . '!important;" class="mobile-title-pad">
                                <img src="' . scheme . $_SERVER['SERVER_NAME'] . base_path . $this->email_company_logo . '" style="margin-bottom:30px!important; width:100px; margin:auto;" />
                              </td>
                            </tr>

                            <tr  bgcolor="' . $this->email_body_background . '">
                              <td align="center" style="padding: 20px; padding-top:30px; color: ' . $this->email_heading_color . '!important;  font-size: 32px; line-height: 36px;text-align:left;" class="mobile-title-pad">
                                ' . $this->email_subject . '
                              </td>
                            </tr>
                            <tr  bgcolor="' . $this->email_body_background . '">
                              <td align="center" style="color: ' . $this->email_body_color . '!important;  padding: 20px; text-align:left;" class="mobile-text-pad">
                                <div style="color: ' . $this->email_body_color . '!important;">' . $this->email_message . '</div>
                               
                              </td>
                            </tr>
                            
                            <tr style="padding-bottom:100px;"  bgcolor="' . $this->email_body_background . '">
                              <td align="center" style="padding: 10px 0 10px 0;">
                                <!-- TABLE-BASED BUTTON -->
                                <table border="0" cellpadding="0" cellspacing="0"  width="100%">
                                  <tr>
                                    <td align="center" style="padding: 20px 0 0 0;">
                                      <table border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td bgcolor="' . $this->email_button_background . '" style="padding: 12px 18px 12px 18px; -webkit-border-radius:3px; border-radius:3px; " align="center" class="mobile-button"><a href="' . scheme . $_SERVER['SERVER_NAME'] . base_path . '" style="color: ' . $this->email_button_color . '!important; text-decoration: none;margin-bottom:30px;!important;"><span style="color: ' . $this->email_button_color . '!important">Visit webite  &rarr;</span></a></td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                             <tr style="width:100%;text-align:center; padding:10px; background:' . $this->email_footer_background . '!important;color:' . $this->email_footer_color . '!important; margin-top:80px!important; padding-top:50px;">
                                <td align="center" style="padding: 10px 0 10px 0; color:' . $this->email_footer_color . '!important; font-size: 14px; line-height: 18px;" class="mobile-text-pad">
                                  If you received this email by mistake, please ignore.<br><br>Need any help? <a href="tel:' . $this->email_phone . '" style="color:' . $this->email_link_color . '!important; text-decoration: none;">Call Us</a> here.
                                </td>
                              </tr>
                              <tr style="width:100%;padding:10px; text-align:center;background:' . $this->email_footer_background . '!important;">
                                <td align="center" style="padding: 10px 0 10px 0; color: ' . $this->email_footer_color . '!important; font-family: Arial, sans-serif; font-weight: normal; font-size: 14px; line-height: 18px;" class="mobile-text-pad">
                                  <span class="apple-links" style="color: ' . $this->email_footer_color . '!important; text-decoration: none;">All rights reserved <br>&copy; ' . $this->email_site_name . ', ' . $this->email_company_location . ' </span>
                                </td>
                              </tr>

                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>  
          </body>
      </html>';

    if (empty($this->email_to)) {
      return ($body);
    }

    //required files
    require_once __path__ . 'plugins/emails/Exception.php';
    require_once __path__ . 'plugins/emails/PHPMailer.php';
    require_once __path__ . 'plugins/emails/SMTP.php';
    // create object of PHPMailer class with boolean parameter which sets/unsets exception.


    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
      $mail->isSMTP(); // using SMTP protocol
      $mail->Host = $this->email_domain; // SMTP host as gmail
      $mail->SMTPAuth = $this->email_SMTPAuth;  // enable smtp authentication
      $mail->Username = $this->email_mail;  // sender gmail host
      $mail->Password = $this->email_password; // sender gmail host password
      $mail->SMTPSecure = $this->email_SMTPSecure;  // for encrypted connection
      $mail->Port = $this->email_port;   // port for SMTP

      $mail->setFrom($this->email_mail, $this->email_site_name); // sender's email and name
      $mail->addAddress($this->email_to, $this->email_username);  // receiver's email and name
      if ($this->email_attachment) {
        $dest_arr = explode(".", $this->email_attachment);
        $dest = uniqid() . end($dest_arr);
        $mail->addAttachment($this->email_attachment, $dest);
      }

      $mail->Subject = $this->email_subject;
      $mail->Body    = $body;
      $mail->IsHtml($this->IsHtml);
      foreach ($this->email_cc as $key) {
        $mail->AddBCC($key);
      }

      $mail->send();
      $this->email_error = false;
      return $this->email_success;
    } catch (Exception $e) {
      // handle error.
      $this->email_error = $mail->ErrorInfo;
      return false;
    }
  }

  public function clean_string($value = '', $remove = true)
  {
    if ($remove) {
      //remove html entities
      $value = preg_replace("/&#?[a-z0-9]+;/i", " ", strip_tags($value));
      $value = preg_replace("/[^A-Za-z0-9-_., ]/", " ", $value);
    }
    return  preg_replace('/\s+/', ' ', $value);
  }





















  // non keywords

  public $replace = [
    "scifi" => "sci-fi",
  ];

  //phrases to remove no matter the search cycle
  public $main_removals = [
    "movies",
  ];


  //match_against removals
  public $match_removals = [
    "the",
    "from",
    "where",
    "when",
    "what",
    "these",
    "will",
    "about",
    "are",
    "for",
    "how",
    "that",
    "this",
    "was",
    "who",
    "www",
    "und",
    "with",
    "and",
    "movies"
  ];


  public function match_set($text = '')
  {
    /* -> remove multiple spaces and cleaning search string*/
    $text2 = $text = $this->clean_string(strtolower(trim(
      preg_replace("/[^A-Za-z0-9 ]/", " ", $text)
    )));
    /* -> if text is empty return empty array */
    if (empty($text) || $text == " ") {
      return false;
    }

    /* replace correctly spelled words*/
    foreach ($this->replace as $key => $val) {
      $text2 = str_replace($key, $val, $text2);
    }

    /* remove non keyword words*/
    $explode = array_unique(explode(" ", $text2));
    $arr = [];
    foreach ($explode as $val) {
      if (!in_array($val, $this->match_removals)) {
        array_push($arr, $val);
      }
    }


    //escape all the values
    $conn = $this->database;
    $escaped = array_map(function ($a) use ($conn) {
      return "+" . ($conn->real_escape_string(str_replace("_", " ", $a))) . "*";
    }, array_filter($arr, function ($a) {
      /*a minimum three letters to be searched*/
      if (strlen($a) > 2 && !is_numeric($a)) {
        return $a;
      }
    }));

    if (count($escaped) == 0) {
      return false;;
    }

    /*only 32 words can be searched*/
    $escaped = array_slice($escaped, 0, 32);
    //   print_r($escaped);
    return implode("", $escaped);
  }




  // searching movies
  public function search_filter()
  {

    $text = $this->search_text;
    $cols = $this->cols;
    $match_cols = $this->match_cols;
    $table = $this->activeTable;
    /* -> remove multiple spaces and cleaning search string*/
    $text = $this->clean_string(strtolower(trim(
      preg_replace("/[^A-Za-z0-9. ]/", " ", (htmlspecialchars($original_text = $text)))
    )));
    /* -> if text is empty return empty array */
    if (empty($text) || $text == " ") {
      return [];
    }

    $this->use_database();

    /*what exactly the user has typed*/
    $exact = $this->database->real_escape_string($text);
    /*string to perform search on*/

    $implode2 = $this->match_set($text);
    if (!$implode2) {
      //basic search doesnt apply
      return [];
    }

    // IN BOOLEAN MODE
    //shallow search
    $query = "SELECT {$cols} FROM {$table} WHERE MATCH ({$match_cols}) AGAINST ( '{$implode2}' ) {$this->extra_filter} ORDER BY   id desc LIMIT 50";


    // $query;  

    /*if query doesnt execute return empty*/
    if (!($res = $this->database->query($query))) {
      $this->release_database();
      return [];
    }
    /*fetch all results*/
    $results = $res->fetch_all(MYSQLI_ASSOC);
    $this->release_database();
    return $results;
  }



  //login functions
  public function csrf()
  {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'] || (isset($_POST['honey_pot']) && !empty($_POST['honey_pot']))) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      die("form.find('.alert').remove(); form.prepend(`
			<div class='alert alert-danger p3'>
					<p>Request Denied</p>
          </div>
		  `); $(`input[name='csrf_token']`).val(`" . $_SESSION['csrf_token'] . "`);");
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  //admin session
  public function admin_check()
  {
    if (!isset($_SESSION['admin_edits'])) {
      die("form.find('.alert').remove(); form.prepend(`
			<div class='alert alert-danger p3'>
					<p>Request Denied</p>
          </div>
		  `); $(`input[name='csrf_token']`).val(`" . $_SESSION['csrf_token'] . "`);");
    }
  }

  //reporting to front end from form
  public function report($message = "", $class = "danger")
  {
    die("form.find('.alert').remove(); form.prepend(`
			<div class='alert alert-" . $class . " p3'>
					<p>" . $message . "</p>
          </div>
		  `); $(`input[name='csrf_token']`).val(`" . $_SESSION['csrf_token'] . "`);");
  }


  //search functions
  /*clean string to create trees table*/
  public function innerClean($title)
  {
    return strtolower(trim($this->clean_string(preg_replace("/[^A-Za-z0-9 ]/", " ", $title))));
  }

  public function cleanSearch($term)
  {

    $terms = explode(" ", strtolower($this->innerClean($term)));

    $required = "+" . implode("* +", array_filter(array_unique(array_diff(
      $terms,
      [
        'a',
        'all',
        'almost',
        'also',
        'although',
        'an',
        'and',
        'any',
        'are',
        'as',
        'at',
        'be',
        'because',
        'been',
        'both',
        'but',
        'by',
        'can',
        'could',
        'd',
        'did',
        'do',
        'does',
        'either',
        'for',
        'from',
        'had',
        'has',
        'have',
        'having',
        'he',
        'her',
        'here',
        'hers',
        'him',
        'his',
        'how',
        'however',
        'i',
        'if',
        'in',
        'into',
        'is',
        'it',
        'its',
        'just',
        'll',
        'me',
        'might',
        'Mr',
        'Mrs',
        'Ms',
        'my',
        'no',
        'non',
        'nor',
        'not',
        'of',
        'on',
        'one',
        'only',
        'onto',
        'or',
        'our',
        'ours',
        's',
        'shall',
        'she',
        'should',
        'since',
        'so',
        'some',
        'still',
        'such',
        't',
        'than',
        'that',
        'the',
        'their',
        'them',
        'then',
        'there',
        'therefore',
        'these',
        'they',
        'this',
        'those',
        'though',
        'through',
        'thus',
        'to',
        'too',
        'until',
        've',
        'very',
        'was',
        'we',
        'were',
        'what',
        'when',
        'where',
        'wheth',
        'which',
        'while',
        'who',
        'whose',
        'why',
        'will',
        'with',
        'would',
        'yet',
        'you',
        'your',
        'yours',
      ]
    )), function ($a) {
      if (strlen($a) > 2) {
        return $a;
      }
    })) . "*";

    $replace = [
      "scifi" => "sci-fi",
    ];

    foreach ($replace as $key => $word) {
      $required = str_replace($key, $word, $required);
    }
    return ($required);
  }

  function search($table, $cols, $match, $original_term, $order, $limit = 100)
  {
    $term = $this->cleanSearch($original_term);
    $session = __FUNCTION__ . $table . $match;
    $session_results = __FUNCTION__ . $table . $match . '_results';
    if (isset($_SESSION[$session_results]) && isset($_SESSION[$session]) && $_SESSION[$session] == $term) {
      return $_SESSION[$session_results];
    }

    if (empty(str_replace(" ", "", str_replace("*", "", str_replace("+", "", $term))))) {
      //lets do exact search
      return $this->exactPattern($table, $cols, $match, $original_term, $order, $limit);
    }
    $this->use_database();
    $query = "SELECT {$cols} FROM {$table} WHERE MATCH ({$match}) AGAINST('{$term}' IN BOOLEAN MODE)  {$this->sql_extra} ORDER BY {$order} LIMIT {$limit}";
    $data = $this->database->query($query)->fetch_all(MYSQLI_ASSOC);
    $this->release_database();
    // cache results 
    $_SESSION[$session_results] = $data;
    $_SESSION[$session] = $term;
    return $data;
  }





  function exactPattern($table, $cols, $match, $original_term, $order, $limit = 100)
  {
    $term = $this->innerClean($original_term);
    $session = __FUNCTION__ . $table . $match;
    $session_results = __FUNCTION__ . $table . $match . '_results';
    if (isset($_SESSION[$session_results]) && isset($_SESSION[$session]) && $_SESSION[$session] == $term) {
      return $_SESSION[$session_results];
    }
    if (empty(str_replace(" ", "", str_replace("*", "", str_replace("+", "", $term))))) {
      //lets do exact search
      return [];
    }
    $this->use_database();
    $new_term = '"' . $term . '"';
    $query = "SELECT {$cols} FROM {$table} WHERE MATCH ({$match}) AGAINST('{$new_term}' IN BOOLEAN MODE)  {$this->sql_extra} ORDER BY {$order} LIMIT {$limit}";
    $data = $this->database->query($query)->fetch_all(MYSQLI_ASSOC);
    $this->release_database();
    // cache results 
    $_SESSION[$session_results] = $data;
    $_SESSION[$session] = $term;
    return $data;
  }


  public function exactSearch($table, $cols, $match, $term, $order, $limit = 100)
  {
    $term = strip_tags($term);
    $session = __FUNCTION__ . $table . $match;
    $session_results = __FUNCTION__ . $table . $match . '_results';
    if (isset($_SESSION[$session_results]) && isset($_SESSION[$session]) && $_SESSION[$session] == $term) {
      return $_SESSION[$session_results];
    } else {
      $_SESSION[$session] = $term;
      $this->activeTable = $table;
      $this->comparisons = $this->sql_extra === "" ? [[$match, ' = ', $term]] :  [[$match, ' = ', $term], ['is_suspended', ' = ', '']];
      $this->joiners = $this->sql_extra === "" ? [''] : ['', ' && '];
      $this->order = $order;
      $this->cols = $cols;
      $this->limit = $limit;
      $this->offset =  0;
      /*page*/
      return  $_SESSION[$session_results] = $this->getData();
    }
  }


  //displaying registred users
  public function displayUsers($rows)
  {
    if (count($rows) === 0) {
      return "";
    }
    $string = "";

    foreach ($rows as $row) {
      $string .= '
		<button class="main_div">
		  <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
			<path style="fill:#2EE6E6;" d="M256,512C114.837,512,0,397.163,0,256S114.837,0,256,0s256,114.837,256,256S397.163,512,256,512z" />
			<path style="fill:#26BFBF;" d="M256.055,0.001v511.998C397.193,511.969,512,397.145,512,256S397.193,0.031,256.055,0.001z" />
			<path style="fill:#FFAA00;" d="M256,512c-65.737,0-129.113-25.38-177.014-71.192C101.181,360.756,173.838,306.087,256,306.087 c82.202,0,154.901,54.615,177.122,134.588C385.279,486.577,321.869,512,256,512z" />
			<path style="fill:#F28D00;" d="M256.055,511.998c65.849-0.013,129.238-25.433,177.067-71.322 c-22.217-79.955-94.891-134.561-177.067-134.586V511.998z" />
			<path style="fill:#FFEECC;" d="M256,339.478c-64.445,0-116.87-52.424-116.87-116.87s52.424-116.87,116.87-116.87 s116.87,52.424,116.87,116.87S320.445,339.478,256,339.478z" />
			<path style="fill:#FFD9B3;" d="M256.055,105.741v233.734c64.42-0.029,116.815-52.439,116.815-116.866 S320.474,105.771,256.055,105.741z" />
		  </svg>
		  <p>' . $row['name'] . '</p>
		  <p>' . $row['email'] . '</p>
		  <p>' . ($row['verify'] === "verified" ? "Verified" : "Not Verified") . '</p>
		  <p>
		  <form action="' . base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF'])) . '" method="post" enctype="multipart/form-data" class="ajax">
			<input type="hidden" value="' . csrf_token . '" name="csrf_token">
			<input type="hidden" value="' . $row['id'] . '" name="id">
			<input class="button" type="submit" name="edit" value="' . $row['is_admin'] . '" data-help_text="Submit your details here.">
		  </form>
		  </p>
		  <p>

		  <form action="' . base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF'])) . '" method="post" enctype="multipart/form-data" class="ajax">
			<input type="hidden" value="' . csrf_token . '" name="csrf_token">
			<input type="hidden" value="' . $row['id'] . '" name="id">
			<input class="button" type="submit" name="edit" value="' . $row['is_suspended'] . '" data-help_text="Submit your details here.">
		  </form>
		  </p>

       <p>
		  <form action="' . base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF'])) . '" method="post" enctype="multipart/form-data" class="ajax">
			<input type="hidden" value="' . csrf_token . '" name="csrf_token">
			<input type="hidden" value="' . $row['id'] . '" name="delete">
      <span onclick="if(confirm(`Delete this user?`)){$(this).parents(`form`).trigger(`submit`);}" class="button">Delete</span>
		  </form>
		  </p>
	
	  </button>
	';
    }

    return $string;
  }



  public function track_ip()
  {
    //regulate non sign up users to sign up
    if (!isset($_SESSION['email']) && !isset($_SESSION['over'])) {
      $element = new Element();
      $ip = $_SERVER['HTTP_CLIENT_IP'] ?: ($_SERVER['HTTP_X_FORWARDED_FOR']  ?: $_SERVER['REMOTE_ADDR']);
      $date = date("Y-m-d");
      $element->activeTable = "lentec_iptracker";
      if (!isset($_SESSION['ipcounter'])) {
        $element->joiners = ['', ' && '];
        $element->comparisons = [['_ip', ' = ', $ip], ['_date', ' = ', $date]];
        $element->cols = '_times';
        $element->limit = 1;
        $data = $element->getData();
        if (count($data) === 0) {
          $_SESSION['ipcounter'] = 0;
        } else {
          $_SESSION['ipcounter'] = $data[0]['_times'];
        }
      }

      $_SESSION['ipcounter']++;
      if ($_SESSION['ipcounter'] >= 20) {
        $_SESSION['over'] = 'SET';
      }

      $element->insertData = [
        [
          '_date' => $date,
          '_ip' => $ip,
          '_times' => $_SESSION['ipcounter'],
        ]
      ];
      $element->saveData('_times');
    }
    if (isset($_SESSION['over']) && !in_array(current_page_table, dis_allowed)) {
      header('location:' . base_path . 'sign_in');
      die("");
    }
  }
}



function error_handler($level = "", $message = "", $file = "", $line = "", $context = "")
{
}
set_error_handler('error_handler');




define('base_path', str_replace(basename(realpath(".")), "", str_replace($_SERVER['DOCUMENT_ROOT'], "", str_replace("\\", "/", realpath(".")))));
// define('base_path', '/');
define("scheme", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://");
define('isAjax', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
define('isIframe', (isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe') ? true : false);
define('isBot', preg_match('/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl|facebookexternalhit/i', $_SERVER['HTTP_USER_AGENT']));


$ogurl = scheme . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
define('og_url', $ogurl);
define('ogurl', $ogurl);
$link = base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));
foreach ($_GET as $key => $val) {
  if ($key != 'edit') {
    $link .= "/" . $key . "/" . $val;
  }
}
define('close_edit', $link);

// lets get page table data 
//current page table
define('current_page_table', str_replace(".php", "", basename($_SERVER['PHP_SELF'])));
//scrf token


if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] =  bin2hex(random_bytes(32));
}

define('csrf_token', $_SESSION['csrf_token']);

define('dis_allowed', [
  'sign_in',
  'sign_up',
  'verify_account',
  'reset_password',
  'change_password',
]);
define('_404', '
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500"><g id="freepik--404--inject-14"><path d="M147.68,287.64H86.83V260.17l60.85-72.34H176.8v73.9h15.09v25.91H176.8v22.48H147.68Zm0-25.91V223.89l-32.16,37.84Z" style="fill:#407BFF"></path><path d="M202.3,249.51q0-34.29,12.34-48t37.61-13.7q12.13,0,19.93,3a36.79,36.79,0,0,1,12.71,7.79,41.59,41.59,0,0,1,7.75,10.09,52.38,52.38,0,0,1,4.55,12.34,115.36,115.36,0,0,1,3.36,28q0,32.72-11.07,47.89t-38.13,15.18q-15.18,0-24.53-4.84a39.76,39.76,0,0,1-15.33-14.19q-4.35-6.64-6.77-18.17A124.33,124.33,0,0,1,202.3,249.51Zm33.14.08q0,23,4.05,31.37t11.77,8.41a12.34,12.34,0,0,0,8.82-3.57q3.74-3.57,5.5-11.28t1.76-24q0-23.94-4.06-32.19t-12.18-8.24q-8.28,0-12,8.41T235.44,249.59Z" style="fill:#407BFF"></path><path d="M371.74,287.64H310.89V260.17l60.85-72.34h29.12v73.9H416v25.91H400.86v22.48H371.74Zm0-25.91V223.89l-32.15,37.84Z" style="fill:#407BFF"></path></g><g id="freepik--Planets--inject-14"><g style="opacity:0.30000000000000004"><path d="M201,145.62a1.87,1.87,0,1,1-1.86-1.87A1.86,1.86,0,0,1,201,145.62Z" style="fill:#407BFF"></path><circle cx="72.97" cy="216.13" r="1.32" style="fill:#407BFF"></circle><circle cx="291.05" cy="408.33" r="1.89" style="fill:#407BFF"></circle><circle cx="336.5" cy="332" r="1.32" style="fill:#407BFF"></circle><path d="M424.17,95.62a1.32,1.32,0,1,1-1.32-1.32A1.32,1.32,0,0,1,424.17,95.62Z" style="fill:#407BFF"></path><path d="M172.75,69a1.32,1.32,0,1,1-1.32-1.32A1.33,1.33,0,0,1,172.75,69Z" style="fill:#407BFF"></path><circle cx="277.7" cy="136.94" r="1.32" style="fill:#407BFF"></circle></g><circle cx="141.23" cy="116.36" r="21.91" style="fill:#407BFF"></circle><circle cx="141.23" cy="116.36" r="21.91" style="fill:#fff;opacity:0.7000000000000001"></circle><path d="M133.68,99.83A21.84,21.84,0,0,0,125,101.6a21.92,21.92,0,0,0,24.87,34.89h0a21.92,21.92,0,0,0-16.23-36.65Z" style="fill:#407BFF;opacity:0.2"></path><path d="M131.5,105.62a2,2,0,1,1-2-2A2,2,0,0,1,131.5,105.62Z" style="fill:#407BFF;opacity:0.2"></path><path d="M155.06,103.62a2,2,0,1,1-2-2A2,2,0,0,1,155.06,103.62Z" style="fill:#407BFF;opacity:0.2"></path><path d="M151.06,117.9a3.28,3.28,0,1,1-3.28-3.28A3.28,3.28,0,0,1,151.06,117.9Z" style="fill:#407BFF;opacity:0.2"></path><path d="M140.64,127.25a4.38,4.38,0,1,1-4.38-4.38A4.38,4.38,0,0,1,140.64,127.25Z" style="fill:#407BFF;opacity:0.2"></path><circle cx="382.2" cy="376.25" r="19.23" transform="translate(-71.8 661.78) rotate(-76.72)" style="fill:#407BFF"></circle><circle cx="382.2" cy="376.25" r="19.23" transform="translate(-71.8 661.78) rotate(-76.72)" style="fill:#fff;opacity:0.30000000000000004"></circle><path d="M394.33,361.34a19.22,19.22,0,0,0-17.67,33.32,19,19,0,0,0,5.53.82,19.23,19.23,0,0,0,12.14-34.14Z" style="fill:#407BFF;opacity:0.4"></path><path d="M363.83,382c-20.53,9.66-5.22,17.11,23.71,6.71,26.79-9.63,37-21.77,13-18C401.83,375.76,368.28,388.83,363.83,382Z" style="fill:#407BFF"></path></g><g id="freepik--Astronaut--inject-14"><path d="M394.1,187.83C367.21,206,332.4,230,322.79,287.64h-2.05c9.35-57,42.89-81.57,69.79-99.81Z" style="opacity:0.2"></path><path d="M255,368.27c-17,0-33.81-7.67-42-20.19-5.05-7.74-10.92-23.95,6.56-45.58l1.55,1.26c-12.36,15.3-14.64,30.65-6.43,43.23,10,15.3,33.59,23,53.73,17.52,20.63-5.61,33.15-23.55,34.36-49.22,4.13-87.81,50.78-114.86,84.84-134.61,21.17-12.27,36.46-21.13,33.1-39.84-.47-2.59-1.5-4.38-3.17-5.48-4.35-2.87-12.85-.88-22.69,1.41-19.31,4.5-45.75,10.66-61.5-16.13l1.73-1c15,25.53,39.57,19.8,59.32,15.2,10.29-2.39,19.17-4.46,24.24-1.13,2.15,1.41,3.47,3.64,4,6.8,3.61,20.08-13,29.72-34.05,41.92-33.67,19.52-79.77,46.25-83.85,133-1.26,26.6-14.32,45.21-35.84,51.06A52.88,52.88,0,0,1,255,368.27Z" style="fill:#407BFF"></path><path d="M255,368.27c-17,0-33.81-7.67-42-20.19-5.05-7.74-10.92-23.95,6.56-45.58l1.55,1.26c-12.36,15.3-14.64,30.65-6.43,43.23,10,15.3,33.59,23,53.73,17.52,20.63-5.61,33.15-23.55,34.36-49.22,4.13-87.81,50.78-114.86,84.84-134.61,21.17-12.27,36.46-21.13,33.1-39.84-.47-2.59-1.5-4.38-3.17-5.48-4.35-2.87-12.85-.88-22.69,1.41-19.31,4.5-45.75,10.66-61.5-16.13l1.73-1c15,25.53,39.57,19.8,59.32,15.2,10.29-2.39,19.17-4.46,24.24-1.13,2.15,1.41,3.47,3.64,4,6.8,3.61,20.08-13,29.72-34.05,41.92-33.67,19.52-79.77,46.25-83.85,133-1.26,26.6-14.32,45.21-35.84,51.06A52.88,52.88,0,0,1,255,368.27Z" style="fill:#fff;opacity:0.2"></path><path d="M312.76,97a46.05,46.05,0,0,1,13.58,2.13s11,18.77,12.3,23.07c-.46,4.24-7.61,11.19-7.61,11.19Z" style="fill:#407BFF"></path><path d="M312.76,97a46.05,46.05,0,0,1,13.58,2.13s11,18.77,12.3,23.07c-.46,4.24-7.61,11.19-7.61,11.19Z" style="fill:#fff;opacity:0.30000000000000004"></path><path d="M345.34,188.13a141.41,141.41,0,0,1-11.56-16.38q-1.26-2.17-2.39-4.42c-.43-.85-.84-1.7-1.24-2.56a10.76,10.76,0,0,1-1.21-2.69c-1.2-12.67,3.14-22-1-32.17l-16.48,6.44s1.4,18.12,4.6,29c2,6.73,6.48,12.55,10.81,17.94,1.35,1.68,2.65,3.41,4,5.1s2.71,3.06,4,4.65c1.95,2.41,2.59,4.72,1.12,7.56l-.25.45c-.42.74,1.54,1.58,2.78,0,2-2.58,1.72-2.42,3.46-4.62,1.06-1.33,2.27-2.78,3.32-4A3.37,3.37,0,0,0,345.34,188.13Z" style="fill:#407BFF"></path><path d="M345.34,188.13a141.41,141.41,0,0,1-11.56-16.38q-1.26-2.17-2.39-4.42c-.43-.85-.84-1.7-1.24-2.56a10.76,10.76,0,0,1-1.21-2.69c-1.2-12.67,3.14-22-1-32.17l-16.48,6.44s1.4,18.12,4.6,29c2,6.73,6.48,12.55,10.81,17.94,1.35,1.68,2.65,3.41,4,5.1s2.71,3.06,4,4.65c1.95,2.41,2.59,4.72,1.12,7.56l-.25.45c-.42.74,1.54,1.58,2.78,0,2-2.58,1.72-2.42,3.46-4.62,1.06-1.33,2.27-2.78,3.32-4A3.37,3.37,0,0,0,345.34,188.13Z" style="fill:#fff;opacity:0.7000000000000001"></path><path d="M341.31,182.92a54.69,54.69,0,0,1-8.66,7.52c.43.48.85,1,1.28,1.46a43.92,43.92,0,0,0,8.5-7.51Z" style="fill:#407BFF;opacity:0.30000000000000004"></path><path d="M345.34,188.13l-.12-.14a5.18,5.18,0,0,0-1.27,3.17,5,5,0,0,0,.38,2.35l.95-1.13A3.37,3.37,0,0,0,345.34,188.13Z" style="fill:#407BFF;opacity:0.30000000000000004"></path><path d="M308.84,109a35.38,35.38,0,0,1-6.37,7.19,23.27,23.27,0,0,1-4.42,3,19,19,0,0,1-2.58,1.09l-.68.22-.22.06-.47.13a5.93,5.93,0,0,1-.88.14,7.55,7.55,0,0,1-2.51-.23,12.24,12.24,0,0,1-2.94-1.27,25,25,0,0,1-2.15-1.41,40.31,40.31,0,0,1-3.58-3,53.16,53.16,0,0,1-6-6.74,2.51,2.51,0,0,1,3.35-3.62l.08,0c2.36,1.5,4.74,3.08,7.06,4.49,1.18.69,2.32,1.39,3.45,1.93a15.29,15.29,0,0,0,1.59.72,3.12,3.12,0,0,0,1.07.26c.06,0,0-.07-.37-.06a2.93,2.93,0,0,0-.35,0l-.22.05,0,0,.33-.17a13.53,13.53,0,0,0,1.29-.79,18.4,18.4,0,0,0,2.5-2.12,63.62,63.62,0,0,0,4.9-5.79l0,0a5,5,0,0,1,8,5.93Z" style="fill:#407BFF"></path><path d="M308.84,109a35.38,35.38,0,0,1-6.37,7.19,23.27,23.27,0,0,1-4.42,3,19,19,0,0,1-2.58,1.09l-.68.22-.22.06-.47.13a5.93,5.93,0,0,1-.88.14,7.55,7.55,0,0,1-2.51-.23,12.24,12.24,0,0,1-2.94-1.27,25,25,0,0,1-2.15-1.41,40.31,40.31,0,0,1-3.58-3,53.16,53.16,0,0,1-6-6.74,2.51,2.51,0,0,1,3.35-3.62l.08,0c2.36,1.5,4.74,3.08,7.06,4.49,1.18.69,2.32,1.39,3.45,1.93a15.29,15.29,0,0,0,1.59.72,3.12,3.12,0,0,0,1.07.26c.06,0,0-.07-.37-.06a2.93,2.93,0,0,0-.35,0l-.22.05,0,0,.33-.17a13.53,13.53,0,0,0,1.29-.79,18.4,18.4,0,0,0,2.5-2.12,63.62,63.62,0,0,0,4.9-5.79l0,0a5,5,0,0,1,8,5.93Z" style="fill:#fff;opacity:0.7000000000000001"></path><path d="M272.29,102.42l1.17,2s.89,2.62,2.68,3.1l4.86-1.57-.25-.41h0c-.62-.94-.55-2.77-.34-4.29s-.57-1.57-1.15-1.19a3.82,3.82,0,0,0-.84,1.65,7.77,7.77,0,0,0-.79-.93l-1.48-1.48a1.72,1.72,0,0,0-2.34-.06l-1.2,1.07A1.71,1.71,0,0,0,272.29,102.42Z" style="fill:#407BFF"></path><path d="M272.29,102.42l1.17,2s.89,2.62,2.68,3.1l4.86-1.57-.25-.41h0c-.62-.94-.55-2.77-.34-4.29s-.57-1.57-1.15-1.19a3.82,3.82,0,0,0-.84,1.65,7.77,7.77,0,0,0-.79-.93l-1.48-1.48a1.72,1.72,0,0,0-2.34-.06l-1.2,1.07A1.71,1.71,0,0,0,272.29,102.42Z" style="fill:#fff;opacity:0.7000000000000001"></path><path d="M317.67,95.22a59.64,59.64,0,0,0-15.34,6.47,4.32,4.32,0,0,0-1.94,4.53c1.93,9.44,6.32,22.08,11.06,30.13l22.11-9.15c.15-3.9-5.22-16.52-10.69-28.72C321.89,96.29,320,94.66,317.67,95.22Z" style="fill:#407BFF"></path><path d="M317.67,95.22a59.64,59.64,0,0,0-15.34,6.47,4.32,4.32,0,0,0-1.94,4.53c1.93,9.44,6.32,22.08,11.06,30.13l22.11-9.15c.15-3.9-5.22-16.52-10.69-28.72C321.89,96.29,320,94.66,317.67,95.22Z" style="fill:#fff;opacity:0.8"></path><path d="M326.3,106.21l-4.39-1.47c1,2.57,4.53,5.82,7,7.73C328.11,110.47,327.22,108.37,326.3,106.21Z" style="fill:#407BFF;opacity:0.30000000000000004"></path><path d="M316.22,85.32c-1.83-3.48-5.78-5.23-10.52-4.84-4,.34-7.54,4.42-7.12,6.62S302.36,90.24,303,91l-2.77,2a3,3,0,0,0-.6,4.29c1.17,1.48,2.71,3,3.6,4.12,7.66-.2,13.33-3.12,15.38-5.93C317.84,91.92,318,88.78,316.22,85.32Z" style="fill:#407BFF"></path><path d="M316.22,85.32c-1.83-3.48-5.78-5.23-10.52-4.84-4,.34-7.54,4.42-7.12,6.62S302.36,90.24,303,91l-2.77,2a3,3,0,0,0-.6,4.29c1.17,1.48,2.71,3,3.6,4.12,7.66-.2,13.33-3.12,15.38-5.93C317.84,91.92,318,88.78,316.22,85.32Z" style="fill:#fff;opacity:0.8"></path><path d="M312.46,87.48a7.57,7.57,0,1,1-9.81-4.3A7.58,7.58,0,0,1,312.46,87.48Z" style="fill:#263238"></path><path d="M377.39,177.6c-.11-3.29-.26-3-.35-5.77-.06-1.7-.07-3.59-.08-5.22a3.36,3.36,0,0,0-2.7-3.28c-1.32-.27-2.65-.52-4-.8-1.73-.37-3.44-.77-5.13-1.26-1.32-.38-2.62-.8-3.91-1.27s-2.74-1-4.08-1.62c-1.58-.67-3.14-1.39-4.68-2.14-1.73-.82-3.44-1.68-5.15-2.55-6.58-10.89-6.72-18.07-13.78-26.49l-15.16,6.86s11.14,19.76,18.72,28.14c4.37,4.82,11.22,7,17.33,8.58,4.41,1.13,8.88,2,13.35,2.83,1.74.32,3.63.44,5.13,1.48a5.74,5.74,0,0,1,2.14,3.45q.1.42.18.84C375.41,180.22,377.46,179.58,377.39,177.6Z" style="fill:#407BFF"></path><path d="M377.39,177.6c-.11-3.29-.26-3-.35-5.77-.06-1.7-.07-3.59-.08-5.22a3.36,3.36,0,0,0-2.7-3.28c-1.32-.27-2.65-.52-4-.8-1.73-.37-3.44-.77-5.13-1.26-1.32-.38-2.62-.8-3.91-1.27s-2.74-1-4.08-1.62c-1.58-.67-3.14-1.39-4.68-2.14-1.73-.82-3.44-1.68-5.15-2.55-6.58-10.89-6.72-18.07-13.78-26.49l-15.16,6.86s11.14,19.76,18.72,28.14c4.37,4.82,11.22,7,17.33,8.58,4.41,1.13,8.88,2,13.35,2.83,1.74.32,3.63.44,5.13,1.48a5.74,5.74,0,0,1,2.14,3.45q.1.42.18.84C375.41,180.22,377.46,179.58,377.39,177.6Z" style="fill:#fff;opacity:0.8"></path><path d="M369.7,162.4c-.6-.13-1.2-.26-1.81-.41.05,3.46-1.57,9.42-2.16,11.23l1.9.36A38.11,38.11,0,0,0,369.7,162.4Z" style="fill:#407BFF;opacity:0.30000000000000004"></path><path d="M377,166.61a3.36,3.36,0,0,0-2.69-3.28l-1-.19a4.58,4.58,0,0,0,1.63,2.9,5.09,5.09,0,0,0,2,1.14C377,167,377,166.8,377,166.61Z" style="fill:#407BFF;opacity:0.30000000000000004"></path><path d="M311.05,87.54c.4,1.52-1.3,3.11-2.65,1.8a30.83,30.83,0,0,0-4.12-3.69c-1.39-.87.46-2.39,2.65-1.8A5.94,5.94,0,0,1,311.05,87.54Z" style="fill:#fff"></path><path d="M311.16,135.86c-.7.26.58,1.46.58,1.46s14-4.79,22.5-9.72a1.88,1.88,0,0,0-.68-1.58A216,216,0,0,1,311.16,135.86Z" style="fill:#407BFF"></path><path d="M311.16,135.86c-.7.26.58,1.46.58,1.46s14-4.79,22.5-9.72a1.88,1.88,0,0,0-.68-1.58A216,216,0,0,1,311.16,135.86Z" style="fill:#fff;opacity:0.5"></path><path d="M321.46,94.56c2.76,1.4,5.35,2.87,8,4.5,1.29.82,2.57,1.65,3.84,2.55s2.53,1.82,3.8,2.86l.47.39.59.54a12.74,12.74,0,0,1,1,1c.32.35.59.69.85,1s.54.68.77,1a43.8,43.8,0,0,1,2.58,4,59.05,59.05,0,0,1,4,8.35,2.52,2.52,0,0,1-4.19,2.62l-.05-.06c-2-2.13-3.93-4.37-5.87-6.46s-3.91-4.21-5.54-5.14c-2.27-1.41-4.8-2.82-7.31-4.2l-7.56-4.2h0a5,5,0,0,1,4.68-8.84Z" style="fill:#407BFF"></path><path d="M321.46,94.56c2.76,1.4,5.35,2.87,8,4.5,1.29.82,2.57,1.65,3.84,2.55s2.53,1.82,3.8,2.86l.47.39.59.54a12.74,12.74,0,0,1,1,1c.32.35.59.69.85,1s.54.68.77,1a43.8,43.8,0,0,1,2.58,4,59.05,59.05,0,0,1,4,8.35,2.52,2.52,0,0,1-4.19,2.62l-.05-.06c-2-2.13-3.93-4.37-5.87-6.46s-3.91-4.21-5.54-5.14c-2.27-1.41-4.8-2.82-7.31-4.2l-7.56-4.2h0a5,5,0,0,1,4.68-8.84Z" style="fill:#fff;opacity:0.8"></path><path d="M349.73,125.74l-.85-2.13s-.47-2.72-2.16-3.48l-5,.79.17.44h0c.46,1,.11,2.83-.34,4.29s.31,1.65.95,1.36c.36-.16.71-.81,1.09-1.5a8.46,8.46,0,0,0,.63,1l1.23,1.69a1.72,1.72,0,0,0,2.3.44l1.36-.87A1.7,1.7,0,0,0,349.73,125.74Z" style="fill:#407BFF"></path><path d="M349.73,125.74l-.85-2.13s-.47-2.72-2.16-3.48l-5,.79.17.44h0c.46,1,.11,2.83-.34,4.29s.31,1.65.95,1.36c.36-.16.71-.81,1.09-1.5a8.46,8.46,0,0,0,.63,1l1.23,1.69a1.72,1.72,0,0,0,2.3.44l1.36-.87A1.7,1.7,0,0,0,349.73,125.74Z" style="fill:#fff;opacity:0.8"></path><path d="M317.24,106.06l-1.22.1-7.49,18.08a4,4,0,0,0,1.22-.1s9.76-3.64,12.71-5C320.48,115.05,317.24,106.06,317.24,106.06Z" style="fill:#407BFF;opacity:0.30000000000000004"></path><path d="M303.57,110.8a43.41,43.41,0,0,0,5,13.44c3.66-1.26,9.76-3.64,12.72-5A135.36,135.36,0,0,1,316,106.16C312.87,106.37,306,109,303.57,110.8Z" style="fill:#fff"></path><path d="M311,114.71a2.58,2.58,0,1,1-1.73-3.21A2.58,2.58,0,0,1,311,114.71Z" style="fill:#407BFF;opacity:0.30000000000000004"></path><path d="M312.91,111.27a.85.85,0,1,1-.56-1A.84.84,0,0,1,312.91,111.27Z" style="fill:#407BFF;opacity:0.6000000000000001"></path><path d="M315.15,110.4a.85.85,0,1,1-1.62-.49.84.84,0,0,1,1.05-.56A.85.85,0,0,1,315.15,110.4Z" style="fill:#407BFF;opacity:0.6000000000000001"></path><polygon points="318.29 118.19 309.04 121.84 308.49 120.02 317.73 116.37 318.29 118.19" style="fill:#407BFF;opacity:0.5"></polygon></g><g id="freepik--Rocket--inject-14"><path d="M267.26,257.17a94,94,0,0,1-1.68,17.35q-1.77,7.71-5.5,11.28a12.3,12.3,0,0,1-8.81,3.57q-7.71,0-11.77-8.41a23.79,23.79,0,0,1-1.21-3.11,144.31,144.31,0,0,0-15.92,16l-5.31,6.26a38.62,38.62,0,0,0,9.77,7.19q9.34,4.83,24.52,4.84c1.78,0,3.5-.05,5.17-.15a143.39,143.39,0,0,0,15.1-29l14.85-38.72Z" style="opacity:0.2"></path><path d="M133.39,310l17.5,17.5,49-46.17C183,274.88,150.16,293.19,133.39,310Z" style="fill:#263238"></path><path d="M194.42,371c-5.59-5.6-17.5-17.5-17.5-17.5l46.17-49C229.5,321.35,211.19,354.22,194.42,371Z" style="fill:#263238"></path><path d="M261.4,260.7l19.09-36.81L243.68,243a144.22,144.22,0,0,0-32.44,23l-62.75,59.07,30.83,30.83,59.07-62.75A144.22,144.22,0,0,0,261.4,260.7Z" style="fill:#407BFF"></path><path d="M261.4,260.7l19.09-36.81L243.68,243a144.22,144.22,0,0,0-32.44,23l-62.75,59.07,30.83,30.83,59.07-62.75A144.22,144.22,0,0,0,261.4,260.7Z" style="fill:#fff;opacity:0.6000000000000001"></path><circle cx="222.2" cy="282.18" r="12.9" style="fill:#fff"></circle><circle cx="222.2" cy="282.18" r="8.29" style="fill:#407BFF"></circle><polygon points="189.75 344.82 159.56 314.63 184.28 291.37 213.01 320.11 189.75 344.82" style="fill:#407BFF;opacity:0.30000000000000004"></polygon><path d="M140.22,337.62c-22.6,1.83-30.09,16.3-32.65,35.53-1.3,9.81-1.88,19.74-10.11,25.48a2.77,2.77,0,0,0,1.63,5.06c30.34-.95,44.49-15.8,46.27-22a43.06,43.06,0,0,1-2.49,9.47,2.76,2.76,0,0,0,4,3.39c8.51-5.33,19.19-15.15,19.9-31.08C160.51,354.6,140.22,337.62,140.22,337.62Z" style="fill:#407BFF"></path><path d="M140.22,337.62c-22.6,1.83-30.09,16.3-32.65,35.53-1.3,9.81-1.88,19.74-10.11,25.48a2.77,2.77,0,0,0,1.63,5.06c30.34-.95,44.49-15.8,46.27-22a43.06,43.06,0,0,1-2.49,9.47,2.76,2.76,0,0,0,4,3.39c8.51-5.33,19.19-15.15,19.9-31.08C160.51,354.6,140.22,337.62,140.22,337.62Z" style="fill:#fff;opacity:0.2"></path><polygon points="170.28 370.3 134.08 334.1 153.36 329.93 174.45 351.02 170.28 370.3" style="fill:#407BFF"></polygon></g></svg>
');
