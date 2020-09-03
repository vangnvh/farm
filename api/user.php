<?php
	session_start();
	
	set_time_limit(600);

	require_once('../config.php' );
	require_once('../tool.php' );
	
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
		
	}else if($ac == "location")
	{
		
		$user = '';
		if(isset($_REQUEST['user']))
		{
			$user = $_REQUEST['user'];
		}
		
		$sql = "SELECT d1.id, d4.partner_code, d4.partner_name FROM res_user_company d1 LEFT OUTER JOIN res_user d2 ON(d1.user_id = d2.id) LEFT OUTER JOIN res_company d3 ON(d1.company_id = d3.id) LEFT OUTER JOIN res_partner d4 ON(d3.partner_id = d4.id) WHERE (d2.user_name='".$user."' OR d2.email='".$user."') AND d1.inactive =0 AND d1.status =0 AND d2.status =0 ORDER BY d4.partner_name ASC";
		
		echo respTable($db, $sql);
		
	}
	else if($ac == "gettable")
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
			
	}
	pg_close($db);
?>