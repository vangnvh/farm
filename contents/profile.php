<!-- Page Title
============================================= -->
<section id="page-title">
	<div class="container clearfix">
		<h1><?php echo __('Profile');?></h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><?php echo __('Profile');?></li>
			
		</ol>
	</div>
</section><!-- #page-title end -->
<section id="content">
	<div class="content-wrap">
		<div class="container clearfix">
		<?php
		$partner_name = '';
		$partner_code = '';
		$vat = '';
		$phone = '';
		$address = '';
		$email = '';
		$partner_id = '';
		$customer_id = $LOGIN_CUSTOMER_ID;
		$ac = "edit";
		$parent_id = "";
		
		
		if($customer_id != "")
		{
			$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.vat, d2.email, d2.address, d1.parent_id , d1.partner_id, d2.commercial_name, d2.contact_name, d2.contact_mobile, d2.contact_email FROM customer d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND (d1.id='".$customer_id."')";
			
			
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$partner_code = $row["partner_code"];
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
			<h3><?php echo __('Company Info');?>: &nbsp;&nbsp;&nbsp;<span style="color:orange"><?php echo $partner_code;?></span</h3>
			<form id="frmRegister" name="frmRegister" class="nobottommargin">
			
				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2 col-form-label">
							<label for="editcompany_name"><?php echo __('Company Name');?><span style="color:red"><small >*</small></span>:</label>
						</div>
						<div class="col-sm-10">
							<input type="text" autocomplete="off" id="editcompany_name" name="editcompany_name" value="<?php echo $partner_name; ?>" class="form-control" maxlength = "250" />
						</div>
					</div>
				</div>
				
				
				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2 col-form-label">
						<label for="editvat"><?php echo __('VAT');?> <span style="color:red"></span></label>
						</div>
						<div class="col-sm-2">
							<input type="text" id="editvat" name="editvat" value="<?php echo $vat; ?>" class="form-control" maxlength = "64"  />
						</div>
						
						<div class="col-sm-2 col-form-label">
							<label for="editphone"><?php echo __('Phone');?> <span style="color:red"></span>:</label>
						</div>
						<div class="col-sm-2">
							<input type="text" id="editphone" name="editphone" value="<?php echo $phone; ?>" class="form-control" maxlength = "50"  />
						</div>
						<div class="col-sm-1 col-form-label">
						<label for="editemail"><?php echo __('Email');?>:</label>
						</div>
						<div class="col-sm-3">
							<input type="text" id="editemail" name="editemail" value="<?php echo $email; ?>" class="form-control" maxlength = "150" />
						</div>
						
					</div>
				</div>
				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2">
						<label for="editaddress"><?php echo __('Address');?> <span style="color:red"></span>:</label>
						</div>
						<div class="col-sm-10">
						<textarea id="editaddress" name="editaddress" class="form-control" maxlength = "250"><?php echo $address; ?></textarea>
						</div>
					</div>
				</div>

	
				
				<div class="clear"></div>
				
				<div class="col_half nobottommargin">
					<a class="button button-3d nomargin" href="javascript:saveCustomer()" ><?php echo __('Update Customer');?></a> 
				</div>
				
			</form>
			
			
		</div>
	
		<br>
		<div class="container clearfix">
		
			<form id="frmAccount" name="frmAccount" class="nobottommargin" >
				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2">
							<label for="edituser_name"><?php echo __('User Name');?> <span style="color:red"><small >*</small></span></label>
						</div>
						<div class="col-sm-10">
							<input type="text" autocomplete="off" id="edituser_name" name="edituser_name" class="form-control" maxlength = "50" readonly="readonly" value="<?php echo $LOGIN_USER_NAME; ?>" onkeydown="if(event.keyCode == 13){doRegister();}" />
						</div>
					</div>

				</div>
				<div class="col-sm-12 form-group">
				

					<div class="row">
						<div class="col-sm-2">
							<label for="editold_password"><?php echo __('Old Password');?> <span style="color:red"><small >*</small></span></label>
						</div>
						<div class="col-sm-2">
							<input type="password" autocomplete="off" id="editold_password" name="editold_password" value="" class="form-control" maxlength = "36" onkeydown="if(event.keyCode == 13){doRegister();}" />
						</div>
						<div class="col-sm-2">
							<label for="editpassword"><?php echo __('New Password');?> <span style="color:red"><small >*</small></span>:</label>
						</div>
						<div class="col-sm-2">
							<input type="password" autocomplete="off" id="editpassword" name="editpassword" value="" class="form-control" maxlength = "36" onkeydown="if(event.keyCode == 13){doRegister();}" />
						</div>
						
						<div class="col-sm-2">
							<label for="editre_password"><?php echo __('Re-enter Password');?> <span style="color:red"><small >*</small></span></label>
						</div>
						<div class="col-sm-2">
							<input type="password" maxlength = "36"  autocomplete="off" id="editre_password" name="editre_password" value="" class="form-control" onkeydown="if(event.keyCode == 13){doRegister();}" />
						</div>
						
					</div>
				</div>
					
				<div class="clear"></div>

				<div class="col_half nobottommargin">
					<a class="button button-3d nomargin" href="javascript:doRegister()" value="register"><?php echo __('Update Account');?></a> 
				</div>
				<div class="col_half col_last" style="text-align:right">
					<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
				</div>

			</form>
		</div>

	</div>

</section><!-- #content end -->


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
						alert("<?php echo __('Data is saved');?>");
						document.location.href ='<?php echo URL;?>';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		
		}
		
		function doRegister()
		{
			
			var ctr = document.frmAccount.edituser_name;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, Enter user name');?>");
				return false;
			}
			var user_name = ctr.value;
			
			ctr = document.frmAccount.editold_password;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, Enter password');?>");
				return false;
			}
			var old_password = ctr.value;
			old_password = Sha1.hash(old_password);
			
			ctr = document.frmAccount.editpassword;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, Enter password');?>");
				return false;
			}
			var password = ctr.value;
			if(document.frmAccount.editre_password.value != password)
			{
				document.frmAccount.editre_password.focus();
				alert("<?php echo __('Pasword is not mark');?>");
				return false;
			}

			password = Sha1.hash(password);
			
			var _url = '<?php echo URL;?>includes/action.php?ac=updateUser';
			_url = _url + '&user=' + encodeURIComponent(user_name);
			_url = _url + '&pass=' + encodeURIComponent(password);
			_url = _url + '&old_pass=' + encodeURIComponent(old_password);
			_url = _url + '&user_id=' + encodeURIComponent('<?php echo $LOGIN_USER_ID;?>');
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						alert("<?php echo __('Data is saved');?>");
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/login';
					}else if(message == "INVALID_USER")
					{
						alert("<?php echo __('User or email exist');?>");
					}else if(message == "INVALID_PASSWORD")
					{
						alert("<?php echo __('INVALID_PASSWORD');?>");
					}
					else{
						alert(message);
					}
				}
				
			}, true);

		}

	</script>