<?php

function buildRouting($values, $parent_id, $sIndex,$url)
{
	
	$len = 0;
	
	for($i = 0; $i<count($values); $i++)
	{
		if($values[$i][1] == $parent_id)
		{
			$len += 1;
		}
	}
	$index = 0;
	for($i = 0; $i<count($values); $i++)
	{
		
		if($values[$i][1] == $parent_id)
		{
			$index += 1;
			
			$id = $values[$i][0];
			$name = $values[$i][2];
			
		?>
		
		<tr>
			<td width="15" align="right" ><img src="<?php echo $url;?>assets/images/arrow_child.gif" border="0" /></td>
			<td style="padding-left:5px; padding-top:5px"><a href="javascript:loadEmployees('<?php echo $id;?>')"> <?php echo $name;?></a> <a href="javascript:addDepartment('<?php echo $id; ?>')"><img src="<?php echo $url;?>assets/images/add.png"/></a> &nbsp; <a href="javascript:delDepartment('<?php echo $id; ?>')"><img src="<?php echo $url;?>assets/images/remove.png"/></a></td>
		  </tr>
		  <tr>
			<td width="15" <?php if($i<($len-1)){?>style="background:url(<?php echo $url;?>assets/images/arrow_child_space.gif) repeat-y" <?php } ?>></td>
			<td style="padding-left:5px"><table width="100%" border="0" cellpadding="0" cellspacing="0"><?php buildRouting($values, $id, "", $url);?></table></td>
		</tr>
		<?php
		}
	} 
}

?>
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Departments');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item active" ><a href="<?php echo URL;?><?php echo $lang; ?>/department"><?php echo __('Employees');?></a></li>
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
						<a href="javascript:addDepartment('')"><img src="<?php echo URL;?>assets/images/add.png"></a>
						<?php
							$sql = "SELECT d1.id, d1.name, d1.parent_id FROM hr_department d1  WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."'";
							$result = pg_exec($db, $sql);
							$numrows = pg_numrows($result);	
							$departments = array();
							for($i = 0; $i < $numrows; $i++) 
							{
								$row = pg_fetch_array($result, $i);
								$arr = array();
								$arr[0] = $row["id"];
								$arr[1] = $row["parent_id"];
								$arr[2] = $row["name"];
								$departments[$i] = $arr;
							}
							
						?>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<?php buildRouting($departments, "", "", URL);?>
						</table>
		
					</div>
					<div class="col-sm-8">
						<div id="pnEmployee"></div>
					</div>
				</div>
				
				<br>
				
		
		</div>

	</div>
	<script>
		function addDepartment(parent_id)
		{
			var name = prompt("<?php echo __('Enter department name');?>", "");
			if (name == null || name == "") {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=addDepartment';
			_url = _url + '&name=' + encodeURIComponent(name);
			_url = _url + '&parent_id=' + parent_id;
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/department';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function editDepartment(id, name)
		{
			var name = prompt("<?php echo __('Enter department name');?>", name);
			if (name == null || name == "") {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=editDepartment';
			_url = _url + '&name=' + encodeURIComponent(name);
			_url = _url + '&id=' + id;
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/department';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function delDepartment(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delDepartment';
			_url = _url + '&id=' + encodeURIComponent(id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/department';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function doSearch(p)
		{
			var search = document.getElementById('editsearch').value;
			document.location.href ='<?php echo URL;?><?php echo $lang; ?>/department?search=' + encodeURIComponent(search) + "&p=" + p;
		}
		function delRows()
		{
			var count = 0;
			var checkboxes = document.getElementsByName("[]");
			var ids = "";
			for (var i= 0; i<checkboxes.length; i++) {
				if(checkboxes[i].checked && checkboxes[i].value != "")
				{
					if(ids != "")
					{
						ids += ",";
					}
					ids += checkboxes[i].id;
					count += 1;
				}
			}
			if(ids == "")
			{
				alert("<?php echo __('Please check to delete');?>");
				return;
			}
			var result = confirm(count + ". <?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			delDepartment(ids);
		}
		var department_id = "";
		function loadEmployees(departId)
		{
			department_id = departId;
			var _url = '<?php echo URL;?>includes/department_employee.php';
			_url = _url + '?company_id=<?php echo $LOGIN_COMPANY_ID;?>&department_id=' + departId;
			loadPage('pnEmployee', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		function delEmployee(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delDepartmentEmployee';
			_url = _url + '&id=' + encodeURIComponent(id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadEmployees(department_id);
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function addDepartmentEmployee(department_id)
		{
			var ctr = document.getElementById('emp' + department_id);
			if(ctr.value == '')
			{
				alert("Select employee");
				return;
				
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=addDepartmentEmployee';
			_url = _url + '&employee_id=' + ctr.value;
			_url = _url + '&department_id=' + department_id;
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadEmployees(department_id);
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		
	</script>

</section><!-- #content end -->
