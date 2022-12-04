<?php
session_start();
require_once '../controlers/sign_inControler.php';
require_once '../blades/header.php';
?>
<section class="area-sign-in">
	<div class="sign-in">
		<h3 class="center">Sign In</h3>
		<form action="<?php echo base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));  ?>" method="post" enctype="multipart/form-data" class="ajax">
			<input type="email" name="email" placeholder="Your Email" required="" data-help_text="Type email for your account here">
			<input type="password" name="password" placeholder="Password" required="" data-help_text="Type your password here">
			<input type="hidden" value="<?php echo csrf_token; ?>" name="csrf_token">
			<input type="text" name="honey_pot" class="honey_pot">
			<input type="submit" name="login" value="Sign In" data-help_text="Access your account here">
			<p>A new Member? <a href="<?php echo base_path; ?>sign_up">Sign Up</a></p><br>
			<p>Forgotten Password? <a href="<?php echo base_path; ?>reset_password">Resest Here</a></p>
		</form>
	</div>
</section>
<?php
require_once '../blades/footer.php';
?>