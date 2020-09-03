
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Location');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><a href="<?php echo URL;?><?php echo $lang; ?>/mrplocation"><?php echo __('Location');?></a></li>
			
		</ol>
	</div>

</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		<div class="container clearfix">
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
		</div>

	</div>
	<script>

		
		<?php if($LOGIN_COMPANY_ID != ""){?>
		
		function loadLocation()
		{
			var _url = '<?php echo URL;?>includes/mrp_location.php?ac=view&rel_id=<?php echo $LOGIN_COMPANY_ID;?>';
		
			loadPage('pnLocation', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		loadLocation();
		
		
		<?php
		}
		?>
		function addLocation()
		{
			var _url = '<?php echo URL;?>includes/mrp_location.php?ac=addLine&rel_id=<?php echo $LOGIN_COMPANY_ID;?>';
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
	</script>


</section><!-- #content end -->
