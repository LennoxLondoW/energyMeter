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



if (isset($_POST['stats'])) {
	$date2 = date_create($_POST['start_date']);
	$date1 = date_create($_POST['end_date']);
	$diff = date_diff($date1, $date2);
	$difference =  abs($diff->format("%R%a"));
	if ($difference <= 31) {
		$table1 = "lentec_total_equipment_daily";
		$table2 = "lentec_total_meter_daily";
		$title = "Daily " . $_POST['eqipment_name'] . " analytics";
		$limit = 366;
	} elseif ($difference > 366) {
		$table1 = "lentec_total_equipment_annually";
		$table2 = "lentec_total_meter_annually";
		$_POST['start_date'] = date_format($date2, "Y");
		$_POST['end_date'] = date_format($date1, "Y");
		$title = "Annual " . $_POST['eqipment_name'] . " analytics";
		$limit = 10;
	} else {
		$table1 = "lentec_total_equipment_monthly";
		$table2 = "lentec_total_meter_monthly";
		$_POST['start_date'] = date_format($date2, "Y-m");
		$_POST['end_date'] = date_format($date1, "Y-m");
		$title = "Monthly " . $_POST['eqipment_name'] . " analytics";
		$limit = 12;
	}

	$element = new Element();
	$element->activeTable = $table1;
	$element->comparisons = [["period_date", " >= ", $_POST['start_date']], ["period_date", " <= ", $_POST['end_date']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY " . ($_POST['chart_type'] === "pyramid" ? "unit_measurement DESC" : "period_date ASC ");
	$element->cols = "period_date as label, unit_measurement as y";
	$element->limit = $limit;
	$element->offset = 0;
	$real = $element->getData();

	$element = new Element();
	$element->activeTable = $table2;
	$element->comparisons = [["period_date", " >= ", $_POST['start_date']], ["period_date", " <= ", $_POST['end_date']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY " . ($_POST['chart_type'] === "pyramid" ? "unit_measurement DESC" : "period_date ASC ");
	$element->cols = "period_date as label, unit_measurement as y";
	$element->limit = $limit;
	$element->offset = 0;
	$aparent = $element->getData();

	$total_real = array_sum(array_column($real, "y"));
	$total_apparent = array_sum(array_column($aparent, "y"));
	$difference = abs($total_apparent - $total_real);

	$piechart = [
		["label" => "Meter Readings", "y" => $total_apparent],
		["label" => "Equipment Consumption", "y" => $total_real],
		["label" => "Un-accounted Energy", "y" => $difference],
	];


	echo '
	var options = {
		animationEnabled: true,
		theme: "light2",
		title:{
			text: "Energy Analytics"
		},
		axisX:{
			title: "Date",
		},
		axisY: {
			title: "Enegy Consumed (kw)",
		},
		toolTip:{
			shared:true
		},  
		legend:{
			cursor:"pointer",
			verticalAlign: "bottom",
			horizontalAlign: "left",
			dockInsidePlotArea: true,
			itemclick: toogleDataSeries
		},
		data: [{
			type: "line",
			showInLegend: true,
			name: "Meter Reading",
			markerType: "square",
			color: "#F08080",
			dataPoints: JSON.parse(`' . json_encode($aparent) . '`)
		},
		{
			type: "line",
			showInLegend: true,
			name: "Equipment Consumption",
			lineDashType: "dash",
			markerType: "triangle",
			dataPoints: JSON.parse(`' . json_encode($real) . '`)
		}]
	};
	$("#graph2").CanvasJSChart(options);
	
	';

	$frontEnd->drawPriceCharts("doughnut", $piechart, "Energy Donought ", $x = ["Energy Consumed (kW)", ""], $y = ["Type", ""]);
	$frontEnd->report("Success", "success");
}




$element = new Element();
$element->activeTable = "lentec_analytics_all";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
