
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Company');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><a href="<?php echo URL;?><?php echo $lang; ?>/company"><?php echo __('Company');?></a></li>
			
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
		$id = "";
		$parent_id = "";
		
		
		if($uri !='/' && $uri != '')
		{
			$items = explode("/", substr($uri, 1));
			if(count($items)> 2)
			{
				$ac = $items[2];
				if($ac == "edit")
				{
					$id = $items[3];
				}else if($ac == "clone")
				{
					$id = $items[3];
					
				}
			}
		}
		
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
			$bank_no = "";
			$bank_name = "";
		
			
			if($id != "")
			{
				$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.vat, d2.email, d2.address, d1.partner_id , d2.bank_no, d2.bank_name FROM res_company d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND d1.id='".$id."'";
				
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);
				if($numrows>0)
				{
					$row = pg_fetch_array($result, 0);
					$partner_id = $row["partner_id"];
					$partner_code = $row["partner_code"];
					$partner_name = $row["partner_name"];
					$vat = $row["vat"];
					$phone = $row["phone"];
					$bank_no = $row["bank_no"];
					$bank_name = $row["bank_name"];
					$address = $row["address"];
					$bank_no = $row["bank_no"];
					$bank_name = $row["bank_name"];
					
				}
			}
			
		
		?>
		<div class="col_full nobottommargin">
			<a class="button button-3d nomargin" href="javascript:saveLine()" value="register"><?php echo __('Save');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/company" value="register"><?php echo __('Back');?></a> 
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
					
					<br>
					<div class="col_one_third">
						<label for="editvat"><?php echo __('Vat');?></label>
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
					<br>
					<div class="row">
							<div class="col-sm-2">
								<label for="editvat"><?php echo __('Bank No');?> <span style="color:red"><small ></small></span></label>
								<input type="text" id="editbank_no" name="editbank_no" value="<?php echo $bank_no; ?>" class="form-control" maxlength = "64"  />
							</div>
							<div class="col-sm-10">
								<label for="editcompany_name"><?php echo __('Bank Name');?> <span style="color:red"><small ></small></span></label>
								<input type="text" autocomplete="off" id="editbank_name" name="editbank_name" value="<?php echo $bank_name; ?>" class="form-control" maxlength = "250" />
							</div>
							
					</div>
					<br>
					<div class="col_full">
						<label for="editaddress"><?php echo __('Address');?></label>
						<textarea id="editaddress" name="editaddress" class="form-control" maxlength = "250"><?php echo $address; ?></textarea>
					</div>
					<?php if($id != ""){?>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<a href="javascript:addLocation();"> + Thêm vị trí</a>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div id="pnLocation"></div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<a href="javascript:addUser();"> + Thêm tài khoản</a>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div id="pnUsers"></div>
						</div>
					</div>
					<br>
					<?php } ?>
					

					<div class="clear"></div>

		
	
					<div class="col_half nobottommargin">
						<a class="button button-3d nomargin" href="javascript:saveLine()" value="register"><?php echo __('Save');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/company"><?php echo __('Back');?></a> 
					</div>
					<div class="col_half col_last" style="text-align:right">
						<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
					</div>
			</form>
		</div>

	</div>
	<script>

		var ac = '<?php echo $ac;?>';
		
		function saveLine()
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
			
			ctr = document.frmRegister.editbank_no;
			var bank_no = ctr.value;
			
			ctr = document.frmRegister.editbank_name;
			var bank_name = ctr.value;
			
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
		
			var _url = '<?php echo URL;?>includes/action.php?ac=saveCompany';
	
			_url = _url + '&partner_code=' + encodeURIComponent(partner_code);
			_url = _url + '&partner_name=' + encodeURIComponent(partner_name);
			_url = _url + '&partner_id=<?php echo $partner_id;?>';
			_url = _url + '&id=<?php echo $id;?>';
			_url = _url + '&phone=' + encodeURIComponent(phone);
			_url = _url + '&vat=' + encodeURIComponent(vat);
			_url = _url + '&email=' + encodeURIComponent(email);
			_url = _url + '&address=' + encodeURIComponent(address);
			_url = _url + '&bank_no=' + encodeURIComponent(bank_no);
			_url = _url + '&bank_name=' + encodeURIComponent(bank_name);
		

			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message.length == 36)
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/companyform/edit/' + message;
					}else if(message == "CODE_AVAIBLE")
					{
						alert("Mã công ty đang tồn tại");
						return;
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		
		}
		<?php if($id != ""){?>
		
		function loadLocation()
		{
			var _url = '<?php echo URL;?>includes/mrp_location.php?ac=view&rel_id=<?php echo $id;?>';
		
			loadPage('pnLocation', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		loadLocation();
		
		function loadUsers()
		{
			var _url = '<?php echo URL;?>includes/user_company.php?ac=view&rel_id=<?php echo $id;?>';
		
			loadPage('pnUsers', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		loadUsers();
		<?php
		}
		?>
		function addLocation()
		{
			var _url = '<?php echo URL;?>includes/mrp_location.php?ac=addLine&rel_id=<?php echo $id;?>';
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						loadLocation();
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		
		function saveLocation(id, theInput, name)
		{
			var _url = '<?php echo URL;?>includes/mrp_location.php?ac=saveLine';
			_url = _url + '&id=' + id;
			_url = _url + '&name=' + name;
			_url = _url + '&value=' + encodeURIComponent(theInput.value);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function delLocation(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/mrp_location.php?ac=delLine';
			_url = _url + '&id=' + id;
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadLocation();
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function addUser()
		{
			var _url = '<?php echo URL;?>includes/user_company.php?ac=addLine&rel_id=<?php echo $id;?>';
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						loadUsers();
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function saveUser(id, theInput, name)
		{
			var _url = '<?php echo URL;?>includes/user_company.php?ac=saveLine';
			_url = _url + '&id=' + id;
			_url = _url + '&name=' + name;
			var value = theInput.value;
			if(name == "password")
			{
				if(value == "")
				{
					return;
				}
				value = Sha1.hash(value);
			}
			_url = _url + '&value=' + encodeURIComponent(value);
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function delUser(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/user_company.php?ac=delLine';
			_url = _url + '&id=' + id;
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadUsers();
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
	</script>


</section><!-- #content end -->
