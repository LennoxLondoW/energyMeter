<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
require_once '../app/extensions/app.validation.php';
require_once '../app.extensions/app.front.extension.php';
//loging in check
if (!isset($_SESSION['email'])) {
	//do something
	// $_SESSION['logged_page'] = str_replace(".php","",basename($_SERVER['PHP_SELF']));
	// header('location:'.base_path."/sign_in");
	// die();
} else {
	// do something
	// if(!isset($_SESSION['admin_edits'])){
	// 	header('location:' . $page );
	// }
}


///fetching graphical data
// "lentec_entries_monthly_expense" => [
// 	"id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
// 	"_date_ varchar(255) NOT NULL UNIQUE KEY",
// 	"amount double(20,10) NOT NULL"
//   ],
//   "lentec_entries_annual_expense" => [
// 	"id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
// 	"_date_ varchar(255) NOT NULL UNIQUE KEY",
// 	"months_calculated varchar(255) NOT NULL",
// 	"amount double(20,10) NOT NULL"
//    ]


if (isset($_POST['stats'])) {
	$date2 = date_create($_POST['start_date']);
	$date1 = date_create($_POST['end_date']);
	$diff = date_diff($date1, $date2);
	$difference =  abs($diff->format("%R%a"));
	$table = $difference > 366 ? "lentec_entries_annual_expense" : "lentec_entries_monthly_expense";

	if ($table === "lentec_entries_annual_expense") {
		$_POST['start_date'] = date_format($date2, "Y");
		$_POST['end_date'] = date_format($date1, "Y");
		$title = "Annual price analytics";
	} else {
		$_POST['start_date'] = date_format($date2, "Y-m");
		$_POST['end_date'] = date_format($date1, "Y-m");
		$title = "Monthly price analytics";
	}

	$element = new Element();
	$element->activeTable = $table;
	$element->comparisons = [["_date_", " >= ", $_POST['start_date']], ["_date_", " <= ", $_POST['end_date']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY " . ($_POST['chart_type'] === "pyramid" ? "amount DESC" : "_date_ ASC ");
	$element->cols = "_date_  as label, amount * 120 as y";
	$element->limit = 12;
	$element->offset = 0;

	$data = $element->getData();
	$frontEnd->drawPriceCharts($_POST['chart_type'], $data, $title);
	$frontEnd->report("Success", "success");
}


$element = new Element();
$element->activeTable = "lentec_analytics_price";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
