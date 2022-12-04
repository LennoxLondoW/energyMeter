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


// "lentec_single_equipment_daily" => [
// 	"id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
// 	"unique_id varchar(255) NOT NULL UNIQUE KEY", //has id and date concatinated
// 	"eqipment_name varchar(255) NOT NULL",
// 	"period_date varchar(255) NOT NULL",
// 	"unit_measurement double(20,10) NOT NULL",
// 	"total_hours double(20,10) NOT NULL",
//   ],
//   "lentec_single_equipment_monthly" => [
// 	"id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
// 	"unique_id varchar(255) NOT NULL UNIQUE KEY", //has id and date concatinated
// 	"eqipment_name varchar(255) NOT NULL",
// 	"period_date varchar(255) NOT NULL",
// 	"unit_measurement double(20,10) NOT NULL",
// 	"total_hours double(20,10) NOT NULL",
// 	"days_calculated int(255) NOT NULL",
//   ],
//   "lentec_single_equipment_annually" => [
// 	"id int(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
// 	"unique_id varchar(255) NOT NULL UNIQUE KEY", //has id and date concatinated
// 	"eqipment_name varchar(255) NOT NULL",
// 	"period_date varchar(255) NOT NULL",
// 	"unit_measurement double(20,10) NOT NULL",
// 	"total_hours double(20,10) NOT NULL",
// 	"months_calculated int(255) NOT NULL",



if (isset($_POST['stats'])) {
	$date2 = date_create($_POST['start_date']);
	$date1 = date_create($_POST['end_date']);
	$diff = date_diff($date1, $date2);
	$difference =  abs($diff->format("%r%a"));
	$table = $difference <= 31 ? "lentec_single_equipment_daily" : // daily
		($difference > 366 ? "lentec_single_equipment_annually" : "lentec_single_equipment_monthly");

	if ($table === "lentec_single_equipment_annually") {
		$_POST['start_date'] = date_format($date2, "Y");
		$_POST['end_date'] = date_format($date1, "Y");
		$title = "Annual " . $_POST['eqipment_name'] . " analytics";
		$limit = 10;
	} elseif ($table === "lentec_single_equipment_monthly") {
		$_POST['start_date'] = date_format($date2, "Y-m");
		$_POST['end_date'] = date_format($date1, "Y-m");
		$title = "Monthly " . $_POST['eqipment_name'] . " analytics";
		$limit = 12;
	} else {
		$title = "Daily " . $_POST['eqipment_name'] . " analytics";
		$limit = 366;
	}



	$element = new Element();
	$element->activeTable = $table;
	$element->comparisons = [["period_date", " >= ", $_POST['start_date']], ["period_date", " <= ", $_POST['end_date']], ["eqipment_name", " = ", $_POST['eqipment_name']]];
	$element->joiners = ['', ' && ', ' && '];
	$element->order = " BY " . ($_POST['chart_type'] === "pyramid" ? "unit_measurement DESC" : "period_date ASC ");
	$element->cols = "period_date as label, unit_measurement as y, total_hours as label2";
	$element->limit = $limit;
	$element->offset = 0;
	$data = $element->getData();
	$frontEnd->drawPriceCharts($_POST['chart_type'], array_map(function ($row) {
		$row['label'] .= " (" . round($row['label2'], 1) . " hrs)";
		return $row;
	}, $data), $title, $x = ["Energy Consumed (kW)", ""], $y = ["Date", ""]);

	$frontEnd->report("Success", "success");
}





//fetch meters if not fetched
if (isset($_SESSION['equipment_data'])) {
	$equipment_data = $_SESSION['equipment_data'];
} else {
	$element = new Element();
	$element->activeTable = "lentec_equipment_data";
	$element->comparisons = [];
	$element->joiners = [''];
	$element->order = " BY equipment_name ASC ";
	$element->cols = "equipment_name, equipment_rating, equipment_quantity";
	$element->limit = 1000;
	$element->offset = 0;
	/*get_data*/
	$equipment_data = $_SESSION['equipment_data'] = $element->getData();
}


$element = new Element();
$element->activeTable = "lentec_analytics_equipment";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
