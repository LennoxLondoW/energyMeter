<?php
session_start();

// if(!isset($_SESSION['admin_edits']))
// {
// 	$_SESSION['url']= base_path."migrate";
// 	header('location:'.base_path."admin");
// 	die();
// }
/*main operation file*/
require_once '../app/extensions/app.migration.php';

foreach (glob("*Migration.php") as $key => $value) {
	echo "<p><a href='".base_path."migrations/".$value."'>".explode(".", $value)[0]."</a></p>";
}

?>