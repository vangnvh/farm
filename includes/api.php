<?php
	session_start();
	
	set_time_limit(600);

	require_once('../config.php' );
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
	
	
	function findReceiptNo($db, $id) {
		$sql = "SELECT receipt_number FROM res_receipt_no WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);	
		$i = 1;
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$s = $row["receipt_number"];
			$i = (int) $s;
			$i = $i + 1;
			$sql = "UPDATE res_receipt_no SET receipt_number=" .$i.", receipt_date=NOW(), write_date=NOW() WHERE id='".$id."'";
			$rs = pg_exec($db, $sql);
		}else
		{
			$sql = "INSERT INTO res_receipt_no(receipt_number, id, receipt_date) VALUES(1,'".$id."',NOW())";
			$rs = pg_exec($db, $sql);
		}
		return $i;
			
	}
	
	
	$ac = '';
	if(isset($_REQUEST['ac']))
	{
		$ac = $_REQUEST['ac'];
	}
	if(isset($_POST['ac']))
	{
		$ac = $_POST['ac'];
	}
	
	
	if($ac == "register")
	{
		
		$user = '';
		if(isset($_REQUEST['user']))
		{
			$user = $_REQUEST['user'];
		}
		$pass = '';
		if(isset($_REQUEST['pass']))
		{
			$pass = $_REQUEST['pass'];
		}
		$sql = "SELECT d1.id, d1.password, d.supplier_id, d.customer_id, d.employee_id, d1.user_name, d3.partner_name, d.company_id FROM res_user_company d LEFT OUTER JOIN res_user d1 ON(d.user_id = d1.id) LEFT OUTER JOIN customer d2 ON(d.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE (d1.user_name='".$user."' OR d1.email='".$user."') AND d.status =0 AND d.inactive =0 AND d1.status =0";
		
		
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$user_id = $row["id"];
			$s = hash("sha256", "[".$user_id."]".$pass);
			$len = strlen($pass);
			for($i = 0; $i<$len; $i++)
			{
				$s = $s.chr($i + 48);
			}
			$pass = hash("md5", $s);
			if($pass == $row["password"])
			{
				echo "user_id=".$user_id.";company_id=".$row["company_id"].";employee_id=".$row["employee_id"].";customer_id=".$row["customer_id"].";token=".$user_id;
				
			}else{
				echo 'INCORRECT';
			}
			
		}else{
			echo 'INCORRECT';
		}
		
	}else if($ac == "gettable")
	{
		
		$sql = '';
		if(isset($_REQUEST['q']))
		{
			$sql = $_REQUEST['q'];
		}
		if(isset($_POST['q']))
		{
			$sql = $_POST['q'];
		}
		
		if($sql != "")
		{
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			$numfields = pg_num_fields($result);
			
			for($i =0; $i <$numfields; $i++)
			{
				if($i>0)
				{
					echo "\t";
				}
				echo pg_field_name ( $result , $i );
			}
			for($j=0; $j<$numrows; $j++)
			{
				echo "\n";
				$row = pg_fetch_array($result, $j);
				
				for($i =0; $i <$numfields; $i++)
				{
					if($i>0)
					{
						echo "\t";
					}
					echo $row[$i];
				}
			}
		}
			
	}else if($ac == "workorder_routing_add")
	{
		$workorder_id = '';
		if(isset($_REQUEST['workorder_id']))
		{
			$workorder_id = $_REQUEST['workorder_id'];
		}
		$routing_id = '';
		if(isset($_REQUEST['routing_id']))
		{
			$routing_id = $_REQUEST['routing_id'];
		}
		$quantity = '';
		if(isset($_REQUEST['quantity']))
		{
			$quantity = $_REQUEST['quantity'];
		}
		$done_quantity = '';
		if(isset($_REQUEST['done_quantity']))
		{
			$done_quantity = $_REQUEST['done_quantity'];
		}
		$start_date = '';
		if(isset($_REQUEST['start_date']))
		{
			$start_date = $_REQUEST['start_date'];
		}
		$end_date = '';
		if(isset($_REQUEST['end_date']))
		{
			$end_date = $_REQUEST['end_date'];
		}
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$employee_id = '';
		if(isset($_REQUEST['employee_id']))
		{
			$employee_id = $_REQUEST['employee_id'];
		}
		$company_id = '';
		if(isset($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		$workorder_routing_id = gen_uuid();
		$sql = "INSERT INTO mrp_workorder_routing(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", start_date";
		$sql = $sql.", end_date";
		$sql = $sql.", start_work";
		$sql = $sql.", end_work";
		$sql = $sql.", create_uid";
		$sql = $sql.", write_uid";
		$sql = $sql.", coeffecient";
		$sql = $sql.", employee_id";
		$sql = $sql.", quantity";
		$sql = $sql.", workorder_id";
		$sql = $sql.", routing_id";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$workorder_routing_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$company_id."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".str_replace("'", "''", $start_date)."'";
		$sql = $sql.", '".str_replace("'", "''", $end_date)."'";
		$sql = $sql.", '".str_replace("'", "''", $start_date)."'";
		$sql = $sql.", '".str_replace("'", "''", $end_date)."'";
		$sql = $sql.", '".str_replace("'", "''", $user_id)."'";
		$sql = $sql.", '".str_replace("'", "''", $user_id)."'";
		$sql = $sql.", 1";
		$sql = $sql.", '".str_replace("'", "''", $employee_id)."'";
		$sql = $sql.", '".str_replace("'", "''", $done_quantity)."'";
		$sql = $sql.", '".str_replace("'", "''", $workorder_id)."'";
		$sql = $sql.", '".str_replace("'", "''", $routing_id)."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		echo "OK";	
		
	}
	pg_close($db);
?>