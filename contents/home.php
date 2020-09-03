<?php
$company_code = "";
$company_name = "";
$sql = "select d2.partner_code, d2.partner_name FROM res_company d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.id='".$LOGIN_EMPLOYEE_ID."'";
$result = pg_exec($db, $sql);
$numrows = pg_numrows($result);
if($numrows>0)
{
	$row = pg_fetch_array($result, 0);
	$company_code = $row["partner_code"];
	$company_name = $row["partner_name"];
}
?>

<section id="content">

	<div class="content-wrap">

		<div class="container clearfix">
		<?php
		if($LOGIN_USER_ID != '')
		{
		?>
			<div class="row">
				<div class="col-sm-6">
					<a href="<?php echo URL;?><?php echo $lang; ?>/employee">Nhân viên</a>
					<br>
					<?php
					$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.email, d2.address, d2.barcode FROM hr_employee d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."' lIMIT 10";
					$result = pg_exec($db, $sql);
					$numrows = pg_numrows($result);	
					?>
					<div class="table-responsive">
					  <table class="table table-bordered nobottommargin">
						<thead>
						  <tr>
							<th width="30">#</th>
							
							<th  width="80" nowrap="nowrap"><?php echo __('Employee Code');?></th>
							<th nowrap="nowrap"><?php echo __('Employee Name');?></th>
							<th  width="80" nowrap="nowrap"><?php echo __('Phone');?></th>
							<th  width="80" nowrap="nowrap"><?php echo __('Email');?></th>
						  </tr>
						</thead>
						<tbody>
						  <tr>
							<?php
							for($j =0; $j<$numrows; $j++)
							{
								$row = pg_fetch_array($result, $j);
								
								$employee_id = $row["id"];
								$partner_code = $row["partner_code"];
								$partner_name = $row["partner_name"];
								$phone = $row["phone"];
								$email = $row["email"];
								$address = $row["address"];
						
							?>
							<td><?php echo $j + 1; ?></td>
		
							<td><?php echo $partner_code; ?></td>
							<td><?php echo $partner_name; ?></td>
							<td><?php echo $email; ?></td>
							<td><?php echo $phone; ?></td>
						  </tr>
						 <?php
							}
							?>
						</tbody>
					  </table>
					</div>
				</div>
			
				<div class="col-sm-6">
				<a href="<?php echo URL;?><?php echo $lang; ?>/customer">Khách hàng</a>
				<br>
				<?php
				$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.email, d2.address FROM customer d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."' lIMIT 10";
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);	
				?>
				<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30">#</th>
						
						<th  width="80" nowrap="nowrap"><?php echo __('Code');?></th>
						<th nowrap="nowrap"><?php echo __('Name');?></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Phone');?></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Email');?></th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<?php
						for($j =0; $j<$numrows; $j++)
						{
							$row = pg_fetch_array($result, $j);
							
							$employee_id = $row["id"];
							$partner_code = $row["partner_code"];
							$partner_name = $row["partner_name"];
							$phone = $row["phone"];
							$email = $row["email"];
							$address = $row["address"];
					
						?>
						<td><?php echo $j + 1; ?></td>
	
						<td><?php echo $partner_code; ?></td>
						<td><?php echo $partner_name; ?></td>
						<td><?php echo $email; ?></td>
						<td><?php echo $phone; ?></td>
					  </tr>
					 <?php
						}
						?>
					</tbody>
				  </table>
				</div>
			</div>
		</div>
		<br>
		<?php
			$sql = "SELECT d1.id FROM hr_employee d1 WHERE d1.status =0 AND d1.rel_id='".$LOGIN_USER_ID."'";
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
			if($employee_ids != "")
			{
				$sql = "SELECT d1.rel_id, d3.name AS product_name, d2.quantity FROM hr_employee_rel d1 LEFT OUTER JOIN sale_order_product d2 ON(d1.rel_id = d2.id) LEFT OUTER JOIN product d3 ON(d2.product_id = d3.id) WHERE d1.status =0 AND (".$employee_ids.")";
				
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);	
				
				$employee_ids = "";
				for($j =0; $j<$numrows; $j++)
				{
					$row = pg_fetch_array($result, $j);
					
					$id = $row["rel_id"];
					if($employee_ids != "")
					{
						$employee_ids = $employee_ids.",";
					}
					$employee_ids = $employee_ids."".$id."";
				}
			}
			if($employee_ids != "")
			{
				
		?>
		<div class="row">
			<div class="col-sm-12">
				<h3>Kết hoạch sản xuất</h3>
				<br>
				
				<div id="pnProducts"></div>
				<script>
					function loadProduct()
					{
						var _url = '<?php echo URL;?>includes/productassign.php?ids=<?php echo $employee_ids;?>';
						
						loadPage('pnProducts', _url, function(status, message)
						{
							if(status== 0)
							{
								
							}
							
						}, false);
					}
					
					
					loadProduct();
					
				</script>
			</div>
		</div>
		<br>
		<?php
		}
		?>
		<?php
			$sql = "SELECT d1.id FROM customer d1 WHERE d1.status =0 AND d1.rel_id='".$LOGIN_USER_ID."'";
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
				$employee_ids = $employee_ids." d1.customer_id ='".$id."'";
			}
			if($employee_ids != "")
			{
				
			$first_order_id = "";
						
			$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d1.delivery_date, d3.partner_name FROM sale_order d1 LEFT OUTER JOIN customer d2 ON(d1.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.status =0 AND (".$employee_ids.") AND d1.delivery_date>='".date("Y-m-d")." 00:00:00' ORDER BY d1.create_date DESC";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);	
			if($numrows>0)
			{
		?>
		<h3>Đơn hàng đặt</h3>
		<div class="row">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-2">
						<?php
						
							for($i =0; $i<$numrows; $i++)
							{
								$row = pg_fetch_array($result, $i);
								$order_id = $row["id"];
								$receipt_no = $row["receipt_no"];
								if($first_order_id == "")
								{
									$first_order_id = $order_id;
								}
								
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
						?>
						<div class="spost clearfix">
									
							<div class="entry-c">
								<div class="entry-title">
									<h4><a href="javascript:loadOrder('<?php echo $order_id;?>')"><?php echo $receipt_no;?>. <?php echo $partner_name;?></a></h4>
								</div>
								<ul class="entry-meta">
									<li class="color"><?php echo $receipt_date;?> - <?php echo $delivery_date;?></li>
									
								</ul>
							</div>
						</div>
						<?php
							}
						?>
					</div>
					<div class="col-sm-10">
						<div id="pnRouting"></div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function loadOrder(order_id)
			{
				var _url = '<?php echo URL;?>includes/order_overview.php?order_id=' + order_id;
				loadPage('pnRouting', _url, function(status, message)
				{
					if(status== 0)
					{
						
					}
					
				}, false);
			}
			<?php 
			if($first_order_id  != "")
			{
			?>
			loadOrder('<?php echo $first_order_id;?>');
			<?php
			}
			?>
		</script>
		<?php
			}
		}
		?>
		<?php
		$first_order_id = "";
						
		$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d1.delivery_date, d3.partner_name FROM sale_order d1 LEFT OUTER JOIN customer d2 ON(d1.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."' AND d1.delivery_date>='".date("Y-m-d")." 00:00:00' ORDER BY d1.create_date DESC";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		if($numrows>0)
		{
		?>
		<h3>Đơn hàng</h3>
		<div class="row">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-2">
						<?php
							
							for($i =0; $i<$numrows; $i++)
							{
								$row = pg_fetch_array($result, $i);
								$order_id = $row["id"];
								$receipt_no = $row["receipt_no"];
								if($first_order_id == "")
								{
									$first_order_id = $order_id;
								}
								
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
						?>
						<div class="spost clearfix">
									
							<div class="entry-c">
								<div class="entry-title">
									<h4><a href="javascript:loadOrder('<?php echo $order_id;?>')"><?php echo $receipt_no;?>. <?php echo $partner_name;?></a></h4>
								</div>
								<ul class="entry-meta">
									<li class="color"><?php echo $receipt_date;?> - <?php echo $delivery_date;?></li>
									
								</ul>
							</div>
						</div>
						<?php
							}
						?>
					</div>
					<div class="col-sm-10">
						<div id="pnRouting"></div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function loadOrder(order_id)
			{
				var _url = '<?php echo URL;?>includes/order_overview.php?order_id=' + order_id;
				loadPage('pnRouting', _url, function(status, message)
				{
					if(status== 0)
					{
						
					}
					
				}, false);
			}
			<?php 
			if($first_order_id  != "")
			{
			?>
			loadOrder('<?php echo $first_order_id;?>');
			<?php
			}
			?>
		</script>
		</div>
	<?php
		}
	}else
	{
	?>
	Nội dung trang chủ
	<?php
	}
	?>
</div>

</section>