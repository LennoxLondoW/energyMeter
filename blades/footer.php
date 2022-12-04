<?php
if (!isAjax) {
	echo '</div>';
	if ($element->page_editable) {
		$current_page = basename(str_replace(".php", "", $_SERVER["PHP_SELF"]));
?>

		<div class="edit_div">
			<table>
				<caption>
					<h3>Customize Page Meta Tags</h3>
				</caption>
				<thead>
					<tr>
						<th>Object</th>
						<th>Data</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Page Title</td>
						<td><span class="non_ck" <?php $element->is_editable(current_page_table, 'page_title', 'text'); ?>><?php echo $page_title;  ?> </span></td>
					</tr>
					<tr>
						<td>Page Description</td>
						<td><span class="non_ck" <?php $element->is_editable(current_page_table, 'page_description', 'text'); ?>><?php echo $page_description;  ?> </span></td>
					</tr>
					<tr>
						<td>Page Keywords</td>
						<td><span class="non_ck" <?php $element->is_editable(current_page_table, 'page_keywords', 'text'); ?>><?php echo $page_keywords;  ?> </span></td>
					</tr>
					<tr>
						<td>Site Location</td>
						<td><span class="non_ck" <?php $element->is_editable('navbar', 'site_location', 'text'); ?>><?php echo $site_location;  ?></td>
					</tr>
					<tr>
						<td>Site 404</td>
						<td><span class="non_ck" <?php $element->is_editable('navbar', 'site_404', 'text'); ?>><?php echo $site_404;  ?> </span></td>
					</tr>
					<tr>
						<td>Og:locale</td>
						<td><span class="non_ck" <?php $element->is_editable('navbar', 'og_locale', 'text'); ?>><?php echo $og_locale;  ?> </span></td>
					</tr>
					<tr>
						<td>Og:site_name</td>
						<td><span class="non_ck" <?php $element->is_editable('navbar', 'og_sitename', 'text'); ?>><?php echo $og_sitename;  ?> </span></td>
					</tr>
					<tr>
						<td>Og:type</td>
						<td><span class="non_ck" <?php $element->is_editable('navbar', 'og_type', 'text'); ?>><?php echo $og_type;  ?> </span></td>
					</tr>
					<tr>
						<td>Twitter:card</td>
						<td><span class="non_ck" <?php $element->is_editable('navbar', 'og_twittercard', 'text'); ?>><?php echo $og_twittercard;  ?> </span></td>
					</tr>
					<tr>
						<td>Page Icon</td>
						<td><span> <img style="height:20px; width:20px;" <?php $element->is_editable(current_page_table, 'page_icon', 'image'); ?> src="<?php echo base_path . $page_icon; ?>"></span></td>
					</tr>
				</tbody>
			</table>
		</div>

	<?php
	}
	?>

	<footer>



		<ul class="menu">
			<li class="first leaf menu-mlid-720">
				<a href="https://oglio.com/" class="non_spa">
					&copy;<?php echo date("Y"); ?> <span class="non_ck" <?php $element->is_editable('navbar', 'og_sitename', 'text'); ?>><?php echo $og_sitename; ?></span>
				</a>
			</li>
		</ul>
	</footer>




	<!-- app js  -->
	<script src="<?php echo base_path; ?>js/app.js/jquery.min.js?v=2.9"></script>
	<script src="<?php echo base_path; ?>js/app.js/sweetalert.js?v=2.9"></script>
	<script src="<?php echo base_path; ?>js/app.js/spa.js?v=3.91"></script>
	<script src="<?php echo base_path; ?>js/app.js/functions.js?v=2.913"></script>
	<script src="https://cdn.ckeditor.com/4.19.1/full/ckeditor.js"></script>
	<script src="<?php echo base_path; ?>js/app.js/helper.js?v=2.9"></script>
	<!-- <script src="<?php echo base_path; ?>plugins/apk/application.js?v=5"></script> -->
	<i class="add-to" style="position: fixed; right: 10px; bottom: 150px; background: #FF8D1B; border-radius: 5px; padding: 5px; color: #fff; cursor: pointer; display: none;"> <i class="fa fa-download settings add-to-btn"></i></i>

	<!-- app js  -->
	<script src="<?php echo base_path; ?>plugins/date_picker/zebra_datepicker.min.js"></script>
	<script src="<?php echo base_path; ?>plugins/canvasjs/jquery.canvasjs.min.js"></script>
	<script src="<?php echo base_path; ?>js/index.js?v=2.991"></script>

	<script>
		$("body").on("click", ".Zebra_DatePicker_Icon, #period_date", function() {
			$("#meter_serial_number").val("");
			$(".temp").remove();
		})

		$("body").on("change", "#meter_serial_number", function() {
			var val1 = $("#period_date").val();
			var val2 = $("#meter_serial_number").val();

			if (val1.replace(/ /g, '') != '' && val2.replace(/ /g, '') != '') {
				var link = $("#base_path").val() + $("#current_page_table").val() + "/unique_id/" + encodeURI(val1 + val2);
				var div = `<div class="temp"><div data-dynamic="` + link + `"></div></div>`;
				var form = $(this).parents('form');
				form.find('.temp').remove();
				form.append(div);

				loadDynamic();

				//activate dynamics

			}
		});

		$("body").on("change", "#_date_", function() {
			var val1 = $("#_date_").val();

			if (val1.replace(/ /g, '') != '') {
				var link = $("#base_path").val() + $("#current_page_table").val() + "/unique_id/" + encodeURI(val1);
				var div = `<div class="temp"><div data-dynamic="` + link + `"></div></div>`;
				var form = $(this).parents('form');
				form.find('.temp').remove();
				form.append(div);
				loadDynamic();
			}
		});


		function toogleDataSeries(e) {
			if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
				e.dataSeries.visible = false;
			} else {
				e.dataSeries.visible = true;
			}
			e.chart.render();
		}

		function isJsonString(str) {
			try {
				var json = JSON.parse(str);
			} catch (e) {
				return false;
			}
			return json;
		}

		$("body").on("submit", ".bulk_equpment", function(e) {
			// [{
			// 	"period_date": "2022-02-19",
			// 	"total_hours": 5.4,
			// 	"meter_json": {
			// 		"equipment_name": "Television",
			// 		"equipment_rating": "1.20",
			// 		"equipment_quantity": "40"
			// 	}
			// }]
			e.preventDefault();
			$(this).find(".check").remove();
			var arr = isJsonString($(this).find('textarea:first').val());

			if (!arr) {
				alert("Enter a valid json");
				return;
			}


			if (arr.length > 100) {
				alert("Only upto 50 elements allowed");
				return;
			}

			var meter_serial_number = $(this).find('select:first').val();


			for (var i in arr) {
				arr[i]['bypass'] = 'set';
				arr[i]['meter_json'] = meter_serial_number;
				console.log(arr[i]);
				$.ajax({
					url: "?",
					type: "POST",
					timeout: 60000,
					data: arr[i],
					success: function(result) {
						// console.log(result);
						eval(result);

					},
					error: function(a, b, c) {
						console.log(result);
					}
				});

			}
			return false;
		});


		$("body").on("submit", ".bulk_meter", function(e) {
			e.preventDefault();
			$(this).find(".check").remove();
			var arr = isJsonString($(this).find('textarea:first').val());

			if (!arr) {
				alert("Enter a valid json");
				return;
			}


			if (arr.length > 100) {
				alert("Only upto 50 elements allowed");
				return;
			}

			var meter_serial_number = $(this).find('select:first').val();


			for (var i in arr) {
				arr[i]['bypass'] = 'set';
				arr[i]['meter_serial_number'] = meter_serial_number;
				console.log(arr[i]);
				$.ajax({
					url: "?",
					type: "POST",
					timeout: 60000,
					data: arr[i],
					success: function(result) {
						console.log(result);
						eval(result);

					},
					error: function(a, b, c) {
						console.log(result);
					}
				});

			}
			return false;
		});
	</script>
	</body>

	</html>
<?php } ?>