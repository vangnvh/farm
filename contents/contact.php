<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('contact_us');?></h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><?php echo __('contact_us');?></li>
			
		</ol>
	</div>

</section><!-- #page-title end -->
<section id="google-map" class="gmap slider-parallax">
<div class="mapouter"><div class="gmap_canvas"><iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=46D%20C%C3%A2y%20Keo%2C%20P.%20Tam%20Ph%C3%BA%2C%20Q.%20Th%E1%BB%A7%20%C4%90%E1%BB%A9c%2C%20Tp.%20H%E1%BB%93%20Ch%C3%AD%20Minh&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://www.embedgooglemap.net/blog/divi-discount-code-elegant-themes-coupon/">itada.com.vn</a></div><style>.mapouter{position:relative;text-align:right;height:300px;width:100%;}.gmap_canvas {overflow:hidden;background:none!important; height:300px;width:100%;}</style></div>

</section>
<section id="content">

	<div class="content-wrap">

		<div class="container clearfix">

			<!-- Postcontent
			============================================= -->
			<div class="postcontent nobottommargin">

				<h3><?php echo __('Send us an Email');?></h3>

				<div class="form-widget">

					<div class="form-result"></div>

					<form class="nobottommargin" id="frmContact" name="frmContact">

						<div class="form-process"></div>

						<div class="col_one_third">
							<label for="editname"><?php echo __('Name');?> <small>*</small></label>
							<input type="text" id="editname" name="editname" value="<?php echo $LOGIN_CUSTOMER_NAME;?>" class="sm-form-control required" />
						</div>

						<div class="col_one_third">
							<label for="editemail"><?php echo __('Email Address');?> <span style="color:red"><small >*</small></span></label>
							<input type="email" id="editemail" name="editemail" value="" maxlength="250" class="required email sm-form-control" />
						</div>

						<div class="col_one_third col_last">
							<label for="editphone"><?php echo __('Phone');?></label>
							<input type="text" id="editphone" name="editphone" value="" class="sm-form-control" />
						</div>

						<div class="clear"></div>

						<div class="col_two_third">
							<label for="editsubject"><?php echo __('Subject');?> <span style="color:red"><small >*</small></span></label>
							<input type="text" id="editsubject" name="editsubject" maxlength="250" value="" class="required sm-form-control" />
						</div>
						
								
						<div class="clear"></div>

						<div class="col_full">
							<label for="editmessage"><?php echo __('Message');?> <small>*</small></label>
							<textarea class="required sm-form-control" id="editmessage" name="editmessage" rows="6" cols="30"></textarea>
						</div>
						
						<div class="col_full">
							<button class="button button-3d nomargin" type="button" onclick="sendMessage()" ><?php echo __('Send Message');?></button>
						</div>

			

					</form>
				</div>

			</div><!-- .postcontent end -->

			<!-- Sidebar
			============================================= -->
			<div class="sidebar col_last nobottommargin">

				<address>
					<strong><?php echo __('Headquarters');?>:</strong><br>
					<?php echo CONTACT_ADDRESS;?>
				</address>
				<abbr title="Phone Number"><strong><?php echo __('Phone');?>:</strong></abbr> <?php echo CONTACT_TEL; ?><br>
				<abbr title="Fax"><strong><?php echo __('Fax');?>:</strong></abbr> <?php echo CONTACT_FAX; ?><br>
				<abbr title="Email Address"><strong><?php echo __('Email');?>:</strong></abbr> <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a>
			</div><!-- .sidebar end -->

		</div>

	</div>
</section><!-- #content end -->


	<script>

		function sendMessage()
		{
			var ctr = document.frmContact.editname;
			if(ctr.value == '')
			{
				
				alert("<?php echo __('Please, enter company name');?>");
				ctr.focus();
				return false;
			}
			var name = ctr.value;
			ctr = document.frmContact.editemail;
			var email = ctr.value;
			if(validate_email(email) == false)
			{
				ctr.focus();
				alert("<?php echo __('Invalid email');?>");
				return false;
			}
			
			ctr = document.frmContact.editphone;
			var phone = ctr.value;
			
			
			ctr = document.frmContact.editsubject;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter subject');?>");
				return false;
			}
			var subject = ctr.value;
			
			
			
			ctr = document.frmContact.editmessage;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter message');?>");
				return false;
			}
			var message = ctr.value;
			
			
			var _url = '<?php echo URL;?>includes/action.php?ac=sendContactMessage';
			_url = _url + '&name=' + encodeURIComponent(name);
			
			_url = _url + '&phone=' + encodeURIComponent(phone);
			_url = _url + '&email=' + encodeURIComponent(email);
			_url = _url + '&subject=' + encodeURIComponent(subject);
			
			_url = _url + '&message=' + encodeURIComponent(message);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						alert("<?php echo __('Your message is sent');?>");
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
	</script>