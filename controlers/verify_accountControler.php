<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
require_once '../app/extensions/app.validation.php';

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
if (isset($_POST['request_email'])) {
	//check forgery
	$validation = new Validation();
	$validation->csrf();
	//lets validate the email
	if (!$validation->is_correct_email($_POST['email'])) {
		$validation->report('This account does not exist.');
	}

	$validation->activeTable = "lentec_users";
	$validation->comparisons = [["email", " = ", strip_tags($_POST['email'])],];
	$validation->joiners = [''];
	$validation->order = " BY id DESC ";
	$validation->cols = "*";
	$validation->limit = 1;
	$validation->offset = 0;
	$data = $validation->getData();
	if (count($data) === 0) {
		$validation->report('This account does not exist.');
	}

	if ($data[0]['verify'] === 'verified') {
		$validation->report("This account has already been verified. <a href='" . base_path . "sign_in'>Proceed to login</a>");
	}
	//limit emails
	$limit = isset($_SESSION['LIMIT']) ? $_SESSION['LIMIT'] : 0;
	if (++$limit > 5) {
		$validation->report('request denied. Please try again later');
	}
	$_SESSION['LIMIT'] = $limit;
	$validation->email_username = $data[0]['name'];
	$link = scheme . $_SERVER['SERVER_NAME'] . base_path . 'verify_account/account/' . $_POST['email'] . '/' . 'verify/' . $data[0]['verify'];
	$validation->email_message = '
   								<h3>Verify Your Account</h3>
								<p>
									Hello ' . $data[0]['name'] . ', Click the link below to verify your account.<br><br>
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
	$validation->report('Please check your email for an activation link.', 'success');
}



//body creation
if (!isset($_GET['account']) || !isset($_GET['verify'])) {
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

$verify = strip_tags(($_GET['verify']));
if ($verify === "none") {
	$html = '
		<div class="alert alert-info">A verification link was sent to your email, please click it to verify your account.</div>
			<form action="' . base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF'])) . '" method="post" enctype="multipart/form-data" class="ajax">
				<input type="hidden" name="email" value="' . strip_tags($_GET['account']) . '" readonly>
          		<input type="hidden" value="' . csrf_token . '" name="csrf_token">
				<input type="text" name="honey_pot" class="honey_pot">
				<input  type="submit" name="request_email" value="Request another email"  data-help_text="If you did not receive any email, you can request a new one here.">
				<p>Not a memeber? <a href="' . base_path . 'sign_up">Sign Up</a></p>
				</form>
				';
} else {
	//lets verify user
	$validation->activeTable = 'lentec_users';
	$validation->comparisons = [
		["email", " = ", strip_tags($_GET['account'])],
		["verify", " = ", $verify],
	];
	$validation->joiners = ['', ' && '];
	$validation->order = " BY id DESC ";
	$validation->cols = "*";
	$validation->limit = 1;
	$validation->offset = 0;
	$data = $validation->getData();
	if (count($data) > 0) {
		//update account as verified
		$validation->comparisons = [
			["email", " = ", strip_tags($_GET['account'])],
		];
		$validation->joiners = [''];
		$validation->update_data = ['verify' => 'verified'];
		if (!$validation->updateData()) {
			$html = '<div class="alert alert-info">Something wrong has happened, please contact admin</div>';
		} else {
			$html = '<div class="alert alert-info">Account verified. <a href="' . base_path . 'sign_in">Proceed to login</a></div>';
		}
	} elseif ($data[0]['verify'] === 'verified') {
		$html = '<div class="alert alert-info">This account has already been verified. <a href="' . base_path . 'sign_in">Proceed to login</a></div>';
	} else {
		$html = '
		<div class="alert alert-info">Account Verification Failed.</div>
			<form action="' . base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF'])) . '" method="post" enctype="multipart/form-data" class="ajax">
				<input type="hidden" name="email" value="' . strip_tags($_GET['account']) . '" readonly>
          		<input type="hidden" value="' . csrf_token . '" name="csrf_token">
				<input type="text" name="honey_pot" class="honey_pot">
				<input type="submit" name="request_email" value="Request another email" data-help_text="If you did not receive any email, you can request a new one here.">
				<p>Not a memeber? <a href="' . base_path . 'sign_up">Sign Up</a></p>
				</form>
		';
	}
}



$element = new Element();
$element->activeTable = "lentec_verify_account";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element->GetElementData();
