<?php
session_start();
require_once '../controlers/homeControler.php';
require_once '../blades/header.php';




?>

<style>

</style>



<div class="container center"><br>
	<h2 class="non_ck" <?php $element->is_editable(current_page_table, 'title_text', 'text'); ?>><?php echo $title_text; ?></h2>
	<div class="holder">
		<div class="holder_item no_bottom">

			<button>
				<a href="<?php echo base_path; ?>meters">
					<img <?php $element->is_editable(current_page_table, 'image_1', 'image'); ?> src="<?php echo base_path . $image_1; ?>">
				</a>
			</button>
			<button><a href="<?php echo base_path; ?>meters">
					<h2 class="non_ck" <?php $element->is_editable(current_page_table, 'title_1', 'text'); ?>><?php echo $title_1; ?></h2>
					<h1><?php echo $_SESSION['total_meters']; ?></h1>
				</a>
			</button>
		</div>

		<div class="holder_item no_bottom">

			<button>
				<a href="<?php echo base_path; ?>equipment">
					<img <?php $element->is_editable(current_page_table, 'image_2', 'image'); ?> src="<?php echo base_path . $image_2; ?>">
				</a>
			</button>
			<button>
				<a href="<?php echo base_path; ?>equipment">
					<h2 class="non_ck" <?php $element->is_editable(current_page_table, 'title_2', 'text'); ?>><?php echo $title_2; ?></h2>
					<h1><?php echo $_SESSION['total_equipment']; ?></h1>
				</a>
			</button>
		</div>

		<div class="holder_item">
			<a href="<?php echo base_path; ?>equipment">
				<button>
					<a href="<?php echo base_path; ?>equipment">
						<img <?php $element->is_editable(current_page_table, 'image_3', 'image'); ?> src="<?php echo base_path . $image_3; ?>">
					</a>
				</button>
				<button>
					<a href="<?php echo base_path; ?>equipment">
						<h2 class="non_ck" <?php $element->is_editable(current_page_table, 'title_3', 'text'); ?>><?php echo $title_3; ?></h2>
						<h1><?php echo $_SESSION['total_rating']; ?></h1>
					</a>
				</button>
		</div>
	</div>
</div>


<?php
require_once '../blades/footer.php';
?>