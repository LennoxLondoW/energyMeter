
const toggles = document.querySelectorAll('.drop-down-menu');

function toggle(id) {
	//close other dropdowns
	for (var i = 0; i < toggles.length; i++) {
		if (toggles[i].id === id) {
			continue;
		}
		toggles[i].style.display = "none";;
	}
	var element = document.getElementById(id);
	if (element.style.display === "block") {
		element.style.display = "none";
	} else {
		element.style.display = "block";
	}
}


const icons = {
	equipment_name: 'fas fa-cog',
	file: 'fas fa-image',
	equipment_rating: 'fas fa-line-chart',
	equipment_quantity: 'fas fa-pie-chart',
	origin: 'fas fa-user',
	edit_field: 'fas fa-edit',
	meter_serial_number: 'fas fa-barcode',
	lat: 'fas fa-water',
	lng: 'fas fa-grip-lines-vertical',
	meter_name: 'fas fa-plug',
};


const placeholders = {
	equipment_name: 'Equipment Name',
	file: 'Item Image',
	equipment_rating: 'Equipment Rating',
	equipment_quantity: 'Equipment Quantity',
	origin: 'origin',
	edit_field: 'Edit field',
	meter_serial_number: 'Meter Serial Number',
	lat: 'Meter latitude location',
	lng: 'Meter Longitude location',
	meter_name: 'Meter Name',
};


var current = false;
var addingField = false;
var todelete = false;

$(function () {
	after_spa();
	$("body").on("click", ".close", function () {
		$(this).parents(".drop-down-menu").fadeOut(100);
	});

	$("body").on("click", ".addItem", function () {
		if ($(this).hasClass('edit_item')) {
			current = todelete = $(this);
			var json = current.find('textarea:first').val();
			
		}
		else {
			addingField = $("#main_item_field");
			var json = $(this).parent().find('textarea:first').val();
		}
		Swal.fire({
			html: form(JSON.parse(json)),
			confirmButtonText: 'Close',
		});
	});
});



function form(arr) {

	var string = "";
	string+= `
	
	<div class="main__content">

		<form class="ajax"  action="` + $("#base_path").val() + `home/edit/true"   method="post" enctype="multipart/form-data">
			<h2>`+arr['form_title']+`</h2>`;
			//let create those input fields
			for(var index in arr){ 
				if(index === 'id' || index === 'form_title'){
					continue;
				}
				string+=`
				<div class="input_wrap `+(index === "origin" || index === "edit_field"? "hidden":"")+`">
						<i class="`+icons[index]+`"></i>
						`+(
							index === 'file'? 
							    `
								<input placeholder="`+placeholders[index]+`" value="`+arr[index]+`" class="clr hidden" type="hidden" name="`+index+`" readonly>
								<input placeholder="`+placeholders[index]+`" class="clr" type="file" name="`+index+`" >
								`:
								`<input placeholder="`+placeholders[index]+`" value="`+arr[index]+`" class="clr" type="text" name="`+index+`" required>`
						)+`
						
				</div>`;
			}
						
			string+=`
				<div class="input_wrap left">
				    
					<input type="hidden" value="` + ($('input[name="csrf_token"]:first').val()) + `" name="csrf_token">
					`+(
						arr['edit_field'] === undefined?
						`<button class="submit" name="add_item" type="submit" style="color:#fff; background:#09c; padding:10px; border:none; outline:none; border-radius:5px;">Add New</button>`:
						`<input  type="hidden" name="edit_val" value="` + arr[arr['edit_field']] + `">
						<button class="submit" name="edit_item" type="submit" style="color:#fff; background:#09c; padding:10px; border:none; outline:none; border-radius:5px;">Save</button>`
					)+`
					
				</div>

		</form>

		`+(
			arr['edit_field'] === undefined?
			``:
			`<form class="ajax" action="` + $("#base_path").val() + `home/edit/true" method="post" enctype="multipart/form-data" >
				<div class="input_wrap left">
					<input  type="hidden" name="delete" value="` + arr[arr['edit_field']] + `">
					<input  type="hidden" name="index" value="` + arr['edit_field'] + `">
					<input  type="hidden" name="origin" value="` + arr['origin'] + `">
					<input type="hidden" value="` + ($('input[name="csrf_token"]:first').val()) + `" name="csrf_token">
					<button name="delete_item" onclick="if(confirm('Delete this artist')){$(this).parents('form').trigger('submit');}" class="submit" type="button" style="background:red;width: 100%;padding: 10px; border:none; outline:none; border-radius: 5px; color:#fff;">Delete</button>
				</div>
			</form>`

		)+`
		
	</div>`;
	return string;
}

// "equipment_name varchar(255) NOT NULL UNIQUE KEY",
// "equipment_rating double(10,2) NOT NULL",
// "equipment_quantity int(255) NOT NULL",
// "file varchar(255) NOT NULL",

function addEquipment() {
	return `
		<div class="main__content">
			<form class="ajax"  action="` + $("#base_path").val() + `home/edit/true"   method="post" enctype="multipart/form-data">
				<h2>Add new Equipment</h2>
				<div class="input_wrap">
						<i class="fas fa-user"></i>
						<input placeholder="Equipment name" class="clr" type="text" name="equipment_name" required>
				</div>

				<div class="input_wrap">
						<i class="fas fa-line-chart"></i>
						<input placeholder="Equipment Rating" class="clr" type="text" name="equipment_rating" required>
				</div>

				<div class="input_wrap">
						<i class="fas fa-pie-chart"></i>
						<input placeholder="Equipment Quantity" class="clr" type="text" name="equipment_quantity" required>
				</div>

				<div class="input_wrap">
						<i class="fas fa-image"></i>
						<input placeholder="Equipment name" class="clr" type="file" name="file" required>
				</div>


				<div class="input_wrap left">
						<input type="hidden" value="` + ($('input[name="csrf_token"]:first').val()) + `" name="csrf_token">
						<input type="hidden" value="lentec_equipment_data" name="origin">
						<button class="submit" name="add_item" type="submit" style="color:#fff; background:#09c; padding:10px; border:none; outline:none; border-radius:5px;">Submit</button>
				</div>
			</form>
		</div>`;
}


function before_spa() {
	$(".drop-down-menu, .nav-links").css("display", "none");
}

function after_spa() {
	if($("#period_date").attr("id") !== undefined){
		$('#period_date').Zebra_DatePicker();
	}

	if($("#graph_form").attr("id") !== undefined){
		$('#graph_form').trigger("submit");
	}
}


function openNav() {
	document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
	document.getElementById("mySidenav").style.width = "0";
}