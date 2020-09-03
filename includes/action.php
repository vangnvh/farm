<?php
	session_start();
	
	set_time_limit(600);

	require_once('../config.php' );
	require_once('../tool.php' );
	
	
	$LOGIN_COMPANY_ID = "";
	if(isset($_SESSION["company_id"]))
	{
		$LOGIN_COMPANY_ID = $_SESSION["company_id"];
	}
	if($LOGIN_COMPANY_ID == "")
	{
		$LOGIN_COMPANY_ID = COMPANY_ID;
		
	}
	$LOGIN_USER_ID = "";
	if(isset($_SESSION["user_id"]))
	{
		$LOGIN_USER_ID = $_SESSION["user_id"];
	}
	
	$ac = '';
	if(isset($_REQUEST['ac']))
	{
		$ac = $_REQUEST['ac'];
	}
	if($ac == "checkChatUserStatus")
	{
		$data = '';
		if(isset($_REQUEST['data']))
		{
			$data = $_REQUEST['data'];
		}
		$items = explode(",", $data);
		$sql = "SELECT id, online FROM res_user WHERE status =0";
		$data = '';
		for($i =0; $i<count($items); $i++)
		{
			if($data != "")
			{
				$data = $data." OR ";
			}
			$data = $data." id='".$items[$i]."'";
			
		}
		$sql = $sql." AND (".$data .")";
		
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		$data = '';
		for($i = 0; $i<$numrows; $i++)
		{
			if($data != "")
			{
				$data = $data.",";
			}
			$row = pg_fetch_array($result, $i);
			$data = $data.$row["id"]."=".$row["online"];
		}
		echo $data;
	}else if($ac == "login")
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
		$sql = "SELECT d1.id, d1.password, d.supplier_id, d.customer_id, d.employee_id, d1.user_name, d3.partner_name, d.company_id, d4.parent_id AS parent_company_id FROM res_user_company d LEFT OUTER JOIN res_user d1 ON(d.user_id = d1.id) LEFT OUTER JOIN customer d2 ON(d.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) LEFT OUTER JOIN res_company d4 ON(d.company_id = d4.id) WHERE (d1.user_name='".$user."' OR d1.email='".$user."')  AND d.status =0 AND d.inactive =0 AND d1.status =0";
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
				$_SESSION["user_id"] = $user_id ;
				$_SESSION["supplier_id"] = $row["supplier_id"];
				$_SESSION["customer_id"] = $row["customer_id"];
				$_SESSION["employee_id"] = $row["employee_id"];
				$_SESSION["user_name"] = $row["user_name"];
				$_SESSION["company_id"] = $row["company_id"];
				$_SESSION["parent_company_id"] = $row["parent_company_id"];
				$_SESSION["customer_name"] = $row["partner_name"];
				echo 'OK';
			}else{
				echo 'INVALID_PASSWORD';
			}
			
		}else{
			echo 'INVALID_USER';
		}
		
	}else if($ac == "logout")
	{
		$_SESSION["user_id"] = "";
		$_SESSION["supplier_id"] = "";
		$_SESSION["customer_id"] = "";
		$_SESSION["employee_id"] = "";
		$_SESSION["user_name"] = "";
		$_SESSION["customer_name"] = "";
		echo 'OK';
	}else if($ac == "sendContactMessage")
	{
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$email = '';
		if(isset($_REQUEST['email']))
		{
			$email = $_REQUEST['email'];
		}
		$phone = '';
		if(isset($_REQUEST['phone']))
		{
			$phone = $_REQUEST['phone'];
		}
		$subject = '';
		if(isset($_REQUEST['subject']))
		{
			$subject = $_REQUEST['subject'];
		}
		
		$message = '';
		if(isset($_REQUEST['message']))
		{
			$message = $_REQUEST['message'];
		}
		
		$content = "Liên hệ từ: http://crm.vinacert.vn<br>";
		$content = $content."Từ: <b>".$name."</b><br>";
		$content = $content."Email: <b>".$email."</b><br>";
		$content = $content."Điện thoại: <b>".$phone."</b><br>";
		$content = $content."Chủ đề: <b>".$subject."</b><br>";

		$content = $content."Nội dung: <b>".$message."</b><br>";
		
		$id = gen_uuid();
		$sql = "INSERT INTO mail_message(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", subject";
		$sql = $sql.", body";
		$sql = $sql.", is_html";
		$sql = $sql.", from_account_id";
		$sql = $sql.", send";
		$sql = $sql.", mail_to";
		$sql = $sql.", mail_from";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".str_replace("'", "''", $subject)."'";
		$sql = $sql.", '".str_replace("'", "''", $content)."'";
		$sql = $sql.", 1";
		$sql = $sql.", '987d67fb-9c51-450d-83b5-12e370ba3f44'";
		$sql = $sql.", 0";
		$sql = $sql.", 'cskh@vinacert.vn'";
		$sql = $sql.", '".str_replace("'", "''", $email)."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		
		echo "OK";
	}
	else if($ac == "register")
	{
		$company_name = '';
		if(isset($_REQUEST['company_name']))
		{
			$company_name = $_REQUEST['company_name'];
		}
		$commercial_name = '';
		if(isset($_REQUEST['commercial_name']))
		{
			$commercial_name = $_REQUEST['commercial_name'];
		}
		$vat = '';
		if(isset($_REQUEST['vat']))
		{
			$vat = $_REQUEST['vat'];
		}
		$phone = '';
		if(isset($_REQUEST['phone']))
		{
			$phone = $_REQUEST['phone'];
		}
		$email = '';
		if(isset($_REQUEST['email']))
		{
			$email = $_REQUEST['email'];
		}
		$address = '';
		if(isset($_REQUEST['address']))
		{
			$address = $_REQUEST['address'];
		}
		$contact_name = '';
		if(isset($_REQUEST['contact_name']))
		{
			$contact_name = $_REQUEST['contact_name'];
		}
		$contact_mobile = '';
		if(isset($_REQUEST['contact_mobile']))
		{
			$contact_mobile = $_REQUEST['contact_mobile'];
		}
		$contact_email = '';
		if(isset($_REQUEST['contact_email']))
		{
			$contact_email = $_REQUEST['contact_email'];
		}
		$user = '';
		if(isset($_REQUEST['user_name']))
		{
			$user = $_REQUEST['user_name'];
		}
		$pass = '';
		if(isset($_REQUEST['pass']))
		{
			$pass = $_REQUEST['pass'];
		}
		
		$sql = "SELECT d1.id FROM res_user_company d LEFT OUTER JOIN res_user d1 ON(d.user_id = d1.id) WHERE (d1.user_name='".str_replace("'", "''", $user)."' OR d1.email='".str_replace("'", "''", $user)."') AND d.status =0 AND d.inactive =0 AND d1.status =0";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			echo 'AVAIBLE';
		}else
		{
			
			$partner_id = gen_uuid();
			$partner_code = '';
			$sql = "INSERT INTO res_partner(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_code";
			$sql = $sql.", partner_name";
			$sql = $sql.", vat";
			$sql = $sql.", phone";
			$sql = $sql.", email";
			$sql = $sql.", address";
			$sql = $sql.", commercial_name";
			$sql = $sql.", contact_name";
			$sql = $sql.", contact_mobile";
			$sql = $sql.", contact_email";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$partner_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".str_replace("'", "''",$user)."'";
			$sql = $sql.", '".str_replace("'", "''", $company_name)."'";
			$sql = $sql.", '".str_replace("'", "''",$vat)."'";
			$sql = $sql.", '".str_replace("'", "''", $phone)."'";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '".str_replace("'", "''", $address)."'";
			$sql = $sql.", '".str_replace("'", "''", $commercial_name)."'";
			$sql = $sql.", '".str_replace("'", "''", $contact_name)."'";
			$sql = $sql.", '".str_replace("'", "''", $contact_mobile)."'";
			$sql = $sql.", '".str_replace("'", "''", $contact_email)."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			
			$company_id = $partner_id;
			$sql = "INSERT INTO res_company(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_id";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$company_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_id."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			
			$user_id = gen_uuid();
			$s = hash("sha256", "[".$user_id."]".$pass);
			$len = strlen($pass);
			for($i = 0; $i<$len; $i++)
			{
				$s = $s.chr($i + 48);
			}
			$pass = hash("md5", $s);
			$sql = "INSERT INTO res_user(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", user_name";
			$sql = $sql.", name";
			$sql = $sql.", password";
			$sql = $sql.", partner_id";
			$sql = $sql.", email";
			$sql = $sql.", lang_id";
			$sql = $sql.", gmt_offset";
			$sql = $sql.", date_format";
			$sql = $sql.", time_format";
			$sql = $sql.", thousands_sep";
			$sql = $sql.", decimal_point";
			$sql = $sql.", type";
			$sql = $sql.", inactive";
			$sql = $sql.", online";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$user_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".str_replace("'", "''", $user)."'";
			$sql = $sql.", '".str_replace("'", "''",$user)."'";
			$sql = $sql.", '".$pass."'";
			$sql = $sql.", ''";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '76'";
			$sql = $sql.", '420'";
			$sql = $sql.", 'DD/MM/YYYY'";
			$sql = $sql.", 'HH:MM:SS'";
			$sql = $sql.", '.'";
			$sql = $sql.", ','";
			$sql = $sql.", ''";
			$sql = $sql.", 0";
			$sql = $sql.", 0";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			$sql = "INSERT INTO res_user_company(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", user_id";
			$sql = $sql.", inactive";
			$sql = $sql.", customer_id";
			$sql = $sql.", group_id";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".gen_uuid()."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$user_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", ''";
			$sql = $sql.", '7911f06c-1fbc-46ba-8633-3fd14ab3c0e6'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			echo 'OK';
			
		}

	}else if($ac == "addEmployee")
	{
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$sql = "SELECT d3.partner_code, d3.partner_name, d3.phone, d3.email, d3.address, d3.email, d1.company_id, d3.vat FROM res_user d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.id='".$user_id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$company_id = $LOGIN_COMPANY_ID;
			$partner_code = $row["partner_code"];
			$partner_name = $row["partner_name"];
			
			$phone = $row["phone"];
			$vat = $row["vat"];
			$address = $row["address"];
			$email = $row["email"];
			
			$partner_id = gen_uuid();
			$sql = "INSERT INTO res_partner(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_code";
			$sql = $sql.", partner_name";
			$sql = $sql.", vat";
			$sql = $sql.", phone";
			$sql = $sql.", email";
			$sql = $sql.", address";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$partner_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_code."'";
			$sql = $sql.", '".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", '".str_replace("'", "''",$vat)."'";
			$sql = $sql.", '".str_replace("'", "''", $phone)."'";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '".str_replace("'", "''", $address)."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			
			$employee_id = gen_uuid();
			$sql = "INSERT INTO hr_employee(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_id";
			$sql = $sql.", barcode";
			$sql = $sql.", rel_id";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$employee_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_id."'";
			$sql = $sql.", ''";
			$sql = $sql.", '".$user_id."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			echo $employee_id;
			
			
		}else
		{
			echo 'NOT FOUND';
		}
	}
	else if($ac == "addCustomer")
	{
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$sql = "SELECT d3.partner_code, d3.partner_name, d3.phone, d3.email, d3.address, d3.email, d1.company_id, d3.vat FROM res_user d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.id='".$user_id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$company_id = $LOGIN_COMPANY_ID;
			$partner_code = $row["partner_code"];
			$partner_name = $row["partner_name"];
			
			$phone = $row["phone"];
			$vat = $row["vat"];
			$address = $row["address"];
			$email = $row["email"];
			
			$partner_id = gen_uuid();
			$sql = "INSERT INTO res_partner(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_code";
			$sql = $sql.", partner_name";
			$sql = $sql.", vat";
			$sql = $sql.", phone";
			$sql = $sql.", email";
			$sql = $sql.", address";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$partner_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_code."'";
			$sql = $sql.", '".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", '".str_replace("'", "''",$vat)."'";
			$sql = $sql.", '".str_replace("'", "''", $phone)."'";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '".str_replace("'", "''", $address)."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			
			$customer_id = gen_uuid();
			$sql = "INSERT INTO customer(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_id";
			$sql = $sql.", rel_id";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$customer_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_id."'";
			$sql = $sql.", '".$user_id."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			 echo $customer_id;
			
			
		}else
		{
			echo 'NOT FOUND';
		}
	}
	else if($ac == "saveEmployee")
	{
		
		$partner_id = '';
		if(isset($_REQUEST['partner_id']))
		{
			$partner_id = $_REQUEST['partner_id'];
		}
		$employee_id = '';
		if(isset($_REQUEST['employee_id']))
		{
			$employee_id = $_REQUEST['employee_id'];
		}
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		
		$partner_name = '';
		if(isset($_REQUEST['partner_name']))
		{
			$partner_name = $_REQUEST['partner_name'];
		}
		$partner_code = '';
		if(isset($_REQUEST['partner_code']))
		{
			$partner_code = $_REQUEST['partner_code'];
		}
		$sql = "SELECT d1.id FROM hr_employee d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id =d2.id) WHERE d1.id !='".$employee_id."' AND d2.partner_code='".$partner_code."' AND d1.status=0 AND d1.company_id='".$LOGIN_COMPANY_ID."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		if($numrows>0)
		{
			echo "CODE_AVAIBLE";
			exit();
		}
		
		$vat = '';
		if(isset($_REQUEST['vat']))
		{
			$vat = $_REQUEST['vat'];
		}
		$phone = '';
		if(isset($_REQUEST['phone']))
		{
			$phone = $_REQUEST['phone'];
		}
		$email = '';
		if(isset($_REQUEST['email']))
		{
			$email = $_REQUEST['email'];
		}
		$address = '';
		if(isset($_REQUEST['address']))
		{
			$address = $_REQUEST['address'];
		}
		
		if($partner_id != '')
		{
			$sql = "UPDATE res_partner SET partner_name='".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", vat='".str_replace("'", "''", $vat)."'";
			$sql = $sql.", partner_code='".str_replace("'", "''", $partner_code)."'";
			$sql = $sql.", phone='".str_replace("'", "''", $phone)."'";
			$sql = $sql.", email='".str_replace("'", "''", $email)."'";
			$sql = $sql.", address='".str_replace("'", "''", $address)."'";
		
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$partner_id."'";
			$result = pg_exec($db, $sql);
		}else
		{
			$partner_id = gen_uuid();
			$sql = "INSERT INTO res_partner(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_code";
			$sql = $sql.", partner_name";
			$sql = $sql.", vat";
			$sql = $sql.", phone";
			$sql = $sql.", email";
			$sql = $sql.", address";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$partner_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_code."'";
			$sql = $sql.", '".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", '".str_replace("'", "''",$vat)."'";
			$sql = $sql.", '".str_replace("'", "''", $phone)."'";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '".str_replace("'", "''", $address)."'";
			$sql = $sql.")";
			
			$result = pg_exec($db, $sql);
			
		}
		if($employee_id == "")
		{
			$employee_id = gen_uuid();
			$sql = "INSERT INTO hr_employee(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_id";

			$sql = $sql." )VALUES(";
			$sql = $sql."'".$employee_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_id."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		
		echo $employee_id;
		
	}else if($ac == 'delEmployee')
	{
		$employee_id = '';
		if(isset($_REQUEST['id']))
		{
			$employee_id = $_REQUEST['id'];
		}
		$items = explode(",", $employee_id);
		for($i =0; $i<count($items); $i++)
		{
			$employee_id = $items[$i];
			$sql = "SELECT d1.partner_id FROM hr_employee d1 WHERE d1.id='".$employee_id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$partner_id = $row["partner_id"];
				$sql = "UPDATE res_partner SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$partner_id."'";
				$result = pg_exec($db, $sql);
				
				$sql = "UPDATE hr_employee SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$employee_id."'";
				$result = pg_exec($db, $sql);
			}
		}
		
		echo 'OK';
	}else if($ac == "saveCustomer")
	{
		$rt = "OK";
		
		$partner_id = '';
		if(isset($_REQUEST['partner_id']))
		{
			$partner_id = $_REQUEST['partner_id'];
		}
		$customer_id = '';
		if(isset($_REQUEST['customer_id']))
		{
			$customer_id = $_REQUEST['customer_id'];
		}
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		
		$partner_name = '';
		if(isset($_REQUEST['partner_name']))
		{
			$partner_name = $_REQUEST['partner_name'];
		}
		$partner_code = '';
		if(isset($_REQUEST['partner_code']))
		{
			$partner_code = $_REQUEST['partner_code'];
		}
		
		$vat = '';
		if(isset($_REQUEST['vat']))
		{
			$vat = $_REQUEST['vat'];
		}
		$phone = '';
		if(isset($_REQUEST['phone']))
		{
			$phone = $_REQUEST['phone'];
		}
		$email = '';
		if(isset($_REQUEST['email']))
		{
			$email = $_REQUEST['email'];
		}
		$address = '';
		if(isset($_REQUEST['address']))
		{
			$address = $_REQUEST['address'];
		}
		
		
		if($partner_id != '')
		{
			$sql = "UPDATE res_partner SET partner_name='".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", vat='".str_replace("'", "''", $vat)."'";
			$sql = $sql.", partner_code='".str_replace("'", "''", $partner_code)."'";
			$sql = $sql.", phone='".str_replace("'", "''", $phone)."'";
			$sql = $sql.", email='".str_replace("'", "''", $email)."'";
			$sql = $sql.", address='".str_replace("'", "''", $address)."'";
		
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$partner_id."'";
			$result = pg_exec($db, $sql);
		}else
		{
			$partner_id = gen_uuid();
			$sql = "INSERT INTO res_partner(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_code";
			$sql = $sql.", partner_name";
			$sql = $sql.", vat";
			$sql = $sql.", phone";
			$sql = $sql.", email";
			$sql = $sql.", address";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$partner_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_code."'";
			$sql = $sql.", '".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", '".str_replace("'", "''",$vat)."'";
			$sql = $sql.", '".str_replace("'", "''", $phone)."'";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '".str_replace("'", "''", $address)."'";
			$sql = $sql.")";
			
			$result = pg_exec($db, $sql);
			
		}
		if($customer_id == "")
		{
			$customer_id = gen_uuid();
			$sql = "INSERT INTO customer(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_id";
			
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$customer_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_id."'";

			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		
		echo $rt;
		
	}
	else if($ac == 'delCustomer')
	{
		$employee_id = '';
		if(isset($_REQUEST['id']))
		{
			$employee_id = $_REQUEST['id'];
		}
		$items = explode(",", $employee_id);
		for($i =0; $i<count($items); $i++)
		{
			$employee_id = $items[$i];
			$sql = "SELECT d1.partner_id FROM customer d1 WHERE d1.id='".$employee_id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$partner_id = $row["partner_id"];
				$sql = "UPDATE res_partner SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$partner_id."'";
				$result = pg_exec($db, $sql);
				
				$sql = "UPDATE customer SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$employee_id."'";
				$result = pg_exec($db, $sql);
				
				
			}
		}
		
		echo 'OK';
	}else if($ac == "delUserCompany")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "SELECT d1.user_id FROM res_user_company d1 WHERE d1.id='".$id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$user_id = $row["user_id"];
				$sql = "UPDATE res_user SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$user_id."'";
				$result = pg_exec($db, $sql);
				
				$sql = "UPDATE res_user_company SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$id."'";
				$result = pg_exec($db, $sql);
				
			}
		}
		
		echo 'OK';
	}
	else if($ac == "delOrder")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "UPDATE sale_order SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
			
			$sql = "UPDATE sale_order_product SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE order_id='".$id."'";
			$result = pg_exec($db, $sql);
		}
		
		echo 'OK';
	}else if($ac == "delPlanning")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "UPDATE mrp_workorder_planning_employee SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE planning_id='".$id."'";
			$result = pg_exec($db, $sql);
			
			$sql = "UPDATE mrp_workorder_planning SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}
		
		echo 'OK';
	}
	else if($ac == "updateUser")
	{
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
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
		
		$old_pass = '';
		if(isset($_REQUEST['old_pass']))
		{
			$old_pass = $_REQUEST['old_pass'];
		}
		
		$sql = "SELECT d1.id, d1.password, d.supplier_id, d.customer_id, d.employee_id, d1.user_name, d3.partner_name FROM res_user_company d LEFT OUTER JOIN res_user d1 ON(d.user_id = d1.id) LEFT OUTER JOIN customer d2 ON(d.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE (d1.user_name='".$user."' OR d1.email='".$user."') AND d.status =0 AND d.inactive =0 AND d1.status =0 AND d1.id !='".$user_id."'";
		
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			echo 'INVALID_USER';
		}else
		{
			$sql = "SELECT d1.id, d1.password, d.supplier_id, d.customer_id, d.employee_id, d1.user_name, d3.partner_name FROM res_user_company d LEFT OUTER JOIN res_user d1 ON(d.user_id = d1.id) LEFT OUTER JOIN customer d2 ON(d.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d.status =0 AND d.inactive =0 AND d1.status =0 AND d1.id ='".$user_id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$user_id = $row["id"];
				
				$s = hash("sha256", "[".$user_id."]".$old_pass);
				$len = strlen($old_pass);
				for($i = 0; $i<$len; $i++)
				{
					$s = $s.chr($i + 48);
				}
				$old_pass = hash("md5", $s);
				
				if($old_pass == $row["password"])
				{
					$s = hash("sha256", "[".$user_id."]".$pass);
					$len = strlen($pass);
					for($i = 0; $i<$len; $i++)
					{
						$s = $s.chr($i + 48);
					}
					$pass = hash("md5", $s);
					$sql = "UPDATE res_user SET user_name ='".str_replace("'", "''", $user)."', password='".str_replace("'", "''", $pass)."', write_date=NOW() WHERE id='".$user_id."'";
				
					$rs = pg_exec($db, $sql);
					$_SESSION["user_id"] = "";
					$_SESSION["supplier_id"] = "";
					$_SESSION["customer_id"] = "";
					$_SESSION["employee_id"] = "";
					$_SESSION["user_name"] = "";
					$_SESSION["customer_name"] = "";
					
					echo 'OK';
				}else{
					echo 'INVALID_PASSWORD';
				}
				
			}else{
				echo 'INVALID_USER';
			}
		}
	}else if($ac == "parentCustomer")
	{
		$parent_id = '';
		if(isset($_REQUEST['parent_id']))
		{
			$parent_id = $_REQUEST['parent_id'];
		}
		$customer_id = '';
		if(isset($_REQUEST['customer_id']))
		{
			$customer_id = $_REQUEST['customer_id'];
		}
		
		$sql = "UPDATE customer SET write_date=NOW(), parent_id='".$parent_id."' WHERE status =0 AND id ='".$customer_id."'";
		$rs = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "saveChanged")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$t = '';
		if(isset($_REQUEST['t']))
		{
			$t = $_REQUEST['t'];
		}
		$c = '';
		if(isset($_REQUEST['c']))
		{
			$c = $_REQUEST['c'];
		}
		$v = '';
		if(isset($_REQUEST['v']))
		{
			$v = $_REQUEST['v'];
		}
		$sql = "UPDATE ".$t." SET ".$c." ='".str_replace("'", "''", $v)."', write_date=NOW() WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		
		echo "OK";
	}
	else if($ac == "saveOrderProduct")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$value = '';
		if(isset($_REQUEST['value']))
		{
			$value = $_REQUEST['value'];
		}
		if($name == "name")
		{
				$sql = "SELECT d1.id FROM product d1 WHERE d1.name='".str_replace("'", "''", $value). "'";
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);
				
				if($numrows>0)
				{
					$row = pg_fetch_array($result, 0);
					$value = $row["id"];
					$name = "product_id";
				}else{
					$name = "product_id";
					$product_id = gen_uuid();
					$sql = "INSERT INTO product(";
					$sql = $sql."id";
					$sql = $sql.", create_date";
					$sql = $sql.", company_id";
					$sql = $sql.", status";
					$sql = $sql.", name";
					$sql = $sql.", type";
					$sql = $sql." )VALUES(";
					$sql = $sql."'".$product_id."'";
					$sql = $sql.", NOW()";
					$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
					$sql = $sql.", 0";
					$sql = $sql.", '".str_replace("'", "''", $value)."'";
					$sql = $sql.", 'PRODUCT'";
					$sql = $sql.")";
					$result = pg_exec($db, $sql);
					$value = $product_id;
					
				}
		}
		if($name == "unit_name")
		{
				$sql = "SELECT d1.id FROM product_unit d1 WHERE d1.name='".str_replace("'", "''", $value). "'";
				$result = pg_exec($db, $sql);
				$numrows = pg_numrows($result);
				
				if($numrows>0)
				{
					$row = pg_fetch_array($result, 0);
					$value = $row["id"];
					$name = "unit_id";
				}else{
					$name = "unit_id";
					$unit_id = gen_uuid();
					$sql = "INSERT INTO product_unit(";
					$sql = $sql."id";
					$sql = $sql.", create_date";
					$sql = $sql.", company_id";
					$sql = $sql.", status";
					$sql = $sql.", name";
					$sql = $sql." )VALUES(";
					$sql = $sql."'".$unit_id."'";
					$sql = $sql.", NOW()";
					$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
					$sql = $sql.", 0";
					$sql = $sql.", '".str_replace("'", "''", $value)."'";
					$sql = $sql.")";
					$result = pg_exec($db, $sql);
					$value = $unit_id;
					
				}
		}
		
		$sql = "UPDATE sale_order_product SET ".$name." ='".str_replace("'", "''", $value). "'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		
		$result = pg_exec($db, $sql);
		if($name == "quantity")
		{
			$sql = "UPDATE mrp_workorder SET ".$name." ='".str_replace("'", "''", $value). "'";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE order_product_id='".$id."'";
			$result = pg_exec($db, $sql);
		}
	
		echo 'OK';
	}else if($ac == "addOrderProduct")
	{
		$order_id = '';
		if(isset($_REQUEST['order_id']))
		{
			$order_id = $_REQUEST['order_id'];
		}
		$product_id = '';
		if(isset($_REQUEST['product_id']))
		{
			$product_id = $_REQUEST['product_id'];
		}
		$order_product_id = gen_uuid();
		$sql = "INSERT INTO sale_order_product(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", quantity";
		$sql = $sql.", order_id";
		$sql = $sql.", product_id";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$order_product_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
		$sql = $sql.", 0";
		$sql = $sql.", 1";
		$sql = $sql.", '".$order_id."'";
		$sql = $sql.", '".$product_id."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		
		$production_id = gen_uuid();
		
		
		echo 'OK';
	}
	else if($ac == "delOrderProduct")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$sql = "UPDATE sale_order_product SET status =1";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		echo 'OK';
	}else if($ac == "addRouting")
	{
		$order_product_id = '';
		if(isset($_REQUEST['order_product_id']))
		{
			$order_product_id = $_REQUEST['order_product_id'];
		}
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$production_id = "";
		$sql = "SELECT d1.id, d1.production_id FROM mrp_workorder d1 WHERE d1.order_product_id='".$order_product_id. "'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$production_id = $row["production_id"];
			$workorder_id = $row["id"];
			$category_id = "";
			$sql = "SELECT d1.id FROM mrp_routing_category d1 WHERE d1.category_name='".str_replace("'", "''", $name). "'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$category_id = $row["id"];
			}else{
				$category_id = gen_uuid();
				$sql = "INSERT INTO mrp_routing_category(";
				$sql = $sql."id";
				$sql = $sql.", create_date";
				$sql = $sql.", company_id";
				$sql = $sql.", status";
				$sql = $sql.", category_name";
				$sql = $sql." )VALUES(";
				$sql = $sql."'".$category_id."'";
				$sql = $sql.", NOW()";
				$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
				$sql = $sql.", 0";
				$sql = $sql.", '".$name."'";
				$sql = $sql.")";
				$result = pg_exec($db, $sql);
			}
			$routing_id= gen_uuid();
			$sql = "INSERT INTO mrp_routing(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", category_id";
			$sql = $sql.", production_id";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$routing_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$category_id."'";
			$sql = $sql.", '".$production_id."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			
			$workorder_routing_id= gen_uuid();
			$sql = "INSERT INTO mrp_workorder_routing(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", routing_id";
			$sql = $sql.", workorder_id";
			$sql = $sql.", proccess";
			$sql = $sql.", start_date";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$workorder_routing_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$routing_id."'";
			$sql = $sql.", '".$workorder_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", NOW()";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			
			echo "OK";
			
		}
	}else if($ac == "delRouting")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$sql = "UPDATE mrp_routing SET status =1";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		
		echo 'OK';
	}else if($ac == "doSaveOrder")
	{
		$order_id = '';
		if(isset($_REQUEST['order_id']))
		{
			$order_id = $_REQUEST['order_id'];
		}
		$receipt_no = "";
		if(isset($_REQUEST['receipt_no']))
		{
			$receipt_no = $_REQUEST['receipt_no'];
		}
		$receipt_date = "";
		if(isset($_REQUEST['receipt_date']))
		{
			$receipt_date = $_REQUEST['receipt_date'];
		}
		$delivery_date = "";
		if(isset($_REQUEST['delivery_date']))
		{
			$delivery_date = $_REQUEST['delivery_date'];
		}
		$company_id = "";
		if(isset($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		
		$sql = "SELECT d1.company_id, d2.partner_id FROM sale_order d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id = d2.id) WHERE d1.id='".str_replace("'", "''", $order_id). "'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		if($numrows == 0)
		{
			$order_id = gen_uuid();
			$sql = "INSERT INTO sale_order(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$order_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
			
		
		}
		if($receipt_no == "")
		{
			$sql = "SELECT d3.partner_code FROM sale_order d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.id='".str_replace("'", "''", $order_id). "'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows > 0)
			{
				$row = pg_fetch_array($result, 0);
				$receipt_no = $row["partner_code"].".";
			}
			$receipt_no = $receipt_no.findReceiptNo($db, $LOGIN_COMPANY_ID."sale_order");
		}
		$sql = "UPDATE sale_order SET company_id ='".$company_id. "'";
		if($receipt_date == "")
		{
			$sql = $sql.", receipt_date=NOW()";
		}else{
			$sql = $sql.", receipt_date='".$receipt_date."'";
		}
		if($delivery_date == "")
		{
			$sql = $sql.", delivery_date= NULL";
		}else{
			$sql = $sql.", delivery_date='".$delivery_date."'";
		}
		$sql = $sql.", receipt_no='".str_replace("'", "''", $receipt_no)."'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$order_id."'";
		$result = pg_exec($db, $sql);
		
		echo $order_id;
	}else if($ac == "addPlanning")
	{
		$order_product_id = '';
		if(isset($_REQUEST['order_product_id']))
		{
			$order_product_id = $_REQUEST['order_product_id'];
		}
		$receipt_no = '';
		if(isset($_REQUEST['receipt_no']))
		{
			$receipt_no = $_REQUEST['receipt_no'];
		}
		
		$sql = "SELECT d1.id, d1.quantity, (SELECT SUM(quantity) FROM mrp_workorder_planning WHERE status =0 AND workorder_id=d1.id) AS planning_qty  FROM mrp_workorder d1 WHERE d1.order_product_id='".str_replace("'", "''", $order_product_id). "'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		$workorder_id = "";
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$workorder_id = $row["id"];
			$quantity = $row["quantity"];
			$planning_qty = $row["planning_qty"];
			if($planning_qty != "")
			{
				$quantity  = $quantity - $planning_qty;
			}
			if($receipt_no == "")
			{
				$receipt_no = findReceiptNo($db, $LOGIN_COMPANY_ID."mrp_workorder_planning");
			}
			if($quantity == "")
			{
				$quantity = "1";
			}
			$planning_id = gen_uuid();
			$sql = "INSERT INTO mrp_workorder_planning(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", workorder_id";
			$sql = $sql.", receipt_no";
			$sql = $sql.", receipt_date";
			$sql = $sql.", start_date";
			$sql = $sql.", end_date";
			$sql = $sql.", quantity";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$planning_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$workorder_id."'";
			$sql = $sql.", '".$receipt_no."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", ".$quantity;
			$sql = $sql.")";
		
			$result = pg_exec($db, $sql);
			echo $planning_id;
			
		}
	}else if($ac == "doSavePlanning")
	{
		$planning_id = '';
		if(isset($_REQUEST['planning_id']))
		{
			$planning_id = $_REQUEST['planning_id'];
		}
		$receipt_no = "";
		if(isset($_REQUEST['receipt_no']))
		{
			$receipt_no = $_REQUEST['receipt_no'];
		}
		$start_date = "";
		if(isset($_REQUEST['start_date']))
		{
			$start_date = $_REQUEST['start_date'];
		}
		$end_date = "";
		if(isset($_REQUEST['end_date']))
		{
			$end_date = $_REQUEST['end_date'];
		}
		$quantity = "";
		if(isset($_REQUEST['quantity']))
		{
			$quantity = $_REQUEST['quantity'];
		}
		$sql = "UPDATE mrp_workorder_planning SET quantity ='".$quantity. "'";
		if($start_date == "")
		{
			$sql = $sql.", start_date=NOW()";
		}else{
			$sql = $sql.", start_date='".$start_date."'";
		}
		if($end_date == "")
		{
			$sql = $sql.", end_date=NOW()";
		}else{
			$sql = $sql.", end_date='".$end_date."'";
		}
		$sql = $sql.", receipt_no='".str_replace("'", "''", $receipt_no)."'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$planning_id."'";
		$result = pg_exec($db, $sql);
		
		echo "OK";
		
	}else if($ac == "addPlanningEmployee")
	{
		$planning_id = '';
		if(isset($_REQUEST['planning_id']))
		{
			$planning_id = $_REQUEST['planning_id'];
		}
		$routing_id = '';
		if(isset($_REQUEST['routing_id']))
		{
			$routing_id = $_REQUEST['routing_id'];
		}
		$employee_id = '';
		if(isset($_REQUEST['employee_id']))
		{
			$employee_id = $_REQUEST['employee_id'];
		}
		$quantity = 1;
		if(isset($_REQUEST['quantity']))
		{
			$quantity = $_REQUEST['quantity'];
		}
		$sql = "SELECT SUM(d1.quantity) AS qty FROM mrp_workorder_planning_employee d1 WHERE d1.status =0 AND d1.routing_id='".$routing_id. "' AND d1.planning_id='".$planning_id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		$qty = 0;
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$qty = $row["qty"];
		}
		$quantity = $quantity - $qty;
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
		if(WS_HOST != "")
		{
			$sql = "SELECT d1.rel_id FROM hr_employee d1 WHERE d1.status=0 AND d1.id='".$employee_id."'";
			
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$user_id = $row["rel_id"];
				if($user_id != "")
				{
					sendWS(WS_HOST, WS_PORT, '{"action": "send", "name": "'.$user_id.'", "data": "{\"action\": \"notification\", \"type\": \"text\", \"data\": \"Planning_Added\"}"}');
				}
				
				
			}
		}
		
		echo "OK";
	}else if($ac == "delRoutingEmployee")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$sql = "UPDATE mrp_workorder_planning_employee SET status =1";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		if(WS_HOST != "")
		{
				
			$sql = "SELECT d1.rel_id FROM hr_employee d1 LEFT OUTER JOIN mrp_workorder_planning_employee d2 ON(d1.id =d2.employee_id) WHERE d1.status=0 AND d2.id='".$id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$user_id = $row["rel_id"];
				sendWS(WS_HOST, WS_PORT, '{"action": "send", "name": "'.$user_id.'", "data": "{\"action\": \"notification\", \"type\": \"text\", \"data\": \"Planning_Remove\"}"}');
			}
		}
		
		echo 'OK';
	}else if($ac == 'delDepartment')
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "UPDATE hr_department SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}
		echo 'OK';
	}else if($ac == "addDepartment")
	{
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$parent_id = '';
		if(isset($_REQUEST['parent_id']))
		{
			$parent_id = $_REQUEST['parent_id'];
		}
		
		$department_id = gen_uuid();
		$sql = "INSERT INTO hr_department(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", name";
		$sql = $sql.", parent_id";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$department_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$name."'";
		$sql = $sql.", '".$parent_id."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "editDepartment")
	{
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		
		$department_id = gen_uuid();
		$sql = "UPDATE hr_department SET write_date=NOW(), name='".str_replace("'", "''", $name)."' WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "delDepartmentEmployee")
	{
	
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
	
		$sql = "UPDATE hr_department_employee SET write_date=NOW(), status=1 WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "addDepartmentEmployee")
	{
	
		$employee_id = '';
		if(isset($_REQUEST['employee_id']))
		{
			$employee_id = $_REQUEST['employee_id'];
		}
		$department_id = '';
		if(isset($_REQUEST['department_id']))
		{
			$department_id = $_REQUEST['department_id'];
		}
		
		$department_emloyee_id = gen_uuid();
		$sql = "INSERT INTO hr_department_employee(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", employee_id";
		$sql = $sql.", department_id";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$department_emloyee_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$employee_id."'";
		$sql = $sql.", '".$department_id."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "saveProduction")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$sql = "SELECT d1.id FROM mrp_production d1 WHERE d1.id='".$id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			$sql = "UPDATE mrp_production SET write_date=NOW(), name='".str_replace("'", "''", $name)."' WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}else{
			$sql = "INSERT INTO mrp_production(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", name";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".str_replace("'", "''", $name)."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		echo "OK";
	}else if($ac == "delProduction")
	{
	
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
	
		$sql = "UPDATE mrp_production SET write_date=NOW(), status=1 WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		$sql = "UPDATE mrp_routing SET write_date=NOW(), status=1 WHERE production_id='".$id."'";
		$result = pg_exec($db, $sql);
		echo "OK";
	}
	else if($ac == "addProductEmployee")
	{
	
		$employee_id = '';
		if(isset($_REQUEST['employee_id']))
		{
			$employee_id = $_REQUEST['employee_id'];
		}
		$rel_id = '';
		if(isset($_REQUEST['rel_id']))
		{
			$rel_id = $_REQUEST['rel_id'];
		}
		
		$department_emloyee_id = gen_uuid();
		$sql = "INSERT INTO hr_employee_rel(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", employee_id";
		$sql = $sql.", rel_id";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$department_emloyee_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$employee_id."'";
		$sql = $sql.", '".$rel_id."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "saveUser")
	{
		
		
		$user_company_id = '';
		if(isset($_REQUEST['user_company_id']))
		{
			$user_company_id = $_REQUEST['user_company_id'];
		}
		$company_id = '';
		if(isset($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
		$user_name = '';
		if(isset($_REQUEST['user_name']))
		{
			$user_name = $_REQUEST['user_name'];
		}
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		
		$email = '';
		if(isset($_REQUEST['email']))
		{
			$email = $_REQUEST['email'];
		}
		$user_id = '';
		if(isset($_REQUEST['user_id']))
		{
			$user_id = $_REQUEST['user_id'];
		}
		$password = '';
		if(isset($_REQUEST['password']))
		{
			$password = $_REQUEST['password'];
		}
		
		
			
		$rt = "";
		
		$sql = "SELECT id FROM res_user WHERE user_name='".str_replace("'", "''", $user_name)."' AND id!='".$user_id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		if($numrows>0)
		{
			$rt = "INVALID_USER";
		}
		if($rt == "")
		{
			if($user_id != '')
			{
				$s = hash("sha256", "[".$user_id."]".$password);
				$len = strlen($password);
				for($i = 0; $i<$len; $i++)
				{
					$s = $s.chr($i + 48);
				}
				$password = hash("md5", $s);
		
				$sql = "UPDATE res_user SET user_name='".str_replace("'", "''", $user_name)."'";
				$sql = $sql.", name='".str_replace("'", "''", $name)."'";
				$sql = $sql.", email='".str_replace("'", "''", $email)."'";
				$sql = $sql.", password='".str_replace("'", "''", $password)."'";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$user_id."'";
				$result = pg_exec($db, $sql);
			}else
			{
				
				$user_id = gen_uuid();
				$s = hash("sha256", "[".$user_id."]".$password);
				$len = strlen($password);
				for($i = 0; $i<$len; $i++)
				{
					$s = $s.chr($i + 48);
				}
				$password = hash("md5", $s);
		
				$sql = "INSERT INTO res_user(";
				$sql = $sql."id";
				$sql = $sql.", create_date";
				$sql = $sql.", write_date";
				$sql = $sql.", company_id";
				$sql = $sql.", status";
				$sql = $sql.", user_name";
				$sql = $sql.", name";
				$sql = $sql.", email";
				$sql = $sql.", password";
				$sql = $sql." )VALUES(";
				$sql = $sql."'".$user_id."'";
				$sql = $sql.", NOW()";
				$sql = $sql.", NOW()";
				$sql = $sql.", '".$company_id."'";
				$sql = $sql.", 0";
				$sql = $sql.", '".str_replace("'", "''", $user_name)."'";
				$sql = $sql.", '".str_replace("'", "''", $name)."'";
				$sql = $sql.", '".str_replace("'", "''", $email)."'";
				$sql = $sql.", '".str_replace("'", "''", $password)."'";
				$sql = $sql.")";
				
				$result = pg_exec($db, $sql);
				
			}
			if($user_company_id == "")
			{
				$user_company_id = gen_uuid();
				
				$sql = "INSERT INTO res_user_company(";
				$sql = $sql."id";
				$sql = $sql.", create_date";
				$sql = $sql.", write_date";
				$sql = $sql.", company_id";
				$sql = $sql.", status";
				$sql = $sql.", user_id";
				$sql = $sql.", inactive";
				$sql = $sql." )VALUES(";
				$sql = $sql."'".$user_company_id."'";
				$sql = $sql.", NOW()";
				$sql = $sql.", NOW()";
				$sql = $sql.", '".$company_id."'";
				$sql = $sql.", 0";
				$sql = $sql.", '".$user_id."'";
				$sql = $sql.", 0";
				$sql = $sql.")";
				$result = pg_exec($db, $sql);
			}
			$rt = "OK";
		}
		
		
		echo $rt;
		
	}else if($ac == "saveProduct")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$code = '';
		if(isset($_REQUEST['code']))
		{
			$code = $_REQUEST['code'];
		}
		$sql = "SELECT d1.id FROM product d1 WHERE d1.id='".$id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			$sql = "UPDATE product SET write_date=NOW(), name='".str_replace("'", "''", $name)."', code='".str_replace("'", "''", $code)."' WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}else{
			$sql = "INSERT INTO product(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", name";
			$sql = $sql.", code";
			$sql = $sql.", type";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".str_replace("'", "''", $name)."'";
			$sql = $sql.", '".str_replace("'", "''", $code)."'";
			$sql = $sql.", 'PRODUCT'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		echo "OK";
	}else if($ac == "delProduct")
	{
	
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
	
		$sql = "UPDATE product SET write_date=NOW(), status=1 WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "saveMaterial")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$code = '';
		if(isset($_REQUEST['code']))
		{
			$code = $_REQUEST['code'];
		}
		$unit_id = '';
		if(isset($_REQUEST['unit_id']))
		{
			$unit_id = $_REQUEST['unit_id'];
		}
		$sql = "SELECT d1.id FROM product d1 WHERE d1.id='".$id."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		
		if($numrows>0)
		{
			$sql = "UPDATE product SET write_date=NOW(), name='".str_replace("'", "''", $name)."', code='".str_replace("'", "''", $code)."', unit_id='".str_replace("'", "''", $unit_id)."' WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}else{
			$sql = "INSERT INTO product(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", name";
			$sql = $sql.", code";
			$sql = $sql.", unit_id";
			$sql = $sql.", type";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".str_replace("'", "''", $name)."'";
			$sql = $sql.", '".str_replace("'", "''", $code)."'";
			$sql = $sql.", '".str_replace("'", "''", $unit_id)."'";
			$sql = $sql.", 'MATERIAL'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		echo "OK";
	}else if($ac == "delMaterial")
	{
	
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
	
		$sql = "UPDATE product SET write_date=NOW(), status=1 WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		echo "OK";
	}else if($ac == "saveSupplier")
	{
		$rt = "";
		
		$partner_id = '';
		if(isset($_REQUEST['partner_id']))
		{
			$partner_id = $_REQUEST['partner_id'];
		}
		$supplier_id = '';
		if(isset($_REQUEST['supplier_id']))
		{
			$supplier_id = $_REQUEST['supplier_id'];
		}
	
		$partner_name = '';
		if(isset($_REQUEST['partner_name']))
		{
			$partner_name = $_REQUEST['partner_name'];
		}
		$partner_code = '';
		if(isset($_REQUEST['partner_code']))
		{
			$partner_code = $_REQUEST['partner_code'];
		}
		$sql = "SELECT d1.id FROM supplier d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id =d2.id) WHERE d1.id !='".$supplier_id."' AND d2.partner_code='".$partner_code."' AND d1.status=0 AND d1.status=0 AND d1.company_id='".$LOGIN_COMPANY_ID."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		if($numrows>0)
		{
			echo "CODE_AVAIBLE";
			exit();
		}
		$vat = '';
		if(isset($_REQUEST['vat']))
		{
			$vat = $_REQUEST['vat'];
		}
		$phone = '';
		if(isset($_REQUEST['phone']))
		{
			$phone = $_REQUEST['phone'];
		}
		$email = '';
		if(isset($_REQUEST['email']))
		{
			$email = $_REQUEST['email'];
		}
		$bank_no = '';
		if(isset($_REQUEST['bank_no']))
		{
			$bank_no = $_REQUEST['bank_no'];
		}
		$bank_name = '';
		if(isset($_REQUEST['bank_name']))
		{
			$bank_name = $_REQUEST['bank_name'];
		}
		$address = '';
		if(isset($_REQUEST['address']))
		{
			$address = $_REQUEST['address'];
		}
		
		
		if($partner_id != '')
		{
			$sql = "UPDATE res_partner SET partner_name='".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", vat='".str_replace("'", "''", $vat)."'";
			$sql = $sql.", partner_code='".str_replace("'", "''", $partner_code)."'";
			$sql = $sql.", phone='".str_replace("'", "''", $phone)."'";
			$sql = $sql.", email='".str_replace("'", "''", $email)."'";
			$sql = $sql.", address='".str_replace("'", "''", $address)."'";
			$sql = $sql.", bank_no='".str_replace("'", "''", $bank_no)."'";
			$sql = $sql.", bank_name='".str_replace("'", "''", $bank_name)."'";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$partner_id."'";
			$result = pg_exec($db, $sql);
		}else
		{
			$partner_id = gen_uuid();
			$sql = "INSERT INTO res_partner(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_code";
			$sql = $sql.", partner_name";
			$sql = $sql.", vat";
			$sql = $sql.", phone";
			$sql = $sql.", email";
			$sql = $sql.", address";
			$sql = $sql.", bank_no";
			$sql = $sql.", bank_name";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$partner_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_code."'";
			$sql = $sql.", '".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", '".str_replace("'", "''",$vat)."'";
			$sql = $sql.", '".str_replace("'", "''", $phone)."'";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '".str_replace("'", "''", $address)."'";
			$sql = $sql.", '".str_replace("'", "''", $bank_no)."'";
			$sql = $sql.", '".str_replace("'", "''", $bank_name)."'";
			$sql = $sql.")";
			
			$result = pg_exec($db, $sql);
			
		}
		if($supplier_id == "")
		{
			$supplier_id = gen_uuid();
			$sql = "INSERT INTO supplier(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_id";
			
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$supplier_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_id."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		
		echo $supplier_id;
		
	}
	else if($ac == 'delSupplier')
	{
		$employee_id = '';
		if(isset($_REQUEST['id']))
		{
			$employee_id = $_REQUEST['id'];
		}
		$items = explode(",", $employee_id);
		for($i =0; $i<count($items); $i++)
		{
			$employee_id = $items[$i];
			$sql = "SELECT d1.partner_id FROM supplier d1 WHERE d1.id='".$employee_id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$partner_id = $row["partner_id"];
				$sql = "UPDATE res_partner SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$partner_id."'";
				$result = pg_exec($db, $sql);
				
				$sql = "UPDATE supplier SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$employee_id."'";
				$result = pg_exec($db, $sql);
			}
		}
		
		echo 'OK';
	}else if($ac == "saveProductType")
	{
		$rt = "OK";
		
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		
	
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$description = '';
		if(isset($_REQUEST['description']))
		{
			$description = $_REQUEST['description'];
		}
		
		$factor = '';
		if(isset($_REQUEST['factor']))
		{
			$factor = $_REQUEST['factor'];
		}
		
		if($id == "")
		{
			$id = gen_uuid();
			$sql = "INSERT INTO product_type(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		$sql = "UPDATE product_type SET name='".str_replace("'", "''", $name)."'";
		$sql = $sql.", description='".str_replace("'", "''", $description)."'";
		$sql = $sql.", factor='".str_replace("'", "''", $factor)."'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		echo $rt;
		
	}
	else if($ac == 'delProdutType')
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "UPDATE product_type SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}
		
		echo 'OK';
	}else if($ac == "saveCompany")
	{
		
		
		$partner_id = '';
		if(isset($_REQUEST['partner_id']))
		{
			$partner_id = $_REQUEST['partner_id'];
		}
		$company_id = '';
		if(isset($_REQUEST['company_id']))
		{
			$company_id = $_REQUEST['company_id'];
		}
	
		$partner_name = '';
		if(isset($_REQUEST['partner_name']))
		{
			$partner_name = $_REQUEST['partner_name'];
		}
		$partner_code = '';
		if(isset($_REQUEST['partner_code']))
		{
			$partner_code = $_REQUEST['partner_code'];
		}
		$sql = "SELECT d1.id FROM res_company d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id =d2.id) WHERE d1.id !='".$company_id."' AND d2.partner_code='".$partner_code."' AND d1.status=0 AND AND d1.company_id='".$LOGIN_COMPANY_ID."'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		if($numrows>0)
		{
			echo "CODE_AVAIBLE";
			exit();
		}
		$vat = '';
		if(isset($_REQUEST['vat']))
		{
			$vat = $_REQUEST['vat'];
		}
		$phone = '';
		if(isset($_REQUEST['phone']))
		{
			$phone = $_REQUEST['phone'];
		}
		$email = '';
		if(isset($_REQUEST['email']))
		{
			$email = $_REQUEST['email'];
		}
		$bank_no = '';
		if(isset($_REQUEST['bank_no']))
		{
			$bank_no = $_REQUEST['bank_no'];
		}
		$bank_name = '';
		if(isset($_REQUEST['bank_name']))
		{
			$bank_name = $_REQUEST['bank_name'];
		}
		$address = '';
		if(isset($_REQUEST['address']))
		{
			$address = $_REQUEST['address'];
		}
		
		
		if($partner_id != '')
		{
			$sql = "UPDATE res_partner SET partner_name='".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", vat='".str_replace("'", "''", $vat)."'";
			$sql = $sql.", partner_code='".str_replace("'", "''", $partner_code)."'";
			$sql = $sql.", phone='".str_replace("'", "''", $phone)."'";
			$sql = $sql.", email='".str_replace("'", "''", $email)."'";
			$sql = $sql.", address='".str_replace("'", "''", $address)."'";
			$sql = $sql.", bank_no='".str_replace("'", "''", $bank_no)."'";
			$sql = $sql.", bank_name='".str_replace("'", "''", $bank_name)."'";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$partner_id."'";
			$result = pg_exec($db, $sql);
		}else
		{
			$partner_id = gen_uuid();
			$sql = "INSERT INTO res_partner(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_code";
			$sql = $sql.", partner_name";
			$sql = $sql.", vat";
			$sql = $sql.", phone";
			$sql = $sql.", email";
			$sql = $sql.", address";
			$sql = $sql.", bank_no";
			$sql = $sql.", bank_name";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$partner_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_code."'";
			$sql = $sql.", '".str_replace("'", "''", $partner_name)."'";
			$sql = $sql.", '".str_replace("'", "''",$vat)."'";
			$sql = $sql.", '".str_replace("'", "''", $phone)."'";
			$sql = $sql.", '".str_replace("'", "''", $email)."'";
			$sql = $sql.", '".str_replace("'", "''", $address)."'";
			$sql = $sql.", '".str_replace("'", "''", $bank_no)."'";
			$sql = $sql.", '".str_replace("'", "''", $bank_name)."'";
			$sql = $sql.")";
			
			$result = pg_exec($db, $sql);
			
		}
		if($company_id == "")
		{
			$company_id = gen_uuid();
			$sql = "INSERT INTO res_company(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql.", partner_id";
			$sql = $sql.", parent_id";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$company_id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$company_id."'";
			$sql = $sql.", 0";
			$sql = $sql.", '".$partner_id."'";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		
		echo $company_id;
		
	}
	else if($ac == 'delCompany')
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "SELECT d1.partner_id FROM res_company d1 WHERE d1.id='".$id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$partner_id = $row["partner_id"];
				$sql = "UPDATE res_partner SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$partner_id."'";
				$result = pg_exec($db, $sql);
				
				$sql = "UPDATE res_company SET status =1";
				$sql = $sql.", write_date=NOW()";
				$sql = $sql." WHERE id='".$id."'";
				$result = pg_exec($db, $sql);
			}
		}
		
		echo 'OK';
	}else if($ac == "savePaymentType")
	{
		$rt = "OK";
		
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		
	
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		$description = '';
		if(isset($_REQUEST['description']))
		{
			$description = $_REQUEST['description'];
		}
		
		if($id == "")
		{
			$id = gen_uuid();
			$sql = "INSERT INTO res_payment_category(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		$sql = "UPDATE res_payment_category SET category_name='".str_replace("'", "''", $name)."'";
		$sql = $sql.", description='".str_replace("'", "''", $description)."'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		
		echo "OK";
		
	}
	else if($ac == 'delPaymentType')
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "UPDATE res_payment_category SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}
		
		echo 'OK';
	}
	else if($ac == "savePayment")
	{
		$rt = "OK";
		
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		
	
		$name = '';
		if(isset($_REQUEST['name']))
		{
			$name = $_REQUEST['name'];
		}
		
		if($id == "")
		{
			$id = gen_uuid();
			$sql = "INSERT INTO res_payment(";
			$sql = $sql."id";
			$sql = $sql.", create_date";
			$sql = $sql.", write_date";
			$sql = $sql.", company_id";
			$sql = $sql.", status";
			$sql = $sql." )VALUES(";
			$sql = $sql."'".$id."'";
			$sql = $sql.", NOW()";
			$sql = $sql.", NOW()";
			$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
			$sql = $sql.", 0";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		$sql = "UPDATE res_payment SET payment_name='".str_replace("'", "''", $name)."'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
		
		echo "OK";
		
	}
	else if($ac == 'delPayment')
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$items = explode(",", $id);
		for($i =0; $i<count($items); $i++)
		{
			$id = $items[$i];
			$sql = "UPDATE res_payment SET status =1";
			$sql = $sql.", write_date=NOW()";
			$sql = $sql." WHERE id='".$id."'";
			$result = pg_exec($db, $sql);
		}
		
		echo 'OK';
	}
	pg_close($db);
?>