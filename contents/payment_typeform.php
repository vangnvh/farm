
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Payment Type');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><a href="<?php echo URL;?><?php echo $lang; ?>/payment_type"><?php echo __('Payment Type');?></a></li>
			
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
		
			$name = '';
			$description = '';
			$factor = 0;
		
			
			if($id != "")
			{
				$sql = "SELECT d1.id, d1.name FROM res_payment_category d1 WHERE d1.status =0 AND d1.id='".$id."'";
				
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);
				if($numrows>0)
				{
					$row = pg_fetch_array($result, 0);
					$name = $row["name"];
					
					
				}
				
				
			}
			
		
		?>
		<div class="col_full nobottommargin">
			<a class="button button-3d nomargin" href="javascript:saveLine()" value="register"><?php echo __('Save');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/payment_type" ><?php echo __('Back');?></a> 
		</div>
		<br>
		<form id="frmRegister" name="frmRegister" class="nobottommargin" >

					<div class="row">
							<div class="col-sm-10">
								<label for="editname"><?php echo __('Name');?> <span style="color:red"><small >*</small></span></label>
								<input type="text" id="editname" name="editname" value="<?php echo $name; ?>" class="form-control" maxlength = "64"  />
							</div>
							
							
					</div>
					
					<br>
					
					<div class="col_full">
						<label for="editdescription"><?php echo __('Description');?></label>
						<textarea id="editdescription" name="editdescription" class="form-control" maxlength = "250"><?php echo $description; ?></textarea>
					</div>

					<div class="clear"></div>

				

					<div class="col_half nobottommargin">
						<a class="button button-3d nomargin" href="javascript:saveLine()" value="register"><?php echo __('Save');?></a> <a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/product_type"><?php echo __('Back');?></a> 
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
			var ctr = document.frmRegister.editname;
			if(ctr.value == '')
			{
				alert("<?php echo __('Please, enter name');?>");
				ctr.focus();
				return false;
			}
			
			var name = ctr.value;
			
			ctr = document.frmRegister.editdescription;
			var description = ctr.value;
			

		
			var _url = '<?php echo URL;?>includes/action.php?ac=savePaymentType';
	
			_url = _url + '&name=' + encodeURIComponent(name);
			_url = _url + '&description=' + encodeURIComponent(description);
			_url = _url + '&id=<?php echo $id;?>';

			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/payment_type';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		
		}
	</script>


</section><!-- #content end -->
