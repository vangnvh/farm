<?php

$search = "";

$p = 0;
if(isset($_REQUEST['p']))
{
	$p = $_REQUEST['p'];
}else{
	if(isset($_SESSION["payment_category_type_page"]))
	{
		$p = $_SESSION["payment_category_type_page"];
	}
}
$_SESSION["prdouct_type_page"] = $p;

$ps = 30;
if(isset($_REQUEST['ps']))
{
	$ps = $_REQUEST['ps'];
}else
{
	if(isset($_SESSION["payment_category_type_pages"]))
	{
		$ps = $_SESSION["payment_category_type_pages"];
	}
}
$_SESSION["payment_category_type_pages"] = $ps;	


$items = explode("&", $PARAMS);
for($i = 0; $i<count($items); $i++)
{
	$arr = explode("=", $items[$i]);
	if(count($arr) == 2)
	{
		if($arr[0] == "search")
		{
			$search = urldecode($arr[1]);
			$_SESSION["payment_category_type_search"] = $search;	
		}
		if($arr[0] == "p")
		{
			$p = urldecode($arr[1]);
			$_SESSION["payment_category_type_page"] = $p;
		}
	}
}
if(isset($_SESSION["payment_category_type_search"]))
{
	$search = $_SESSION["payment_category_type_search"];
}


?>
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Payment Category');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><a href="<?php echo URL;?><?php echo $lang; ?>/payment_category_typeform"><?php echo __('Payment Category');?></a></li>
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
						<a class="button button-3d nomargin" href="<?php echo URL;?><?php echo $lang; ?>/payment_typeform/new" value="login"><?php echo __('New Payment Category');?></a>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<input type="text" id="editsearch" class="form-control" value="<?php echo $search; ?>" placeholder="<?php echo __('Search');?>" onKeyDown="if(event.keyCode == 13){doSearch(0);}">
							<div class="input-group-prepend">
								<a href="javascript:doSearch(0)" class="input-group-text"><?php echo __('Search');?></a>
							</div>
						</div>
					</div>
				</div>
				
				<br>
				<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th style="width:30px">#</th>
						<th style="width:40px" style="text-align:center; vertical-align:middle"><input value = "" type="checkbox" class="form-control" name="[]" onclick="doHandleCheckAll(this)" /></th>
						<th nowrap="nowrap"><?php echo __('Name');?></th>
						<th nowrap="nowrap"><?php echo __('Description');?></th>
						
						<th style="width:30px" nowrap="nowrap"><?php echo __('Action');?></th>
						
					  </tr>
					</thead>
					<?php
					$sql = "SELECT d1.id, d1.category_name, d1.description FROM res_payment_category d1 WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."'";
					if($search != "")
					{
						$sql = $sql." AND ( d1.category_name ILIKE '%".str_replace("'", "''", $search)."%')";
					}
					$arr = paging($sql, $p, $ps, "d1.category_name ASC");
					
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
					?>
					<tbody>
					  
						<?php
						for($j =0; $j<$numrows; $j++)
						{
							$row = pg_fetch_array($result, $j);
							$color = EVEN_COLOR;
							if($j %2 == 0)
							{
								$color = ODD_COLOR;
							}
							$id = $row["id"];
							$name = $row["category_name"];
							$description = $row["description"];
							
					
						?>
						<tr style="background-color:<?php echo $color;?>">
						<td ><?php echo $j + 1; ?></td>
						<td style="width:40px" style="text-align:center; vertical-align:middle"><input type="checkbox" class="form-control" name="[]" id="<?php echo $id;?>" /></td>
						<td><?php echo $name; ?></td>
						<td><?php echo $description; ?></td>
						<td style="width:40px" nowrap="nowrap"><a href="<?php echo URL;?><?php echo $lang; ?>/payment_typeform/edit/<?php echo $id; ?>"><?php echo __('Edit');?></a> / <a href="javascript:delLine('<?php echo $id; ?>')"><?php echo __('Delete');?></a></td>
						
					  </tr>
					 <?php
						}
						?>
					</tbody>
				  </table>
				</div>
				<br>
				<div class="row">
						<div class="col-sm-6">
							<?php echo __('With checked');?>:&nbsp;&nbsp;&nbsp;<a  href="javascript:delRows()"><?php echo __('Delete');?></a>
						</div>
						<div class="col-sm-6" style="full-rigth">
							<ul class="pagination nobottommargin">
								<?php 
							
								if($page_count == 0)
								{
									$page_count = 1;
								}
								for($i =0; $i<$page_count; $i++)
								{
								?>
								<li class="page-item <?php if($i == $p){ ?> active <?php } ?>" ><a class="page-link" href="javascript:doSearch('<?php echo $i;?>')"><?php echo ($i + 1);?></a></li>
							
								<?php 
								} 
								?>
							
								</ul>
						</div>
						
				</div>
	
		</div>

	</div>
	<script>
		function delLine(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delPaymentType';
			_url = _url + '&id=' + encodeURIComponent(id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/payment_type?p=<?php echo $p?>';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function doSearch(p)
		{
			var search = document.getElementById('editsearch').value;
			document.location.href ='<?php echo URL;?><?php echo $lang; ?>/payment_type?search=' + encodeURIComponent(search) + "&p=" + p;
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
			delLine(ids);
		}
		
	</script>

</section><!-- #content end -->
