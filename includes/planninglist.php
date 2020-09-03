<?php

require_once('../config.php' );


?>

<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<th nowrap="nowrap" align="center" width="120">Công đoạn</th>
		<th  nowrap="nowrap" align="center">Nhân viên</th>

	
	  </tr>
	</thead>
	<?php
	$planning_id = '';
	if(isset($_REQUEST['planning_id']))
	{
		$planning_id = $_REQUEST['planning_id'];
	}
		
	$sql = "SELECT d1.workorder_id FROM mrp_workorder_planning d1 LEFT OUTER JOIN mrp_workorder d2 ON(d1.workorder_id = d2.id) WHERE d1.status =0 AND d1.id='".$planning_id."'";

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	$workorder_id = "";
	if($numrows>0)
	{
		$row = pg_fetch_array($result, 0);
		$workorder_id = $row["workorder_id"];
	}

	$sql = "SELECT d1.id, d3.category_name FROM mrp_workorder_routing d1";
	$sql = $sql." LEFT OUTER JOIN mrp_routing d2 ON(d1.routing_id = d2.id) lEFT OUTER JOIN mrp_routing_category d3 ON(d2.category_id = d3.id)";
	$sql = $sql." WHERE d1.workorder_id='".$workorder_id."'";
	$sql = $sql." AND d1.status =0";
	$sql = $sql." ORDER BY d1.sequence ASC, d2.sequence ASC, d1.create_date ASC";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	
	$sql = "SELECT d1.id, d1.routing_id, d1.quantity, d3.partner_code, d3.partner_name , (SELECT SUM(m.quantity) FROM mrp_workorder_routing_quantity m WHERE m.status =0 AND m.workorder_routing_id = d1.routing_id AND m.employee_id = d1.employee_id) AS done_quantity FROM mrp_workorder_planning_employee d1 LEFT OUTER JOIN hr_employee d2 ON(d1.employee_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) LEFT OUTER JOIN mrp_workorder_planning d4 ON(d1.planning_id = d4.id) WHERE d1.planning_id = '".$planning_id."' AND d1.status =0";
	$sql = $sql." ORDER BY d1.create_date ASC";
	

	
	$result_routing = pg_exec($db, $sql);
	$numrows_routing = pg_numrows($result_routing);	
	$employeeList = array();
	for($i = 0; $i < $numrows_routing; $i++) 
	{
		$row = pg_fetch_array($result_routing, $i);
		$arr = array();
		$arr[0] = $row["id"];
		$arr[1] = $row["routing_id"];
		$arr[2] = $row["quantity"];
		$arr[3] = $row["partner_code"];
		$arr[4] = $row["partner_name"];
		$arr[5] = $row["done_quantity"];
	
		$employeeList[$i] = $arr;
	}
	
	
	?>
	<tbody>
	<?php
	for($j =0; $j<$numrows; $j++)
	{
		$row = pg_fetch_array($result, $j);
		
		$id = $row["id"];
		$category_name = $row["category_name"];
	
	?>
		<tr>
			<td><?php echo $j + 1; ?></td>
			<td><?php echo $category_name;?></td>
			<td><a href="javascript:addPlanningEmployee('<?php echo $id; ?>')">+ Thêm</a>
			<br>
			<?php
			for($k =0; $k<count($employeeList); $k++)
			{
				$arr = $employeeList[$k];
				if($arr[1] == $id)
				{
					$planning_employee_id = $arr[0];
					$quantity = $arr[2];
					$employee_code = $arr[3];
					$employee_name = $arr[4];
					$done_quantity = $arr[5];
					if($quantity == "")
					{
						$quantity = "0";
					}
					if($done_quantity == "")
					{
						$done_quantity = "0";
					}
					
			?>
			<a href="javascript:delRoutingEmployee('<?php echo $planning_employee_id; ?>')">  - </a> <?php echo $employee_code;?>.<?php echo $employee_name;?>  <br> Số lượng: <input type="text" onblur="savePlanningEmployee('<?php echo $planning_employee_id; ?>', this, 'quantity');" style="text-align:right" value="<?php echo $quantity; ?>"/> /<?php echo $done_quantity;?> <br>
			<?php 
				}
			} 
			?>
			</td>
			
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>