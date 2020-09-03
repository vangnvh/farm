<?php
session_start();
require_once('../config.php' );
require_once('../tool.php' );

$LOGIN_COMPANY_ID = "";
if(isset($_SESSION["company_id"]))
{
	$LOGIN_COMPANY_ID = $_SESSION["company_id"];
}

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
		<th style="width:100px" nowrap="nowrap" align="center">Điện tích/ha</th>
		<th style="width:60px" nowrap="nowrap" align="center">Nhóm</th>
		<th style="width:250px" nowrap="nowrap" align="center">Ghi chú</th>
		
		<th nowrap="nowrap" width="30"></th>
	  </tr>
	</thead>
	<?php
	$rel_id = '';
	if(isset($_REQUEST['rel_id']))
	{
		$rel_id = $_REQUEST['rel_id'];
	}
		
	$sql = "SELECT d1.id, d1.name, d1.area, d1.lat, d1.lng, d1.polygon, d1.description, d1.group_name FROM mrp_location d1 WHERE d1.status =0 AND d1.rel_id='".$rel_id."' ORDER BY d1.create_date ASC";

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
			$area = $row["area"];
			$lat = $row["lat"];
			$lng = $row["lng"];
			$description = $row["description"];
			$group_name = $row["group_name"];
		?>
		<tr>
			<td><?php echo $i + 1; ?></td>
			<td><input type="text" onblur="saveLocation('<?php echo $id; ?>', this, 'name');" value="<?php echo $name; ?>" style="width:100%" class="form-control" maxlength = "150"/></td>
			<td style="width:100px"><input type="text" onblur="saveLocation('<?php echo $id; ?>', this, 'area');" value="<?php echo $area; ?>" class="form-control" maxlength = "50"/></td>
			<td><input type="text" onblur="saveLocation('<?php echo $id; ?>', this, 'group_name');" value="<?php echo $group_name; ?>" style="width:100%" class="form-control" maxlength = "150"/></td>
			<td width="30"><a href="javascript:delLocation('<?php echo $id; ?>')">Xóa</a></td>
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
	
	$id = gen_uuid();
	$sql = "INSERT INTO mrp_location(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", rel_id";
	$sql = $sql.", area";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
	$sql = $sql.", 0";
	$sql = $sql.", '".$rel_id."'";
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
		$sql = "UPDATE mrp_location SET status =1";
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
	
	}else if($name == "area")
	{
		if($value == "")
		{
			$value = "0";
		}
	}
	$sql = "UPDATE mrp_location SET ".$name." ='".str_replace("'", "''", $value). "'";
	$sql = $sql.", write_date=NOW()";
	$sql = $sql." WHERE id='".$id."'";
	$result = pg_exec($db, $sql);
	
	echo "OK";
}
?>