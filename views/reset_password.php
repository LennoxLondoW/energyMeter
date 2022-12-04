<?php
session_start();
require_once '../controlers/reset_passwordControler.php';
require_once '../blades/header.php';
?>

<section class="area-sign-in">
	<div class="sign-in">
		<h3 class="center">Reset Password</h3>
		<form action="<?php echo base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));  ?>" method="post" enctype="multipart/form-data" class="ajax">
			<input type="email" name="email" placeholder="Your Email" required="" data-help_text="Enter your email here for us to send a password reset link">
			<input type="hidden" value="<?php echo csrf_token; ?>" name="csrf_token">
			<input type="text" name="honey_pot" class="honey_pot">
			<input type="submit" name="reset" value="Reset Password" data-help_text="Request a reset link from here">
			<p>Or <a href="<?php echo base_path; ?>sign_in">Sign In</a></p>
		</form>
	</div>
</section>


<?php
require_once '../blades/footer.php';
?>