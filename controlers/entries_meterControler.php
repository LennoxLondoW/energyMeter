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


define('fields', [
	"meter_serial_number",
	"period_date",
	"unit_measurement",
]);



//function to validate all post fields
function validation()
{
	$validation = new Validation();
	//validation
	foreach (fields as  $col) {
		if (!isset($_POST[$col])) {
			continue;
		}
		if (empty($_POST[$col])) {
			$validation->report("Field '" . ucfirst(str_replace("_", " ", $col)) . "' is required");
		}
	}
}


//fetching previous data
if (isset($_GET['unique_id'])) {
	$element = new Element();
	$element->activeTable = "lentec_single_meter_daily";
	$element->comparisons = [['unique_id', ' = ', md5($_GET['unique_id'])]];
	$element->joiners = [''];
	$element->order = " BY id ASC ";
	$element->cols = "*";
	$element->limit = 1;
	$element->offset = 0;
	/*get_data*/
	$data = $element->getData();
	if (count($data) == 0) {
		$table = '
			<div class="form-group">
				<div class="alert alert-success">
					Add data for this date
				</div>
		</div>
		';
	} else {
		$table = '
		<div class="form-group">
		<label>Update data on this date<label>
			<table>
			<tbody>
				<tr>
				<td>SNO:</td>
				<td>' . $data[0]['meter_serial_number'] . '</td>
				</tr>
				<tr>
				<td>DATE</td>
				<td>' . $data[0]['period_date'] . '</td>
				</tr>

				<tr>
				<td>QTY:</td>
				<td>' . $data[0]['unit_measurement'] . ' kWh</td>
				</tr>
			</tbody>
			</table>
		</div>
		';
	}


	if (isset($_SESSION['admin_edits'])) {
		$table .= '
		<div class="form-group">
			<label for="meter"> Please enter measurements in KWh</label>
			<input name="unit_measurement" type="number" step="0.0000000001" required>
		</div>

		<div class="form-group">
			<input name="ad_unit" type="submit" value="edit equipment" >
		</div>
		';
	} else {
		$table .= '
		<div class="form-group">
			<div class="alert alert-danger">
				Please log in as admin to edit
			</div>
	</div>
	';
	}

	die($table);
}






if (isset($_POST['bypass'])) {
	$validation = new Validation();

	validation();
	$_POST['unique_id'] = md5($_POST['period_date'] . $_POST['meter_serial_number']);
	unset($_POST['bypass']);
	// save data 
	$validation->insertData = [$_POST];
	$validation->activeTable = 'lentec_single_meter_daily';
	$save = $validation->saveData('unit_measurement');
	if ($save != "success") {
		die("alert(`Something is not right, please try again`;");
	}

	//summation on all meters ata
	//daily
	$element = new Element();
	$element->activeTable = "lentec_single_meter_daily";
	$element->comparisons = [["period_date", " = ", $_POST['period_date']]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as number_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_today = $element->getData();
	if (count($all_today) > 0) {
		$all_today[0]['period_date'] = $_POST['period_date'];
		$element->activeTable = 'lentec_total_meter_daily';
		$element->insertData = $all_today;
		$element->saveData(['unit_measurement', 'number_calculated']);
	}

	// update monthly
	$element = new Element();
	$element->activeTable = "lentec_total_meter_daily";
	$arr = explode("-", $_POST['period_date']);
	$element->comparisons = [["period_date", " LIKE  ", $month = implode("-", array_slice($arr, 0, 2)) . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_month = $element->getData();
	if (count($all_month) > 0) {
		$all_month[0]['period_date'] = str_replace("%", "", $month);
		$element->activeTable = 'lentec_total_meter_monthly';
		$element->insertData = $all_month;
		$element->saveData(['unit_measurement', 'days_calculated']);
	}
	//yearly
	$element = new Element();
	$element->activeTable = "lentec_total_meter_monthly";
	$element->comparisons = [["period_date", " LIKE  ", $arr[0] . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_years = $element->getData();
	if (count($all_years) > 0) {
		$all_years[0]['period_date'] = $arr[0];
		$element->activeTable = 'lentec_total_meter_annually';
		$element->insertData = $all_years;
		$element->saveData(['unit_measurement', 'months_calculated']);
	}
	/// summation on all meters data


	/// analyzing individual meters
	// meter data monthly 
	$element = new Element();
	$element->activeTable = "lentec_single_meter_daily";
	$element->comparisons = [["period_date", " LIKE ", ($month = implode("-", array_slice($arr, 0, 2))) . "%"], ['meter_serial_number', ' = ', $_POST['meter_serial_number']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_month = $element->getData();
	if (count($item_single_month) > 0) {
		$item_single_month[0]['period_date'] = $month;
		$item_single_month[0]['meter_serial_number'] = $_POST['meter_serial_number'];
		$item_single_month[0]['unique_id'] = $month . $_POST['meter_serial_number'];
		$element->activeTable = 'lentec_single_meter_monthly';
		$element->insertData = $item_single_month;
		$element->saveData(['unit_measurement', 'days_calculated']);
	}

	// meter data annually 
	$element = new Element();
	$element->activeTable = "lentec_single_meter_monthly";
	$element->comparisons = [["period_date", " LIKE ", $arr[0] . "%"], ['meter_serial_number', ' = ', $_POST['meter_serial_number']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_year = $element->getData();
	if (count($item_single_year) > 0) {
		$item_single_year[0]['period_date'] = $arr[0];
		$item_single_year[0]['meter_serial_number'] = $_POST['meter_serial_number'];
		$item_single_year[0]['unique_id'] = $arr[0] . $_POST['meter_serial_number'];
		$element->activeTable = 'lentec_single_meter_annually';
		$element->insertData = $item_single_year;
		$element->saveData(['unit_measurement', 'months_calculated']);
	}

	/// analyzing individual meters

	echo ("$('#bulk_meter_result').prepend(`<div class='check form-group' style='color:#09c; font-weight:bold;' >".$_POST['period_date']." &check; </div>`);");
	die();
}


// if (isset($_POST['input_json'])) {
// 	$validation = new Validation();

// 	$_POST['unique_id'] = md5($_POST['period_date'] . $_POST['meter_serial_number']);
// 	unset($_POST['ad_unit']);
// 	unset($_POST['csrf_token']);
// 	// save data 
// 	$validation->insertData = [$_POST];
// 	$validation->activeTable = 'lentec_single_meter_daily';
// 	$save = $validation->saveData('unit_measurement');
// 	if ($save != "success") {
// 		$validation->report("Something is not right, please try again");
// 	}

// 	//summation on all meters ata
// 	//daily
// 	$element = new Element();
// 	$element->activeTable = "lentec_single_meter_daily";
// 	$element->comparisons = [["period_date", " = ", $_POST['period_date']]];
// 	$element->joiners = [''];
// 	$element->order = " BY id DESC ";
// 	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as number_calculated ";
// 	$element->limit = 1000;
// 	$element->offset = 0;
// 	$all_today = $element->getData();
// 	if (count($all_today) > 0) {
// 		$all_today[0]['period_date'] = $_POST['period_date'];
// 		$element->activeTable = 'lentec_total_meter_daily';
// 		$element->insertData = $all_today;
// 		$element->saveData(['unit_measurement', 'number_calculated']);
// 	}

// 	// update monthly
// 	$element = new Element();
// 	$element->activeTable = "lentec_total_meter_daily";
// 	$arr = explode("-", $_POST['period_date']);
// 	$element->comparisons = [["period_date", " LIKE  ", $month = implode("-", array_slice($arr, 0, 2)) . "%"]];
// 	$element->joiners = [''];
// 	$element->order = " BY id DESC ";
// 	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as days_calculated ";
// 	$element->limit = 1000;
// 	$element->offset = 0;
// 	$all_month = $element->getData();
// 	if (count($all_month) > 0) {
// 		$all_month[0]['period_date'] = str_replace("%", "", $month);
// 		$element->activeTable = 'lentec_total_meter_monthly';
// 		$element->insertData = $all_month;
// 		$element->saveData(['unit_measurement', 'days_calculated']);
// 	}
// 	//yearly
// 	$element = new Element();
// 	$element->activeTable = "lentec_total_meter_monthly";
// 	$element->comparisons = [["period_date", " LIKE  ", $arr[0] . "%"]];
// 	$element->joiners = [''];
// 	$element->order = " BY id DESC ";
// 	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as months_calculated ";
// 	$element->limit = 1000;
// 	$element->offset = 0;
// 	$all_years = $element->getData();
// 	if (count($all_years) > 0) {
// 		$all_years[0]['period_date'] = $arr[0];
// 		$element->activeTable = 'lentec_total_meter_annually';
// 		$element->insertData = $all_years;
// 		$element->saveData(['unit_measurement', 'months_calculated']);
// 	}
// 	/// summation on all meters data


// 	/// analyzing individual meters
// 	// meter data monthly 
// 	$element = new Element();
// 	$element->activeTable = "lentec_single_meter_daily";
// 	$element->comparisons = [["period_date", " LIKE ", ($month = implode("-", array_slice($arr, 0, 2))) . "%"], ['meter_serial_number', ' = ', $_POST['meter_serial_number']]];
// 	$element->joiners = ['', ' && '];
// 	$element->order = " BY id DESC ";
// 	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as days_calculated ";
// 	$element->limit = 1000;
// 	$element->offset = 0;
// 	$item_single_month = $element->getData();
// 	if (count($item_single_month) > 0) {
// 		$item_single_month[0]['period_date'] = $month;
// 		$item_single_month[0]['meter_serial_number'] = $_POST['meter_serial_number'];
// 		$item_single_month[0]['unique_id'] = $month . $_POST['meter_serial_number'];
// 		$element->activeTable = 'lentec_single_meter_monthly';
// 		$element->insertData = $item_single_month;
// 		$element->saveData(['unit_measurement', 'days_calculated']);
// 	}

// 	// meter data annually 
// 	$element = new Element();
// 	$element->activeTable = "lentec_single_meter_monthly";
// 	$element->comparisons = [["period_date", " LIKE ", $arr[0] . "%"], ['meter_serial_number', ' = ', $_POST['meter_serial_number']]];
// 	$element->joiners = ['', ' && '];
// 	$element->order = " BY id DESC ";
// 	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as months_calculated ";
// 	$element->limit = 1000;
// 	$element->offset = 0;
// 	$item_single_year = $element->getData();
// 	if (count($item_single_year) > 0) {
// 		$item_single_year[0]['period_date'] = $arr[0];
// 		$item_single_year[0]['meter_serial_number'] = $_POST['meter_serial_number'];
// 		$item_single_year[0]['unique_id'] = $arr[0] . $_POST['meter_serial_number'];
// 		$element->activeTable = 'lentec_single_meter_annually';
// 		$element->insertData = $item_single_year;
// 		$element->saveData(['unit_measurement', 'months_calculated']);
// 	}

// 	/// analyzing individual meters

// 	echo ("$('#meter_serial_number').trigger('change');");
// 	$validation->report('data successfully updated', 'success');
// }

if (isset($_POST['ad_unit'])) {
	$validation = new Validation();

	// $validation->csrf();
	//validate empty filed
	validation();
	$_POST['unique_id'] = md5($_POST['period_date'] . $_POST['meter_serial_number']);
	unset($_POST['ad_unit']);
	unset($_POST['csrf_token']);
	// save data 
	$validation->insertData = [$_POST];
	$validation->activeTable = 'lentec_single_meter_daily';
	$save = $validation->saveData('unit_measurement');
	if ($save != "success") {
		$validation->report("Something is not right, please try again");
	}

	//summation on all meters ata
	//daily
	$element = new Element();
	$element->activeTable = "lentec_single_meter_daily";
	$element->comparisons = [["period_date", " = ", $_POST['period_date']]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as number_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_today = $element->getData();
	if (count($all_today) > 0) {
		$all_today[0]['period_date'] = $_POST['period_date'];
		$element->activeTable = 'lentec_total_meter_daily';
		$element->insertData = $all_today;
		$element->saveData(['unit_measurement', 'number_calculated']);
	}

	// update monthly
	$element = new Element();
	$element->activeTable = "lentec_total_meter_daily";
	$arr = explode("-", $_POST['period_date']);
	$element->comparisons = [["period_date", " LIKE  ", $month = implode("-", array_slice($arr, 0, 2)) . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_month = $element->getData();
	if (count($all_month) > 0) {
		$all_month[0]['period_date'] = str_replace("%", "", $month);
		$element->activeTable = 'lentec_total_meter_monthly';
		$element->insertData = $all_month;
		$element->saveData(['unit_measurement', 'days_calculated']);
	}
	//yearly
	$element = new Element();
	$element->activeTable = "lentec_total_meter_monthly";
	$element->comparisons = [["period_date", " LIKE  ", $arr[0] . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_years = $element->getData();
	if (count($all_years) > 0) {
		$all_years[0]['period_date'] = $arr[0];
		$element->activeTable = 'lentec_total_meter_annually';
		$element->insertData = $all_years;
		$element->saveData(['unit_measurement', 'months_calculated']);
	}
	/// summation on all meters data


	/// analyzing individual meters
	// meter data monthly 
	$element = new Element();
	$element->activeTable = "lentec_single_meter_daily";
	$element->comparisons = [["period_date", " LIKE ", ($month = implode("-", array_slice($arr, 0, 2))) . "%"], ['meter_serial_number', ' = ', $_POST['meter_serial_number']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_month = $element->getData();
	if (count($item_single_month) > 0) {
		$item_single_month[0]['period_date'] = $month;
		$item_single_month[0]['meter_serial_number'] = $_POST['meter_serial_number'];
		$item_single_month[0]['unique_id'] = $month . $_POST['meter_serial_number'];
		$element->activeTable = 'lentec_single_meter_monthly';
		$element->insertData = $item_single_month;
		$element->saveData(['unit_measurement', 'days_calculated']);
	}

	// meter data annually 
	$element = new Element();
	$element->activeTable = "lentec_single_meter_monthly";
	$element->comparisons = [["period_date", " LIKE ", $arr[0] . "%"], ['meter_serial_number', ' = ', $_POST['meter_serial_number']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_year = $element->getData();
	if (count($item_single_year) > 0) {
		$item_single_year[0]['period_date'] = $arr[0];
		$item_single_year[0]['meter_serial_number'] = $_POST['meter_serial_number'];
		$item_single_year[0]['unique_id'] = $arr[0] . $_POST['meter_serial_number'];
		$element->activeTable = 'lentec_single_meter_annually';
		$element->insertData = $item_single_year;
		$element->saveData(['unit_measurement', 'months_calculated']);
	}

	/// analyzing individual meters

	echo ("$('#meter_serial_number').trigger('change');");
	$validation->report('data successfully updated', 'success');
}


//fetch meters if not fetched
if (isset($_SESSION['meter_data'])) {
	$meters_data = $_SESSION['meter_data'];
} else {
	$element = new Element();
	$element->activeTable = "lentec_meters_data";
	$element->comparisons = [];
	$element->joiners = [''];
	$element->order = " BY meter_name ASC ";
	$element->cols = "meter_name, meter_serial_number";
	$element->limit = 1000;
	$element->offset = 0;
	/*get_data*/
	$meters_data = $_SESSION['meter_data'] = $element->getData();
}

$element = new Element();
$element->activeTable = "lentec_entries_meter";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
