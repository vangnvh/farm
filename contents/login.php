<?php

$user = '';
if(isset($_REQUEST['user']))
{
	$user = $_REQUEST['user'];
}
$pass = '';
if(isset($_REQUEST['pass']))
{
	$pass = $_REQUEST['pass'];
}
$message = "";
if($user != "")
{
	
	$user = '';
	if(isset($_REQUEST['user']))
	{
		$user = $_REQUEST['user'];
	}
	$pass = '';
	if(isset($_REQUEST['pass']))
	{
		$pass = $_REQUEST['pass'];
	}
	$sql = "SELECT d1.id, d1.password, d.supplier_id, d.customer_id, d.employee_id, d1.user_name, d3.partner_name, d.company_id , d4.parent_id AS parent_company_id FROM res_user_company d LEFT OUTER JOIN res_user d1 ON(d.user_id = d1.id) LEFT OUTER JOIN customer d2 ON(d.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) LEFT OUTER JOIN res_company d4 ON(d.company_id = d4.id) WHERE (d1.user_name='".$user."' OR d1.email='".$user."') AND d.status =0 AND d.inactive =0 AND d1.status =0";
	
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);
	
	if($numrows>0)
	{
		$row = pg_fetch_array($result, 0);
		$user_id = $row["id"];
		$s = hash("sha256", "[".$user_id."]".$pass);
		$len = strlen($pass);
		for($i = 0; $i<$len; $i++)
		{
			$s = $s.chr($i + 48);
		}
		$pass = hash("md5", $s);
		
		
		if($pass == $row["password"])
		{
			$_SESSION["user_id"] = $user_id ;
			$_SESSION["supplier_id"] = $row["supplier_id"];
			$_SESSION["customer_id"] = $row["customer_id"];
			$_SESSION["employee_id"] = $row["employee_id"];
			$_SESSION["user_name"] = $row["user_name"];
			$_SESSION["customer_name"] = $row["partner_name"];
			$_SESSION["company_id"] = $row["company_id"];
			$_SESSION["parent_company_id"] = $row["parent_company_id"];
			$message = 'OK';
		}else{
			$message ='INVALID_PASSWORD';
		}
		
	}else{
		$message ='INVALID_USER';
	}
}
	
?>
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
		<form id="frmSubmit" name="frmSubmit" action="<?php echo URL;?><?php echo $lang; ?>/login" method="post">
			<input type="hidden" autocomplete="off" name="user" id="user" />
			<input type="hidden" autocomplete="off" name="pass" id="pass" />
		</form>
		<div class="container clearfix">

			<div class="row">
				<div class="col-sm-3 "></div>
				<div class="col-sm-6 ">

					<div class="well well-lg nobottommargin">
						<form id="frmLogin" name="frmLogin" class="nobottommargin">

							<h3><?php echo __('Login to your Account');?></h3>
							<span style="color:red">
							<?php
							if($message == "INVALID_PASSWORD")
							{
								echo __('INVALID_PASSWORD');
							}else if($message == "INVALID_USER")
							{
								echo __('INVALID_USER');
							}
							?>
							</span>
							<div class="col_full">
								<label for="user"><?php echo __('Username');?>:</label>
								<input type="text" autocomplete="off" onKeyDown="if(event.keyCode == 13){doLogin();}" name="user" id="user" value="<?php echo $user;?>" class="form-control" />
							</div>

							<div class="col_full">
								<label for="login-form-password"><?php echo __('Password');?>:</label>
								<input type="password" onKeyDown="if(event.keyCode == 13){doLogin();}" autocomplete="off" name="pwd" id="pwd" value="" class="form-control" />
							</div>

							<div class="col_full nobottommargin">
								<a class="button button-3d nomargin" onClick="doLogin()" value="login"><?php echo __('Login');?></a>
								<a href="javascript:alert('forget password')" class="fright"><?php echo __('Forgot Password?');?></a>
							</div>

						</form>
					</div>
				</div>
				<div class="col-sm-3 "></div>	
			</div>

		</div>

	</div>
</section><!-- #content end -->

<script>
	function doLogin() 
	{
		var ctr = document.frmLogin.user;
		if(ctr.value == '')
		{
			
			alert("<?php echo __('Please, enter user name');?>");
			ctr.focus();
			return false;
		}
		var user = document.frmLogin.user.value;
		ctr = document.frmLogin.pwd;
		var pass = ctr.value;
		pass = Sha1.hash(pass);
		document.frmSubmit.user.value = user;
		document.frmSubmit.pass.value = pass;
		document.frmSubmit.submit();
		
		/*var _url = '<?php echo URL;?>includes/action.php?ac=login';
		_url = _url + '&user=' + encodeURIComponent(user);
		_url = _url + '&pass=' + encodeURIComponent(pass);
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					document.location.href ='<?php echo URL;?><?php echo $lang; ?>';
				}else if(message == "INVALID_PASSWORD")
				{
					alert("<?php echo __('INVALID_PASSWORD');?>");
				}else if(message == "INVALID_USER")
				{
					alert("<?php echo __('INVALID_USER');?>");
				}else{
					alert(message);
				}
			}
			
		}, true);*/
			
	}
	document.frmLogin.user.focus();
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
		
		_url = _url + '&vat=' + encodeURIComponent(vat);
		_url = _url + '&phone=' + encodeURIComponent(phone);
		_url = _url + '&email=' + encodeURIComponent(email);
		_url = _url + '&address=' + encodeURIComponent(address);
		_url = _url + '&user_name=' + encodeURIComponent(user_name);
		_url = _url + '&pass=' + encodeURIComponent(password);
		
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					document.frmLogin.user.value = user_name;
					document.frmLogin.pwd.value = document.frmRegister.editpassword.value;
					doLogin();
					
				}else if(message == "AVAIBLE")
				{
					alert("<?php echo __('User or email exist');?>");
				}else if(message.indexOf("COMPANY") != -1)
				{
					alert("Confirm customer");
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	<?php
	if($message == "OK")
	{
	?>
	document.location.href ='<?php echo URL;?><?php echo $lang; ?>';
	<?php }?>
	
</script>