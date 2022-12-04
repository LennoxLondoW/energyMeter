<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
//loging in check
if (!isset($_SESSION['email'])) {
	// do something
	$_SESSION['logged_page'] = str_replace(".php", "", basename($_SERVER['PHP_SELF']));
	header('location:' . base_path . "/sign_in");
	die();
}

//check if this is admin
if (!isset($_SESSION['admin_edits'])) {
	header('location:' . base_path . "/home");
	die();
}



//editing user roles
if (isset($_POST['edit'])) {
	$element = new Element();
	$element->csrf();
	$id = strip_tags($_POST['id']);
	$edit = strip_tags($_POST['edit']);
	$allowed = [
		"Suspended" => "Not Suspended",
		"Not Suspended" => "Suspended",
		"Not Admin" => "Admin",
		"Admin" => "Not Admin",
	];

	if (!isset($allowed[$edit])) {
		die('form.find(`.alert`).remove(); Swal.fire({icon:`error`, html: `Request denied`}); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
	}

	$cols = [
		"Suspended" => "is_suspended",
		"Not Suspended" => "is_suspended",
		"Not Admin" => "is_admin",
		"Admin" => "is_admin",
	];
	$element->activeTable = 'lentec_users';
	$element->comparisons = [["id", " = ", $id], [$cols[$edit], " = ", $edit], ["is_admin", " != ", 'Main Admin']];
	$element->joiners = ['', ' && ', ' && '];
	$element->update_data = [
		$cols[$edit] => $allowed[$edit]
	];
	if ($element->updateData()) {
		die('form.find(`.alert`).remove();form.find(`[type="submit"]`).html(`' . $allowed[$edit] . '`).val(`' . $allowed[$edit] . '`); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
	}

	die('form.find(`.alert`).remove(); Swal.fire({icon:`error`, html: `Something is not right`}); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
}



//editing user roles
if (isset($_POST['delete'])) {
	$element = new Element();
	$element->csrf();
	$id = strip_tags($_POST['delete']);

	$element->activeTable = 'lentec_users';
	$element->comparisons = [["id", " = ", $id], ["is_admin", " != ", 'Main Admin']];
	$element->joiners = ['', ' && '];

	if ($element->deleteData()) {
		die('form.parents(`.main_div`).remove(); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
	}

	die('form.find(`.alert`).remove(); Swal.fire({icon:`error`, html: `Something is not right`}); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
}



//editing user roles
if (isset($_POST['edit_youtube'])) {
	$element = new Element();
	$element->csrf();
	$video_id = strip_tags($_POST['video_id']);
	$edit_youtube = strip_tags($_POST['edit_youtube']);
	$allowed = [
		"Suspended" => "Not Suspended",
		"Not Suspended" => "Suspended",
	];

	if (!isset($allowed[$edit_youtube])) {
		die('form.find(`.alert`).remove(); Swal.fire({icon:`error`, html: `Request denied`}); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
	}


	$element->activeTable = 'all_youtube';
	$element->comparisons = [["video_id", " = ", $video_id]];
	$element->joiners = [''];
	$element->update_data = [
		'is_suspended' => $allowed[$edit_youtube]
	];
	if ($element->updateData()) {
		die('form.find(`.alert`).remove();form.find(`[type="submit"]`).html(`' . $allowed[$edit_youtube] . '`).val(`' . $allowed[$edit_youtube] . '`); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
	}

	die('form.find(`.alert`).remove(); Swal.fire({icon:`error`, html: `Something is not right`}); $(`input[name="csrf_token"]`).val(`' . $_SESSION['csrf_token'] . '`);');
}






$pagination = '
<section class="view_users center">
  <div class="paginate">
    <a  PREVLINK>Prev</a>
    <a  NEXTLINK>Next</a>
  </div>
</section>
';

//lets fetch the trending genres
if (isset($_GET['search'])) {
	$users = new Element();
	$users_data = $users->search(
		$table = "lentec_users",
		$cols = "id, name, email, is_admin, is_suspended, verify",
		$match = "keywords",
		$term = $_GET['search'],
		$order = " id DESC ",
	);
} else {
	$users = new Element();
	$users->activeTable = "lentec_users";
	$users->comparisons = [];
	$users->joiners = [''];
	$users->order = " BY id DESC ";
	$users->cols = "id, name, email, is_admin, is_suspended, verify";
	$users->limit = 50;
	$users->offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
	/*get_data*/
	$users_data = $users->getData($pagination);
}


//total number of registerd users
if (!isset($_SESSION['registered users'])) {
	$users->use_database();
	$_SESSION['registered'] = $users->database->query("SELECT COUNT(id) as total FROM lentec_users")->fetch_all(MYSQLI_ASSOC);
	$users->release_database();
}

//page data
$element = new Element();
$element->activeTable = "lentec_view_users";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
