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
	"period_date",
	"total_hours",
	"meter_json",
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
	$arr = explode('{"equipment_name":"', $_GET['unique_id']);
	$arr2 = explode('"', end($arr));
	$unique = $arr[0] . $arr2[0];
	$element = new Element();
	$element->activeTable = "lentec_single_equipment_daily";
	$element->comparisons = [['unique_id', ' = ', md5($unique)]];
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
				<td>Name:</td>
				<td>' . $data[0]['eqipment_name'] . '</td>
				</tr>
				<tr>
				<td>DATE</td>
				<td>' . $data[0]['period_date'] . '</td>
				</tr>

				<tr>
				<td>PWR(all combined):</td>
				<td>' . $data[0]['unit_measurement'] . ' kWh</td>
				</tr>

				<tr>
				<td>HOURS:</td>
				<td>' . $data[0]['total_hours'] . ' Hrs</td>
				</tr>
			</tbody>
			</table>
		</div>
		';
	}


	if (isset($_SESSION['admin_edits'])) {
		$table .= '
		<div class="form-group">
			<label for="meter"> Please enter time in hours</label>
			<input name="total_hours" max="24" min="0" type="number" step="0.0000000001" required>
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
	// validation();

	$meter_json = $_POST['meter_json'];
	$json_array = json_decode($_POST['meter_json'], true);

	$arr = explode('{"equipment_name":"', $_POST['meter_json']);
	$arr2 = explode('"', end($arr));
	$unique = $arr2[0];

	

	$_POST['unique_id'] = md5($_POST['period_date'] . $unique);
	$_POST['eqipment_name'] = $json_array['equipment_name'];
	$_POST['unit_measurement'] = $json_array['equipment_rating'] * $json_array['equipment_quantity'] * $_POST['total_hours'];
	unset($_POST['bypass']);
	unset($_POST['meter_json']);
	// save data 
	$validation->insertData = [$_POST];
	$validation->activeTable = 'lentec_single_equipment_daily';
	$save = $validation->saveData(['unit_measurement', 'total_hours']);
	if ($save != "success") {
		die("alert(`Something is not right, please try again`;");
	}

	

	//summation on all meters ata
	//daily
	$element = new Element();
	$element->activeTable = "lentec_single_equipment_daily";
	$element->comparisons = [["period_date", " = ", $_POST['period_date']]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as number_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_today = $element->getData();
	if (count($all_today) > 0) {
		$all_today[0]['period_date'] = $_POST['period_date'];
		$element->activeTable = 'lentec_total_equipment_daily';
		$element->insertData = $all_today;
		$element->saveData(['unit_measurement', 'number_calculated', 'total_hours']);
	}

	// update monthly
	$element = new Element();
	$element->activeTable = "lentec_total_equipment_daily";
	$arr = explode("-", $_POST['period_date']);
	$element->comparisons = [["period_date", " LIKE  ", $month = implode("-", array_slice($arr, 0, 2)) . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_month = $element->getData();

	if (count($all_month) > 0) {
		$all_month[0]['period_date'] = str_replace("%", "", $month);
		$element->activeTable = 'lentec_total_equipment_monthly';
		$element->insertData = $all_month;
		$element->saveData(['unit_measurement', 'days_calculated', 'total_hours']);
	}
	//yearly
	$element = new Element();
	$element->activeTable = "lentec_total_equipment_monthly";
	$element->comparisons = [["period_date", " LIKE  ", $arr[0] . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_years = $element->getData();
	if (count($all_years) > 0) {
		$all_years[0]['period_date'] = $arr[0];
		$element->activeTable = 'lentec_total_equipment_annually';
		$element->insertData = $all_years;
		$element->saveData(['unit_measurement', 'months_calculated', 'total_hours']);
	}
	/// summation on all meters data


	/// analyzing individual meters
	// meter data monthly 
	$element = new Element();
	$element->activeTable = "lentec_single_equipment_daily";
	$element->comparisons = [["period_date", " LIKE ", ($month = implode("-", array_slice($arr, 0, 2))) . "%"], ['eqipment_name', ' = ', $_POST['eqipment_name']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_month = $element->getData();

	if (count($item_single_month) > 0) {
		$item_single_month[0]['period_date'] = $month;
		$item_single_month[0]['eqipment_name'] = $_POST['eqipment_name'];
		$item_single_month[0]['unique_id'] = md5($month .  $unique);
		$element->activeTable = 'lentec_single_equipment_monthly';
		$element->insertData = $item_single_month;
		$element->saveData(['unit_measurement', 'days_calculated', 'total_hours']);
	}

	// meter data annually 
	$element = new Element();
	$element->activeTable = "lentec_single_equipment_monthly";
	$element->comparisons = [["period_date", " LIKE ", $arr[0] . "%"], ['eqipment_name', ' = ', $_POST['eqipment_name']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_year = $element->getData();
	if (count($item_single_year) > 0) {
		$item_single_year[0]['period_date'] = $arr[0];
		$item_single_year[0]['eqipment_name'] = $_POST['eqipment_name'];
		$item_single_year[0]['unique_id'] = md5($arr[0] . $unique);
		$element->activeTable = 'lentec_single_equipment_annually';
		$element->insertData = $item_single_year;
		$element->saveData(['unit_measurement', 'months_calculated', 'total_hours']);
	}

	/// analyzing individual meters

	echo ("$('#bulk_equpment_result').prepend(`<div class='check form-group' style='color:#09c; font-weight:bold;' >".$_POST['period_date']." &check; </div>`);");
	die();
}



if (isset($_POST['ad_unit'])) {
	$validation = new Validation();

	// $validation->csrf();
	//validate empty filed
	validation();

	$meter_json = $_POST['meter_json'];
	$json_array = json_decode($_POST['meter_json'], true);

	$arr = explode('{"equipment_name":"', $_POST['meter_json']);
	$arr2 = explode('"', end($arr));
	$unique = $arr[0] . $arr2[0];



	$_POST['unique_id'] = md5($_POST['period_date'] . $unique);
	$_POST['eqipment_name'] = $json_array['equipment_name'];
	$_POST['unit_measurement'] = $json_array['equipment_rating'] * $json_array['equipment_quantity'] * $_POST['total_hours'];
	unset($_POST['ad_unit']);
	unset($_POST['csrf_token']);
	unset($_POST['meter_json']);
	// save data 
	$validation->insertData = [$_POST];
	$validation->activeTable = 'lentec_single_equipment_daily';
	$save = $validation->saveData(['unit_measurement', 'total_hours']);
	if ($save != "success") {
		$validation->report("Something is not right, please try again");
	}


	//summation on all meters ata
	//daily
	$element = new Element();
	$element->activeTable = "lentec_single_equipment_daily";
	$element->comparisons = [["period_date", " = ", $_POST['period_date']]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as number_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_today = $element->getData();
	if (count($all_today) > 0) {
		$all_today[0]['period_date'] = $_POST['period_date'];
		$element->activeTable = 'lentec_total_equipment_daily';
		$element->insertData = $all_today;
		$element->saveData(['unit_measurement', 'number_calculated', 'total_hours']);
	}

	// update monthly
	$element = new Element();
	$element->activeTable = "lentec_total_equipment_daily";
	$arr = explode("-", $_POST['period_date']);
	$element->comparisons = [["period_date", " LIKE  ", $month = implode("-", array_slice($arr, 0, 2)) . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_month = $element->getData();

	if (count($all_month) > 0) {
		$all_month[0]['period_date'] = str_replace("%", "", $month);
		$element->activeTable = 'lentec_total_equipment_monthly';
		$element->insertData = $all_month;
		$element->saveData(['unit_measurement', 'days_calculated', 'total_hours']);
	}
	//yearly
	$element = new Element();
	$element->activeTable = "lentec_total_equipment_monthly";
	$element->comparisons = [["period_date", " LIKE  ", $arr[0] . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_years = $element->getData();
	if (count($all_years) > 0) {
		$all_years[0]['period_date'] = $arr[0];
		$element->activeTable = 'lentec_total_equipment_annually';
		$element->insertData = $all_years;
		$element->saveData(['unit_measurement', 'months_calculated', 'total_hours']);
	}
	/// summation on all meters data


	/// analyzing individual meters
	// meter data monthly 
	$element = new Element();
	$element->activeTable = "lentec_single_equipment_daily";
	$element->comparisons = [["period_date", " LIKE ", ($month = implode("-", array_slice($arr, 0, 2))) . "%"], ['eqipment_name', ' = ', $_POST['eqipment_name']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as days_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_month = $element->getData();

	if (count($item_single_month) > 0) {
		$item_single_month[0]['period_date'] = $month;
		$item_single_month[0]['eqipment_name'] = $_POST['eqipment_name'];
		$item_single_month[0]['unique_id'] = md5($month .  $unique);
		$element->activeTable = 'lentec_single_equipment_monthly';
		$element->insertData = $item_single_month;
		$element->saveData(['unit_measurement', 'days_calculated', 'total_hours']);
	}

	// meter data annually 
	$element = new Element();
	$element->activeTable = "lentec_single_equipment_monthly";
	$element->comparisons = [["period_date", " LIKE ", $arr[0] . "%"], ['eqipment_name', ' = ', $_POST['eqipment_name']]];
	$element->joiners = ['', ' && '];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(unit_measurement) as unit_measurement, SUM(total_hours) as total_hours, COUNT(unit_measurement) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$item_single_year = $element->getData();
	if (count($item_single_year) > 0) {
		$item_single_year[0]['period_date'] = $arr[0];
		$item_single_year[0]['eqipment_name'] = $_POST['eqipment_name'];
		$item_single_year[0]['unique_id'] = md5($arr[0] . $unique);
		$element->activeTable = 'lentec_single_equipment_annually';
		$element->insertData = $item_single_year;
		$element->saveData(['unit_measurement', 'months_calculated', 'total_hours']);
	}

	/// analyzing individual meters

	echo ("$('#meter_serial_number').trigger('change');");
	$validation->report('data successfully updated', 'success');
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
$element->activeTable = "lentec_entries_equpment";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
