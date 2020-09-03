<?php

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

	function __($k) 
	{
		global $langs;
		foreach($langs as $key => $item)
		{
			if($k == $key)
			{
				return $item;
			}				
		}
		return $k;
	}

?>
<style type="text/css">
.autocomplete {
  /*the container must be positioned relative:*/
  position: relative;
  display: inline-block;
}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}
.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}
.autocomplete-items div:hover {
  /*when hovering an item:*/
  background-color: #e9e9e9;
}
.autocomplete-active {
  /*when navigating through the items using the arrow keys:*/
  background-color: DodgerBlue !important;
  color: #ffffff;
}

-->
</style>
<script>


</script>
<a href="javascript:addRegisterTesting()">+ Thêm</a>
  <table class="table table-bordered nobottommargin" >
	<thead>
	  <tr>
		<th width="30" align="center">#</th>
		<th nowrap="nowrap" align="center">Chị tiêu</th>
		<th nowrap="nowrap" align="center"  width="200">Ghi chú</th>
		<th nowrap="nowrap" align="center" width="30"></th>
	  </tr>
	</thead>
	<?php
	$register_id = '';
	if(isset($_REQUEST['register_id']))
	{
		$register_id = $_REQUEST['register_id'];
	}
	$ac = '';
	if(isset($_REQUEST['ac']))
	{
		$ac = $_REQUEST['ac'];
	}
	if($ac == "new")
	{
		$sql = "INSERT INTO customer_register_line(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", write_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", register_id";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".gen_uuid()."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".COMPANY_ID."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$register_id."'";
		$sql = $sql.")";
		$result = pg_exec($db, $sql);
	}else if($ac == "del")
	{
		$id = '';
		if(isset($_REQUEST['id']))
		{
			$id = $_REQUEST['id'];
		}
		$sql = "UPDATE customer_register_line SET status =1, write_date=NOW() WHERE id='".$id."'";
		$result = pg_exec($db, $sql);
	}
	
	$cview = '';
	if(isset($_REQUEST['cview']))
	{
		$cview = $_REQUEST['cview'];
	}
	
	$sql = "SELECT d1.id, d1.rel_id, d1.name, d1.description, d2.name AS product_name FROM customer_register_line d1 LEFT OUTER JOIN product d2 ON(d1.rel_id = d2.id) WHERE d1.status =0 AND d1.register_id ='".$register_id."' ORDER BY d1.create_date ASC";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	?>
	<tbody>
		<?php
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$id = $row["id"];
			$name = $row["product_name"];
			if($name == "")
			{
				$name = $row["name"];
			}
			$description = $row["description"];
			
			
		?>
		<tr>
			<td><?php echo $j + 1; ?></td>
			<td><div  class="autocomplete" style=""><input type="text" id="editname<?php echo $j; ?>" onblur="saveRegisterTesting('<?php echo $id; ?>', this, 'name');" name="<?php echo $id; ?>"  value="<?php echo $name; ?>" class="form-control" maxlength = "150"/></div></td>
			<td><input type="text" id="editdescription<?php echo $j; ?>" onblur="saveRegisterTesting('<?php echo $id; ?>', this, 'description');" name="<?php echo $id; ?>"  value="<?php echo $description; ?>" class="form-control" maxlength = "150"/></td>
			<td width="30"><a href="javascript:delRegisterTesting('<?php echo $id; ?>')">Xóa</a></td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
<script>

function addRegisterTesting()
{
	var _url = '<?php echo URL;?>includes/testingregister.php?ac=new&register_id=<?php echo $register_id;?>';
	loadPage('<?php echo $cview;?>', _url, function(status, message)
	{
		if(status== 0)
		{
			
		}
		
	}, false);
}

function delRegisterTesting(id)
{
	var result = confirm("<?php echo __('Want to delete?');?>");
	if (!result) {
		return;
	}
	var _url = '<?php echo URL;?>includes/testingregister.php?ac=del&id=' +id;
	loadPage('<?php echo $cview;?>', _url, function(status, message)
	{
		if(status== 0)
		{
			
		}
		
	}, false);
}
function saveRegisterTesting(id, theInput, c)
{
	var _url = '<?php echo URL;?>includes/action.php?ac=saveChanged&id=' + id;
	_url = _url + "&c=" + c;
	_url = _url + "&v=" + encodeURIComponent(theInput.value);
	_url = _url + "&t=customer_register_line";
	loadPage('<?php echo $cview;?>', _url, function(status, message)
	{
		if(status== 0)
		{
			if(message != "OK")
			{
				alert(message);
			}
		}
		
	}, true);
}

<?php
for($j =0; $j<$numrows; $j++)
{
?>
autocomplete(document.getElementById("editname<?php echo $j; ?>"), countries);
<?php
}
?>

</script>