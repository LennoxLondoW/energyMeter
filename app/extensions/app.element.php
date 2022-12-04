<?php
require_once '../app/app.php';
/**
 *  navbar operations
 */

class Element extends App
{
	/*page editable*/
	public $page_editable = false;
	/* fetch element data */
	public function GetElementData()
	{

		$this->page_editable = isset($_SESSION['admin_edits']) && isset($_GET['edit']) ? true : false;
		$_fetched = $this->activeTable . "___" . $this->limit . "___" . $this->offset;
		if (isset($_SESSION[$_fetched])) {
			$element_data = $_SESSION[$_fetched];
		} else {
			$element_data =  $this->getData();
			if (!isset($_SESSION['admin_edits'])) {
				$_SESSION[$_fetched] = $element_data;
			}
		}
		/*fetch element data*/

		/*extract data*/
		if (count($element_data) === 0) {
			/*set null fields*/
			$col_array = explode(",", $this->cols);
			extract(array_combine($col_array, array_fill(0, count($col_array), "null")));
			/* set variables as global */
			foreach (explode(",", $this->cols) as  $index) {
				$GLOBALS[$index] = "NULL";
			}
		} else {
			/* set variables as global */
			foreach ($element_data as  $row) {
				$GLOBALS[$row['section_id']] = $row['section_title'];
			}
		}
	}

	/*get blog data*/
	public function getBlogData($paginate = false)
	{
		$data = $paginate ? $this->getData($paginate) : $this->getData();
		// print_r($data);
		$blog_offset = "_0";
		foreach ($data as  $row) {
			$blog_offset = $row['section_id'];
			$GLOBALS[$row['section_id']] = $row['section_title'];
		}
		$arr = explode("_", $blog_offset);
		$this->blog_offset = end($arr);
	}



	/*setting element as editable*/
	public function is_editable($table, $section_id, $type = "text", $return = false)
	{
		 $table = "lentec_" . $table;
		//lets check if this element exist
		$element_id = $table . "__check__" . $section_id;
		if (!isset($_SESSION[$element_id])) {
			$element = new Element();
			$element->activeTable = $table;
			$element->comparisons = [["section_id", " = ", $section_id]];
			$element->joiners = [''];
			$element->order = " BY id DESC ";
			$element->cols = "section_id, section_title";
			$element->limit = 1;
			$element->offset = 0;
			/*get_data*/
			$data = $element->getData(); 

			if (count($data) === 0) {
				$GLOBALS[$section_id] = $insert  = $type === "text" ? "lorem ipsum donor" : "images/app.images/none.png";
				$element->insertData = [
					[
						"section_id" => $section_id,
						"section_title" => $insert
					]
				];
				$element->saveData();
			}
			// $_SESSION[$element_id] = "SET";
		}

		if ($return) {
			return isset($_SESSION['admin_edits']) && isset($_GET['edit']) ? " data-editable='true' data-type='" . $type . "' data-table='" . $table . "' data-section_id='" . $section_id . "' " : "";
		}

		echo isset($_SESSION['admin_edits']) && isset($_GET['edit']) ? " data-editable='true' data-type='" . $type . "' data-table='" . $table . "' data-section_id='" . $section_id . "' " : "";
	}
}

/*adding new element*/
if (isset($_POST['add_element'])  && (!isset($_SESSION['microtime2']) || $_SESSION['microtime2'] != $_POST['microtime2'])) {
	$_SESSION['microtime2'] = $_POST['microtime2'];
	$element = new Element();
	$element->activeTable = $_POST['table'];
	$add = $_POST['add_element'];
	unset($_POST['add_element']);
	unset($_POST['table']);
	unset($_POST['microtime2']);
	foreach ($_POST as $key => $value) {
		$element->insertData[] = [
			"section_id" => $key,
			"section_title" => $value
		];
	}

	if (($reply = $element->saveData()) === 'success') {
		$autofocus = $add;
		if (isset($_SESSION[$element->activeTable])) {
			unset($_SESSION[$element->activeTable]);
		}
	} else {
		echo "<script>alert(`" . $reply . "`);</script>";
	}
}



/*deleting element*/
if (isset($_POST['delete_element']) && (!isset($_SESSION['microtime']) || $_SESSION['microtime'] != $_POST['microtime'])) {
	$_SESSION['microtime'] = $_POST['microtime'];
	$element = new Element();
	$table = $element->activeTable = $_POST['table'];
	$del = $_POST['delete_element'];
	unset($_POST['delete_element']);
	unset($_POST['table']);
	unset($_POST['microtime']);
	// fetch where to delete and delete the images
	$element->comparisons =  array_values(array_map(function ($a) use ($table) {
		$element_id = $table . "__check__" . $a;
		if (isset($_SESSION[$element_id])) {
			unset($_SESSION[$element_id]);
		}
		return ["section_id", " = ", $a];
	}, $_POST));

	$element->joiners = array_merge([''], array_fill(0, count($_POST) - 1, ' || '));
	$element->order = " BY id DESC ";
	$element->cols = "section_title";
	$element->limit = 2000;
	$element->offset = 0;

	$data = $element->getData();
	/*check if image and delete*/
	foreach ($data as $key => $row) {
		if (substr($row['section_title'], 0, 7) === "uploads" && is_file("../" . $row['section_title'])) {
			unlink("../" . $row['section_title']);
		}
	}
	/*delete db occurence here*/
	if ($element->deleteData()) {
		$autofocus2 = $del;
		if (isset($_SESSION[$element->activeTable])) {
			unset($_SESSION[$element->activeTable]);
		}
		$element->order = " BY id ASC ";
		$element->cols = " id, section_id, section_title";
		/*fetch like loops to rearrange the deleted loop*/
		$element->comparisons =  array_values(array_map(function ($a) {
			$arr = explode("_", $a);
			//remove the incrementing digit
			array_pop($arr);
			//create search string without incrementing digit
			return ["section_id", " LIKE ", implode("_", $arr) . "_%"];
		}, $_POST));
		//fetch new data with new conditionals
		$data = $element->getData();
		$increaments = [];
		$element->joiners = [''];
		$element->limit = 1;
		//loop and update the new section titles
		foreach ($data as $key => $row) {
			/*find the root without incrementing*/
			$arr = explode("_", $row['section_id']);
			//remove the incrementing digit
			array_pop($arr);
			//incrementing index 
			$incrementing_index = implode("_", $arr) . "_";
			$digit = !isset($increaments[$incrementing_index]) ? 1 : $increaments[$incrementing_index] + 1;
			$increaments[$incrementing_index] = $digit;

			//update query
			$element->update_data = ['section_id' => $incrementing_index . $digit];
			$element->comparisons = [["id", " = ", $row['id']]];
			if (!$element->updateData()) {
				die($element->database_error);
			}
		}
	} else {
		die($element->database_error);
	}
}


/*editing element data*/
if (isset($_POST['edit_element'])) {
	$element = new Element();
	$element->activeTable = $_POST['table'];
	$element->comparisons = [['section_id', ' = ', $_POST['section_id']]];
	$element->joiners = [''];
	$element->order = " BY id DESC ";
	$element->cols = "section_id, section_title";
	$element->limit = 1;
	$element->offset = 0;
	// print_r($_POST);
	/*get_data to check if element exist*/
	if (count(($data = $element->getData())) === 0) {
		die("Swal.fire({icon:'info',text: 'This element does not exist!'})");
	}

	/*check whether is file*/
	if (isset($_FILES['file'])) {
		$element->max_file_size = $_POST['section_id'] === "about_download_link" ? 20000000 : 5000000;
		$element->upload_file = $_FILES['file'];
		$upload = $_POST['section_id'] === "about_download_link" ? $upload = $element->save_any_file() : $upload = $element->save_image();
		if ($upload) {
			$prev_image = $data['0']['section_title'];
			/*delete previous images*/
			if (substr($prev_image, 0, 7) === "uploads") {
				if (is_file("../" . $prev_image)) {
					unlink("../" . $prev_image);
				}
			}

			//update data 
			$element->update_data = ['section_title' => $upload];
			if ($element->updateData()) {
				if (isset($_SESSION[$element->activeTable])) {
					unset($_SESSION[$element->activeTable]);
				}

				$text =  $_POST['section_id'] !== "about_download_link" ?
					" Swal.close(); $('[data-section_id=" . '"' . $_POST['section_id'] . '"' . "], [data-section_id=" . '"' . $_POST['section_id'] . '"' . "] img').attr('src',`" . base_path . "{$upload}`).attr('href',`" . scheme . $_SERVER['SERVER_NAME'] . base_path . "{$upload}`).css('background-image',`url(" . base_path . "{$upload})`)"
					:
					" Swal.close(); $('[data-section_id=" . '"' . $_POST['section_id'] . '"' . "]').attr('href',`" . base_path . "{$upload}`);";
				die($text);
			}
			die("Swal.fire({icon:'info',text: `" . $element->database_error . "`})");
		}
		die("Swal.fire({icon:'info',text: `" . $element->upload_error . "`})");
	}

	//edit only text
	else {
		$element->update_data = ['section_title' => $_POST['section_title']];
		if ($element->updateData()) {
			$text  = " Swal.close(); $('[data-section_id=" . '"' . $_POST['section_id'] . '"' . "], [data-section_id=" . '"' . $_POST['section_id'] . '"' . "] img').html(`<s class='holder' style='text-decoration:inherit;'>{$_POST['section_title']}</s>`);";
			if (substr($_POST['section_id'], 0, 6) === '_icon_') {
				//editing icons
				$text .= " $('[data-section_id=" . '"' . $_POST['section_id'] . '"' . "]').attr('class',`hidden editable {$_POST['section_title']}`);";
			}
			if (isset($_SESSION[$element->activeTable])) {
				unset($_SESSION[$element->activeTable]);
			}


			die($text);
		}
		die("Swal.fire({icon:'info',text: `" . $element->database_error . "`})");
	}
}
