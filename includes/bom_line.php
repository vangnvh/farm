<?php

session_start();
require_once('../config.php' );
require_once('../tool.php' );

$LOGIN_COMPANY_ID = "";
if(isset($_SESSION["company_id"]))
{
	$LOGIN_COMPANY_ID = $_SESSION["company_id"];
}
$LOGIN_PARENT_COMPANY_ID = "";
if(isset($_SESSION["parent_company_id"]))
{
	$LOGIN_PARENT_COMPANY_ID = $_SESSION["parent_company_id"];
}
$lang = LANGUAGE;

$lang_id = '76';
if($lang == "ru")
{
	$lang_id ='52';
}
else if($lang == "en-US" || $lang == "en")
{
	$lang_id ='1';
}
else if($lang == "cn" )
{
	$lang_id ='8';
}else if($lang == "jp")
{
	$lang_id ='35';
}else if($lang == "kr")
{
	$lang_id ='39';
}else if($lang == "fr")
{
	$lang_id ='23';
}

include( ABSPATH .'includes/lang/'.$lang.'.php');

$ac = 'view';
if(isset($_REQUEST['ac']))
{
	$ac = $_REQUEST['ac'];
}


?>
<?php
if($ac == "view")
{
	$bom_id = '';
	if(isset($_REQUEST['bom_id']))
	{
		$bom_id = $_REQUEST['bom_id'];
	}
	$index = '';
	if(isset($_REQUEST['index']))
	{
		$index = $_REQUEST['index'];
	}
	$sql = "SELECT d1.id, d2.code, d2.name, d2.unit_id , d1.quantity FROM mrp_bom_line d1";
	$sql = $sql." LEFT OUTER JOIN product d2 ON(d1.product_id = d2.id)";
	$sql = $sql." WHERE d1.bom_id='".$bom_id."'";
	$sql = $sql." AND d1.status =0";
	$sql = $sql." ORDER BY d1.create_date ASC";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
?>
<a href="javascript:addMaterial('<?php echo $bom_id; ?>', '<?php echo $index; ?>')">+ Thêm vật tư</a>
<br>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th nowrap="nowrap" align="center" width="60">Mã</th>
		<th nowrap="nowrap" align="center">Tên</th>
		<th nowrap="nowrap" align="center"  width="60">ĐVT</th>
		<th  nowrap="nowrap" align="center">Số lượng</th>
		<th nowrap="nowrap" align="center"  width="30"></th>
	  </tr>
	</thead>
	<tbody>
	<?php
	for($j =0; $j<$numrows; $j++)
	{
		$row = pg_fetch_array($result, $j);
		
		$id = $row["id"];
		$code = $row["code"];
		$name = $row["name"];
		$unit_id = $row["unit_id"];
		$quantity = $row["quantity"];
	
	?>
		<tr>
			<td><?php echo $code;?></td>
			<td><?php echo $name;?></td>
			<td><?php echo $unit_id;?></td>
			<td width="150"><input type="text" onblur="saveMaterial('<?php echo $id; ?>', this, 'quantity');" value="<?php echo $quantity; ?>" class="form-control" maxlength = "50" style="width:100%; text-align:right" /></td>
			<td width="30"><a class="button" href="javascript:removeMaterial('<?php echo $id; ?>', '<?php echo $bom_id; ?>', '<?php echo $index; ?>')">-</a></td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>

<?php
}else if($ac == "materialList")
{
	$func = '';
	if(isset($_REQUEST['func']))
	{
		$func = $_REQUEST['func'];
	}
	$search = '';
	if(isset($_REQUEST['search']))
	{
		$search = $_REQUEST['search'];
	}

	$sql = "SELECT d1.id, d1.code, d1.name FROM product d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id= d2.id) WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d1.company_id ='".$LOGIN_PARENT_COMPANY_ID."') AND d1.type='MATERIAL'";
	if($search != "")
	{
		$search = str_replace("'", "''", $search);
		$sql = $sql." AND (d1.name ILIKE '%".$search."%' OR d1.code ILIKE '%".$search."%')";
	}
	$sql = $sql." ORDER BY d1.name ASC";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
?>
<div class="row">
	
	
	<div class="col-sm-6">
	</div>
	<div class="col-sm-6">
		<div class="input-group">
			<input type="text" id="editsearchProduct" class="form-control" value="<?php echo $search; ?>" placeholder="<?php echo __('Search');?>" onKeyDown="if(event.keyCode == 13){doSearchProduct();}">
			<div class="input-group-prepend">
				<a href="javascript:doSearchProduct()" class="input-group-text"><?php echo __('Search');?></a>
			</div>
		</div>
	</div>
</div>
<br>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<?php if($func != ""){?>
		<th nowrap="nowrap"></th>
		<?php } ?>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Code');?></th>
		<th  nowrap="nowrap" align="center" ><?php echo __('Name');?></th>
	  </tr>
	</thead>
	<tbody>
		<?php
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$id = $row["id"];
			$code = $row["code"];
			$name = $row["name"];

		?>
		<tr>
			<td><?php echo $j + 1; ?></td>
			<?php if($func != ""){?>
			<td  style="text-align:center; width:20px"><a class="button" href="javascript:<?php echo $func; ?>('<?php echo $id; ?>')">+</a></td>
			<?php } ?>
			<td><?php echo $code;?></td>
			<td><?php echo $name;?></td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>
<script>
	function doSearchProduct()
	{
		var _url = '<?php echo URL;?>includes/bom_line.php?ac=materialList&func=<?php echo $func;?>';
		var search = document.getElementById('editsearchProduct').value;
		_url = _url + "&search=" + encodeURIComponent(search);
		loadPage('pnFullDialogContent', _url, function(status, message)
		{
			
			
		}, false);
	}
</script>

<?php
}else if($ac == "addMaterial")
{
	
	$product_id = '';
	if(isset($_REQUEST['product_id']))
	{
		$product_id = $_REQUEST['product_id'];
	}
	$bom_id = '';
	if(isset($_REQUEST['bom_id']))
	{
		$bom_id = $_REQUEST['bom_id'];
	}
	
	$id = gen_uuid();
	$sql = "INSERT INTO mrp_bom_line(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", write_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", product_id";
	$sql = $sql.", bom_id";
	$sql = $sql.", quantity";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
	$sql = $sql.", 0";
	$sql = $sql.", '".$product_id."'";
	$sql = $sql.", '".$bom_id."'";
	$sql = $sql.", 1";
	$sql = $sql.")";
	$result = pg_exec($db, $sql);
	echo "OK";
	
}else if($ac == "delMaterial")
{
	$id = '';
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}	
	$sql = "UPDATE mrp_bom_line SET status =1, write_date=NOW() WHERE id ='".$id."'";
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
	$value = '';
	if(isset($_REQUEST['value']))
	{
		$value = $_REQUEST['value'];
	}
	if($name == "quantity")
	{
		if($value == "")
		{
			$value = "0";
		}
		
	}
	$sql = "UPDATE mrp_bom_line SET ".$name." ='".str_replace("'", "''", $value). "'";
	$sql = $sql.", write_date=NOW()";
	$sql = $sql." WHERE id='".$id."'";
	$result = pg_exec($db, $sql);
	
}
?>