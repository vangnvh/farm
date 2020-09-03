<?php
session_start();
require_once('../config.php' );
require_once('../tool.php' );

$LOGIN_COMPANY_ID = "";
if(isset($_SESSION["company_id"]))
{
	$LOGIN_COMPANY_ID = $_SESSION["company_id"];
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
?>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<th nowrap="nowrap" align="center" width="120">Tên</th>
		<th style="width:100px" nowrap="nowrap" align="center">Ngày kể từ khi trồng</th>
		<th style="width:250px" nowrap="nowrap" align="center">Hướng dẫn</th>
		<th nowrap="nowrap" width="30"></th>
	  </tr>
	</thead>
	<?php
	$production_id = '';
	if(isset($_REQUEST['production_id']))
	{
		$production_id = $_REQUEST['production_id'];
	}
		
	$sql = "SELECT d1.id, d2.category_name, d1.days, d1.description FROM mrp_routing d1 LEFT OUTER JOIN mrp_routing_category d2 ON(d1.category_id = d2.id) WHERE d1.status =0 AND d1.production_id='".$production_id."' ORDER BY d1.create_date ASC";

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	

	?>
	<tbody>
		<?php
		
			
		for($i =0; $i<$numrows; $i++)
		{
			$row = pg_fetch_array($result, $i);
			
			$id = $row["id"];
			$category_name = $row["category_name"];
			$description = $row["description"];
			$days = $row["days"];
		?>
		<tr>
			<td rowspan="2"><?php echo $i + 1; ?></td>
			<td><div  class="autocomplete" style="width:100%" style=""><input type="text" onblur="saveRouting('<?php echo $id; ?>', this, 'category_name');" value="<?php echo $category_name; ?>" style="width:100%" class="form-control" maxlength = "150" id="editcategory_name<?php echo $i;?>"/></div></td>
			<td style="width:100px"><input type="text" onblur="saveRouting('<?php echo $id; ?>', this, 'days');" value="<?php echo $days; ?>" class="form-control" maxlength = "50" id="editdays<?php echo $i;?>" /></td>
			<td style="width:250px"><input type="text" onblur="saveRouting('<?php echo $id; ?>', this, 'description');" value="<?php echo $description; ?>" class="form-control" maxlength = "250" /></td>
			<td width="30"><a href="javascript:delRouting('<?php echo $id; ?>')">Xóa</a></td>
			
		</tr>
		<tr>
			<td colspan="4" id="pnMaterial<?php echo $i;?>"></td>
		</tr>
		<script>
			loadPage('pnMaterial<?php echo $i;?>', '<?php echo URL;?>includes/bom_line.php?bom_id=<?php echo $id;?>&index=<?php echo $i;?>', function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
			
		</script>
		<?php
		}
		?>
	</tbody>
</table>
</div>
<script>
	<?php
		$sql = "SELECT d1.id, d1.category_name FROM mrp_routing_category d1 WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."' ORDER BY d1.category_name ASC";
		
		$result1 = pg_exec($db, $sql);
		$numrows1 = pg_numrows($result1);	
			
		?>
		var categories = [
		<?php
		for($j =0; $j<$numrows1; $j++)
		{
			$row = pg_fetch_array($result1, $j);
			$name = $row["category_name"];
			if($j>0)
			{
				echo ",";
			}
			echo "'".$name."'";
		} 
		?>
		];
		<?php
for($i =0; $i<$numrows; $i++)
{
?>
autocomplete(document.getElementById("editcategory_name<?php echo $i;?>"), categories);
<?php
}
?>
var last_index;
var last_bom_id;
function addMaterial(bom_id, index)
{
	last_bom_id = bom_id;
	last_index = index;
	var _url = '<?php echo URL;?>includes/bom_line.php?ac=materialList&func=doAddMaterial';
	openPopup(_url);
}
function doAddMaterial(id)
{
	closePopup();
	var _url = '<?php echo URL;?>includes/bom_line.php?ac=addMaterial';
	_url = _url + '&bom_id=' + last_bom_id + '&product_id=' + id;
	loadPage('pnMaterial' + last_index, _url, function(status, message)
	{
		if(status== 0)
		{
			if(message == "OK")
			{
				loadPage('pnMaterial' + last_index, '<?php echo URL;?>includes/bom_line.php?bom_id=' + last_bom_id + '&index=' + last_index, function(status, message)
				{
					if(status== 0)
					{
					
					}
					
				}, false);
				
			}
			else{
				alert(message);
			}
		}
		
	}, true);
}
function removeMaterial(id, bom_id, index)
{
	var result = confirm("<?php echo __('Want to delete?');?>");
	if (!result) {
		return;
	}
	var _url = '<?php echo URL;?>includes/bom_line.php?ac=delMaterial';
	_url = _url + '&id=' + id;
	loadPage('gotoTop', _url, function(status, message)
	{
		if(status== 0)
		{
			if(message == "OK")
			{
				
				loadPage('pnMaterial' + index, '<?php echo URL;?>includes/bom_line.php?bom_id=' + bom_id + '&index=' + index, function(status, message)
				{
					if(status== 0)
					{
					
					}
					
				}, false);
			}
			else{
				alert(message);
			}
		}
		
	}, true);
}
function saveMaterial(id, theInput, name)
{
	var _url = '<?php echo URL;?>includes/bom_line.php?ac=saveRouting';
	_url = _url + '&id=' + id;
	_url = _url + '&name=' + name;
	_url = _url + '&value=' + encodeURIComponent(theInput.value);
	
	loadPage('gotoTop', _url, function(status, message)
	{
		if(status== 0)
		{
			
		}
		
	}, true);
}
</script>

<?php
}else if($ac == "addRouting")
{
	$production_id = '';
	if(isset($_REQUEST['production_id']))
	{
		$production_id = $_REQUEST['production_id'];
	}
	
	$id = gen_uuid();
	$sql = "INSERT INTO mrp_routing(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", production_id";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
	$sql = $sql.", 0";
	$sql = $sql.", '".$production_id."'";
	$sql = $sql.")";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "delRouting")
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
		$sql = "UPDATE mrp_routing SET status =1";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
	}
	echo "OK";
}else if($ac == "saveRouting")
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
	if($name == "category_name")
	{
		$sql = "SELECT d1.id FROM mrp_routing_category d1 WHERE d1.category_name='".str_replace("'", "''", $value). "'";
		$result = pg_exec($db, $sql);
		$numrows = pg_numrows($result);
		$category_id = "";
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$category_id = $row["id"];
		}
		else{
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
			$sql = $sql.", '".$value."'";
			$sql = $sql.")";
			$result = pg_exec($db, $sql);
		}
		$sql = "UPDATE mrp_routing SET category_id ='".$category_id. "'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
	}else if($name == "days")
	{
		if($value == "")
		{
			$value = "0";
		}
		$sql = "UPDATE mrp_routing SET ".$name." ='".str_replace("'", "''", $value). "'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
	}else 
	{
		$sql = "UPDATE mrp_routing SET ".$name." ='".str_replace("'", "''", $value). "'";
		$sql = $sql.", write_date=NOW()";
		$sql = $sql." WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
	}
	
	
	echo "OK";
}
?>