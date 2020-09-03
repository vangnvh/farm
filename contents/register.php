
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('account');?></h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item  active " aria-current="page"><?php echo __('account');?></li>
		</ol>
	</div>

</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		<div class="container clearfix">

			<div class="col_full nobottommargin">
				
				<h3><?php echo __('register_message');?></h3>

				<form id="frmRegister" name="frmRegister" class="nobottommargin" >

				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2 col-form-label">
							<label for="editcompany_name"><?php echo __('Name');?> <span style="color:red"><small >*</small></span>:</label>
						</div>
						<div class="col-sm-10">
							<input type="text" autocomplete="off" id="editcompany_name" name="editcompany_name" value="" class="form-control" maxlength = "250" />
						</div>
					</div>
				</div>
			
				
				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2 col-form-label">
						<label for="editvat"><?php echo __('VAT');?></label>
						</div>
						<div class="col-sm-2">
							<input type="text" id="editvat" name="editvat" value="" class="form-control" maxlength = "64"  />
						</div>
						
						<div class="col-sm-2 col-form-label">
							<label for="editphone"><?php echo __('Phone');?>:</label>
						</div>
						<div class="col-sm-2">
							<input type="text" id="editphone" name="editphone" value="" class="form-control" maxlength = "50"  />
						</div>
						<div class="col-sm-1 col-form-label">
						<label for="editemail"><?php echo __('Email');?>:</label>
						</div>
						<div class="col-sm-3">
							<input type="text" id="editreg_phone" name="editemail" value="" class="form-control" maxlength = "150" />
						</div>
						
					</div>
				</div>
				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2">
						<label for="editaddress"><?php echo __('Address');?>:</label>
						</div>
						<div class="col-sm-10">
						<textarea id="editaddress" name="editaddress" class="form-control" maxlength = "250"></textarea>
						</div>
					</div>
				</div>
				
				

				<div class="clear"></div>
					<div class="col-sm-12 form-group">
						<div class="row">
							<div class="col-sm-2 col-form-label">
								<label for="edituser_name"><?php echo __('User Name');?> <span style="color:red"><small >*</small></span>:</label>
							</div>
							<div class="col-sm-10">
								<input type="text" autocomplete="off" id="edituser_name" name="edituser_name" value="" class="form-control" maxlength = "50" onkeydown="if(event.keyCode == 13){doRegister();}" />
							</div>
						</div>
					</div>
				<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-2 col-form-label">
						<label for="editvat"><?php echo __('Password');?> <span style="color:red"><small >*</small></span></label>
						</div>
						<div class="col-sm-4">
							<input type="password" id="editpassword" name="editpassword" value="" class="form-control" maxlength = "64"  />
						</div>
						
						<div class="col-sm-2 col-form-label">
							<label for="editre_password"><?php echo __('Re-enter Password');?><span style="color:red"><small >*</small></span>:</label>
						</div>
						<div class="col-sm-4">
							<input type="password" id="editre_password" name="editre_password" value="" class="form-control" maxlength = "50"  />
						</div>

						
					</div>
				</div>

					<div class="clear"></div>

					<div class="col_half nobottommargin">
						<a class="button button-3d button-black nomargin" href="javascript:doRegister()" value="register"><?php echo __('Register Now');?></a> 
					</div>
					<div class="col_half col_last" style="text-align:right">
						<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
					</div>

				</form>

			</div>

		</div>

	</div>

</section><!-- #content end -->

<script>
	
	function doRegister()
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
		
		ctr = document.frmRegister.editaddress;
		
		var address = ctr.value;
		
		var contact_name = "";
		
		var contact_mobile = "";
		
		var contact_email = "";
		
		
		
		ctr = document.frmRegister.edituser_name;
		if(ctr.value == '')
		{
			ctr.focus();
			alert("<?php echo __('Please, Enter user name');?>");
			return false;
		}
		var user_name = ctr.value;
		
		ctr = document.frmRegister.editpassword;
		if(ctr.value == '')
		{
			ctr.focus();
			alert("<?php echo __('Please, Enter password');?>");
			return false;
		}
		var password = ctr.value;
		if(document.frmRegister.editre_password.value != password)
		{
			document.frmRegister.editre_password.focus();
			alert("<?php echo __('Pasword is not mark');?>");
			return false;
		}

		password = Sha1.hash(password);
		var _url = '<?php echo URL;?>includes/action.php?ac=register';
		_url = _url + '&company_name=' + encodeURIComponent(company_name);
		_url = _url + '&commercial_name=' + encodeURIComponent(company_name);
		_url = _url + '&vat=' + encodeURIComponent(vat);
		_url = _url + '&phone=' + encodeURIComponent(phone);
		_url = _url + '&email=' + encodeURIComponent(email);
		_url = _url + '&address=' + encodeURIComponent(address);
		_url = _url + '&user_name=' + encodeURIComponent(user_name);
		_url = _url + '&contact_name=' + encodeURIComponent(contact_name);
		_url = _url + '&contact_mobile=' + encodeURIComponent(contact_mobile);
		_url = _url + '&contact_email=' + encodeURIComponent(contact_email);
		_url = _url + '&pass=' + encodeURIComponent(password);
		
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					alert("<?php echo __('Data is saved');?>");
					document.location.href ='<?php echo URL;?><?php echo $lang; ?>/login';
					
				}else if(message == "AVAIBLE")
				{
					alert("<?php echo __('User or email exist');?>");
				}
				else{
					alert(message);
				}
			}
			
		}, true);
		
		
		
	}
	
</script>