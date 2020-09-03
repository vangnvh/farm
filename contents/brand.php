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


if($rel_id != "")
{
	
	$sql = "select d1.id, d1.name, dl1.name AS name_lang, d1.answer, dl2.name AS answer_lang FROM faq_category_rel d LEFT OUTER JOIN faq d1 ON(d.rel_id = d1.id) LEFT OUTER JOIN res_lang_rel dl1 ON(d1.id = dl1.rel_id AND dl1.lang_id='".$lang_id."' AND dl1.status =0 AND dl1.column_name='name') LEFT OUTER JOIN res_lang_rel dl2 ON(d1.id = dl2.rel_id AND dl2.lang_id='".$lang_id."' AND dl2.status =0 AND dl2.column_name='answer') WHERE d.status =0 AND d1.status =0 AND d.category_id='".$rel_id."'";
	
}else
{
	
	
	$sql = "select d1.id, d1.name, dl1.name AS name_lang, d1.answer, dl2.name AS answer_lang FROM faq d1 LEFT OUTER JOIN res_lang_rel dl1 ON(d1.id = dl1.rel_id AND dl1.lang_id='".$lang_id."' AND dl1.status =0 AND dl1.column_name='name') LEFT OUTER JOIN res_lang_rel dl2 ON(d1.id = dl2.rel_id AND dl2.lang_id='".$lang_id."' AND dl2.status =0 AND dl2.column_name='answer') WHERE d1.status =0 AND d1.company_id='".COMPANY_ID."'";
	
}
$sql = $sql." ORDER BY d1.sequence ASC";

$result_items = pg_exec($db, $sql);
$numrows_items = pg_numrows($result_items);	
?>
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Brand');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item <?php if($rel_id == ''){ ?> active <?php } ?>" ><a href="<?php echo URL;?><?php echo $lang; ?>/brand"><?php echo __('Brand');?></a></li>
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

				<div class="col_full nobottommargin">
				<a class="button button-3d nomargin" href="<?php echo URL;?><?php echo $lang; ?>/brandform/new/<?php echo $LOGIN_CUSTOMER_ID; ?>" value="login"><?php echo __('New');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/brandform/list/<?php echo $LOGIN_CUSTOMER_ID; ?>" value="login"><?php echo __('Customers');?></a>
				</div>
				<br>
				<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30">#</th>
						<th  width="80" nowrap="nowrap"><?php echo __('Customer Code');?></th>
						<th nowrap="nowrap"><?php echo __('Company Name');?></th>
						
						<th  width="80" nowrap="nowrap"><?php echo __('Vat');?></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Phone');?></th>
						<th  width="250" nowrap="nowrap"><?php echo __('Address');?></th>
						<th  width="80" nowrap="nowrap"><?php echo __('Action');?></th>
						
					  </tr>
					</thead>
					<?php
					$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.vat, d2.email, d2.address, d1.parent_id FROM customer d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND (d1.parent_id='".$LOGIN_CUSTOMER_ID."' OR d1.id='".$LOGIN_CUSTOMER_ID."') ORDER BY d1.create_date ASC";
					$result = pg_exec($db, $sql);
					$numrows = pg_numrows($result);	
					?>
					<tbody>
					  <tr>
						<?php
						for($j =0; $j<$numrows; $j++)
						{
							$row = pg_fetch_array($result, $j);
							
							$customer_id = $row["id"];
							$partner_code = $row["partner_code"];
							$partner_name = $row["partner_name"];
							$phone = $row["phone"];
							$vat = $row["vat"];
							$address = $row["address"];
							$parent_id = $row["parent_id"];
						?>
						<td><?php echo $j + 1; ?></td>
						<td><?php echo $partner_code; ?></td>
						<td><?php echo $partner_name; ?></td>
						<td><?php echo $vat; ?></td>
						<td><?php echo $phone; ?></td>
						<td><?php echo $address; ?></td>
						<td nowrap="nowrap"><a href="<?php echo URL;?><?php echo $lang; ?>/brandform/edit/<?php echo $customer_id; ?>"><?php echo __('Edit');?></a> | <a href="<?php echo URL;?><?php echo $lang; ?>/brandform/clone/<?php echo $LOGIN_CUSTOMER_ID; ?>/<?php echo $LOGIN_CUSTOMER_ID; ?>"><?php echo __('Clone');?></a> <?php if($parent_id != ""){?>| <a href="javascript:delCustomer('<?php echo $customer_id; ?>')"><?php echo __('Delete');?></a><?php } ?></td>
						
					  </tr>
					 <?php
						}
						?>
					</tbody>
				  </table>
				</div>
	
		</div>

	</div>
	<script>
		function delCustomer(customer_id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delCustomer';
			_url = _url + '&customer_id=' + encodeURIComponent(customer_id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/brand';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
	</script>

</section><!-- #content end -->
