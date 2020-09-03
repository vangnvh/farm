<?php

require_once('../config.php' );


$ac = "view";
if(isset($_REQUEST['ac']))
{
	$ac = $_REQUEST['ac'];
}
if($ac == "view")
{
$order_id = "";
if(isset($_REQUEST['order_id']))
{
	$order_id = $_REQUEST['order_id'];
}

	
		
	$sql = "SELECT d1.receipt_no, d1.receipt_date, d1.delivery_date, d3.partner_name FROM sale_order d1 LEFT OUTER JOIN customer d2 ON(d1.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.status =0 AND d1.id='".$order_id."'";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);
	$receipt_no = "";
	$partner_name = "";
	$receipt_date = "";
	$delivery_date = "";
	if($numrows>0)
	{
		$row = pg_fetch_array($result, 0);

		$receipt_no = $row["receipt_no"];
		
		$partner_name = $row["partner_name"];
		$receipt_date = $row["receipt_date"];
		if($receipt_date != "")
		{
			$firstIndex = stripos($receipt_date, " ");
			if($firstIndex != -1)
			{
				$receipt_date = substr($receipt_date, 0, $firstIndex);
				$arr = explode("-", $receipt_date);
				if(count($arr)>2)
				{
					$receipt_date = $arr[1]."/". + $arr[2]."/". + $arr[0];
				}
			}
		}
		$delivery_date = $row["delivery_date"];
		if($delivery_date != "")
		{
			$firstIndex = stripos($delivery_date, " ");
			if($firstIndex != -1)
			{
				$delivery_date = substr($delivery_date, 0, $firstIndex);
				$arr = explode("-", $delivery_date);
				if(count($arr)>2)
				{
					$delivery_date = $arr[1]."/". + $arr[2]."/". + $arr[0];
				}
			}
		}
	}
	$sql = "SELECT d1.id FROM sale_order_product d1 WHERE d1.order_id='".$order_id."' AND d1.status =0 ORDER BY d1.create_date ASC";
	$result_product = pg_exec($db, $sql);
	$numrows_product = pg_numrows($result_product);

	
?>

<?php echo $receipt_no;?>. <?php echo $partner_name;?>
<br>
<?php echo $receipt_date;?> - <?php echo $delivery_date;?>
<?php
for($i=0; $i<$numrows_product; $i++)
{
?>
<div id="pnOrderView<?php echo $i;?>"></div>
<?php 
} 
?>
<script>
function loadOrderView(order_product_id , index)
{
	var _url = '<?php echo URL;?>includes/order_overview.php?ac=product&order_product_id=' + order_product_id;
	loadPage('pnOrderView' + index, _url, function(status, message)
	{
		if(status== 0)
		{
			
		}
		
	}, false);
}
<?php
for($i=0; $i<$numrows_product; $i++)
{
	$row = pg_fetch_array($result_product, $i);
	$order_product_id = $row["id"];
?>
loadOrderView('<?php echo $order_product_id;?>', <?php echo $i;?>);
<?php 
} 
?>
</script>

<?php
}else if($ac == "product")
{
	function get_millis(){
	  list($usec, $sec) = explode(' ', microtime());
	  return (int) ((int) $sec * 1000 + ((float) $usec * 1000));
	}
	$mi = get_millis();

	$order_product_id = "";
	if(isset($_REQUEST['order_product_id']))
	{
		$order_product_id = $_REQUEST['order_product_id'];
	}	

	$sql = "SELECT d1.id, d1.quantity, d2.name FROM sale_order_product d1 LEFT OUTER JOIN product d2 ON(d1.product_id = d2.id) WHERE d1.id='".$order_product_id."' AND d1.status =0 ORDER BY d1.create_date ASC";
	
	
	

	
	$result_product = pg_exec($db, $sql);
	$numrows_product = pg_numrows($result_product);
	for($i=0; $i<$numrows_product; $i++)
	{
		$row = pg_fetch_array($result_product, $i);
		$product_name = $row["name"];
		$order_product_id = $row["id"];
		$order_quantity = $row["quantity"];
		
		$sql = "SELECT d1.id, d4.category_name, (SELECT SUM(m.quantity) FROM mrp_workorder_routing m WHERE m.workorder_id = d3.id AND m.routing_id = d1.id) AS done_quantity FROM mrp_routing d1 LEFT OUTER JOIN mrp_production d2 ON(d1.production_id = d2.id) LEFT OUTER JOIN mrp_workorder d3 ON(d1.production_id = d3.production_id) LEFT OUTER JOIN mrp_routing_category d4 ON(d1.category_id = d4.id) WHERE d1.status =0 AND d3.order_product_id='".$order_product_id."' ORDER BY d1.create_date ASC";
		$result_routing = pg_exec($db, $sql);
		$numrows_routing = pg_numrows($result_routing);
		
		$w = $numrows_routing *68;
		if($w<400)
		{
			$w = 400;
		}
		
		$sql = "SELECT to_char(d1.end_date,'dd-MM-yyyy') as year_month, SUM(d1.quantity) FROM mrp_workorder_routing d1 LEFT OUTER JOIN mrp_workorder d2 ON(d1.workorder_id = d2.id) WHERE d1.status =0  AND d2.order_product_id='".$order_product_id."' GROUP BY 1 ORDER BY 1";
		$result_workorder_routing = pg_exec($db, $sql);
		$numrows_workorder_routing = pg_numrows($result_workorder_routing);
		
		$sql = "SELECT to_char(d1.end_date,'dd-MM-yyyy') as year_month, d1.routing_id, SUM(d1.quantity) AS quantity FROM mrp_workorder_routing d1 LEFT OUTER JOIN mrp_workorder d2 ON(d1.workorder_id = d2.id) WHERE d1.status =0  AND d2.order_product_id='".$order_product_id."' GROUP BY 1, d1.routing_id ORDER BY 1";
		$result_workorder_routing_1 = pg_exec($db, $sql);
		$numrows_workorder_routing_1 = pg_numrows($result_workorder_routing_1);
	
	
	
?>
<hr>
<b><?php echo $order_quantity;?>. <?php echo $product_name;?></b>
<div class="row">
	<div class="col-md-12">
		<div style="overflow:auto">
			<canvas id="chart_bar_category<?php echo $mi;?>" style="width:<?php echo $w;?>px" height="400px"></canvas >
		</div>	
		
	</div>
</div>
<script>
var barChartData<?php echo $mi;?> = {
	labels: [
	<?php
	for($j=0; $j<$numrows_routing; $j++)
	{
		$row_routing = pg_fetch_array($result_routing, $j);
		if($j>0)
		{
			echo ",";
		}
		$done_quantity = $row_routing["done_quantity"];
		$quantity = $order_quantity - $done_quantity;
		$per = 0;
		if($order_quantity != 0)
		{
			$per = ($done_quantity/ $order_quantity) * 100;
		}
		echo "'".$row_routing["category_name"]." (".$per."%)'";
		
	}
	?>
	],
	datasets: [
	{
		label: 'Hoàn thành',
		backgroundColor: '#00c292',
		data: [
			<?php
			for($j=0; $j<$numrows_routing; $j++)
			{
				$row_routing = pg_fetch_array($result_routing, $j);
				if($j>0)
				{
					echo ",";
				}
				echo $row_routing["done_quantity"];
			}
			?>
		]
	}
	, {
		label: 'Phải làm',
		backgroundColor: '#fb3a3a',
		data: [
			<?php
			for($j=0; $j<$numrows_routing; $j++)
			{
				$row_routing = pg_fetch_array($result_routing, $j);
				if($j>0)
				{
					echo ",";
				}
				$done_quantity = $row_routing["done_quantity"];
				$quantity = $order_quantity - $done_quantity;
				
				echo $quantity;
			}
			?>
		]
	}]

};

var ctx<?php echo $mi;?> = document.getElementById('chart_bar_category<?php echo $mi;?>').getContext('2d');
window.myBar<?php echo $mi;?> = new Chart(ctx<?php echo $mi;?>, {
	type: 'bar',
	data: barChartData<?php echo $mi;?>,
	options: {
		title: {
			display: true,
			text: 'Công đoạn'
		},
		tooltips: {
			mode: 'index',
			intersect: false
		},
		responsive: false,
		scales: {
			xAxes: [{
				stacked: true,
			}],
			yAxes: [{
				stacked: true
			}]
		}
	}
});
</script>


<?php
	}
}
?>
