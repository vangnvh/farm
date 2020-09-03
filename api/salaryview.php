<?php

require_once('../config.php' );

$fdate = "";
if(isset($_REQUEST['fdate']))
{
	$fdate = $_REQUEST['fdate'];
}
$tdate = "";
if(isset($_REQUEST['tdate']))
{
	$tdate = $_REQUEST['tdate'];
}
$user_id = "";
if(isset($_REQUEST['user_id']))
{
	$user_id = $_REQUEST['user_id'];
}
$sql = "SELECT d1.id FROM hr_employee d1 WHERE d1.status =0 AND d1.rel_id='".$user_id."'";
$result = pg_exec($db, $sql);
$numrows = pg_numrows($result);	
$employee_ids = "";
for($j =0; $j<$numrows; $j++)
{
	$row = pg_fetch_array($result, $j);
	
	$id = $row["id"];
	if($employee_ids != "")
	{
		$employee_ids = $employee_ids." OR ";
	}
	$employee_ids = $employee_ids." d1.employee_id='".$id."'";
}
if($employee_ids == "")
{
	$employee_ids = "1=0";
}
?>
Ngày: <?php echo $fdate;?> - <?php echo $tdate;?>
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
	
		
	$sql = "SELECT SUM(d1.quantity) AS quantity, SUM(d1.quantity * d1.unit_price) AS amount, SUM(d1.bonus) AS bonus, SUM(d1.deduction) AS deduction, d2.id, d3.category_name FROM mrp_workorder_routing d1 LEFT OUTER JOIN mrp_routing d2 ON(d1.routing_id = d2.id) LEFT OUTER JOIN mrp_routing_category d3 ON(d2.category_id = d3.id) WHERE d1.status =0 AND (".$employee_ids.") AND d1.end_date>='".$fdate."' AND d1.end_date<='".$tdate."' GROUP BY d2.id, d3.category_name";
	

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