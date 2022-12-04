<?php
//current page
if (!in_array(current_page_table, dis_allowed)) {
  $_SESSION['current_page'] = substr($_SERVER['REQUEST_URI'], strlen(base_path));
}
require_once '../app.extensions/app.front.extension.php';
// $frontEnd->track_ip();
if (!isAjax) {
  // get navbar data
  require_once '../controlers/navbarControler.php';
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <!-- dynamics -->
    <title><?php echo $page_title;  ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="keywords" content="<?php echo $page_keywords; ?>" />
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta property="og:site_name" content="<?php echo $site_title; ?>">
    <meta property="og:type" content="<?php echo $og_type; ?>">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:url" content="<?php echo $ogurl; ?>">
    <meta property="og:image" <?php $element->is_editable(current_page_table, 'page_icon', 'image'); ?> content="<?php echo $ICON = (substr($page_icon, 0, 4) !== 'http' ? scheme . $_SERVER['SERVER_NAME'] . base_path . $page_icon : $page_icon); ?>">
    <meta property="og:image:url" <?php $element->is_editable(current_page_table, 'page_icon', 'image'); ?> content="<?php echo $ICON; ?>">
    <meta name="twitter:image" content="<?php echo $ICON; ?>">
    <meta name="twitter:title" content="<?php echo $page_title; ?>">
    <meta name="twitter:text:title" content="<?php echo $page_title; ?>">
    <meta name="twitter:description" content="<?php echo $page_description; ?>">
    <meta property="twitter:card" content="<?php echo $og_type; ?>">

    <!-- app css  -->
    <link href="<?php echo base_path; ?>css/app.css/original.css?v=1.25" rel="stylesheet" type="text/css" media="all" />
    <link <?php $element->is_editable(current_page_table, 'page_icon', 'image'); ?> href="<?php echo $ICON; ?>" rel="icon" />
    <!-- <link href="<?php echo base_path; ?>plugins/apk/manifest.json?v=3" rel="manifest"> -->
    <!-- app css  -->

    <!-- user cusrom css  -->
    <link type="text/css" rel="stylesheet" href="<?php echo base_path; ?>css/style.css?v=3.9" media="all">
    <link rel="stylesheet" href="<?php echo base_path; ?>plugins/date_picker/css/default/zebra_datepicker.min.css" type="text/css">
    <!-- user cusrom css  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />




  </head>

  <body id="header_main" style="width: 100%; overflow-x: hidden;">
    <input type="hidden" id="base_path" name="in1" value="<?php echo base_path; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token; ?>">
    <!-- navbar set -->
    <header class="h">
      <div class="wrapper h">
        <div class="nav-brand">
          <a href="<?php echo base_path . "home" ?>"><img <?php $element->is_editable('navbar', 'site_icon', 'image'); ?> src="<?php echo base_path . $site_icon; ?>" alt="<?php echo $site_title; ?>"></a>
        </div>
        <div class="nav-links h" id="nav-links">
          <ul>
            <li><a href="<?php echo base_path; ?>home">Dashboard</a></li>
            <li class="dropdown">
              <a href="#" onclick="toggle('drop4')">Analytics</a>
              <ul class="drop-down-menu" id="drop4">
                <a href="#" class="text-right w-100 close">&times;</a>
                <li><a href="<?php echo base_path; ?>analytics_all">All analytics</a></li>
                <li><a href="<?php echo base_path; ?>analytics_equipment">Equipment Analytics</a></li>
                <li><a href="<?php echo base_path; ?>analytics_meter">Meter Analytics</a></li>
                <li><a href="<?php echo base_path; ?>analytics_price">Bill Analytics</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" onclick="toggle('drop3')">Data Entry</a>
              <ul class="drop-down-menu" id="drop3">
                <a href="#" class="text-right w-100 close">&times;</a>
                <li><a href="<?php echo base_path; ?>entries_meter">Meter Entries</a></li>
                <li><a href="<?php echo base_path; ?>entries_equpment">Equipment Entries</a></li>
                <li><a href="<?php echo base_path; ?>entries_expense">Expense Entries</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" onclick="toggle('drop1')">Gadget data</a>

              <ul class="drop-down-menu" id="drop1">
                <a href="#" class="text-right w-100 close">&times;</a>
                <li><a href="<?php echo base_path; ?>equipment">Equipment data</a></li>
                <li><a href="<?php echo base_path; ?>meters">Meter data</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" onclick="toggle('drop2')">Account</a>
              <ul class="drop-down-menu" id="drop2">
                <a href="#" class="text-right w-100 close">&times;</a>

                <?php

                if (isset($_SESSION['admin_edits'])) {
                  echo '
                              <li><a href="' . base_path . 'view_users">View Users</a></li>
                              <li><a href="' . base_path . 'admin">CMS</a></li>
                              <li><a class="non_spa" href="' . base_path . 'home/logout/true">Logout</a>';
                } elseif (isset($_SESSION['email'])) {
                  echo ' <li><a class="non_spa" href="' . base_path . 'home/logout/true">Logout</a>';
                } else {
                  echo ' <li><a href="' . base_path . 'sign_in">Sign in</a>';
                }

                ?>
              </ul>
            </li>
          </ul>
        </div>
        <div class="nav-toggler">
          <a href="#" class="text-black dec_none" onclick="toggle('nav-links')">&#x2630;</a>
        </div>
      </div>
    </header>

    <div class="field" id="main_field" style="min-height:88vh; padding-top:10px;">
    <?php
  }


  if ($element->page_editable) {
    echo   '<div class="edit_div">
              <a class="non_spa" href="' . close_edit . '">Close Edit</a>
          </div>';
  } elseif (isset($_SESSION['admin_edits'])) {
    echo   '<div class="edit_div">
              <a class="non_spa" href="' . close_edit . '/edit/true">Edit page</a>
          </div>';
  }
    ?>
    <!-- used by spa  -->
    <input type="hidden" value="<?php echo $page_title;  ?>" id="page_title_holder">
    <input type="hidden" value="<?php echo (substr($page_icon, 0, 4) !== 'http' ? scheme . $_SERVER['SERVER_NAME'] . base_path . $page_icon : $page_icon)  ?>" id="page_icon_holder">
    <input type="hidden" value="<?php echo $page_description;  ?>" id="page_description_holder">
    <!-- used by spa  -->