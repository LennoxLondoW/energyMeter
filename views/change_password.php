<?php
session_start();
require_once '../controlers/change_passwordControler.php';
require_once '../blades/header.php';
?>
<section class="area-sign-in">
	<div class="sign-in">
		<h3 class="center">Enter New Password</h3>
		<?php echo $html; ?>
	</div>
</section>
<?php
require_once '../blades/footer.php';
?>

