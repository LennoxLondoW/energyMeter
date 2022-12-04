<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
require_once '../app/extensions/app.validation.php';
//loging in check
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
if (isset($_POST['reset'])) {
	//check forgery


	$validation = new Validation();
	$validation->csrf();
	//lets validate the email
	if (!$validation->is_correct_email($_POST['email'])) {
		$validation->report('his account does not exist.');
	}

	$validation->activeTable = "lentec_users";
	$validation->comparisons = [["email", " = ", strip_tags($_POST['email'])], ["verify", " = ", "verified"]];
	$validation->joiners = ['', ' && '];
	$validation->order = " BY id DESC ";
	$validation->cols = "*";
	$validation->limit = 1;
	$validation->offset = 0;
	$data = $validation->getData();
	if (count($data) === 0) {
		$validation->report('This account does not exist or has not been verified.');
	}

	//lets generare random password
	$password = bin2hex(random_bytes(rand(8, 10)));
	$validation->update_data = ["password" => $password];
	if (!$validation->updateData()) {
		$validation->report('Something is not right, please try again.');
	}

	$limit = isset($_SESSION['LIMIT2']) ? $_SESSION['LIMIT2'] : 0;
	if (++$limit > 5) {
		$validation->report('request denied. Please try again later');
	}
	$_SESSION['LIMIT2'] = $limit;
	$validation->email_username = $data[0]['name'];
	$link = scheme . $_SERVER['SERVER_NAME'] . base_path . 'change_password/account/' . $_POST['email'] . '/' . 'token/' . $password;
	$validation->email_message = '
   								<h3>Password reset</h3>
								<p>
									Hello ' . $data[0]['name'] . ', You requested for password reset link. Click the link below to reset your password.<br><br>
									<div><a style="background:#09c; color:#fff; border-radius: 5px; border:none; outline:none; padding:10px;" href="' . $link . '">Reset</a></div>
									<br>
									<br>
									If you have trouble clicking the link, open this url in your browser. <br> ' . $link . '
								</p>
   							';
	$validation->email_subject = 'Password Resetting';
	$validation->email_to = $_POST['email'];
	$validation->email_cc = [];
	$validation->email_attachment = false;
	// send email
	if (!$validation->send_email()) {
		$validation->report($validation->email_error);
	}
	$validation->report('Please check your email for password reset link.', 'success');
}

$element = new Element();
$element->activeTable = "lentec_reset_password";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
