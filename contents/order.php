<?php
$page_name = "";

$sql = "select d1.id, d1.url_id, d1.name, dl.name AS name_lang FROM faq_category d1 LEFT OUTER JOIN res_lang_rel dl ON(d1.id = dl.rel_id AND dl.lang_id='".$lang_id."' AND dl.status =0 AND dl.column_name='name') WHERE d1.company_id='".COMPANY_ID."' AND d1.status =0 ORDER BY d1.sequence ASC";
$result = pg_exec($db, $sql);
$numrows = pg_numrows($result);
for($ri = 0; $ri < $numrows; $ri++) 
{
	$row = pg_fetch_array($result, $ri);
	if($row["id"] == $rel_id)
	{
		$page_name = $row["name_lang"];
		if($page_name == '' )
		{
			$page_name =  $row["name"];
		}	
	}
	
}
$search = "";
$p = 0;
$items = explode("&", $PARAMS);
for($i = 0; $i<count($items); $i++)
{
	$arr = explode("=", $items[$i]);
	if(count($arr) == 2)
	{
		if($arr[0] == "search")
		{
			$search = urldecode($arr[1]);
		}
		if($arr[0] == "p")
		{
			$p = urldecode($arr[1]);
		}
	}
}
	

?>
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Orders');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item <?php if($rel_id == ''){ ?> active <?php } ?>" ><a href="<?php echo URL;?><?php echo $lang; ?>/order"><?php echo __('Orders');?></a></li>
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

				<div class="row">
					<div class="col-sm-8">
						<a class="button button-3d nomargin" href="<?php echo URL;?><?php echo $lang; ?>/orderform/new/<?php echo $LOGIN_CUSTOMER_ID; ?>" value="login"><?php echo __('New Order');?></a>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<input type="text" id="editsearch" class="form-control" value="<?php echo $search; ?>" placeholder="<?php echo __('Search');?>">
							<div class="input-group-prepend">
								<a href="javascript:doSearch()" class="input-group-text"><?php echo __('Search');?></a>
							</div>
						</div>
					</div>
				</div>
		
				
				<br>
				<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30">#</th>
						<th width="40" style="text-align:center; vertical-align:middle"><input value = "" type="checkbox" class="form-control" name="[]" onclick="doHandleCheckAll(this)" /></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Receipt No');?></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Receipt Date');?></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Delivery Date');?></th>
						<th width="80" nowrap="nowrap"><?php echo __('Customer Code');?></th>
						<th nowrap="nowrap"><?php echo __('Customer Name');?></th>
					
						<th width="150" nowrap="nowrap"><?php echo __('Action');?></th>
						
					  </tr>
					</thead>
					<?php
					$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d1.delivery_date, d3.partner_code,d3.partner_name FROM sale_order d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d2.parent_id='".$LOGIN_COMPANY_ID."')";
					
					if($search != "")
					{
						$sql = $sql." AND ( d1.receipt_no ILIKE '%".str_replace("'", "''", $search)."%' OR d3.partner_name ILIKE '%".str_replace("'", "''", $search)."%')";
					}
					
					$p = 0;
					$ps = 20;
					$items = explode("&", $PARAMS);
					for($i = 0; $i<count($items); $i++)
					{
						$arr = explode("=", $items[$i]);
						if(count($arr) == 2)
						{
							if($arr[0] == "search")
							{
								$search = urldecode($arr[1]);
							}
							else if($arr[0] == "p")
							{
								$p = urldecode($arr[1]);
							}
							else if($arr[0] == "ps")
							{
								$ps = urldecode($arr[1]);
							}
						}
					}

					$arr = paging($sql, $p, $ps, "d1.create_date ASC");
					
					$item_count = 0;
					$sql = $arr[1];
					$result = pg_exec($db, $sql);
					$numrows = pg_numrows($result);	
					if($numrows>0)
					{
						$row = pg_fetch_array($result, 0);
						$item_count = $row[0];
					}
					
					$page_count = (int)($item_count / $ps);
					if ($item_count - ($page_count * $ps) > 0)
					{
						$page_count = $page_count + 1;
					}
					
					$start = 0;
					if($item_count>0)
					{
						$start = ($p * $ps) + 1;
					}
					$end = $p + 1;
					if((($p + 1) * $ps)<$item_count)
					{
						$end = ($p + 1) * $ps;
					}else
					{
						$end = $item_count;
					}

					$sql = $arr[0];
					
					
					
					$result = pg_exec($db, $sql);
					$numrows = pg_numrows($result);	
					
					$result = pg_exec($db, $sql);
					$numrows = pg_numrows($result);	
					?>
					<tbody>
					  <tr>
						<?php
						for($j =0; $j<$numrows; $j++)
						{
							$row = pg_fetch_array($result, $j);
							
							$order_id = $row["id"];
							$receipt_no = $row["receipt_no"];
							
							$partner_name = $row["partner_name"];
							$receipt_date = $row["receipt_date"];
							$partner_code = $row["partner_code"];
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
						<td><?php echo $j + 1; ?></td>
						<td style="text-align:center; vertical-align:middle"><input type="checkbox" class="form-control" name="[]" id="<?php echo $order_id;?>" /></td>
						<td><?php echo $receipt_no; ?></td>
						<td><?php echo $receipt_date; ?></td>
						<td><?php echo $delivery_date; ?></td>
						<td><?php echo $partner_code; ?></td>
						<td><?php echo $partner_name; ?></td>
						<td nowrap="nowrap" width="160"><a href="<?php echo URL;?><?php echo $lang; ?>/orderform/edit/<?php echo $order_id; ?>"><?php echo __('Edit');?></a> / <a href="javascript:delRow('<?php echo $order_id; ?>')"><?php echo __('Delete');?></a></td>
						
					  </tr>
					 <?php
						}
						?>
					</tbody>
				  </table>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-4">
						<?php echo __('With checked');?>:&nbsp;&nbsp;&nbsp;<a  href="javascript:delRows()"><?php echo __('Delete');?></a>
					</div>
					<div class="col-sm-8">
						<ol class="breadcrumb">
							<?php 
						
							if($page_count == 0)
							{
								$page_count = 1;
							}
							for($i =0; $i<$page_count; $i++)
							{
							?>
							<li class="breadcrumb-item <?php if($i == $p){ ?> active <?php } ?>" ><a href="javascript:doSearch(<?php echo ($i);?>)"><?php echo ($i + 1);?></a></li>
							<?php 
							} 
							?>
						
							</ol>
					</div>
					
			</div>
	
		</div>

	</div>
	<script>
		function delRow(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delOrder';
			_url = _url + '&id=' + encodeURIComponent(id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/order';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function doSearch()
		{
			var search = document.getElementById('editsearch').value;
			document.location.href ='<?php echo URL;?><?php echo $lang; ?>/order?search=' + encodeURIComponent(search);
		}
		function delRows()
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
				alert("<?php echo __('Please check to delete');?>");
				return;
			}
			var result = confirm(count + ". <?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			delRow(ids);
		}
	</script>

</section><!-- #content end -->
