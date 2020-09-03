<?php

require_once('../config.php' );

$department_id = "";
if(isset($_REQUEST['department_id']))
{
	$department_id = $_REQUEST['department_id'];
}
$company_id = "";
if(isset($_REQUEST['company_id']))
{
	$company_id = $_REQUEST['company_id'];
}


?>

		<?php
		
		$sql = "SELECT d1.id, d1.department_id, d3.partner_code, d3.partner_name, d1.employee_id FROM hr_department_employee d1 LEFT OUTER JOIN hr_employee d2 ON(d1.employee_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) LEFT OUTER JOIN hr_department d4 ON(d1.department_id = d4.id) WHERE d1.department_id = '".$department_id."' AND d1.status =0";
		$sql = $sql." ORDER BY d3.partner_code ASC";
		
		
		$result_emp = pg_exec($db, $sql);
		$numrows_emp = pg_numrows($result_emp);	
		$emloyees = array();
		$employee_ids = "";
		for($i = 0; $i < $numrows_emp; $i++) 
		{
			$row = pg_fetch_array($result_emp, $i);
			$arr = array();
			$arr[0] = $row["id"];
			$arr[1] = $row["partner_code"];
			$arr[2] = $row["partner_name"];
			$arr[3] = $row["department_id"];
			$arr[4] = $row["employee_id"];
			$emloyees[$i] = $arr;
			if($employee_ids != "")
			{
				$employee_ids = $employee_ids." AND ";
			}
			$employee_ids = $employee_ids." d1.id !='".$row["employee_id"]."'";
		}
		
		$sql = "SELECT d1.id, d2.partner_code, d2.partner_name FROM hr_employee d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND d1.company_id='".$company_id."'";
		if($employee_ids != "")
		{
				$sql = $sql." AND (".$employee_ids.")";
		}
		$sql= $sql." ORDER BY d2.partner_code ASC";
		
		$result_emp = pg_exec($db, $sql);
		$numrows_emp = pg_numrows($result_emp);	
		$emloyeesList = array();
		for($i = 0; $i < $numrows_emp; $i++) 
		{
			$row = pg_fetch_array($result_emp, $i);
			$arr = array();
			$arr[0] = $row["id"];
			$arr[1] = $row["partner_code"];
			$arr[2] = $row["partner_name"];
			$emloyeesList[$i] = $arr;
		}
		
	

		?>
		
		<select class="form-control" id="emp<?php echo $department_id;?>">
		
		<?php
		for($k =0; $k<count($emloyeesList); $k++)
		{
			$arr = $emloyeesList[$k];
			$employee_id = $arr[0];
			$employee_code = $arr[1];
			$employee_name = $arr[2];
			$hasItem = false;
			for($n =0; $n<count($emloyees); $n++)
			{
				if( $emloyees[$n][4] == $employee_id && $emloyees[$n][3] == $department_id)
				{
					$hasItem = true;
					break;
				}
			}
			if($hasItem == false)
			{
		?>
			<option value="<?php echo $employee_id;?>"><?php echo $employee_code;?>. <?php echo $employee_name;?></option>
		<?php
			}
		}
		?>
		</select>
		<a href="javascript:addDepartmentEmployee('<?php echo $department_id; ?>')">+ Thêm</a>
		<br>
		<?php
		for($k =0; $k<count($emloyees); $k++)
		{
			$arr = $emloyees[$k];
			if($arr[3] == $department_id)
			{
				$department_employee_id = $arr[0];
				$employee_code = $arr[1];
				$employee_name = $arr[2];
				
		?>
		<a href="javascript:delEmployee('<?php echo $department_employee_id; ?>')">  - </a> <?php echo $employee_code;?>. <?php echo $employee_name;?><br>
		<?php 
			}
		} 
		?>
		
		
	