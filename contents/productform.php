
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Product');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item ><a href="<?php echo URL;?><?php echo $lang; ?>/product"><?php echo __('Product');?></a></li>
			
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
		
		$name = '';
		
		if($id != "")
		{
			$sql = "SELECT d1.id, d1.name, d1.code FROM product d1 WHERE d1.status =0 AND d1.id='".$id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$name = $row["name"];
				$code = $row["code"];
				$id = $row["id"];

			}
		}else
		{
			$id= gen_uuid();
		}
			
		
		?>
		<form id="frmRegister" name="frmRegister" class="nobottommargin" >
			<div class="row">
	
				<div class="col-sm-2">
					<label for="editcode"><?php echo __('Code');?> <span style="color:red"><small >*</small></span></label>
					<input type="text" autocomplete="off" id="editcode" name="editcode" value="<?php echo $code; ?>" class="form-control" maxlength = "250" />
				</div>
				<div class="col-sm-10">
					<label for="editname"><?php echo __('Name');?> <span style="color:red"><small >*</small></span></label>
					<input type="text" autocomplete="off" id="editname" name="editname" value="<?php echo $name; ?>" class="form-control" maxlength = "250" />
				</div>
			</div>
					
			<br>
				<div class="clear"></div>

				<div class="col_half nobottommargin">
					<a class="button button-3d nomargin" href="javascript:saveProduct()"><?php echo __('Save');?></a> 
					<a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/product"><?php echo __('Back');?></a> 
					
				</div>
				<div class="col_half col_last" style="text-align:right">
					<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
				</div>

			</form>
		</div>

	</div>
	<script>
		
		
		function saveProduct()
		{
			var ctr = document.frmRegister.editcode;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter code');?>");
				return false;
			}
			var code = ctr.value;
			var ctr = document.frmRegister.editname;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter name');?>");
				return false;
			}
			var name = ctr.value;
			
			var _url = '<?php echo URL;?>includes/action.php?ac=saveProduct';
			_url = _url + '&name=' + encodeURIComponent(name);
			_url = _url + '&code=' + encodeURIComponent(code);
			_url = _url + '&id=<?php echo $id;?>';
			_url = _url + '&company_id=<?php echo $LOGIN_COMPANY_ID;?>';
	
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/product';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		
		}
	</script>


</section><!-- #content end -->
