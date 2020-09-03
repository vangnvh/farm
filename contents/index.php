<?php
	session_start();
	
	
	$sql = "";
	$lang = LANGUAGE;
	if(isset($_COOKIE[ID.'lang'])) {
		//$lang = $_COOKIE[ID.'lang'];
	} 
	$page = "home";
	$rel_id = "";
	$LOGIN_USER_ID = "";
	if(isset($_SESSION["user_id"]))
	{
		$LOGIN_USER_ID = $_SESSION["user_id"];
	}
	$LOGIN_USER_NAME = "";
	if(isset($_SESSION["user_name"]))
	{
		$LOGIN_USER_NAME = $_SESSION["user_name"];
	}
	$LOGIN_CUSTOMER_NAME = "";
	if(isset($_SESSION["customer_name"]))
	{
		$LOGIN_CUSTOMER_NAME = $_SESSION["customer_name"];
	}
	
	$LOGIN_CUSTOMER_ID = "";
	if(isset($_SESSION["customer_id"]))
	{
		$LOGIN_CUSTOMER_ID = $_SESSION["customer_id"];
	}
	$LOGIN_EMPLOYEE_ID = "";
	if(isset($_SESSION["employee_id"]))
	{
		$LOGIN_EMPLOYEE_ID = $_SESSION["employee_id"];
	}
	
	$LOGIN_COMPANY_ID = "";
	if(isset($_SESSION["company_id"]))
	{
		$LOGIN_COMPANY_ID = $_SESSION["company_id"];
	}
	if($LOGIN_COMPANY_ID == "")
	{
		$LOGIN_COMPANY_ID = COMPANY_ID;
	}
	$LOGIN_PARENT_COMPANY_ID = "";
	if(isset($_SESSION["parent_company_id"]))
	{
		$LOGIN_PARENT_COMPANY_ID = $_SESSION["parent_company_id"];
	}
	$position = strpos($uri, "?"); 
	$PARAMS = "";
	if($position == true)
	{
		$PARAMS = substr($uri, $position + 1);
		$uri = substr($uri, 0, $position);
	}
  
	
	if($uri !='/' && $uri != '')
	{
		$items = explode("/", substr($uri, 1));
		$first_item = '';
		$last_item = '';
		if(count($items)> 0)
		{
			$lang = $items[0];
			setcookie(ID.'lang', $lang, time() + (86400 * 360), "/");
			if(count($items)>1)
			{
				$first_item = $items[1];
				
			}
		}
		
		$i = strpos($first_item, "-");
		if($i>0)
		{
			$last_item = substr($first_item, 0, $i);
		}
		
		
		if($first_item == "faq")
		{
			$page = 'faq';
		}else if($first_item == "post")
		{
			$page = 'post';
		}else if($first_item == "forum")
		{
			$page = 'forum';
		}else if($first_item == "login")
		{
			$page = 'login';
		}else if($first_item == "register")
		{
			$page = 'register';
		}
		else if($first_item == "contact")
		{
			$page = 'contact';
		}else if($first_item == "post")
		{
			$page = 'post';
		}else if($first_item == "productform")
		{
			$page = 'productform';
		}else if($first_item == "request")
		{
			$page = 'request';
		}else if($first_item == "requestform")
		{
			$page = 'requestform';
		}
		else if($first_item == "brand")
		{
			$page = 'brand';
		}else if($first_item == "brandform")
		{
			$page = 'brandform';
		}else if($first_item == "profile")
		{
			$page = 'profile';
		}else if($first_item == "order_request")
		{
			$page = 'order_request';
		}else if($first_item == "order")
		{
			$page = 'order';
		}else if($first_item == "orderform")
		{
			$page = 'orderform';
		}else if($first_item == "employee")
		{
			$page = 'employee';
		}else if($first_item == "employeeform")
		{
			$page = 'employeeform';
		}else if($first_item == "tracking")
		{
			$page = 'tracking';
		}else if($first_item == "customer")
		{
			$page = 'customer';
			
		}else if($first_item == "customerform")
		{
			$page = 'customerform';
		}else if($first_item == "supplier")
		{
			$page = 'supplier';
			
		}else if($first_item == "supplierform")
		{
			$page = 'supplierform';
		}
		else if($first_item == "user")
		{
			$page = 'user';
			
		}else if($first_item == "userform")
		{
			$page = 'userform';
		}
		else if($first_item == "department")
		{
			$page = 'department';
			
		}else if($first_item == "material")
		{
			$page = 'material';
		}else if($first_item == "materialform")
		{
			$page = 'materialform';
		}else if($first_item == "production")
		{
			$page = 'production';
		}else if($first_item == "productionform")
		{
			$page = 'productionform';
		}else if($first_item == "product")
		{
			$page = 'product';
		}else if($first_item == "productform")
		{
			$page = 'productform';
		}else if($first_item == "product_type")
		{
			$page = 'product_type';
			
		}else if($first_item == "product_typeform")
		{
			$page = 'product_typeform';
		}else if($first_item == "company")
		{
			$page = 'company';
		}else if($first_item == "companyform")
		{
			$page = 'companyform';
		}else if($first_item == "mrplocation")
		{
			$page = 'mrplocation';
		}else if($first_item == "payment_type")
		{
			$page = 'payment_type';
		}else if($first_item == "payment_typeform")
		{
			$page = 'payment_typeform';
		}else if($first_item == "payment")
		{
			$page = 'payment';
		}else if($first_item == "paymentform")
		{
			$page = 'paymentform';
		}
		else
		{
			if(is_numeric($last_item))
			{
				$sql = "SELECT d1.category_id, d1.rel_id FROM url_web d1 WHERE d1.sequence=".$last_item;
				
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);
				if($numrows>0)
				{
					$row = pg_fetch_array($result, 0);
					$category_id = $row["category_id"];
					$rel_id = $row["rel_id"];
					if($category_id == "faq_category")
					{
						$page = 'faq';
					}else if($category_id == "post_category")
					{
						$page = 'post';
					}else if($category_id == "forum_category")
					{
						$page = 'forum';
					}
					
				}else {
					$page = '404';
				}
				
				
			}
			else if($last_item != '')
			{
				$page = '404';
			}
			
		}
		
	}
	if(strrpos(",vi,en,ru", ','.$lang) == -1)
	{
		$lang = LANGUAGE;
	}
	$lang_id = '76';
	if($lang == "ru")
	{
		$lang_id ='52';
	}
	else if($lang == "en-US" || $lang == "en")
	{
		$lang_id ='1';
	}
	else if($lang == "cn" )
	{
		$lang_id ='8';
	}else if($lang == "jp")
	{
		$lang_id ='35';
	}else if($lang == "kr")
	{
		$lang_id ='39';
	}else if($lang == "fr")
	{
		$lang_id ='23';
	}
	
	include( ABSPATH .'includes/lang/'.$lang.'.php');
	
	
	$PAGE_TILE = META_TITLE;
	$PAGE_KEYWORD = META_KEYWORD;
	$PAGE_DESCRIPTION = META_DESCRIPTION;
	if($rel_id != "")
	{
			$sql = "SELECT d1.title, d1.keyword, d1.description, dl.name AS k, dl2.name AS d, dl3.name AS t FROM meta d1 LEFT OUTER JOIN res_lang_rel dl ON(d1.id = dl.rel_id AND dl.lang_id='".$lang_id."' AND dl.status =0 AND dl.column_name='keyword') LEFT OUTER JOIN res_lang_rel dl2 ON(d1.id = dl2.rel_id AND dl2.lang_id='".$lang_id."' AND dl2.status =0 AND dl2.column_name='description') LEFT OUTER JOIN res_lang_rel dl3 ON(d1.id = dl3.rel_id AND dl3.lang_id='".$lang_id."' AND dl3.status =0 AND dl3.column_name='title') WHERE d1.rel_id='".$rel_id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$s = $row["k"];
				if($s != "")
				{
					$PAGE_KEYWORD = $s;
				}else{
					$s = $row["keyword"];
					if($s != "")
					{
						$PAGE_KEYWORD = $s;
					}
				}
				
				$s = $row["d"];
				if($s != "")
				{
					$PAGE_DESCRIPTION = $s;
				}else{
					$s = $row["description"];
					if($s != "")
					{
						$PAGE_DESCRIPTION = $s;
					}
				}
				
				$s = $row["t"];
				if($s != "")
				{
					$PAGE_DESCRIPTION = $s;
				}else{
					$s = $row["title"];
					if($s != "")
					{
						$PAGE_TILE = $s;
					}
				}
				
			}
			
	}
	//sendWS(WS_HOST, WS_PORT, '{"action": "send", "name": "553943fd-325b-4bdb-fa46-27ca40cf3cf6", "data": "My Messsage"}');

?>
<html lang="en-US">
<head>
	<title><?php echo $PAGE_TILE;?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="Vang Nguyen Van" />
	<meta name="robots" content="INDEX,FOLLOW" />
	<meta name="description" content="<?php echo $PAGE_DESCRIPTION;?>" />
	<meta name="keywords" content="<?php echo $PAGE_KEYWORD;?>" />
	<link rel="canonical" href="<?php echo URL;?><?php echo $lang; ?>" />
	<meta http-equiv="content-language" content="vi" /><meta name="google-site-verification" content="Nh8ONkx92XXxW_E12xgaiv9K7kWYuQVGPxuyMr9tNUY" /><link rel="SHORTCUT ICON" href="<?php echo URL;?>assets/images/favicon.ico" />
      

	<!-- Stylesheets
	============================================= -->
	<link href="http://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,900" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php echo URL;?>assets/css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo URL;?>assets/css/style.css?v=5" type="text/css" />

	<link rel="stylesheet" href="<?php echo URL;?>assets/css/dark.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo URL;?>assets/css/font-icons.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo URL;?>assets/css/animate.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo URL;?>assets/css/magnific-popup.css" type="text/css" />

	<!-- Bootstrap Switch CSS -->
	<link rel="stylesheet" href="<?php echo URL;?>assets/css/components/bs-switches.css" type="text/css" />

	<link rel="stylesheet" href="<?php echo URL;?>assets/css/responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo URL;?>assets/datepicker/foopicker.css" type="text/css" />
	<meta name='viewport' content='initial-scale=1, viewport-fit=cover'>

	<!-- Seo Demo Specific Stylesheet -->
	<link rel="stylesheet" href="<?php echo URL;?>assets/css/colors.php?color=FE9603" type="text/css" /> <!-- Theme Color -->
	<link rel="stylesheet" href="<?php echo URL;?>assets/seo/css/fonts.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo URL;?>assets/seo/seo.css" type="text/css" />
	<script src="<?php echo URL;?>assets/chartjs/Chart.js"></script>
	<script src="<?php echo URL;?>assets/js/jquery.js"></script>
	<script src="<?php echo URL;?>assets/js/controller.js"></script>
	
  <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_API_KEY;?>&libraries=geometry&sensor=false"></script>
	
	<style type="text/css">

		
		.chatbox {
		  border: 2px solid #dedede;
		  background-color: #ffffff;
		  border-radius: 5px;
		  padding: 10px;
		  margin: 10px 0;
		 
		}
		

		.time-right {
		  text-align: right;
		  color: #aaa;
		}

		
		.time-left {
		  text-align: left;
		  color: #999;
		}

</style>
</head>

<body class="stretched">
	
	<!-- Document Wrapper
	============================================= -->
	<div id="wrapper" class="clearfix">

		<!-- Top Bar
		============================================= -->
		<div id="top-bar" class="transparent-topbar">

			<div class="container clearfix">

				<div class="col_half nobottommargin clearfix">

					<!-- Top Links
					============================================= -->
					<div class="top-links">
						<ul>
							<?php
							if($LOGIN_USER_ID == '')
							{
							?>
							<li><a href="<?php echo URL;?><?php echo $lang; ?>/login"><?php echo __('login');?></a></li>
							<li><a href="<?php echo URL;?><?php echo $lang; ?>/register"><?php echo __('Register');?></a></li>
							<?php }else{ ?>
							<li><a href="<?php echo URL;?><?php echo $lang; ?>/profile"><?php if($LOGIN_CUSTOMER_NAME != ""){ echo $LOGIN_CUSTOMER_NAME; }else{ echo $LOGIN_USER_NAME;} ?> </a></li>
							<li><a href="javascript:logout()"><?php echo __('logout');?></a></li>
							<?php } ?>
							<li><a href="<?php echo URL;?><?php echo $lang; ?>">
						
							<img src="<?php echo URL;?>assets/images/flags/<?php echo $lang; ?>.png" alt="Lang"><?php echo __($lang);?></a>
							<?php
								$lang_url = $uri;
								$lang_url = str_replace("/".$lang, "", $lang_url);
								if($lang_url == "/" )
								{
									$lang_url ="";
								}
							?>
								<ul>
									<?php if($lang != 'vi'){?>
									<li><a href="<?php echo URL;?>vi<?php echo $lang_url;?>"><img src="<?php echo URL;?>assets/images/flags/vi.png" alt="Lang"><?php echo __('vi');?></a></li>
									<?php } ?>
									<?php if($lang != 'en'){?>
									<li><a href="<?php echo URL;?>en<?php echo $lang_url;?>"><img src="<?php echo URL;?>assets/images/flags/en.png" alt="Lang"><?php echo __('en');?></a></li>
									<?php } ?>
									<?php if($lang != 'ru'){?>
									<li><a href="<?php echo URL;?>ru<?php echo $lang_url;?>"><img src="<?php echo URL;?>assets/images/flags/ru.png" alt="Lang"><?php echo __('ru');?></a></li>
									<?php } ?>
									<?php if($lang != 'cn'){?>
									<li><a href="<?php echo URL;?>cn<?php echo $lang_url;?>"><img src="<?php echo URL;?>assets/images/flags/cn.png" alt="Lang"><?php echo __('cn');?></a></li>
									<?php } ?>
									<?php if($lang != 'jp'){?>
									<li><a href="<?php echo URL;?>jp<?php echo $lang_url;?>"><img src="<?php echo URL;?>assets/images/flags/jp.png" alt="Lang"><?php echo __('jp');?></a></li>
									<?php } ?>
									<?php if($lang != 'kr'){?>
									<li><a href="<?php echo URL;?>kr<?php echo $lang_url;?>"><img src="<?php echo URL;?>assets/images/flags/kr.png" alt="Lang"><?php echo __('kr');?></a></li>
									<?php } ?>
								
								</ul>
							</li>

						</ul>
					</div><!-- .top-links end -->

				</div>

				<div class="col_half fright dark col_last clearfix nobottommargin">

					<!-- Top Social
					============================================= -->
					<div id="top-social">
						<ul>
							<li><a href="https://www.facebook.com/" class="si-facebook" target="_blank"><span class="ts-icon"><i class="icon-facebook"></i></span><span class="ts-text">Facebook</span></a></li>
							<li><a href="tel:<?php echo CONTACT_TEL; ?>" class="si-call"><span class="ts-icon"><i class="icon-call"></i></span><span class="ts-text"><?php echo CONTACT_TEL; ?></span></a></li>
							<li><a href="mailto:info@itada.com.vn" class="si-email3"><span class="ts-icon"><i class="icon-envelope-alt"></i></span><span class="ts-text"><?php echo CONTACT_EMAIL; ?></span></a></li>
						</ul>
					</div><!-- #top-social end -->

				</div>

			</div>

		</div><!-- #top-bar end -->

		<!-- Header
		============================================= -->
		<header id="header" class="transparent-header floating-header clearfix">

			<div id="header-wrap">

				<div class="container clearfix">

					<div id="primary-menu-trigger"><i class="icon-reorder"></i></div>

					<!-- Logo
					============================================= -->
					<div id="logo">
						<a href="http://www.itada.com.vn/" class="standard-logo" data-dark-logo="<?php echo URL;?>assets/images/logo.png"><img src="<?php echo URL;?>assets/images/logo.png?1" alt="Canvas Logo"></a>
						<a href="http://www.itada.com.vn/" class="retina-logo" data-dark-logo="<?php echo URL;?>assets/images/logo@2x.png"><img src="<?php echo URL;?>assets/images/logo@2x.png?1" alt="Canvas Logo"></a>
					</div><!-- #logo end -->

					<!-- Primary Navigation
					============================================= -->
					<nav id="primary-menu" class="with-arrows">

						<ul>
							<li <?php if($page == 'home'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?>"><div><?php echo __('home');?></div></a></li>
						
							<?php
							if($LOGIN_USER_ID != '')
							{
							?>
							<?php if($LOGIN_EMPLOYEE_ID == ""){?>
							
							<li <?php if($page == 'customer' || $page == 'customerform'){ ?> class="current"<?php } ?>><a href"#"><div>Đơn hàng</div></a>
								<ul>
									<li <?php if($page == 'order' || $page == 'orderform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/order"><div><?php echo __('Orders');?></div></a></li>
									<li  <?php if($page == 'customer' || $page == 'customerform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/customer"><div><?php echo __('Customers');?></div></a></li>
								
									<li  <?php if($page == 'product' || $page == 'productform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/product"><div><?php echo __('Product');?></div></a></li>
									<li  <?php if($page == 'product_type' || $page == 'product_typeform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/product_type"><div><?php echo __('Product Type');?></div></a></li>
							
								</ul>
							</li>
							<li <?php if($page == 'supplier' || $page == 'material'){ ?> class="current"<?php } ?>><a href"#"><div>Mua hàng</div></a>
								<ul>
									
									<li  <?php if($page == 'supplier' || $page == 'supplierform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/supplier"><div><?php echo __('Suppliers');?></div></a></li>
								
									<li  <?php if($page == 'material' || $page == 'material'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/material"><div><?php echo __('Material');?></div></a></li>
									
							
								</ul>
							</li>
							<li <?php if($page == 'customer' || $page == 'customerform'){ ?> class="current"<?php } ?>><a href"#"><div>Sản xuất</div></a>
								<ul>
									
									<li  <?php if($page == 'rmplocation'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/mrplocation"><div><?php echo __('Location');?></div></a></li>
									<li  <?php if($page == 'production' || $page == 'production'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/production"><div><?php echo __('SOP');?></div></a></li>
							
								</ul>
							</li>
							<li <?php if($page == 'user' || $page == 'userform'){ ?> class="current"<?php } ?>><a href"#"><div>Hệ thống</div></a>
								<ul>
									<li  <?php if($page == 'company' || $page == 'companyform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/company"><div><?php echo __('Company');?></div></a></li>
									<li  <?php if($page == 'payment_type' || $page == 'payment_typeform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/payment_type"><div><?php echo __('Payment Type');?></div></a></li>
									<li  <?php if($page == 'payment' || $page == 'paymentform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/payment"><div><?php echo __('Payments');?></div></a></li>
									<li  <?php if($page == 'employee' || $page == 'employeeform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/employee"><div><?php echo __('Employees');?></div></a></li>
									<li  <?php if($page == 'user' || $page == 'userform'){ ?> class="current"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/user"><div><?php echo __('Users');?></div></a></li>
							
								</ul>
							</li>
							
							
							<?php } ?>
							
							<?php
							}
							?>
				
						</ul>
					

						

						<!-- Menu Buttons
						============================================= -->
						<?php
						if($LOGIN_USER_ID == '')
						{
						?>
						<a href="<?php echo URL;?><?php echo $lang; ?>/register" class="button button-rounded fright leftmargin-sm"><?php echo __('Register');?></a>
						<?php } ?>

					</nav><!-- #primary-menu end -->

				</div>

			</div>

		</header><!-- #header end -->
		<?php
		include( ABSPATH .'contents/'.$page.'.php');
		?>
		
		<!-- Footer
		============================================= -->
		<footer id="footer" class="noborder bg-white">

		
			<!-- Copyrights
			============================================= -->
			<div id="copyrights" style="background: url('<?php echo URL;?>assets/seo/images/hero/footer.svg') no-repeat top center; background-size: cover; padding-top: 70px;">

				<div class="container clearfix">

					<div class="col_half">
						Copyrights &copy; 2019 All Rights Reserved by <a href="http://vidu.vn" target="_blank">eFarm</a> Inc.<br>
						<div class="copyright-links"><a href="#"><?php echo __('Terms of Use');?></a> / <a href="#"><?php echo __('Privacy Policy');?></a></div>
					</div>

					<div class="col_half col_last tright">
						<div class="copyrights-menu copyright-links clearfix">
							<a href="<?php echo URL;?><?php echo $lang; ?>"><?php echo __('home');?></a>/<a href="http://www.vidu.vn" target="_blank"><?php echo __('About');?></a>/<a href="<?php echo URL;?><?php echo $lang; ?>/faq"><?php echo __('faqs');?></a>/<a href="<?php echo URL;?><?php echo $lang; ?>/contact"><?php echo __('contact_us');?></a>
						</div>
					</div>

				</div>

			</div><!-- #copyrights end -->

		</footer><!-- #footer end -->

	</div><!-- #wrapper end -->

	<!-- Go To Top
	============================================= -->
	<div id="gotoTop" class="icon-angle-up"></div>
	<div id="pnFullDialog" class="modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" id="closeFullDialog" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					 <h4 class="modal-title" id="pnFullTitle"></h4> 
				</div>
				<div class="modal-body" >
					<div class="white-box" id="pnFullDialogContent">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Đóng</button>
				</div>
			</div>
		</div>
	</div>
	<!-- External JavaScripts
	============================================= -->
	
	<script src="<?php echo URL;?>assets/js/plugins.js"></script>

	<!-- Footer Scripts
	============================================= -->
	<script src="<?php echo URL;?>assets/js/functions.js"></script>
	
	<script src="<?php echo URL;?>assets/js/tool.js"></script>
	<script src="<?php echo URL;?>assets/js/sha1.js"></script>
	


	
		<div id="pnChat" class="display-bottomright" style="padding:4px; z-index:300; width:300; margin-bottom:80px ; background-color:#FEFEFE; display:none;">
		<div id="tabs">
			
		</div>
		<div id="tabs_title">
		</div>
		<div id="tabs_content" style="min-height:300px; background-color:#FAE9E9;">
			
		</div>
		<div id="tabs_message">
			<input type="text" id="editmessage" onKeyDown="if(event.keyCode == 13){postMessage();}"/><input type="button" value="Send" onclick="postMessage()"/>
		</div>
	</div>
	<script>
		
		jQuery(document).ready( function($){
			function pricingSwitcher( elementCheck, elementParent, elementPricing ) {
				elementParent.find('.pts-left,.pts-right').removeClass('pts-switch-active');
				elementPricing.find('.pts-switch-content-left,.pts-switch-content-right').addClass('hidden');

				if( elementCheck.filter(':checked').length > 0 ) {
					elementParent.find('.pts-right').addClass('pts-switch-active');
					elementPricing.find('.pts-switch-content-right').removeClass('hidden');
				} else {
					elementParent.find('.pts-left').addClass('pts-switch-active');
					elementPricing.find('.pts-switch-content-left').removeClass('hidden');
				}
			}

			$('.pts-switcher').each( function(){
				var element = $(this),
					elementCheck = element.find(':checkbox'),
					elementParent = $(this).parents('.pricing-tenure-switcher'),
					elementPricing = $( elementParent.attr('data-container') );

				pricingSwitcher( elementCheck, elementParent, elementPricing );

				elementCheck.on( 'change', function(){
					pricingSwitcher( elementCheck, elementParent, elementPricing );
				});
			});
			
					
		});
		function logout()
		{
			var _url = '<?php echo URL;?>includes/action.php?ac=logout';
		
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						document.location.href ='<?php echo URL ?>';
					}else
					{
						alert(message);
					}
				}
				
			}, true);
		}
		function openPopup(_url)
		{
		
			loadPage('pnFullDialogContent', _url, function(status, message)
			{
				if(status== 0)
				{
					$("#pnFullDialog").modal();
				}
				
			}, false);
		}
		function closePopup()
		{
			var p = document.getElementById('closeFullDialog');
			p.click();
		}
	
	</script>

</body>
</html>
<?php
pg_close($db);
?>