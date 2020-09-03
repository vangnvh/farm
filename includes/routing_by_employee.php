<?php

require_once('../config.php' );

$date = "";
if(isset($_REQUEST['d']))
{
	$date = $_REQUEST['d'];
}
$employee_id = "";
if(isset($_REQUEST['employee_id']))
{
	$employee_id = $_REQUEST['employee_id'];
}

?>
Ngày: <?php echo $date;?>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<th nowrap="nowrap" align="center" >Công đoạn</th>
		<th  nowrap="nowrap"  width="100" align="center">Số lượng</th>
		<th  nowrap="nowrap"  width="100" align="center">Thành tiển</th>
		<th  nowrap="nowrap"  width="100" align="center">Thưởng</th>
		<th  nowrap="nowrap"  width="100" align="center">Phạt</th>
		<th  nowrap="nowrap"  width="100" align="center">Tổng</th>

	
	  </tr>
	</thead>
	<?php
	
		
	$sql = "SELECT SUM(d1.quantity) AS quantity, SUM(d1.quantity * d1.unit_price) AS amount, SUM(d1.bonus) AS bonus, SUM(d1.deduction) AS deduction, d2.id, d3.category_name FROM mrp_workorder_routing d1 LEFT OUTER JOIN mrp_routing d2 ON(d1.routing_id = d2.id) LEFT OUTER JOIN mrp_routing_category d3 ON(d2.category_id = d3.id) WHERE d1.status =0 AND d1.employee_id='".$employee_id."' AND d1.end_date>='".$date." 00:00:00' AND d1.end_date<='".$date." 23:59:59' GROUP BY d2.id, d3.category_name";
	

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	?>
	<tbody>
	<?php
	$grandTotal = 0;
	for($j =0; $j<$numrows; $j++)
	{
		$row = pg_fetch_array($result, $j);

		$category_name = $row["category_name"];
		$quantity = $row["quantity"];
		$amount = $row["amount"];
		$bonus = $row["bonus"];
		$deduction = $row["deduction"];
		$total= $amount + $bonus + $deduction;
		$grandTotal = $grandTotal + $total;
	
	?>
		<tr>
			<td width="30"><?php echo $j + 1; ?></td>
			<td><?php echo $category_name;?></td>
			<td style="text-align:right"><?php echo $quantity;?></td>
			<td style="text-align:right"><?php echo $amount;?></td>
			<td style="text-align:right"><?php echo $bonus;?></td>
			<td style="text-align:right"><?php echo $deduction;?></td>
			<td style="text-align:right"><?php echo $total;?></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="6"><b>Tổng:<b></td>
			
			<td style="text-align:right"><b><?php echo $grandTotal;?></b></td>
		</tr>
	</tbody>
</table>
</div>