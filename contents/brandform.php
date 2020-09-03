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
		<?php
		$ac = "new";
		$customer_id = "";
		$parent_id = "";
		if($uri !='/' && $uri != '')
		{
			$items = explode("/", substr($uri, 1));
			if(count($items)> 2)
			{
				$ac = $items[2];
				if($ac == "edit")
				{
					$customer_id = $items[3];
				}else if($ac == "clone")
				{
					$customer_id = $items[3];
					$parent_id= $items[4];
				}else if($ac == "new")
				{
					$parent_id= $items[3];
				}
			}
		}
		if($ac != "list")
		{
			$partner_name = '';
			$vat = '';
			$phone = '';
			$address = '';
			$email = '';
			$partner_id = '';
			
			if($customer_id != "")
			{
				$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.vat, d2.email, d2.address, d1.parent_id , d1.partner_id FROM customer d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND (d1.id='".$customer_id."')";
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);
				if($numrows>0)
				{
					$row = pg_fetch_array($result, 0);
					$partner_name = $row["partner_name"];
					$phone = $row["phone"];
					$vat = $row["vat"];
					$address = $row["address"];
					$email = $row["email"];
					if($ac == "edit")
					{
						$partner_id = $row["partner_id"];
					}
				
				}
			}
			
		
		?>
		<form id="frmRegister" name="frmRegister" class="nobottommargin" >

					<div class="col_full">
						<label for="editcompany_name"><?php echo __('Company Name');?> <span style="color:red"><small >*</small></span></label>
						<input type="text" autocomplete="off" id="editcompany_name" name="editcompany_name" value="<?php echo $partner_name; ?>" class="form-control" maxlength = "250" />
					</div>
					
					
					<div class="col_one_third">
						<label for="editvat"><?php echo __('VAT');?> <span style="color:red"><small >*</small></span></label>
						<input type="text" id="editvat" name="editvat" value="<?php echo $vat; ?>" class="form-control" maxlength = "64"  />
					</div>
					<div class="col_one_third">
						<label for="editphone"><?php echo __('Phone');?> <span style="color:red"><small >*</small></span></label>
						<input type="text" id="editphone" name="editphone" value="<?php echo $phone; ?>" class="form-control" maxlength = "50"  />
					</div>
					<div class="col_one_third col_last">
						<label for="editemail"><?php echo __('Email');?></label>
						<input type="text" id="editreg_phone" name="editemail" value="<?php echo $email; ?>" class="form-control" maxlength = "150" />
					</div>
					

					<div class="col_full">
						<label for="editaddress"><?php echo __('Address');?> <span style="color:red"><small >*</small></span></label>
						<textarea id="editaddress" name="editaddress" class="form-control" maxlength = "250"><?php echo $address; ?></textarea>
					</div>

					<div class="clear"></div>


					<div class="clear"></div>

					<div class="col_half nobottommargin">
						<a class="button button-3d button-black nomargin" href="javascript:saveCustomer()" value="register"><?php echo __('Save');?></a> 
					</div>
					<div class="col_half col_last" style="text-align:right">
						<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
					</div>

				</form>
		</div>

	</div>
	<script>
		var parent_id ='<?php echo $parent_id;?>';
		var ac = '<?php echo $ac;?>';
		function saveCustomer()
		{
			var ctr = document.frmRegister.editcompany_name;
			if(ctr.value == '')
			{
				
				alert("<?php echo __('Please, enter company name');?>");
				ctr.focus();
				return false;
			}
			var company_name = ctr.value;
			ctr = document.frmRegister.editvat;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter vat');?>");
				return false;
			}
			var vat = ctr.value;
			ctr = document.frmRegister.editphone;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter phone');?>");
				return false;
			}
			var phone = ctr.value;
			
			ctr = document.frmRegister.editemail;
			var email = ctr.value;
			if(email != "" && validate_email(email) == false)
			{
				ctr.focus();
				alert("<?php echo __('Invalid email');?>");
				return false;
			}
			
			ctr = document.frmRegister.editaddress;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter your address');?>");
				return false;
			}
			var address = ctr.value;
			
			var _url = '<?php echo URL;?>includes/action.php?ac=saveCustomer';
			_url = _url + '&company_name=' + encodeURIComponent(company_name);
			_url = _url + '&partner_id=<?php echo $partner_id;?>';
			_url = _url + '&vat=' + encodeURIComponent(vat);
			_url = _url + '&phone=' + encodeURIComponent(phone);
			_url = _url + '&email=' + encodeURIComponent(email);
			_url = _url + '&address=' + encodeURIComponent(address);
			_url = _url + '&parent_id=' + encodeURIComponent(parent_id);
		
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
	<?php
		}else{
	?>
	<div class="col_full nobottommargin">
		<div class="row">
		
			<div class="col-sm-4">
				<form action="#" class="notopmargin nobottommargin">
					<div class="input-group divcenter">
						<input type="text" id="editsearch" class="form-control" placeholder="<?php echo __('Enter your search');?>" required="">
						<div class="input-group-append">
							<button class="btn btn-success" type="button" onclick="loadCustomerList()"><?php echo __('Search');?></button>
						</div>
					</div>
				</form>
			</div>
			<div class="col-sm-8"></div>
		</div>
	</div>
	<br>
	
	<div class="col_full nobottommargin" id="pnCustomerList"></div>
	<script>
		function loadCustomerList()
		{
			var search = document.getElementById('editsearch').value;
			var _url = '<?php echo URL;?>includes/customerlist.php?customer_id=&func=selectCustomer&search=' + encodeURIComponent(search);
			
			loadPage('pnCustomerList', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
			
		}
		function selectCustomer(customer_id)
		{
			var _url = '<?php echo URL;?>includes/action.php?ac=parentCustomer&parent_id=<?php echo $LOGIN_CUSTOMER_ID;?>&customer_id=' + customer_id;
			alert(_url);
			
			loadPage('pnCustomerList', _url, function(status, message)
			{
				if(status== 0)
				{
					document.location.href ='<?php echo URL;?><?php echo $lang; ?>/brand';
				}
				
			}, false);
		}
		loadCustomerList();
	</script>
	<?php
		}
	?>
		

</section><!-- #content end -->
