<?php
if (!defined('__path__')) {
	define('__path__', isset($__path__) ? $__path__ : "../");
}

require_once __path__ . 'app/app.php';
class FrontEnd extends App
{
	public function displayEquipment($rows)
	{
		$equipment = $GLOBALS['equipment'];

		if(count($rows) === 0) {
			return '<div class="_404">'._404.'</div>';
		}

		$dynamic = $equipment->_NEXT === 'no' ? '' : "<j data-dynamic='" . base_path . 'equipment/dynamic/true/'.(isset($_GET['edit'])? 'edit/true/':'').'offset/' . $equipment->_NEXT . "'>
		      <a style='display:none;' href='" . base_path . 'equipment/dynamic/true/offset/' . $equipment->_NEXT . "'></a>
		</j>";

		$class = isset($_SESSION['admin_edits']) && isset($_GET['edit']) ? "addItem edit_item" : "";

		$string = "";
		foreach ($rows as $row) {
			$row['origin'] = $equipment->activeTable;
			$row['form_title'] = 'Edit Equipment';
			$row['edit_field'] = 'file';
			$string.= '
				<button class="item left w-250 '.$class.'">
					<textarea class="hidden">'.json_encode($row).'</textarea>
					<div class="image-wrap">
					   <img src="' . base_path.$row['file'] . '">
					</div>
					<div class="content">
						<table>
						   <tbody>
							  <tr>
								 <td class="no-border-left">Name</td>
								 <td class="no-border-right">'.$row['equipment_name'].'</td>
							  </tr>
	  
							  <tr>
								 <td class="no-border-left">Rating</td>
								 <td class="no-border-right">'.$row['equipment_rating'].'</td>
							  </tr>
	  
							  <tr>
								 <td class="no-border-left">Quantity</td>
								 <td class="no-border-right">'.$row['equipment_quantity'].'</td>
							  </tr>
	  
						   </tbody>
						</table>
					</div>
				</button>';
		  }

		  return $string.$dynamic;
	}


	public function displayMeter($rows, $pag = true)
	{
		$meters = $GLOBALS['meters'];

		if(count($rows) === 0) {
			return '<div class="_404">'._404.'</div>';
		}

		
		$class = isset($_SESSION['admin_edits']) && isset($_GET['edit']) ? "addItem edit_item" : "";

		$string = "";
		foreach ($rows as $row) {
			$row['origin'] = $meters->activeTable;
			$row['form_title'] = 'Edit Meter';
			$row['edit_field'] = 'meter_serial_number';
			$string.= '
			<div class="'.$class.'">
			<textarea class="hidden">'.json_encode($row).'</textarea>
			<h2 class="center">'.$row['meter_name'].'</h2>
			<h3 class="center">SNO:'.$row['meter_serial_number'].'</h3>
			<h4 class="center">['.$row['lat'].','.$row['lng'].']</h4>
			<iframe style="width: 100%; height: 70vh" id="gmap_canvas" src="https://maps.google.com/maps?q='.$row['lat'].','.$row['lng'].'&t=k&z=20&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
			</div>
				'.($pag? $meters->_PAGINATION:"").'
			
			';
			
		  }

		  return $string;
	}

	public function drawPriceCharts($type, $rows, $title, $x=["Bill Incured", "Ksh."], $y=["Date", ""], $div="graph"){
		if(count($rows) === 0){
			echo '$("#'.$div.'").html(`<form><div class="alert alert-danger">There is no data here</div></form>`);';
			return;
		}

		$json =  json_encode($rows);

		echo '
		var options'.$div.' = {
			exportEnabled: true,
			animationEnabled: true,
			zoomEnabled: true,
			title: {
				text: "'.$title.'"              
			},
			axisY: {
				title: "'.$x[0].'",
				prefix: "'.$x[1].'"
			},
			axisX: {
				title: "'.$y[0].'",
				prefix: "'.$y[1].'"
			},
			data: [              
			{
				type: "'.$type.'",
				dataPoints: JSON.parse(`'.$json.'`)
			}
			]
		};
		
		$("#'.$div.'").CanvasJSChart(options'.$div.');
		$("html,body").animate({
			scrollTop: $("#graph").offset().top - 100
		  }, 500);
		';

	}
}

$frontEnd = new FrontEnd();
