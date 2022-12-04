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
	"_date_",
	"amount",
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



//fetching previous data
if (isset($_GET['unique_id'])) {
	$arr = explode("-", $_GET['unique_id']);
	$element = new Element();
	$element->activeTable = "lentec_entries_monthly_expense";
	$element->comparisons = [['_date_', ' = ', implode("-", array_slice($arr, 0, 2))]];
	$element->joiners = [''];
	$element->order = " BY _date_ DESC ";
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
				<td>Month:</td>
				<td>' . $data[0]['_date_'] . '</td>
				</tr>
				<tr>
				<td>BILL EXPENSE</td>
				<td>' . $data[0]['amount'] . '</td>
				</tr>
			</tbody>
			</table>
		</div>
		';
	}


	if (isset($_SESSION['admin_edits'])) {
		$table .= '
		<div class="form-group">
			<label for="meter"> Please enter bill expense</label>
			<input name="amount"  min="0" type="number" step="0.0000000001" required>
		</div>

		<div class="form-group">
			<input name="ad_unit" type="submit" value="edit bill expense" >
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


if (isset($_POST['ad_unit'])) {
	$validation = new Validation();
	$validation->csrf();
	//validate empty filed
	validation();
	$arr = explode("-", $_POST['_date_']);
	$_POST['_date_'] = implode("-", array_slice($arr, 0, 2));
	unset($_POST['ad_unit']);
	unset($_POST['csrf_token']);
	// save data 

	$validation->insertData = [$_POST];
	$validation->activeTable = 'lentec_entries_monthly_expense';
	$save = $validation->saveData('amount');
	if ($save != "success") {
		$validation->report("Something is not right, please try again");
	}

	//summation on all meters ata
	//daily
	$element = new Element();
	$element->activeTable = "lentec_entries_monthly_expense";
	$element->comparisons = [["_date_", " LIKE ", $arr[0] . "%"]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = " SUM(amount) as amount, COUNT(amount) as months_calculated ";
	$element->limit = 1000;
	$element->offset = 0;
	$all_today = $element->getData();

	if (count($all_today) > 0) {
		$all_today[0]['_date_'] = $arr[0];
		$element->activeTable = 'lentec_entries_annual_expense';
		$element->insertData = $all_today;
		$element->saveData(['amount', 'months_calculated']);
	}
	/// analyzing individual meters

	echo ("$('#_date_').trigger('change');");
	$validation->report('data successfully updated', 'success');
}


$element = new Element();
$element->activeTable = "lentec_entries_expense";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
