<?php

$search = "";
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

?>
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Users');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><a href="<?php echo URL;?><?php echo $lang; ?>/user"><?php echo __('Users');?></a></li>
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
						<a class="button button-3d nomargin" href="<?php echo URL;?><?php echo $lang; ?>/userform/new" value="login"><?php echo __('New User');?></a>
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
						<th width="30">#</th>
						<th width="40" style="text-align:center; vertical-align:middle"><input value = "" type="checkbox" class="form-control" name="[]" onclick="doHandleCheckAll(this)" /></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Name');?></th>
						<th nowrap="nowrap"><?php echo __('Email');?></th>
			
						<th  width="80" nowrap="nowrap"><?php echo __('Action');?></th>
						
					  </tr>
					</thead>
					<?php
					$sql = "SELECT d1.id, d2.user_name, d2.email FROM res_user_company d1 LEFT OUTER JOIN res_user d2 ON(d1.user_id = d2.id) WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."' AND d2.user_name != ''";
					if($search != "")
					{
						$sql = $sql." AND ( d2.user_name ILIKE '%".str_replace("'", "''", $search)."%' d2.email ILIKE '%".str_replace("'", "''", $search)."%')";
					}
					
					$arr = paging($sql, $p, $ps, "d2.user_name ASC");
					
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
					  <tr>
						<?php
						for($j =0; $j<$numrows; $j++)
						{
							$row = pg_fetch_array($result, $j);
							
							$user_company_id = $row["id"];
							$user_name = $row["user_name"];
							$email = $row["email"];
							
					
						?>
						<td><?php echo $j + 1; ?></td>
						<td style="text-align:center; vertical-align:middle"><input type="checkbox" class="form-control" name="[]" id="<?php echo $user_company_id;?>" /></td>
						<td><?php echo $user_name; ?></td>
						<td><?php echo $email; ?></td>
						<td nowrap="nowrap"><a href="<?php echo URL;?><?php echo $lang; ?>/userform/edit/<?php echo $user_company_id; ?>"><?php echo __('Edit');?></a> / <a href="javascript:delLine('<?php echo $user_company_id; ?>')"><?php echo __('Delete');?></a></td>
						
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
		function delLine(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delUserCompany';
			_url = _url + '&id=' + encodeURIComponent(id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/user?p=<?php echo $p?>';
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
			document.location.href ='<?php echo URL;?><?php echo $lang; ?>/user?search=' + encodeURIComponent(search) + "&p=" + p;
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
