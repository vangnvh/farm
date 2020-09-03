
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Customers');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><a href="<?php echo URL;?><?php echo $lang; ?>/customer"><?php echo __('Customers');?></a></li>
			
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
					
				}
			}
		}
		if($ac == "edit")
		{
			$partner_name = '';
			$partner_code = '';
			$vat = '';
			$phone = '';
			$address = '';
			$email = '';
			
			$partner_id = '';
			$user_id = "";
			$user_name = "";
			$password = "";
		
			
			if($customer_id != "")
			{
				$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.vat, d2.email, d2.address, d1.partner_id FROM customer d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND d1.id='".$customer_id."'";
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);
				if($numrows>0)
				{
					$row = pg_fetch_array($result, 0);
					$partner_id = $row["partner_id"];
					$partner_code = $row["partner_code"];
					$partner_name = $row["partner_name"];
					
					$phone = $row["phone"];
					$email = $row["vat"];
					$address = $row["address"];
					$email = $row["email"];
					
					
				
				}
				
				
			}
			
		
		?>
		<div class="col_full nobottommargin">
			<a class="button button-3d nomargin" href="javascript:saveCustomer()" value="register"><?php echo __('Save');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/customer" value="register"><?php echo __('Back');?></a> 
		</div>
		<br>
		<form id="frmRegister" name="frmRegister" class="nobottommargin" >

					<div class="row">
							<div class="col-sm-2">
								<label for="editvat"><?php echo __('Code');?> <span style="color:red"><small >*</small></span></label>
								<input type="text" id="editpartner_code" name="editpartner_code" value="<?php echo $partner_code; ?>" class="form-control" maxlength = "64"  />
							</div>
							<div class="col-sm-10">
								<label for="editcompany_name"><?php echo __('Name');?> <span style="color:red"><small >*</small></span></label>
								<input type="text" autocomplete="off" id="editpartner_name" name="editpartner_name" value="<?php echo $partner_name; ?>" class="form-control" maxlength = "250" />
							</div>
							
					</div>
					
					
					<div class="col_one_third">
						<label for="editvat"><?php echo __('VAT');?></label>
						<input type="text" id="editvat" name="editvat" value="<?php echo $vat; ?>" class="form-control" maxlength = "64"  />
					</div>
					<div class="col_one_third">
						<label for="editphone"><?php echo __('Phone');?></label>
						<input type="text" id="editphone" name="editphone" value="<?php echo $phone; ?>" class="form-control" maxlength = "50"  />
					</div>
					<div class="col_one_third col_last">
						<label for="editemail"><?php echo __('Email');?></label>
						<input type="text" id="editreg_phone" name="editemail" value="<?php echo $email; ?>" class="form-control" maxlength = "150" />
					</div>
					

					<div class="col_full">
						<label for="editaddress"><?php echo __('Address');?></label>
						<textarea id="editaddress" name="editaddress" class="form-control" maxlength = "250"><?php echo $address; ?></textarea>
					</div>

					<div class="clear"></div>

				

					<div class="col_half nobottommargin">
						<a class="button button-3d nomargin" href="javascript:saveCustomer()" value="register"><?php echo __('Save');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/customer" value="register"><?php echo __('Back');?></a> 
					</div>
					<div class="col_half col_last" style="text-align:right">
						<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
					</div>

			</form>
		</div>

	</div>
	<script>

		var ac = '<?php echo $ac;?>';
		function saveCustomer()
		{
			var ctr = document.frmRegister.editpartner_name;
			if(ctr.value == '')
			{
				alert("<?php echo __('Please, enter name');?>");
				ctr.focus();
				return false;
			}
			
			var partner_name = ctr.value;
			
			ctr = document.frmRegister.editpartner_code;
			var partner_code = ctr.value;
			
			ctr = document.frmRegister.editvat;
			var vat = ctr.value;
			ctr = document.frmRegister.editphone;
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
			var address = ctr.value;
			
	
			
			
			var _url = '<?php echo URL;?>includes/action.php?ac=saveCustomer';
	
			_url = _url + '&partner_code=' + encodeURIComponent(partner_code);
			_url = _url + '&partner_name=' + encodeURIComponent(partner_name);
			_url = _url + '&partner_id=<?php echo $partner_id;?>';
			_url = _url + '&customer_id=<?php echo $customer_id;?>';
			_url = _url + '&vat=' + encodeURIComponent(vat);
			_url = _url + '&phone=' + encodeURIComponent(phone);
			_url = _url + '&email=' + encodeURIComponent(email);
			_url = _url + '&address=' + encodeURIComponent(address);
		
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/customer';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		
		}
	</script>
	<?php
		}else if($ac == "new")
		{
	?>
	<div class="col_full nobottommargin">
		<div class="row">
		
			<div class="col-sm-4">
				<form action="#" class="notopmargin nobottommargin">
					<div class="input-group divcenter">
						<input type="text" id="editsearch" class="form-control" placeholder="<?php echo __('Enter your search');?>" required="">
						<div class="input-group-append">
							<button class="btn btn-success" type="button" onclick="loadUserList()"><?php echo __('Search');?></button>
						</div>
					</div>
				</form>
			</div>
			<div class="col-sm-8"></div>
		</div>
	</div>
	<br>
	
	<div class="col_full nobottommargin">
		<div class="row">
			<div class="col-sm-12">
				<div id="pnListUser"></div>
			</div>
		</div>
	</div>
	<script>
		function loadUserList()
		{
			var search = document.getElementById('editsearch').value;
			var _url = '<?php echo URL;?>includes/userlist.php?=&func=selectUser&search=' + encodeURIComponent(search);
			
			loadPage('pnListUser', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		loadUserList();
		function selectUser(id)
		{
			var _url = '<?php echo URL;?>includes/action.php?ac=addCustomer&user_id=' + id;
			
			loadPage('pnListUser', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message.length == 36)
					{
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/customerform/edit/' + message;
					}else{
						alert(message);
					}
				}
				
			}, true);
		}
	</script>
	<?php
		
		}
	?>
		

</section><!-- #content end -->
