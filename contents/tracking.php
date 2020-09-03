<?php

$employee_code = "";
$employee_name = "";
$sql = "select d2.partner_code, d2.partner_name FROM hr_employee d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.id='".$LOGIN_EMPLOYEE_ID."'";
$result = pg_exec($db, $sql);
$numrows = pg_numrows($result);
if($numrows>0)
{
	$row = pg_fetch_array($result, 0);
	$employee_code = $row["partner_code"];
	$employee_name = $row["partner_name"];
}

?>
<link rel="stylesheet" type="text/css" href="<?php echo URL;?>assets/css/jsCalendar.css">
<script type="text/javascript" src="<?php echo URL;?>assets/js/jsCalendar.js"></script>
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Tracking');?></h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item <?php if($rel_id == ''){ ?> active <?php } ?>" ><a href="<?php echo URL;?><?php echo $lang; ?>/tracking"><?php echo __('Tracking');?></a></li>
			
		</ol>
	</div>

</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		<div class="container clearfix">
			<div class="row">
					<div class="col-sm-4">
						<div id="my-calendar"></div>
					</div>
					<div class="col-sm-8">
						<b><?php echo $employee_code;?>. <?php echo $employee_name;?></b>
						<hr>
						<div id="pnRouting"></div>
					</div>
			
			</div>
		</div>

	</div>

</section>

<script type="text/javascript">
		// Create the calendar
		var calendar = jsCalendar.new("#my-calendar");

		calendar.onDateClick(function(event, date){
			
			fillRouting(date)
		});
		function formatDate(date) {
			var d = new Date(date),
				month = '' + (d.getMonth() + 1),
				day = '' + d.getDate(),
				year = d.getFullYear();

			if (month.length < 2) 
				month = '0' + month;
			if (day.length < 2) 
				day = '0' + day;

			return [year, month, day].join('-');
		}
		function fillRouting(date)
		{
			var d = formatDate(date);
			
			var _url = '<?php echo URL;?>includes/routing_by_employee.php?employee_id=<?php echo $LOGIN_EMPLOYEE_ID;?>&d=' + encodeURIComponent(d);
	
			loadPage('pnRouting', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		fillRouting(new Date());

		
</script>

