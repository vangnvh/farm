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
		$sql = "SELECT d1.id, d1.password, d.supplier_id, d.customer_id, d.employee_id, d1.user_name, d3.partner_name, d.company_id, d1.avatar FROM res_user_company d LEFT OUTER JOIN res_user d1 ON(d.user_id = d1.id) LEFT OUTER JOIN customer d2 ON(d.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE (d1.user_name='".$user."' OR d1.email='".$user."') AND d.status =0 AND d.inactive =0 AND d1.status =0";
		
		
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
				echo "user_id=".$user_id.";company_id=".$row["company_id"].";employee_id=".$row["employee_id"].";customer_id=".$row["customer_id"].";token=".$user_id.";avatar=".$row["avatar"];
				
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
		$workorder_routing_id = '';
		if(isset($_REQUEST['workorder_routing_id']))
		{
			$workorder_routing_id = $_REQUEST['workorder_routing_id'];
		}
		$quantity = '';
		if(isset($_REQUEST['quantity']))
		{
			$quantity = $_REQUEST['quantity'];
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
		$id = gen_uuid();
		$sql = "INSERT INTO mrp_workorder_routing_quantity(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", create_uid";
		$sql = $sql.", write_uid";
		$sql = $sql.", employee_id";
		$sql = $sql.", receipt_date";
		$sql = $sql.", quantity";
		$sql = $sql.", workorder_routing_id";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$company_id."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", '".$employee_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", ".$quantity;
		$sql = $sql.", '".$workorder_routing_id."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		echo "OK";
		
	}else if($ac == "account_company" 
	|| $ac == "account_contact"
	|| $ac == "account_all"
	)
	{
		$sql = "";
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$search = '';
		if(isset($_REQUEST['search']))
		{
			$search = $_REQUEST['search'];
		}
		if($ac == "account_company")
		{
			
			$sql = "SELECT d1.company_id FROM res_user_company d1 WHERE d1.status =0 AND d1.user_id='".$user_id."'";
			
			
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);	
			$company_ids = "";
			for($j =0; $j<$numrows; $j++)
			{
				$row = pg_fetch_array($result, $j);
				
				$id = $row["company_id"];
				if($company_ids != "")
				{
					$company_ids = $company_ids." OR ";
				}
				$company_ids = $company_ids." d1.company_id='".$id."'";
			}
			if($company_ids == "")
			{
				$company_ids = "1=0";
			}
			$sql = "SELECT d2.id, d4.partner_code, d4.partner_name, d2.avatar, d2.online, d2.online_date FROM hr_employee d1 LEFT OUTER JOIN res_user d2 ON(d1.rel_id = d2.id) LEFT OUTER JOIN res_partner d4 ON(d1.partner_id = d4.id) WHERE d1.status =0 AND d2.status =0 AND (".$company_ids.") AND d2.id !='".$user_id."'";
			if($search != "")
			{
				$sql = $sql." AND (d4.partner_code LIKE '%".str_replace("'", "''", $search)."%' OR d4.partner_name LIKE '%".str_replace("'", "''", $search)."%')";
			}
			$sql = $sql." ORDER BY d4.partner_name LIMIT 100";
		}else if( $ac == "account_all")
		{
			$sql = "SELECT d1.id, d3.partner_code, d3.partner_name, d1.avatar, d1.online, d1.online_date FROM res_user d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.status =0 AND d2.status =0 AND d1.id !='".$user_id."'";
			if($search != "")
			{
				$sql = $sql." AND (d3.partner_code LIKE '%".str_replace("'", "''", $search)."%' OR d3.partner_name LIKE '%".str_replace("'", "''", $search)."%')";
			}
			$sql = $sql." ORDER BY d3.partner_name LIMIT 100";
		}else if($ac == "account_contact")
		{
			$sql = "SELECT d2.id, d4.partner_code, d4.partner_name, d2.avatar, d2.online, d2.online_date FROM res_user_rel d1 LEFT OUTER JOIN res_user d2 ON(d1.user_id = d2.id) LEFT OUTER JOIN res_company d3 ON(d2.company_id = d3.id) LEFT OUTER JOIN res_partner d4 ON(d3.partner_id = d4.id) WHERE d1.status =0 AND d2.status =0 AND d2.id !='".$user_id."'";
			if($search != "")
			{
				$sql = $sql." AND (d4.partner_code LIKE '%".str_replace("'", "''", $search)."%' OR d4.partner_name LIKE '%".str_replace("'", "''", $search)."%')";
			}
			$sql = $sql." ORDER BY d4.partner_name LIMIT 100";
		}else if($ac == "account_group")
		{
			$sql = "SELECT d2.id, d2.name, COUNT(d1.id) AS number FROM res_user_rel d1 LEFT OUTER JOIN chat_group d2 ON(d1.rel_id = d2.id)  WHERE d1.status =0 AND d2.status =0 AND d1.user_id='".$user_id."'";
			if($search != "")
			{
				$sql = $sql." AND d2.name LIKE '%".str_replace("'", "''", $search)."%'";
			}
			$sql = $sql." ORDER BY d2.name LIMIT 100";
		}
		
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);	
		echo '{"accounts":[';
			for($j =0; $j<$numrows; $j++)
			{
				if($j>0)
				{
					echo ',';
				}
				$row = pg_fetch_array($result, $j);
				if($ac == "account_group")
				{
					echo '{"id": "'.$row["id"].'", "name": "'.$row["name"].'", "number": "'.$row["number"].'"}';
				}else{
				echo '{"id": "'.$row["id"].'", "code": "'.$row["partner_code"].'", "name": "'.$row["partner_name"].'", "avatar": "'.$row["avatar"].'"}';
				}
			}
		echo ']}';
	}else if($ac == "avatar")
	{
		$name = ABSPATH.'disk/avatar/avatar.jpeg';
	
		if (file_exists($name))
		{
			$fp = fopen($name, 'rb');
			header("Content-Type: image/jpg");
			header("Content-Length: " . filesize($name));
			fpassthru($fp);
		}
	}else if($ac == "post_chat")
	{
		$company_id = '';
		if(isset($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$friend_user_id = '';
		if(isset($_REQUEST['friend_user_id']))
		{
			$friend_user_id = $_REQUEST['friend_user_id'];
		}
		$message = '';
		if(isset($_REQUEST['message']))
		{
			$message = $_REQUEST['message'];
		}
		$is_seen = 0;
		if(isset($_REQUEST['is_seen']))
		{
			$is_seen = $_REQUEST['is_seen'];
		}
		$rel_id = '';
		if(isset($_REQUEST['rel_id']))
		{
			$rel_id = $_REQUEST['rel_id'];
		}
		$type = '';
		if(isset($_REQUEST['type']))
		{
			$type = $_REQUEST['type'];
		}
		$message_id = gen_uuid();
		$sql = "INSERT INTO chat_message(";
		$sql = $sql."id";
		$sql = $sql.", create_uid";
		$sql = $sql.", create_date";
		$sql = $sql.", write_uid";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", receipt_date";
		$sql = $sql.", rel_id";
		$sql = $sql.", type";
		$sql = $sql.", message";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$message_id."'";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$company_id."'";
		$sql = $sql.", 0";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$rel_id."'";
		$sql = $sql.", '".$type."'";
		$sql = $sql.", '".str_replace("'", "''", $message)."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		$users = explode(",", $user_id.",".$friend_user_id);
		for($i =0; $i<count($users); $i++)
		{
			if($users[$i]== "")
			{
				continue;
			}
			$id = gen_uuid();
			$sql = "INSERT INTO chat_message_user(";
			$sql = $sql."id";
			$sql = $sql.", create_uid";
			$sql = $sql.", create_date";
			$sql = $sql.", write_uid";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", message_id";
			$sql = $sql.", user_id";
			$sql = $sql.", friend_user_id";
			$sql = $sql.", is_seen";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$id."'";
			$sql = $sql.", '".$user_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$user_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$message_id."'";
			$sql = $sql.", '".$user_id."'";
			$sql = $sql.", '".$users[$i]."'";
			$sql = $sql.", ".$is_seen;
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		
		echo "OK";	
	}else if($ac == "getMessages")
	{
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$friend_user_id = '';
		if(isset($_REQUEST['friend_user_id']))
		{
			$friend_user_id = $_REQUEST['friend_user_id'];
		}
		$publish_date = '';
		if(isset($_REQUEST['publish_date']))
		{
			$publish_date = $_REQUEST['publish_date'];
		}
		$sql = "SELECT d2.id, d1.user_id, d1.friend_user_id, d2.message, d2.type, d1.is_seen, d2.receipt_date, d1.write_date FROM chat_message_user d1 LEFT OUTER JOIN chat_message d2 ON(d1.message_id = d2.id) WHERE d1.status =0 AND d2.status =0";
		if($publish_date!= "")
		{
			$sql = $sql." AND d1.write_date>'".$publish_date."'";
		}
		
		$users = explode(",", $friend_user_id);
		$ids = "";
		for($i =0; $i<count($users); $i++)
		{
			if($users[$i]== "")
			{
				continue;
			}
			if($ids != "")
			{
				$ids = $ids." OR ";
			}
			$ids = $ids." ((d1.user_id='".$user_id."' AND d1.friend_user_id='".$users[$i]."') OR (d1.user_id='".$users[$i]."' AND d1.friend_user_id='".$user_id."'))";
			
		}
		if($ids == "")
		{
			$ids = "1=0";
		}
		$sql = $sql." AND (".$ids.")";
		$sql = $sql." ORDER BY d2.receipt_date ASC";
		
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		echo '{"messages":[';
			for($j =0; $j<$numrows; $j++)
			{
				$row = pg_fetch_array($result, $j);
				if($j>0)
				{
					echo ',';
				}
				echo '{"id": "'.$row["id"].'", "user_id": "'.$row["user_id"].'", "friend_user_id": "'.$row["friend_user_id"].'", "message": "'.$row["message"].'", "type": "'.$row["type"].'", "seen": "'.$row["is_seen"].'", "receipt_date": "'.$row["receipt_date"].'", "publish_date": "'.$row["write_date"].'"}';
			}
		echo ']}';
	}else if($ac == "chatList")
	{
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$sql = "SELECT DISTINCT d.* FROM (SELECT  d2.id, d4.partner_code, d4.partner_name, d2.avatar, d2.online, d2.online_date FROM chat_message_user d1 LEFT OUTER JOIN res_user d2 ON(d1.friend_user_id = d2.id) LEFT OUTER JOIN res_company d3 ON(d2.company_id = d3.id) LEFT OUTER JOIN res_partner d4 ON(d3.partner_id = d4.id) WHERE d1.status =0 AND d1.user_id='".$user_id."' AND d1.friend_user_id !='".$user_id."') d";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		echo '{"accounts":[';
			for($j =0; $j<$numrows; $j++)
			{
				if($j>0)
				{
					echo ',';
				}
				$row = pg_fetch_array($result, $j);
				if($ac == "account_group")
				{
					echo '{"id": "'.$row["id"].'", "name": "'.$row["name"].'", "number": "'.$row["number"].'"}';
				}else{
				echo '{"id": "'.$row["id"].'", "code": "'.$row["partner_code"].'", "name": "'.$row["partner_name"].'", "avatar": "'.$row["avatar"].'"}';
				}
			}
		echo ']}';
	}else if($ac == "upload")
	{
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$for_id = '';
		if(isset($_REQUEST['for_id']))
		{
			$for_id = $_REQUEST['for_id'];
		}
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$data = '';
		if(isset($_REQUEST['data']))
		{
			$data = $_REQUEST['data'];
		}
		$ext = "";
		$index = strpos($name, '.');
		if($index != -1)
		{
			$ext = substr($name, $index + 1);
			$name = substr($name, 0, $index);
		}
		$content_length = 0;
		if(isset($_REQUEST['content_length']))
		{
			$content_length = $_REQUEST['content_length'];
		}
		$company_id = 0;
		if(isset($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		$document_id = gen_uuid();
		$sql = "INSERT INTO document(";
		$sql = $sql."id";
		$sql = $sql.", create_uid";
		$sql = $sql.", create_date";
		$sql = $sql.", write_uid";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", for_id";
		$sql = $sql.", document_name";
		$sql = $sql.", extension";
		$sql = $sql.", content_length";
		$sql = $sql.", file_type";
		$sql = $sql.", path";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$document_id."'";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$company_id."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$for_id."'";
		$sql = $sql.", '".str_replace("'", "''", $name)."'";
		$sql = $sql.", '".$ext."'";
		$sql = $sql.", ".$content_length;
		$sql = $sql.", 'file'";
		$sql = $sql.", '".$document_id."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		$ifp = fopen(ABSPATH."disk/".$document_id, "w") or die("Unable to open file!");
		fwrite( $ifp, base64Decode( $data ) );
		fclose( $ifp );
		echo "OK";
	}else if($ac == "document")
	{
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$ext = '';
		if(isset($_REQUEST['extension']))
		{
			$ext = $_REQUEST['extension'];
		}
		$file = ABSPATH."assets/".$name;
		if (file_exists($file))
		{
			
			header('Content-Type: '.getMime($ext));
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else
		{
			$file = ABSPATH."disk/".$name;
			if (file_exists($file))
			{
				
				
				header('Content-Type: '.getMime($ext));
				header('Content-Disposition: attachment; filename="'.basename($file).'.'.$ext.'"');
				header('Content-Length: ' . filesize($file));
				readfile($file);
				exit;
			}
		}

	}else if($ac == "exec")
	{
		$q = '';
		if(isset($_REQUEST['q']))
		{
			$q = $_REQUEST['q'];
		}
		$result = pg_exec($db, $q);
		echo $q;
		return "OK";
	}else if($ac == "add_mrp_workorder_location")
	{
		$rel_id = '';
		if(isset($_REQUEST['rel_id']))
		{
			$rel_id = $_REQUEST['rel_id'];
		}
		$location_id = '';
		if(isset($_REQUEST['location_id']))
		{
			$location_id = $_REQUEST['location_id'];
		}
		$polygon = '';
		if(isset($_REQUEST['polygon']))
		{
			$polygon = $_REQUEST['polygon'];
		}
		
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		
		$company_id = '';
		if(isset($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		$id = gen_uuid();
		$sql = "INSERT INTO mrp_workorder_location(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", create_uid";
		$sql = $sql.", write_uid";
		$sql = $sql.", location_id";
		$sql = $sql.", rel_id";
		$sql = $sql.", polygon";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".gen_uuid()."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$company_id."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", '".$user_id."'";
		$sql = $sql.", '".$location_id."'";
		$sql = $sql.", '".$rel_id."'";
		$sql = $sql.", '".$polygon."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		echo "OK";
		
	}
	pg_close($db);
?>