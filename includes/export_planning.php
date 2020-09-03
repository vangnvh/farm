<?php
	session_start();
	require_once('../config.php' );

	function validurl($str) {
		  $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);             
		  $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);             
		  $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);             
		  $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);             
		  $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);             
		  $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);             
		  $str = preg_replace("/(đ)/", 'd', $str);             
		  $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);             
		  $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);             
		  $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);             
		  $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);             
		  $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);             
		  $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);             
		  $str = preg_replace("/(Đ)/", 'D', $str);        
		  $str = str_replace("/", "-", str_replace("&*#39;","",$str));		  
		  $str = str_replace(" ", "-", str_replace("&*#39;","",$str)); 
				  
		  $str = strtolower($str);

		return $str;
	}
	
	function gen_uuid() {
		 $uuid = array(
		  'time_low'  => 0,
		  'time_mid'  => 0,
		  'time_hi'  => 0,
		  'clock_seq_hi' => 0,
		  'clock_seq_low' => 0,
		  'node'   => array()
		 );

		 $uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
		 $uuid['time_mid'] = mt_rand(0, 0xffff);
		 $uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
		 $uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
		 $uuid['clock_seq_low'] = mt_rand(0, 255);

		 for ($i = 0; $i < 6; $i++) {
		  $uuid['node'][$i] = mt_rand(0, 255);
		 }

		 $uuid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
		  $uuid['time_low'],
		  $uuid['time_mid'],
		  $uuid['time_hi'],
		  $uuid['clock_seq_hi'],
		  $uuid['clock_seq_low'],
		  $uuid['node'][0],
		  $uuid['node'][1],
		  $uuid['node'][2],
		  $uuid['node'][3],
		  $uuid['node'][4],
		  $uuid['node'][5]
		 );

		 return $uuid;
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
	
	if(isset($_POST["submit"])) {
		$target_dir = ABSPATH."uploads/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			$handle = fopen($target_file, "r");
			$column = [];
			$row = [];
			if ($handle) 
			{
				while (($line = fgets($handle)) !== false) {
					if(count($column) == 0)
					{
						$column = explode("\t", $line);
					}
					else
					{
						$row = explode("\t", $line);
					}
					if(count($column) == count($row))
					{
						$id = "";
						$planning_id = "";
						$emp_id = "";
						$routing_id = "";
						$routing_name = "";
						$employee_code = "";
						$employee_name = "";
						$quantity = 0;
						$done = 0;
						$unit_price = 0;
						$bonus = 0;
						$deduction = 0;
						
						for($i = 0; $i<count($column); $i++)
						{
							if($column[$i] == "id")
							{
								$id = $row[$i];
							}else if($column[$i] == "planning_id")
							{
								$planning_id = $row[$i];
								
							}else if($column[$i] == "emp_id")
							{
								$emp_id = $row[$i];
							}else if($column[$i] == "routing_id")
							{
								$routing_id = $row[$i];
							}else if($column[$i] == "routing_name")
							{
								$routing_name = $row[$i];
							}else if($column[$i] == "employee_code")
							{
								$employee_code = $row[$i];
							}else if($column[$i] == "employee_name")
							{
								$employee_name = $row[$i];
							}else if($column[$i] == "quantity")
							{
								$quantity = $row[$i];
							}else if($column[$i] == "done")
							{
								$done = $row[$i];
							}else if($column[$i] == "unit_price")
							{
								$unit_price = $row[$i];
							}else if($column[$i] == "bonus")
							{
								$bonus = $row[$i];
							}else if($column[$i] == "deduction")
							{
								$deduction = $row[$i];
							}
						}
						if($id != "")
						{
							$sql = "SELECT employee_id, routing_id FROM mrp_workorder_planning_employee WHERE id='".$id."'";
							$result = pg_exec($db, $sql);
							$numrows = pg_numrows($result);	
							if($numrows>0)
							{
								$row = pg_fetch_array($result, 0);
								$employee_id = $row["employee_id"];
								$routing_id = $row["routing_id"];
								$sql = "UPDATE mrp_workorder_routing SET status =0, write_date=NOW()";
								if($unit_price != "")
								{
									$sql = $sql.", unit_price=".$unit_price;
								}
								if($bonus != "")
								{
									$sql = $sql.", bonus=".$bonus;
								}if($deduction != "")
								{
									$sql = $sql.", bonus=".$deduction;
								}
								$sql = $sql." WHERE employee_id='".$employee_id."' AND routing_id='".$routing_id."'";
								
								$result = pg_exec($db, $sql);
							}
						}else
						{
							$employee_id = $emp_id;
							$sql = "SELECT d1.id FROM hr_employee d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d2.partner_code='".$employee_code."' AND d1.status =0";
							$result = pg_exec($db, $sql);
							$numrows = pg_numrows($result);	
							
							if($numrows>0)
							{
								$row = pg_fetch_array($result, 0);
								$employee_id = $row["id"];
							}
							if($quantity != "")
							{
							
								
								$sql = "INSERT INTO mrp_workorder_planning_employee(";
								$sql = $sql."id";
								$sql = $sql.", create_date";
								$sql = $sql.", write_date";
								$sql = $sql.", company_id";
								$sql = $sql.", status";
								$sql = $sql.", planning_id";
								$sql = $sql.", employee_id";
								$sql = $sql.", routing_id";
								$sql = $sql.", quantity";
								$sql = $sql." )VALUES(";
								$sql = $sql."'".gen_uuid()."'";
								$sql = $sql.", NOW()";
								$sql = $sql.", NOW()";
								$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
								$sql = $sql.", 0";
								$sql = $sql.", '".$planning_id."'";
								$sql = $sql.", '".$employee_id."'";
								$sql = $sql.", '".$routing_id."'";
								$sql = $sql.", ".$quantity;
								$sql = $sql.")";
								
								$result = pg_exec($db, $sql);
							}
							
						}
					}
				}
				fclose($handle);
				
				echo "Import completed";
			} 
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
		}
	else
	{

	$ac = '';
	if(isset($_REQUEST['ac']))
	{
		$ac = $_REQUEST['ac'];
	}
	
	if($ac == "export")
	{
		$planning_id = "";
		if(isset($_REQUEST['id']))
		{
			$planning_id = $_REQUEST['id'];
		}
		
		$sql = "SELECT d1.receipt_no, d1.quantity, d1.receipt_date, d4.receipt_no AS order_no, d1.workorder_id, d2.production_id, d6.name AS product_name FROM mrp_workorder_planning d1 LEFT OUTER JOIN mrp_workorder d2 ON(d1.workorder_id = d2.id) LEFT OUTER JOIN sale_order_product d3 ON(d2.order_product_id = d3.id) LEFT OUTER JOIN sale_order d4 ON(d3.order_id = d4.id) LEFT OUTER JOIN product d6 ON(d3.product_id = d6.id) WHERE d1.id ='".$planning_id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);	
		$production_id = "";
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$production_id = $row["production_id"];
			$name = $row["receipt_no"]."-".$row["quantity"]."-".$row["product_name"]."-".$row["receipt_date"];
			$name = validurl($name);
			$filename =$name.".xls";
			header('Content-type: vnd.ms-excel; charset=utf-8');
			header('Content-Disposition: attachment; filename='.$filename);
			
			$sql = "SELECT d1.id, d1.routing_id, d1.quantity, d3.partner_code, d3.partner_name, d6.category_name, d1.employee_id , (SELECT SUM(m.quantity) FROM mrp_workorder_routing m WHERE m.status =0 AND m.routing_id = d1.routing_id AND m.employee_id = d1.employee_id AND m.workorder_id = d4.workorder_id) AS done_quantity FROM mrp_workorder_planning_employee d1 LEFT OUTER JOIN hr_employee d2 ON(d1.employee_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) LEFT OUTER JOIN mrp_workorder_planning d4 ON(d1.planning_id = d4.id) LEFT OUTER JOIN mrp_routing d5 ON(d1.routing_id = d5.id) LEFT OUTER JOIN mrp_routing_category d6 ON(d5.category_id = d6.id) WHERE d1.planning_id = '".$planning_id."' AND d1.status =0";
			$sql = $sql." ORDER BY d1.create_date ASC";
		
		
			
			$result_routing = pg_exec($db, $sql);
			$numrows_routing = pg_numrows($result_routing);	
			echo "<html>";
			echo "<head>";
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html;  charset=utf-8\" />";
			echo "</head>";
			echo "<body>";
			
			if($numrows_routing == 0)
			{
				echo "<table>";
				echo "<tr>";
				echo "<td>no</td>";
				echo "<td>planning_id</td>";
				echo "<td>routing_id</td>";
				echo "<td>routing_name</td>";
				echo "<td>employee_code</td>";
				echo "<td>employee_name</td>";
				echo "<td>quantity</td>";
				echo "<td>unit_price</td>";
				echo "<td>bonus</td>";
				echo "<td>deduction</td>";
				echo "</tr>";
				$sql = "SELECT d1.id, d2.category_name FROM mrp_routing d1";
				$sql = $sql." LEFT OUTER JOIN mrp_routing_category d2 ON(d1.category_id = d2.id)";
				$sql = $sql." WHERE d1.production_id='".$production_id."'";
				$sql = $sql." AND d1.status =0";
				$sql = $sql." ORDER BY d1.sequence ASC, d2.sequence ASC, d1.create_date ASC";
				$result_routing = pg_exec($db, $sql);
				$numrows_routing = pg_numrows($result_routing);	
				for($i = 0; $i < $numrows_routing; $i++) 
				{
					$row = pg_fetch_array($result_routing, $i);
					$routing_id = $row["id"];
					$category_name = $row["category_name"];
					echo "<tr>";
					echo "<td>".($i + 1)."</td>";
					echo "<td>".$planning_id."</td>";
					echo "<td>".$routing_id."</td>";
					echo "<td>".$category_name."</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "</tr>";
				}
				echo "</table>";
				
			}else
			{
				echo "<table>";
				echo "<tr>";
				echo "<td>no</td>";
				echo "<td>id</td>";
				echo "<td>planning_id</td>";
				echo "<td>emp_id</td>";
				echo "<td>routing_id</td>";
				echo "<td>routing_name</td>";
				echo "<td>employee_code</td>";
				echo "<td>employee_name</td>";
				echo "<td>quantity</td>";
				echo "<td>done</td>";
				echo "<td>unit_price</td>";
				echo "<td>bonus</td>";
				echo "<td>deduction</td>";
				echo "</tr>";
				for($i = 0; $i < $numrows_routing; $i++) 
				{
					$row = pg_fetch_array($result_routing, $i);
					
					$id = $row["id"];
					$routing_id = $row["routing_id"];
					$category_name = $row["category_name"];
					$quantity = $row["quantity"];
					$employee_code = $row["partner_code"];
					$employee_name = $row["partner_name"];
					$employee_id = $row["employee_id"];
					$done_quantity = $row["done_quantity"];
					echo "<tr>";
					echo "<td>".($i + 1)."</td>";
					echo "<td>".$id."</td>";
					echo "<td>".$planning_id."</td>";
					echo "<td>".$employee_id."</td>";
					echo "<td>".$routing_id."</td>";
					echo "<td>".$category_name."</td>";
					echo "<td>".$employee_code."</td>";
					echo "<td>".$employee_name."</td>";
					echo "<td>".$quantity."</td>";
					echo "<td>".$done_quantity."</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "</tr>";
				}
				echo "</table>";
				
			}
			echo "</body>";
			echo "<html>";
		}	
		
		
	}else if($ac == "import")
	{
	?>
	<!DOCTYPE html>
	<html>
	<body>

	<form action="export_planning.php" method="post" enctype="multipart/form-data">
		Select file to upload:
		<input type="file" name="fileToUpload" id="fileToUpload">
		<input type="submit" value="Upload" name="submit">
	</form>

	</body>
	</html>
	<?php
	}
	}
?>
	
	