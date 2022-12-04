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
if (isset($_POST['change_password'])) {
	//check forgery


	$validation = new Validation();
	$validation->csrf();
	//lets validate the email
	if (!$validation->is_correct_email($_POST['email'])) {
		$validation->report('This account does not exist.');
	}

	//check the password
	if (!$validation->is_correct_password($_POST['password1'])) {
		$validation->report('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
	}

	//check the password matchind
	if ($_POST['password1'] !== $_POST['password2']) {
		$validation->report('Passwords do not match.');
	}

	$validation->activeTable = "lentec_users";
	$validation->comparisons = [["email", " = ", strip_tags($_POST['email'])],];
	$validation->joiners = [''];
	$validation->update_data = ["password" => base64_encode(md5(str_rot13($_POST['password1'])))];
	$data = $validation->getData();
	if (!$validation->updateData()) {
		$validation->report('Something wrong has happened, please contact admin');
	}
	$validation->report("Password changed. <a href='" . base_path . "sign_in'>Proceed to login</a>", "success");
}







//body creation
if (!isset($_GET['account']) || !isset($_GET['token'])) {
	if (!isset($_SESSION['admin_edits'])) {
		header('location:' . $page);
		die();
	}
}


$validation = new Validation();
//lets validate the email
if (!$validation->is_correct_email($_GET['account'])) {
	if (!isset($_SESSION['admin_edits'])) {
		header('location:' . $page);
		die();
	}
}

$token = strip_tags(($_GET['token']));

//lets verify user
$validation->activeTable = 'lentec_users';
$validation->comparisons = [
	["email", " = ", strip_tags($_GET['account'])],
	["password", " = ", $token],
];
$validation->joiners = ['', ' && '];
$validation->order = " BY id DESC ";
$validation->cols = "*";
$validation->limit = 1;
$validation->offset = 0;
$data = $validation->getData();
if (count($data) > 0) {
	//supply passord reset form 
	$html = '
	<form action="' . base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF'])) . '" method="post" enctype="multipart/form-data" class="ajax">
		<input type="hidden" name="email" value="' . strip_tags($_GET['account']) . '" readonly>
		<input type="password" name="password1" placeholder="Password" required="" data-help_text="Password of at least 8 characters in length and should include at least one upper case letter, one number, and one special character.">
		<input type="password" name="password2" placeholder="Confirm Password" required="" data-help_text="Confirm your password here">
		<input type="hidden" value="' . csrf_token . '" name="csrf_token">
		<input type="text" name="honey_pot" class="honey_pot">
		<input type="submit" name="change_password" value="Save" data-help_text="Click here to update your password">
	</form>
	<h6 data-help_text="If everything looks fine, log in from here"><a href="' . base_path . 'sign_in">Sign in now</a></h6>';
} else {
	$html = '
		<div class="alert alert-info">Request Denied.</div>
		<h6  ><a href="' . base_path . 'sign_in">Sign In Now</a> </h6>
		';
}




$element = new Element();
$element->activeTable = "lentec_change_password";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
