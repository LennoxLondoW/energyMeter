<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
require_once '../app/extensions/app.validation.php';
// require_once '../app.extension/functions.php';

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



if (isset($_POST['register'])) {
	$validation = new Validation();
	$validation->activeTable = "lentec_users";
	//check forgery
	$validation->csrf();
	//check if valid username
	if (!$validation->is_correct_name($_POST['name'])) {
		$validation->report('Enter a valid name');
	}

	//check if valid email
	if (!$validation->is_correct_email($_POST['email'])) {
		$validation->report('Enter a valid is_email');
	}
	//check the password
	if (!$validation->is_correct_password($_POST['password1'])) {
		$validation->report('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
	}

	//check the password matchind
	if ($_POST['password1'] !== $_POST['password2']) {
		$validation->report('Passwords do not match.');
	}

	$validation->insertData = [
		[
			"name" => $_POST['name'],
			"email" => $_POST['email'],
			"password" => base64_encode(md5(str_rot13($_POST['password1']))),
			"verify" => ($verify = md5(uniqid() . $_POST['email'] . time())),
			"is_admin" => "Not Admin",
			"is_suspended" => "Not Suspended",
			"keywords" => $_POST['name'] . " " . $_POST['email'],
		]
	];

	//lets try to store this data
	$save = $validation->saveData(false);
	if ($save === "success") {
		//here we send email
		$validation->email_username = $_POST['name'];
		$link = scheme . $_SERVER['SERVER_NAME'] . base_path . 'verify_account/account/' . $_POST['email'] . '/' . 'verify/' . $verify;
		$validation->email_message = '
   								<h3>Verify Your Account</h3>
								<p>
									Hello ' . $_POST['name'] . ', Click the link below to verify your account.<br><br>
									<div><a style="background:#09c; color:#fff; border-radius: 5px; border:none; outline:none; padding:10px;" href="' . $link . '">Verify</a></div>
									<br>
									<br>
									If you have trouble clicking the link, open this url in your browser. <br> ' . $link . '
								</p>
   							';
		$validation->email_subject = 'Account Verification';
		$validation->email_to = $_POST['email'];
		$validation->email_cc = [];
		$validation->email_attachment = false;
		// send email
		if (!$validation->send_email()) {
			$validation->report($validation->email_error);
		}
		die(" window.location.href = `" . base_path . "verify_account/account/" . $_POST['email'] . "/verify/none`");
	} elseif (strstr(strtolower($save), "duplicate entry") !== false) {
		$validation->report('This account already exists.');
	} else {
		$validation->report('Something is not right, please try again.');
	}
}

$element = new Element();
$element->activeTable = "lentec_sign_up";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
