<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
require_once '../app/extensions/app.validation.php';
// require_once '../app.extension/functions.php';
//relocation page
$page = base_path . (isset($_SESSION['current_page']) ? $_SESSION['current_page'] : 'home');
//loging in check
if (!isset($_SESSION['name'])) {
	//do something
} else {
	//do something
	if (!isset($_SESSION['admin_edits'])) {
		header('location:' . $page);
		die();
	}
}

//requesting new verification link
if (isset($_POST['login'])) {
	//check forgery
	$validation = new Validation();
	$validation->csrf();

	//lets validate the email
	if (!$validation->is_correct_email($_POST['email'])) {
		$validation->report("Failed! Check your email or password and try again.");
	}

	//check the password
	if (!$validation->is_correct_password($_POST['password'])) {
		$validation->report("Failed! Check your email or password and try again.");
	}

	$validation->activeTable = "lentec_users";
	$validation->comparisons = [
		["email", " = ", strip_tags($_POST['email'])],
		["password", " = ", base64_encode(md5(str_rot13($_POST['password'])))],
	];
	$validation->joiners = ['', ' && '];
	$validation->order = " BY id DESC ";
	$validation->cols = "*";
	$validation->limit = 1;
	$validation->offset = 0;
	$data = $validation->getData();
	if (count($data) === 0) {
		$validation->report("Failed! Check your email or password and try again.");
	}

	if ($data[0]['verify'] !== 'verified') {
		die(" window.location.href = `" . base_path . "verify_account/account/" . $data[0]['email'] . "/verify/none`");
	}

	if ($data[0]['is_suspended'] === 'Suspended') {
		$validation->report("Your account is suspended, contact admin.");
	}

	session_unset();
	session_destroy();
	session_start();


	$_SESSION['name'] = $data[0]['name'];
	$_SESSION['email'] = $data[0]['email'];
	$_SESSION['phone'] = $data[0]['phone'];
	$_SESSION['admin'] = $data[0]['is_admin'];

	if ($data[0]['is_admin'] === "Admin" || $data[0]['is_admin'] === "Main Admin") {
		$_SESSION['admin_edits'] = "set";
	}
	die(" window.location.href = `" . $page . "`");
}

$element = new Element();
$element->activeTable = "lentec_sign_in";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
