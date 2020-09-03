<?php
session_start();
require_once('../config.php' );
require_once('../tool.php' );


$ac = 'view';
if(isset($_REQUEST['ac']))
{
	$ac = $_REQUEST['ac'];
}
?>
<?php
if($ac == "view")
{
?>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<th nowrap="nowrap" align="center" width="120">Tên</th>
		<th style="width:150px" nowrap="nowrap" align="center">Tên đăng nhập</th>
		<th style="width:150px" nowrap="nowrap" align="center">Mật khẩu</th>
		<th nowrap="nowrap" width="30"></th>
	  </tr>
	</thead>
	<?php
	$rel_id = '';
	if(isset($_REQUEST['rel_id']))
	{
		$rel_id = $_REQUEST['rel_id'];
	}
		
	$sql = "SELECT d1.id, d1.user_id, d2.name, d2.user_name, d2.password FROM res_user_company d1 LEFT OUTER JOIN res_user d2 ON(d1.user_id = d2.id) WHERE d1.status =0 AND d1.company_id='".$rel_id."' ORDER BY d1.create_date ASC";

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	

	?>
	<tbody>
		<?php
		
		for($i =0; $i<$numrows; $i++)
		{
			$row = pg_fetch_array($result, $i);
			
			$id = $row["id"];
			$name = $row["name"];
			$user_name = $row["user_name"];
			$user_id = $row["user_id"];
			$password = $row["password"];
		?>
		<tr>
			<td><?php echo $i + 1; ?></td>
			<td ><input type="text" onblur="saveUser('<?php echo $user_id; ?>', this, 'name');" value="<?php echo $name; ?>" style="width:100%" class="form-control" maxlength = "150"/></td>
			<td style="width:150px"><input type="text" onblur="saveUser('<?php echo $user_id; ?>', this, 'user_name');" value="<?php echo $user_name; ?>" class="form-control" maxlength = "50"/></td>
			<td style="width:150px"><input placeholder="<?php echo $password;?>" type="password" onblur="saveUser('<?php echo $user_id; ?>', this, 'password');"  class="form-control" maxlength = "250" /></td>
			<td width="30"><a href="javascript:delUser('<?php echo $id; ?>')">Xóa</a></td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>

<?php
}else if($ac == "addLine")
{
	$rel_id = '';
	if(isset($_REQUEST['rel_id']))
	{
		$rel_id = $_REQUEST['rel_id'];
	}
	$name = '';
	if(isset($_REQUEST['name']))
	{
		$name = $_REQUEST['name'];
	}
	
	$user_name = '';
	if(isset($_REQUEST['user_name']))
	{
		$user_name = $_REQUEST['user_name'];
	}
	
	$password = '';
	if(isset($_REQUEST['password']))
	{
		$password = $_REQUEST['password'];
	}
	
	$user_id = gen_uuid();
	$sql = "INSERT INTO res_user(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", user_name";
	$sql = $sql.", name";
	$sql = $sql.", password";
	$sql = $sql.", inactive";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$user_id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$rel_id."'";
	$sql = $sql.", 0";
	$sql = $sql.", ''";
	$sql = $sql.", ''";
	$sql = $sql.", ''";
	$sql = $sql.", 0";
	$sql = $sql.")";
	$result = pg_exec($db, $sql);
	
	
	$id = gen_uuid();
	$sql = "INSERT INTO res_user_company(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", user_id";
	$sql = $sql.", inactive";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$rel_id."'";
	$sql = $sql.", 0";
	$sql = $sql.", '".$user_id."'";
	$sql = $sql.", 0";
	$sql = $sql.")";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "delLine")
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
		}
		
		$sql = "UPDATE res_user_company SET status =1";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
	}
	echo "OK";
}else if($ac == "saveLine")
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
	if($name == "password")
	{
		
		$s = hash("sha256", "[".$id."]".$value);
		$len = strlen($value);
		for($i = 0; $i<$len; $i++)
		{
			$s = $s.chr($i + 48);
		}
		$value = hash("md5", $s);
		
	}
	$sql = "UPDATE res_user SET ".$name." ='".str_replace("'", "''", $value). "'";
	$sql = $sql.", write_date=NOW()";
	$sql = $sql." WHERE id='".$id."'";
	
	$result = pg_exec($db, $sql);
	
	
	echo "OK";
}
?>