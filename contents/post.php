<?php
$page_name = "";

$sql = "select d1.id, d1.url_id, d1.name, dl.name AS name_lang, d1.url_name FROM post_category d1 LEFT OUTER JOIN res_lang_rel dl ON(d1.id = dl.rel_id AND dl.lang_id='".$lang_id."' AND dl.status =0 AND dl.column_name='name') WHERE d1.company_id='".COMPANY_ID."' AND d1.type='POST' AND d1.status =0 ORDER BY d1.sequence ASC";
$result_post_category = pg_exec($db, $sql);
$numrows_post_category = pg_numrows($result_post_category);
$url_id = "";

for($ri = 0; $ri < $numrows_post_category; $ri++) 
{
	$row = pg_fetch_array($result_post_category, $ri);
	if($row["id"] == $rel_id)
	{
		$page_name = $row["name_lang"];
		$url_id = $row["url_id"];
		if($page_name == '' )
		{
			$page_name =  $row["name"];
		}	
	}
}
$category_id = $rel_id;
$is_post = "0";

if($page_name == "")
{
	$sql = "select d1.id, d1.url_id, d1.name, dl.name AS name_lang, d1.url_name FROM post d1 LEFT OUTER JOIN res_lang_rel dl ON(d1.id = dl.rel_id AND dl.lang_id='".$lang_id."' AND dl.status =0 AND dl.column_name='name') WHERE d1.id='".$rel_id."' AND d1.status =0 ";
	
	
	$result_post = pg_exec($db, $sql);
	$numrows_post = pg_numrows($result_post);
	if($numrows_post>0)
	{
		$is_post = "1";
		$row_post = pg_fetch_array($result_post, 0);
		$post_id = $row_post["id"];
		$page_name = $row_post["name_lang"];
		$url_id = $row_post["url_id"];
		if($page_name == '' )
		{
			$page_name =  $row_post["name"];
		}	
	}
}

?>

<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo $page_name; ?></h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item <?php if($rel_id == ''){ ?> active <?php } ?>" ><a href="<?php echo URL;?><?php echo $lang; ?>/post"><?php echo __('post');?></a></li>
			<?php if($rel_id != ''){ ?>
			<li class="breadcrumb-item  active " aria-current="page"><?php echo $page_name; ?></li>
			<?php } ?>
		</ol>
	</div>

</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">
	<div class="content-wrap">
		
		<div class="container clearfix">
			<?php 
			if($LOGIN_CUSTOMER_ID != "")
			{
			?>
			<h3><?php echo __('Request Certification');?></h3>
			<div class="col_full nobottommargin">
				<a class="button button-3d nomargin" href="<?php echo URL;?><?php echo $lang; ?>/orderform/<?php echo $category_id; ?>"><?php echo __('Register');?></a>
				<br><br>
		
			<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30" align="center">#</th>
						<th nowrap="nowrap" align="center" style="text-align:center" width="60px"><?php echo __('Request No');?></th>
						<th nowrap="nowrap" align="center" style="text-align:center" width="80px"><?php echo __('Request Date');?></th>
						<th nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Description');?></th>
						<th nowrap="nowrap" align="center" style="text-align:center" width="80px"><?php echo __('Status');?></th>
						<th  width="120px" nowrap="nowrap" style="text-align:center" align="center"><?php echo __('Action');?></th>
					  </tr>
					</thead>
					<?php
					$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d1.status, d1.category_id, d1.value_1 FROM customer_register d1 WHERE d1.status != 1 AND d1.customer_id='".$LOGIN_CUSTOMER_ID."' AND d1.category_id='".$category_id."'  ORDER BY d1.receipt_date DESC";
					
					
					
					$result_order = pg_exec($db, $sql);
					$numrows_order = pg_numrows($result_order);	
					?>
					<tbody>
					  <tr>
						<?php
						for($j =0; $j<$numrows_order; $j++)
						{
							$row = pg_fetch_array($result_order, $j);
							
							$id = $row["id"];
							$receipt_no = $row["receipt_no"];
							$receipt_date = $row["receipt_date"];
							$status = $row["status"];
							$category_id = $row["category_id"];
							$description = "";
							if($category_id == "7637f6d7-19dc-4426-8778-b1d3871efa1e")
							{
								$description = $row["value_1"];
								$description = str_replace(";", " +  ", $description);
								if($description != "")
								{
									$description = substr($description, 2);
								}
							}
							if($receipt_date != "")
							{
								$firstIndex = stripos($receipt_date, " ");
								if($firstIndex != -1)
								{
									$receipt_date = substr($receipt_date, 0, $firstIndex);
									$arr = explode("-", $receipt_date);
									if(count($arr)>2)
									{
										$receipt_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
									}
								}
							}
							
						
						?>
						<td><?php echo $j + 1; ?></td>
						<td><a href="<?php echo URL;?><?php echo $lang; ?>/orderform/<?php echo $category_id; ?>/<?php echo $id; ?>"><?php echo $receipt_no; ?></a></td>
						<td align="center"><?php echo $receipt_date; ?></td>
						<td><?php echo $description; ?></td>
						<td align="center" >
						<?php 
						if($status == "0")
						{
						?>
						<?php echo __('Creating');?>
						<?php
						}else if($status == "2")
						{
						?>
						<?php echo __('To Vinacert');?>
						<?php
						}else if($status == "3")
						{
						?>
						<?php echo __('Approval');?>
						<?php
						}else if($status == "4")
						{
						?>
						<?php echo __('Reject');?>
						<?php
						}
						?>
						</td>
						<td nowrap="nowrap" align="center">
						<a href="<?php echo URL;?><?php echo $lang; ?>/orderform/<?php echo $category_id;?>/<?php echo $id; ?>"><?php echo __('Edit');?></a>
						<?php 
						if($status == "0")
						{
						?>
						| <a  href="javascript:postToVinaCert('<?php echo $id; ?>')"><?php echo __('To Vinacert');?></a>
						| <a href="javascript:delPost('<?php echo $id; ?>')"><?php echo __('Delete');?></a>
						<?php
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
				<br>
				<?php
					$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d1.status, d1.issue_date, d1.verify_date, d1.verify_next_date, d3.name AS category_name, d1.barcode, d4.name AS status_name FROM sale_order d1 LEFT OUTER JOIN sale_order_category d3 ON(d1.category_id = d3.id) LEFT OUTER JOIN sale_order_status d4 ON(d1.status_id = d4.id) WHERE d1.status =0 AND d1.type='CERTIFICATION' AND d1.customer_id='".$LOGIN_CUSTOMER_ID."'  ORDER BY d1.receipt_date DESC";
					$result_order = pg_exec($db, $sql);
					$numrows_order = pg_numrows($result_order);	
					if($numrows_order>0)
					{
					?>
				<h3><?php echo __('Certification');?></h3>
			<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30" align="center">#</th>
						<th width="60px" nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Order No');?></th>
						<th width="80px" nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Order Date');?></th>
						<th nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Order Type');?></th>
						<th width="80px" nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Certification_Code');?></th>
						<th width="80px" nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Issue Date');?></th>
						<th width="80px" nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Status');?></th>
						<th width="80px" nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Verify Date');?></th>
						<th width="80px" nowrap="nowrap" align="center" style="text-align:center"><?php echo __('Next Verify Date');?></th>
						
						
					  </tr>
					</thead>
					
					<tbody>
					  <tr>
						<?php
						for($j =0; $j<$numrows_order; $j++)
						{
							$row = pg_fetch_array($result_order, $j);
							
							$id = $row["id"];
							$receipt_no = $row["receipt_no"];
							
							$status = $row["status"];
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
										$receipt_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
									}
								}
							}
							
							$issue_date = $row["issue_date"];
							if($issue_date != "")
							{
								$firstIndex = stripos($issue_date, " ");
								if($firstIndex != -1)
								{
									$issue_date = substr($issue_date, 0, $firstIndex);
									$arr = explode("-", $issue_date);
									if(count($arr)>2)
									{
										$issue_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
									}
								}
							}
							
							$verify_date = $row["verify_date"];
							if($verify_date != "")
							{
								$firstIndex = stripos($verify_date, " ");
								if($firstIndex != -1)
								{
									$verify_date = substr($verify_date, 0, $firstIndex);
									$arr = explode("-", $verify_date);
									if(count($arr)>2)
									{
										$verify_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
									}
								}
							}
							
							$verify_next_date = $row["verify_next_date"];
							if($verify_next_date != "")
							{
								$firstIndex = stripos($verify_next_date, " ");
								if($firstIndex != -1)
								{
									$verify_next_date = substr($verify_next_date, 0, $firstIndex);
									$arr = explode("-", $verify_next_date);
									if(count($arr)>2)
									{
										$verify_next_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
									}
								}
							}
							$category_name = $row["category_name"];
							$barcode = $row["barcode"];
							$status_name = $row["status_name"];
							
							
							
						?>
						<td><?php echo $j + 1; ?></td>
						<td><?php echo $receipt_no; ?></td>
						<td align="center"><?php echo $receipt_date; ?></td>
						<td nowrap="nowrap" align="center"><?php echo $category_name;?></div>
						<td nowrap="nowrap" align="center"><?php echo $barcode;?></div>
						<td nowrap="nowrap" align="center"><?php echo $issue_date;?></div>
						<td nowrap="nowrap" align="center"><?php echo $status_name;?></div>
						<td nowrap="nowrap" align="center"><?php echo $verify_date;?></div>
						<td nowrap="nowrap" align="center"><?php echo $verify_next_date;?></div>
						
						
						</td>
						
					  </tr>
					 <?php
						}
						?>
					</tbody>
				  </table>
				</div>
			</div>
			<?php
			}
			?>
			<br>
			
					
			<?php
			
			//Display Product
			if($category_id == "53660c5d-61b2-43b2-b1e3-23ac95aad0cd")
			{
			?>
			
			<h3><?php echo __('Standard Base');?></h3>
			<div class="col_full nobottommargin">
				<a class="button button-3d nomargin" href="<?php echo URL;?><?php echo $lang; ?>/productform/new/<?php echo $LOGIN_CUSTOMER_ID; ?>" value="login"><?php echo __('New');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/productform/list_customer/<?php echo $LOGIN_CUSTOMER_ID; ?>"><?php echo __('Customers');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/productform/list_customer_all/<?php echo $LOGIN_CUSTOMER_ID; ?>"><?php echo __('All Customers');?></a> <a class="button button-3d button-black nomargin" href="<?php echo SERVER_URL;?>report/index?report_id=be8fcf96-73b6-4c05-a9cb-2b81cbc8f791&type=view&id=<?php echo $LOGIN_CUSTOMER_ID;?>" target="_blank"><?php echo __('View');?></a>
				
				</div>
				
				<br>
				<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30" style="text-align:center; vertical-align:middle">#</th>
						<th width="40" style="text-align:center; vertical-align:middle"><input value = "" type="checkbox" class="form-control" name="[]" onclick="doHandleCheckAll(this)" /></th>
						<th nowrap="nowrap" style="text-align:center; vertical-align:middle"><?php echo __('Private Name');?></th>
						<th nowrap="nowrap" style="text-align:center; vertical-align:middle"><?php echo __('Product Name');?></th>
						<th nowrap="nowrap" style="text-align:center; vertical-align:middle"><?php echo __('Common Name');?></th>
	
						
						<th  width="60px" nowrap="nowrap" style="text-align:center; vertical-align:middle"><?php echo __('Version');?></th>
						<th  width="100px" nowrap="nowrap" style="text-align:center; vertical-align:middle"><?php echo __('Updated Date');?></th>
						<th  width="120px" nowrap="nowrap" style="text-align:center; vertical-align:middle"><?php echo __('Private Status');?></th>
						<th  width="120px" nowrap="nowrap" style="text-align:center; vertical-align:middle"><?php echo __('Action');?></th>
						
					  </tr>
					</thead>
					<?php
					$sql = "SELECT d1.id, d1.code, d1.name, d1.common_name, d1.version, d1.reference, d1.write_date FROM product_private d1 WHERE d1.status =0 AND d1.customer_id='".$LOGIN_CUSTOMER_ID."' AND d1.type !='FROMCUSTOMER'  ORDER BY d1.create_date ASC";
					
					
					$result = pg_exec($db, $sql);
					$numrows = pg_numrows($result);	
					?>
					<tbody>
					 
						<?php
						for($j =0; $j<$numrows; $j++)
						{
							$row = pg_fetch_array($result, $j);
							
							$private_id = $row["id"];
							$code = $row["code"];
							$name = $row["name"];
							$common_name = $row["common_name"];
							$version = $row["version"];
							$write_date = $row["write_date"];
							
							if($write_date != "")
							{
								$firstIndex = stripos($write_date, " ");
								if($firstIndex != -1)
								{
									$write_date = substr($write_date, 0, $firstIndex);
									$arr = explode("-", $write_date);
									if(count($arr)>2)
									{
										$write_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
									}
								}
							}
							
							$sql = "SELECT d1.id, d1.version, d1.create_date, d3.category_name, d4.conformity_code FROM product_private d1 LEFT OUTER JOIN notification d2 ON(d1.id = d2.notification_id AND d2.status =0) LEFT OUTER JOIN notification_category d3 ON(d2.category_id = d3.id) LEFT OUTER JOIN sale_order_product d4 ON(d1.id = d4.private_id) WHERE d1.status =0 AND d1.parent_id='".$private_id."' AND d1.type='FROMCUSTOMER' ORDER BY d1.create_date DESC";
							
							$result1 = pg_exec($db, $sql);
							$numrows1 = pg_numrows($result1);	
							
							
						?>
						 <tr>
						<td style="text-align:center; vertical-align:middle"><?php echo $j + 1; ?></td>
						<td style="text-align:center; vertical-align:middle"><input type="checkbox" class="form-control" name="[]" id="<?php echo $private_id;?>" /></td>
						
						<td style="text-align:center; vertical-align:middle"><a href="<?php echo SERVER_URL;?>report/index?report_id=819bf5cc-85fc-488b-87d2-f2eca4054796&type=view&id=<?php echo $private_id;?>" target="_blank"><?php echo $code; ?></a></td>
						<td style="text-align:left; vertical-align:middle"><?php echo $name; ?></td>
						<td style="text-align:left; vertical-align:middle"><?php echo $common_name; ?></td>
						<td style="text-align:center; vertical-align:middle"><?php echo $version; ?></td>
						<td style="text-align:center; vertical-align:middle"><?php echo $write_date; ?></td>
						<td style="text-align:left; vertical-align:middle">
							<?php
							for($n =0; $n<$numrows1; $n++)
							{
								$row1 = pg_fetch_array($result1, $n);
								$version1 = $row1["version"];
								$category_name = $row1["category_name"];
								$create_date = $row1["create_date"];
								$conformity_code = $row1["conformity_code"];
								if($create_date != "")
								{
									$firstIndex = stripos($create_date, " ");
									if($firstIndex != -1)
									{
										$create_date = substr($create_date, 0, $firstIndex);
										$arr = explode("-", $create_date);
										if(count($arr)>2)
										{
											$create_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
										}
									}
								}
								$send_id = $row1["id"];
								if($n>0)
								{
									echo "<br> ";
								}
							?>
							<?php echo $n + 1; ?>. <a href="<?php echo SERVER_URL;?>report/index?report_id=819bf5cc-85fc-488b-87d2-f2eca4054796&type=view&id=<?php echo $send_id;?>" target="_blank"><?php echo $version1; ?> - <?php echo $create_date;?> </a> <?php if($conformity_code != ""){?> #<?php echo $conformity_code;?> <?php }?> <?php if($category_name != ""){?> (<?php echo $category_name;?>) <?php }?> | <a href="javascript:clonePrivate('<?php echo $send_id; ?>')"><?php echo __('Clone');?></a> <a href="javascript:delPrivate('<?php echo $send_id; ?>')"><?php echo __('Delete');?></a>
							<?php
							}
							?>
						</td>
						
						<td nowrap="nowrap" style="text-align:left; vertical-align:middle"><a href="<?php echo URL;?><?php echo $lang; ?>/productform/edit/<?php echo $private_id; ?>"><?php echo __('Edit');?></a> | <a href="javascript:clonePrivate('<?php echo $private_id; ?>')"><?php echo __('Clone');?></a> <br> <a  href="javascript:privateToVinaCert('<?php echo $private_id; ?>')"><?php echo __('To Vinacert');?></a> | <a href="javascript:delPrivate('<?php echo $private_id; ?>')"><?php echo __('Delete');?></a></td>
						
					  </tr>
					 <?php
						}
						?>
					</tbody>
				  </table>
				</div>
				<br>
				<?php
				if($LOGIN_CUSTOMER_ID != ""){
			$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d1.status FROM sale_request d1 WHERE (d1.status =-1 OR d1.status =0) AND d1.customer_id='".$LOGIN_CUSTOMER_ID."'  ORDER BY d1.receipt_date DESC";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);	
			if($numrows>0)
			{
			?>
			<br>
			<h3><?php echo __('Testing');?></h3>
			<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30" align="center">#</th>
						<th nowrap="nowrap" align="center"><?php echo __('Request No');?></th>
						<th nowrap="nowrap" align="center"><?php echo __('Request Date');?></th>
						<th  width="120px" nowrap="nowrap" align="center"><?php echo __('Action');?></th>
						
					  </tr>
					</thead>
					
					<tbody>
					  <tr>
						<?php
						for($j =0; $j<$numrows; $j++)
						{
							$row = pg_fetch_array($result, $j);
							
							$id = $row["id"];
							$receipt_no = $row["receipt_no"];
							$receipt_date = $row["receipt_date"];
							$status = $row["status"];
							if($receipt_date != "")
							{
								$firstIndex = stripos($receipt_date, " ");
								if($firstIndex != -1)
								{
									$receipt_date = substr($receipt_date, 0, $firstIndex);
									$arr = explode("-", $receipt_date);
									if(count($arr)>2)
									{
										$receipt_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
									}
								}
							}
							
					
							
						?>
						<td><?php echo $j + 1; ?></td>
						<td><a href="<?php echo URL;?><?php echo $lang; ?>/requestform/<?php echo $category_id?>/<?php echo $id; ?>"><?php echo $receipt_no; ?></a></td>
						<td align="center"><?php echo $receipt_date; ?></td>
						<td nowrap="nowrap" align="center">
						<?php 
						if($status == "-1")
						{
						?>
						<a href="javascript:delSaleRequest('<?php echo $id; ?>')"><?php echo __('Delete');?></a>
						<?php
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
			<?php
			}
			}
			?>
				<?php echo __('With checked');?>:   <a  class="button button-green nomargin" href="javascript:privateToVinaCertChecked()"><?php echo __('To Vinacert');?></a> <a  class="button button-black nomargin" href="javascript:privateDelProduct()"><?php echo __('Delete');?></a>
				
				<script>
		function delPrivate(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delPrivate';
			_url = _url + '&id=' + encodeURIComponent(id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/product';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function report(id, type)
		{
			var _url = '<?php echo SERVER_URL;?>report/index?report_id=be8fcf96-73b6-4c05-a9cb-2b81cbc8f791&type=' + type + '&id=' + id;
			window.open(_url);
		}
		function privateToVinaCertChecked()
		{
			
			var checkboxes = document.getElementsByName("[]");
			var ids = "";
			for (var i= 0; i<checkboxes.length; i++) {
				if(checkboxes[i].checked && checkboxes[i].value != "")
				{
					if(ids != "")
					{
						ids += ",";
					}
					ids += checkboxes[i].id;
				}
			}
			if(ids == "")
			{
				alert("<?php echo __('Please check product to send');?>");
				return;
			}
			privateToVinaCert(ids);
		}
		function privateDelProduct()
		{
			var count = 0;
			var checkboxes = document.getElementsByName("[]");
			var ids = "";
			for (var i= 0; i<checkboxes.length; i++) {
				if(checkboxes[i].checked && checkboxes[i].value != "")
				{
					if(ids != "")
					{
						ids += ",";
					}
					ids += checkboxes[i].id;
					count += 1;
				}
			}
			if(ids == "")
			{
				alert("<?php echo __('Please check product to delete');?>");
				return;
			}
			var result = confirm(count + ". <?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=privateDelProduct';
			_url = _url + '&ids=' + encodeURIComponent(ids);
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/4-Chung-nhan-san-pham';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		
		function privateToVinaCert(id)
		{
			
			var result = confirm("<?php echo __('Are you sure to send');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=privateToVinaCert';
			_url = _url + '&private_id=' + encodeURIComponent(id);
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/4-Chung-nhan-san-pham';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function clonePrivate(private_id)
		{
			var result = confirm("<?php echo __('Are you sure to clone');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=clonePrivate';
			_url = _url + '&private_id=' + private_id;
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message.length == 36)
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/productform/edit/' + message + '/<?php echo $LOGIN_CUSTOMER_ID; ?>';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
	</script>
				
			<?php
			}
			?>
			<?php
			}
			?>
			<br>
			<br>
			<div class="row">

				<div class="col-lg-10 bottommargin">
					<?php
					if($is_post == "0")
					{
					for($ri = 0; $ri < $numrows_post_category; $ri++) 
					{
						$row = pg_fetch_array($result_post_category, $ri);
						$url_id =$row["url_id"];
						$name = $row["name_lang"];
						if($name == '')
						{
							$name =  $row["name"];
						}
						if($rel_id != '' && $row["id"] != $rel_id)
						{
								continue;
						}
						$id =$row["id"];
						
						$sql = "SELECT d2.id, d2.name, d2.photo, d2.description, d2.receipt_date, d2.url_id, d2.url_name FROM post_category_rel d1 LEFT OUTER JOIN post d2 ON(d1.rel_id = d2.id) WHERE d1.status =0 AND d2.status =0 AND d1.category_id ='".$id."'";
						$result_post = pg_exec($db, $sql);
						$numrows_post = pg_numrows($result_post);					
					?>
					<div class="col_full bottommargin-lg clearfix">
						
						<div id="posts">
							<?php
							for($j = 0; $j < $numrows_post; $j++) 
							{
								$row_post = pg_fetch_array($result_post, $j);
								$post_name = $row_post["name"];
								$post_photo = $row_post["photo"];
								$post_description = $row_post["description"];
								$post_date = $row_post["receipt_date"];
								$post_url_id =$row_post["url_id"];
								$post_url_name = $row_post["url_name"];
										
								if($post_date != "")
								{
									$firstIndex = stripos($post_date, " ");
									if($firstIndex != -1)
									{
										$post_date = substr($post_date, 0, $firstIndex);
										$arr = explode("-", $post_date);
										if(count($arr)>2)
										{
											$post_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
										}
									}
								}
							?>
							<div class="entry clearfix">
								<div class="entry-title">
									<h2><a href="blog-single.html"><?php echo $post_name; ?></a></h2>
								</div>
								<ul class="entry-meta clearfix">
									<li><i class="icon-calendar3"></i> <?php echo $post_date; ?></li>
								</ul>
								<div class="entry-content">
									<p><?php echo $post_description; ?></p>
									<a href="<?php echo URL;?><?php echo $lang; ?>/<?php echo $post_url_id; ?>-<?php echo $post_url_name; ?>"class="more-link"><?php echo __('Read More');?></a>
								</div>
							</div>
							<?php
							}
							?>
							
							
						</div>
					</div>
					<?php }
					}else{
						$sql = "SELECT resource FROM res_resource WHERE id='".$rel_id."'";
						$result_post = pg_exec($db, $sql);
						$numrows_post = pg_numrows($result_post);
						if($numrows_post>0)
						{
							
							$row_post = pg_fetch_array($result_post, 0);
							echo $row_post["resource"];
						}
					}
					?>

				</div>
				<div class="col-lg-2">
					<div class="widget  clearfix">

						<h4><?php echo __('post');?></h4>
						<div class="col_full widget_recent_comments nobottommargin">
							<ul>
								<?php
								for($ri = 0; $ri < $numrows_post_category; $ri++) 
								{
									$row = pg_fetch_array($result_post_category, $ri);
									$url_id =$row["url_id"];
									$name = $row["name_lang"];
									$url_name = $row["url_name"];
									if($name == '')
									{
										$name =  $row["name"];
									}
									if($url_name == "")
									{
										$url_name = $name;
									}
									
								?>
								<li><a href="<?php echo URL;?><?php echo $lang; ?>/<?php echo $url_id; ?>-<?php echo $url_name; ?>"><img src="<?php echo URL;?>assets/images/icons/widget-link.png"> <?php echo $name; ?></a></li>
								<?php } ?>
							</ul>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</section><!-- #content end -->
<script>
	function delPost(id)
	{
		var result = confirm("<?php echo __('Want to delete?');?>");
		if (!result) {
			return;
		}
		var _url = '<?php echo URL;?>includes/action.php?ac=delPost';
		_url = _url + '&id=' + encodeURIComponent(id);
		
		
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					
					document.location.reload();
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	function postToVinaCert(id)
	{
		var result = confirm("<?php echo __('Are you sure to send');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=postToVinaCert';
			_url = _url + '&id=' + encodeURIComponent(id);
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.reload();
					}
					else{
						alert(message);
					}
				}
				
			}, true);
	}
</script>


