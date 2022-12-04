<?php
session_start();
require_once '../controlers/sign_upControler.php';
require_once '../blades/header.php';
?>
<section class="area-sign-in">
	<div class="sign-in" >
		<h3 class="center">Sign Up</h3>
		<form action="<?php echo base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));  ?>" method="post" enctype="multipart/form-data" class="ajax">
			<input type="text" name="name" placeholder="Your Name" required="" data-help_text="Please enter your name here">
			<input type="email" name="email" placeholder="Your Email" required="" data-help_text="Please enter your email here">
			<input type="password" name="password1" placeholder="Password" required="" data-help_text="Password of at least 8 characters in length and should include at least one upper case letter, one number, and one special character.">
			<input type="password" name="password2" placeholder="Confirm Password" required="" data-help_text="Confirm your password here">
			<div class="signin-rit">
				<span class="agree-checkbox">
				</span>
			</div>
			<input type="hidden" value="<?php echo csrf_token; ?>" name="csrf_token">
			<input type="text" name="honey_pot" class="honey_pot">
			<input type="submit" name="register" value="Sign Up" data-help_text="Submit your details here.">
			<p>Already have an Account? <a href="<?php echo base_path; ?>sign_in">Sign In</a></p>
		</form>
	</div>
</section>


<?php
require_once '../blades/footer.php';
?>