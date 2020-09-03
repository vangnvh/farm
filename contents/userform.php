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
		<h1><?php echo __('Users');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item ><a href="<?php echo URL;?><?php echo $lang; ?>/user"><?php echo __('Users');?></a></li>
			
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
		$user_company_id = "";
		if($uri !='/' && $uri != '')
		{
			$items = explode("/", substr($uri, 1));
			if(count($items)> 2)
			{
				$ac = $items[2];
				if($ac == "edit")
				{
					$user_company_id = $items[3];
				}else if($ac == "clone")
				{
					$user_company_id = $items[3];
					
				}
			}
		}
		
		$user_name = '';
		$name = '';
		$phone = '';
		$email = '';
		$password = '';
		$user_id = '';
		
		if($user_company_id != "")
		{
			$sql = "SELECT d1.id, d1.user_id, d2.name, d2.user_name, d2.email, d2.password FROM res_user_company d1 LEFT OUTER JOIN res_user d2 ON(d1.user_id = d2.id) WHERE d1.status =0 AND d1.id='".$user_company_id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$user_name = $row["user_name"];
				$name = $row["name"];
				$email = $row["email"];
				$password = $row["password"];
				$user_id = $row["user_id"];

			}
		}
			
		
		?>
		<form id="frmRegister" name="frmRegister" class="nobottommargin" >

					<div class="col_full">
						<label for="editname"><?php echo __('Name');?> <span style="color:red"><small >*</small></span></label>
						<input type="text" autocomplete="off" id="editname" name="editname" value="<?php echo $name; ?>" class="form-control" maxlength = "250" />
					</div>
					
					
					<div class="col_one_third">
						<label for="edituser_name"><?php echo __('User');?> <span style="color:red"><small >*</small></span></label>
						<input type="text" id="edituser_name" name="edituser_name" value="<?php echo $user_name; ?>" class="form-control" maxlength = "64"  />
					</div>
					<div class="col_one_third">
						<label for="editpassword"><?php echo __('Password');?> <span style="color:red"><small >*</small></span></label>
						<input type="text" id="editpassword" name="editpassword" placeholder="<?php echo $password; ?>" class="form-control" maxlength = "64"  />
					</div>
					
					<div class="col_one_third col_last">
						<label for="editemail"><?php echo __('Email');?></label>
						<input type="text" id="editreg_phone" name="editemail" value="<?php echo $email; ?>" class="form-control" maxlength = "150" />
					</div>
					

					<div class="clear"></div>

					<div class="col_half nobottommargin">
						<a class="button button-3d button-black nomargin" href="javascript:saveUser()" value="register"><?php echo __('Save');?></a> 
					</div>
					<div class="col_half col_last" style="text-align:right">
						<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
					</div>

				</form>
		</div>

	</div>
	<script>
		
		
		function saveUser()
		{
			var ctr = document.frmRegister.edituser_name;
			if(ctr.value == '')
			{
				
				alert("<?php echo __('Please, enter user name');?>");
				ctr.focus();
				return false;
			}
			var user_name = ctr.value;
			ctr = document.frmRegister.editname;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter name');?>");
				return false;
			}
			var name = ctr.value;
			ctr = document.frmRegister.editpassword;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter password');?>");
				return false;
			}
			var password = ctr.value;
			
			ctr = document.frmRegister.editemail;
			var email = ctr.value;
			if(email != "" && validate_email(email) == false)
			{
				ctr.focus();
				alert("<?php echo __('Invalid email');?>");
				return false;
			}
			
			password = Sha1.hash(password);
			
			var _url = '<?php echo URL;?>includes/action.php?ac=saveUser';
			_url = _url + '&user_name=' + encodeURIComponent(user_name);
			_url = _url + '&name=' + encodeURIComponent(name);
			_url = _url + '&password=' + encodeURIComponent(password);
			_url = _url + '&email=' + encodeURIComponent(email);
			_url = _url + '&user_id=<?php echo $user_id;?>';
			_url = _url + '&user_id=<?php echo $user_id;?>';
			_url = _url + '&user_company_id=<?php echo $user_company_id;?>';
			_url = _url + '&company_id=<?php echo $LOGIN_COMPANY_ID;?>';
	
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/user';
					}else if(message == "INVALID_USER")
					{
						alert("Invalid user name");
						return;
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		
		}
	</script>


</section><!-- #content end -->
