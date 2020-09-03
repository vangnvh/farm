<?php
session_start();

$LOGIN_COMPANY_ID = "";
if(isset($_SESSION["company_id"]))
{
	$LOGIN_COMPANY_ID = $_SESSION["company_id"];
}
	
require_once('../config.php' );


?>
<style type="text/css">
.container .content {
    display: none;
    padding : 5px;
}
</style>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<th nowrap="nowrap" align="center" width="120">Tên sản phẩm</th>
		<th  nowrap="nowrap" align="center">Đơn vị tính</th>
		<th nowrap="nowrap" align="center" width="120">Số lượng</th>
		<th nowrap="nowrap" align="center" width="200">Bước thực hiện</th>
		<th nowrap="nowrap" align="center" width="200">Kế hoạch ngày</th>
	  </tr>
	</thead>
	<?php
	$product_ids = '';
	if(isset($_REQUEST['ids']))
	{
		$product_ids = $_REQUEST['ids'];
	}
	
	$items = explode(",", $product_ids);
	$product_ids = '';
	for($i =0; $i<count($items); $i++)
	{
		if($product_ids != "")
		{
			$product_ids = $product_ids." OR ";
		}
		$product_ids = $product_ids." d5.id = '".$items[$i]."'";
	}
	$sql = "SELECT d5.id, d2.name, d3.name AS unit_name, d5.quantity FROM sale_order_product d5 LEFT OUTER JOIN product d2 ON(d5.product_id = d2.id) LEFT OUTER JOIN product_unit d3 ON(d5.unit_id = d3.id) WHERE d5.status =0 AND (".$product_ids.") ORDER BY d5.create_date ASC";

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	

	

	
	?>
	<tbody>
		<?php
		
		
						
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$id = $row["id"];
			$name = $row["name"];
			$unit_name = $row["unit_name"];
			$quantity = $row["quantity"];
		?>
		<tr>
			<td><?php echo $j + 1; ?></td>
			<td><?php echo $name; ?></td>
			<td><?php echo $unit_name; ?></td>
			<td><?php echo $quantity; ?></td>
			<td>
			<div class="container">
				<div class="header"><span>  +  </span>

				</div>
				<div class="content" id="pnRouting<?php echo $j;?>">
					
				</div>
			</div>
				
			</td>
			<td>
			<div class="container">
				<div class="header"><span>  +  </span>

				</div>
				<div class="content" id="pnPlanning<?php echo $j;?>">
				</div>
			</div>
		
			</td>

		</tr>
		<tr>
			<td colspan="6" id="pnOrderViewProduct<?php echo $j;?>"></td>
		</tr>
		<?php
		}
		?>
		
	</tbody>
</table>
</div>
<script>
	$(".header").click(function () {
		
		$header = $(this);
		//getting the next element
		$content = $header.next();
		//open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
		$content.slideToggle(500, function () {
			//execute this after slideToggle is done
			//change text of header based on visibility of content div
			$header.text(function () {
				//change text based on condition
				return $content.is(":visible") ? "  -  " : "  +  ";
			});
		});

	});
	
	function loadOrderView(order_product_id , index)
	{
		var _url = '<?php echo URL;?>includes/order_overview.php?ac=product&order_product_id=' + order_product_id;
		loadPage('pnOrderViewProduct' + index, _url, function(status, message)
		{
			if(status== 0)
			{
				
			}
			
		}, false);
	}

	function loadRouting(order_product_id, index)
	{
		var _url = '<?php echo URL;?>includes/productlist.php?ac=routing&order_product_id='+ order_product_id + '&index=' + index;
		
		loadPage('pnRouting' + index, _url, function(status, message)
		{
			if(status== 0)
			{
				
			}
			
		}, false);
	}
	
	function addRouting(order_product_id, index)
	{
		var name = prompt("Please enter your name", "");
		if (name == null || name == "") {
		  return;
		}
		var _url = '<?php echo URL;?>includes/action.php?ac=addRouting';
		_url = _url + '&name=' + encodeURIComponent(name);
		_url = _url + '&order_product_id=' + order_product_id;
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					
					loadRouting(order_product_id, index);
				}
				else{
					alert(message);
				}
			}
			
		}, true);
		
	}
	
	function delRouting(routing_id, order_product_id, index)
	{
		var result = confirm("Want to delete");
		if (!result) {
			return;
		}
		var _url = '<?php echo URL;?>includes/action.php?ac=delRouting';
		_url = _url + '&id=' + routing_id;
		
		
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					
					loadRouting(order_product_id, index);
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	function addPlanning(order_product_id, index)
	{
		var name = prompt("Please enter your name", "");
		if (name == null || name == "") {
		  return;
		}
		var _url = '<?php echo URL;?>includes/action.php?ac=addPlanning';
		_url = _url + '&receipt_no=' + encodeURIComponent(name);
		_url = _url + '&order_product_id=' + order_product_id;
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message.length == 36)
				{
					
					planning(message);
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	function loadPlanning(order_product_id, index)
	{
		var _url = '<?php echo URL;?>includes/productlist.php?ac=planning&order_product_id='+ order_product_id + '&index=' + index;
		
		loadPage('pnPlanning' + index, _url, function(status, message)
		{
			if(status== 0)
			{
				
			}
			
		}, false);
	}
	function planning(planning_id)
	{
		document.location.href ='<?php echo URL;?>vi/orderform/planning/_/' + planning_id + '/home';
	}
	
	function delPlanning(id, order_product_id, index)
	{
		var result = confirm("Want to delete");
		if (!result) {
			return;
		}
		var _url = '<?php echo URL;?>includes/action.php?ac=delPlanning';
		_url = _url + '&id=' + id;
		
		
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					
					loadPlanning(order_product_id, index);
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	<?php
	for($j =0; $j<$numrows; $j++)
	{
		$row = pg_fetch_array($result, $j);
		$id = $row["id"];
	?>
	loadRouting('<?php echo $id;?>', <?php echo $j; ?>); 
	loadPlanning('<?php echo $id;?>', <?php echo $j; ?>); 
	loadOrderView('<?php echo $id;?>', <?php echo $j; ?>); 
	<?php
	}
	?>
</script>
