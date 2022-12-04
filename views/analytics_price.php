<?php
session_start();
require_once '../controlers/analytics_priceControler.php';
require_once '../blades/header.php';
?>
<div class="container ck">
  <h1 class="non_ck" <?php $element->is_editable(current_page_table, 'page_title_phrase', 'text'); ?>><?php echo $page_title_phrase; ?></h1>
  <div style="min-height:70vh; padding:50px 20px;" id="graph_parent">
    <form class="ajax inline center" id="graph_form" method="post" action="<?php echo base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));  ?>">
      <label for="start_date">Start Date: </label><input max="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d" ,strtotime("-365 days")); ?>" type="date" name="start_date" required>
      <label for="end_date">End Date: </label><input max="<?php echo date("Y-m-d"); ?>" value="<?php echo date("Y-m-d"); ?>" type="date" name="end_date" required>
      <label for="end_date">Chart type: </label>
      <select name="chart_type" required>
        <option value="bar">Bar</option>
        <option value="column">Column</option>
        <option value="pie">Pie</option>
        <option value="spline">Spline</option>
        <option value="splineArea">SplineArea</option>
        <option value="scatter">Scatter</option>
        <option value="doughnut">Doughnut</option>
        <option value="line">Line</option>
        <option value="stepLine">StepLine</option>
        <option value="stepArea">StepArea</option>
        <option value="pyramid">Pyramid</option>
      </select>
      <input type="submit" name="stats" value="Get">
    </form>

    <div id="graph">

    </div>
  </div>
</div>


<?php
require_once '../blades/footer.php';
?>