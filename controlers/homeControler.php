<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
require_once '../app/extensions/app.validation.php';
require_once '../app.extensions/app.front.extension.php';
// header('content-type:application/json');
define('fields', [
	"equipment_name",
	"equipment_rating",
	"equipment_quantity",
	'meter_serial_number',
	'meter_name',
	'lat',
	'lng',
]);



//function to validate all post fields
function validation($file = false)
{
	$validation = new Validation();
	//validation
	foreach (fields as  $col) {
		if (!isset($_POST[$col])) {
			continue;
		}
		if (empty($_POST[$col])) {
			$validation->report("Field '" . ucfirst(str_replace("_", " ", $col)) . "' is required");
		} elseif ($col === "equipment_name" && !$validation->is_alphanumeral($_POST[$col])) {
			$validation->report("Only alphanumerals required on '" . ucfirst(str_replace("_", " ", $col)) . "'");
		} elseif ($col === "equipment_rating" && !is_numeric($_POST[$col])) {
			$validation->report("Please enter correct integer on '" . ucfirst(str_replace("_", " ", $col)) . "'");
		} elseif ($col === "equipment_quantity" && !is_numeric($_POST[$col])) {
			$validation->report("Please enter correct integer on '" . ucfirst(str_replace("_", " ", $col)) . "'");
		} elseif ($col === "lat" && !is_numeric($_POST[$col])) {
			$validation->report("Please enter correct number on '" . ucfirst(str_replace("_", " ", $col)) . "'");
		} elseif ($col === "lng" && !is_numeric($_POST[$col])) {
			$validation->report("Please enter correct number on '" . ucfirst(str_replace("_", " ", $col)) . "'");
		} elseif ($col === "meter_name" && !$validation->is_alphanumeral($_POST[$col])) {
			$validation->report("Only alphanumerals required on '" . ucfirst(str_replace("_", " ", $col)) . "'");
		}
	}

	if ($file) {
		//lets upload files
		if (isset($_FILES['file'])) {
			if (!isset($_FILES['file']['name'])) {
				$validation->report("Please add the required image file");
			}

			$validation->max_file_size =  1000000;
			$validation->upload_file = [
				"tmp_name" => $_FILES['file']['tmp_name'],
				"size" => $_FILES['file']['size'],
			];

			$upload = $validation->save_image();
			if (!$upload) {
				$validation->report($validation->upload_error);
			}

			$_POST['file'] = $upload;
		}
	} else {
		//lets upload files
		if (isset($_FILES['file']['name'])) {
			$validation->max_file_size =  1000000;
			$validation->upload_file = [
				"tmp_name" => $_FILES['file']['tmp_name'],
				"size" => $_FILES['file']['size'],
			];

			$upload = $validation->save_image();
			if (!$upload) {
				$validation->report($validation->upload_error);
			}

			$_POST['file'] = $upload;
		}
	}
}


//adding new items
if (isset($_POST['add_item'])) {
	$validation = new Validation();
	//check forgery
	// print_r($_POST);
	$validation->csrf();


	//check admin
	if (!isset($_SESSION['admin_edits']) || !isset($_POST['origin']) || empty($_POST['origin'])) {
		$validation->report("Access denied");
	}

	$equipment = $meters = new Element();
	$validation->activeTable = $meters->activeTable = $equipment->activeTable = strip_tags($_POST['origin']);


	//validations
	validation($file = true);

	if (isset($_POST['csrf_token'])) {
		unset($_POST['csrf_token']);
	}
	if (isset($_POST['honey_pot'])) {
		unset($_POST['honey_pot']);
	}

	unset($_POST['add_item']);
	unset($_POST['origin']);

	$validation->insertData[] = $_POST;

	$save = $validation->saveData(false);
	if ($save === "success") {
		//lets update this client balance
		echo "$('.clr').val('');";
		echo ("addingField.prepend(`" .
			(isset($_POST['equipment_rating']) ?
				$frontEnd->displayEquipment($validation->insertData) : (isset($_POST['meter_serial_number']) ?
					$frontEnd->displayMeter($validation->insertData) :
					""
				)
			)
			. "`); showImages(); Swal.close();");
		$validation->report("The item added successful", $class = "success");
	}

	if (strstr($save, 'Duplicate entry') !== false) {
		$validation->report("The item already exist");
	}
	$validation->report("Something unusual has occured, please try again." . $save);
}





//adding new items
if (isset($_POST['edit_item'])) {
	$validation = new Validation();
	//check forgery
	$validation->csrf();


	//check admin
	if (!isset($_SESSION['admin_edits']) || !isset($_POST['origin']) || empty($_POST['origin']) || !$validation->is_correct_name(str_replace("_", "", $_POST['edit_field']))) {
		$validation->report("Access denied");
	}

	$equipment = $meters = new Element();
	$validation->activeTable = $meters->activeTable = $equipment->activeTable = strip_tags($_POST['origin']);

	//validations
	validation($file = true);
	$validation->comparisons = [[$_POST['edit_field'], " = ", $_POST['edit_val']]];

	if (isset($_POST['csrf_token'])) {
		unset($_POST['csrf_token']);
	}
	if (isset($_POST['honey_pot'])) {
		unset($_POST['honey_pot']);
	}
	unset($_POST['edit_item']);
	unset($_POST['origin']);
	unset($_POST['edit_field']);
	unset($_POST['edit_val']);

	$validation->update_data = $_POST;
	if ($validation->updateData()) {
		//lets update this client balance
		echo "$('.clr').val('');";
		echo ("current.before(`" .
			(isset($_POST['equipment_rating']) ?
				$frontEnd->displayEquipment([$validation->update_data]) : (isset($_POST['meter_serial_number']) ?
					$frontEnd->displayMeter([$validation->update_data]) :
					""
				)
			) . "`).remove(); showImages(); Swal.close();");
		$validation->report("Item editted successfully ", $class = "success");
	}

	if (strstr($validation->database_error, 'Duplicate entry') !== false) {
		$validation->report("The item already exist");
	}

	$validation->report("Something unusual has occured, please try again." . $validation->database_error);
}





//deleting item
// delete_item
if (isset($_POST['delete_item'])) {
	$validation = new Validation();
	$validation->activeTable = strip_tags($_POST['origin']);
	//check forgery
	$validation->csrf();

	//check admin
	if (!isset($_SESSION['admin_edits'])) {
		$validation->report("Access denied");
	}

	//empty checking name
	if (empty($_POST['origin']) || empty($_POST['index'])) {
		$validation->report("Access denied");
	}

	$validation->comparisons = [[strip_tags($_POST['index']), " = ", $_POST['delete']]];


	if ($validation->deleteData()) {
		if (is_file("../" . $_POST['delete'])) {
			unlink("../" . $_POST['delete']);
		}
		//lets update this client balance
		echo ("todelete.remove(); Swal.close();");
		$validation->report("Item deleted successful", $class = "success");
	}
	$validation->report("Something unusual has occured, please try again.");
}



















if (!isset($_SESSION['total_meters'])) {
	$meters = new Element();
	$meters->activeTable = "lentec_meters_data";
	$meters->comparisons = [];
	$meters->joiners = [''];
	$meters->order = " BY meter_name ASC ";
	$meters->cols = " COUNT(meter_name) as total ";
	$meters->limit = 1000;
	$meters->offset = 0;
	/*get_data*/
	$meters_data = $meters->getData();
	$_SESSION['total_meters'] = !isset($meters_data[0]['total']) ? 0 : $meters_data[0]['total'];
}


if (!isset($_SESSION['total_rating']) || !isset($_SESSION['total_equipment'])) {
	$equipment = new Element();
	$equipment->activeTable = "lentec_equipment_data";
	$equipment->comparisons = [];
	$equipment->joiners = [''];
	$equipment->order = " BY equipment_name ASC ";
	$equipment->cols = "SUM(equipment_rating * equipment_quantity) AS total_rating, SUM(equipment_quantity) AS total_equipment";
	$equipment->limit = 1000;
	$equipment->offset =  0;
	/*get_data*/
	$equipment_data = $equipment->getData();
	$_SESSION['total_rating'] = !isset($equipment_data[0]['total_rating']) ? 0 : $equipment_data[0]['total_rating'];
	$_SESSION['total_equipment'] = !isset($equipment_data[0]['total_equipment']) ? 0 : $equipment_data[0]['total_equipment'];
}






$artists = new Element();
$artists->activeTable = "lentec_artists";
$artists->comparisons = [];
$artists->joiners = [''];
$artists->order = " BY name ASC ";
$artists->cols = "*";
$artists->limit = 20;
$artists->offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
/*get_data*/
$artists_data = $artists->getData();

if (isset($_GET['dynamic'])) {
	die($frontEnd->displayEquipment($artists_data));
}




























if (isset($_POST['name'])) {
	$validation = new Validation();
	$validation->activeTable = "lentec_artists";
	//check forgery
	$validation->csrf();


	//check admin
	if (!isset($_SESSION['admin_edits'])) {
		$validation->report("Access denied");
	}


	//empty checking name
	if (empty($_POST['name'])) {
		$validation->report("Please add artist name");
	}

	if (!isset($_FILES['image']['name'])) {
		$validation->report("Please add artist picture");
	}

	// 	to push to database										
	$post_keys = [
		"fb",
		"twitter",
		"insta",
		"youtube",
		"web",
		"tiktok",
		"pint",
		"linkedIn",
		"reddit",
		"telegram",
		"whatsapp",
		"cart"
	];

	//validating empty fields
	foreach ($post_keys as $key => $col) {
		if (empty($_POST[$col])) {
			$_POST[$col] = "#";
		}
	}


	$validation->max_file_size =  1000000;

	$validation->upload_file = [
		"tmp_name" => $_FILES['image']['tmp_name'],
		"size" => $_FILES['image']['size'],
	];
	$upload = $validation->save_image();
	if (!$upload) {
		$validation->report($validation->upload_error);
	}



	$validation->insertData = [
		[
			"name" => strip_tags($_POST['name']),
			"image" => $upload,
			"fb" => strip_tags($_POST['fb']),
			"twitter" => strip_tags($_POST['twitter']),
			"insta" => strip_tags($_POST['insta']),
			"youtube" => strip_tags($_POST['youtube']),
			"web" => strip_tags($_POST['web']),
			"tiktok" => strip_tags($_POST['tiktok']),
			"pint" => strip_tags($_POST['pint']),
			"linkedIn" => strip_tags($_POST['linkedIn']),
			"reddit" => strip_tags($_POST['reddit']),
			"telegram" => strip_tags($_POST['telegram']),
			"whatsapp" => strip_tags($_POST['whatsapp']),
			"cart" => strip_tags($_POST['cart'])
		]
	];

	$save = $validation->saveData(false);
	if ($save === "success") {
		//lets update this client balance
		echo "$('.clr').val('');";
		echo ("$('.artists').prepend(`" . $frontEnd->displayEquipment($validation->insertData) . "`); showImages();");
		$validation->report("The artist " . $_POST['name'] . " added successful");
	}

	if (strstr($save, 'Duplicate entry') !== false) {
		$validation->report("The artist " . $_POST['name'] . " already exist");
	}
	$validation->report("Something unusual has occured, please try again.");
}




// edit artist 

if (isset($_POST['prev_name'])) {
	$validation = new Validation();
	$validation->activeTable = "lentec_artists";
	//check forgery
	$validation->csrf();
	//check admin
	if (!isset($_SESSION['admin_edits'])) {
		$validation->report("Access denied");
	}


	//empty checking name
	if (empty($_POST['prev_name'])) {
		$validation->report("Request denied");
	}



	// 	to push to database										
	$post_keys = [
		"fb",
		"twitter",
		"insta",
		"youtube",
		"web",
		"tiktok",
		"pint",
		"linkedIn",
		"reddit",
		"telegram",
		"whatsapp",
		"cart"
	];

	//validating empty fields
	foreach ($post_keys as $key => $col) {
		if (empty($_POST[$col])) {
			$_POST[$col] = "#";
		}
	}

	if (isset($_FILES['image']['name'])) {
		$validation->max_file_size =  1000000;

		$validation->upload_file = [
			"tmp_name" => $_FILES['image']['tmp_name'],
			"size" => $_FILES['image']['size'],
		];
		$upload = $validation->save_image();
		if (!$upload) {
			$validation->report($validation->upload_error);
		}
		//delete previous file
		$file = "../" . $_POST['image'];
		if (is_file($file)) {
			unlink($file);
		}

		$_POST['image'] = $upload;
	}



	$new_row = [
		[
			"name" => strip_tags($_POST['prev_name']),
			"image" => $_POST['image'],
			"fb" => strip_tags($_POST['fb']),
			"twitter" => strip_tags($_POST['twitter']),
			"insta" => strip_tags($_POST['insta']),
			"youtube" => strip_tags($_POST['youtube']),
			"web" => strip_tags($_POST['web']),
			"tiktok" => strip_tags($_POST['tiktok']),
			"pint" => strip_tags($_POST['pint']),
			"linkedIn" => strip_tags($_POST['linkedIn']),
			"reddit" => strip_tags($_POST['reddit']),
			"telegram" => strip_tags($_POST['telegram']),
			"whatsapp" => strip_tags($_POST['whatsapp']),
			"cart" => strip_tags($_POST['cart'])
		]
	];

	$validation->update_data = [
		"image" => $_POST['image'],
		"fb" => strip_tags($_POST['fb']),
		"twitter" => strip_tags($_POST['twitter']),
		"insta" => strip_tags($_POST['insta']),
		"youtube" => strip_tags($_POST['youtube']),
		"web" => strip_tags($_POST['web']),
		"tiktok" => strip_tags($_POST['tiktok']),
		"pint" => strip_tags($_POST['pint']),
		"linkedIn" => strip_tags($_POST['linkedIn']),
		"reddit" => strip_tags($_POST['reddit']),
		"telegram" => strip_tags($_POST['telegram']),
		"whatsapp" => strip_tags($_POST['whatsapp']),
		"cart" => strip_tags($_POST['cart'])
	];

	$validation->comparisons = [["name", " = ", strip_tags($_POST['prev_name'])]];

	if ($validation->updateData()) {
		//lets update this client balance
		echo "$('.clr').val('');";
		echo ("current.before(`" . $frontEnd->displayEquipment($new_row) . "`); current.remove(); Swal.close();showImages();");
		$validation->report("The artist " . $_POST['name'] . " updated successful");
	}
	$validation->report("Something unusual has occured, please try again.");
}



//delete post 
if (isset($_POST['delete'])) {
	$validation = new Validation();
	$validation->activeTable = "lentec_artists";
	//check forgery
	$validation->csrf();
	//check admin
	if (!isset($_SESSION['admin_edits'])) {
		$validation->report("Access denied");
	}


	//empty checking name
	if (empty($_POST['delete'])) {
		$validation->report("Request denied");
	}



	//delete previous file
	$file = "../" . $_POST['delete'];
	if (is_file($file)) {
		unlink($file);
	}



	$validation->comparisons = [["image", " = ", strip_tags($_POST['delete'])]];

	$delete = $validation->deleteData();
	if ($delete) {
		//lets update this client balance
		echo "$('.clr').val('');";
		echo ("current.remove(); Swal.close();");
		$validation->report("The artist " . $_POST['name'] . " deleted successful");
	}
	$validation->report("Something unusual has occured, please try again.");
}



//fetching this page operational data
$element = new Element();
$element->activeTable = "lentec_home";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
