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

$customer_id = '';
if(isset($_REQUEST['customer_id']))
{
	$customer_id = $_REQUEST['customer_id'];
}
$can_edit = '';
if(isset($_REQUEST['can_edit']))
{
	$can_edit = $_REQUEST['can_edit'];
}

$register_id = '';
if(isset($_REQUEST['register_id']))
{
	$register_id = $_REQUEST['register_id'];
}
$sql = "SELECT rel_id, status FROM customer_register_line WHERE register_id='".$register_id."'";

$result = pg_exec($db, $sql);
$numrows = pg_numrows($result);

$arr_rel = array();
$arr_status = array();
for($i = 0; $i<$numrows; $i++)
{
	$row = pg_fetch_array($result, $i);
	$arr_rel[$i]=$row["rel_id"];
	$arr_status[$i] = $row["status"];
	
}


$sql = "SELECT d1.id, d1.code, d1.name, d1.version, d1.create_date, d1.parent_id FROM product_private d1 LEFT OUTER JOIN notification d2 ON(d1.id = d2.notification_id AND d2.status =0) LEFT OUTER JOIN notification_category  d3 ON(d2.category_id = d3.id) WHERE d1.status =0 AND d1.customer_id='".$customer_id."' AND d1.type !='FROMCUSTOMER' AND d1.id NOT IN(";
$sql = $sql."SELECT d1.private_id FROM sale_order_product d1 LEFT OUTER JOIN sale_order d2 ON(d1.order_id = d2.id) WHERE d1.status =0 AND d2.status =0 AND d2.customer_id='".$customer_id."'";
$sql = $sql.")";
$sql = $sql." ORDER BY d1.create_date ASC";

$result = pg_exec($db, $sql);
$numrows = pg_numrows($result);

?>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<th width="40"></th>
		<th nowrap="nowrap"><?php echo __('Private Name');?></th>
		<th nowrap="nowrap"><?php echo __('Product Name');?></th>
		<th  width="120px" nowrap="nowrap"><?php echo __('Version');?></th>
		<th  width="120px" nowrap="nowrap"><?php echo __('Approval Date');?></th>
	  </tr>
	</thead>
	<tbody>
	<?php
	
		$sql = "";
		$len = count($arr_rel);
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$private_id = $row["id"];
			$hasItem = false;
			$status = 0;
			for($n =0; $n<$len; $n++) 
			{
				
				$key = $arr_rel[$n];
				
				if($key == $private_id)
				{
					$status = $arr_status[$n];
					$hasItem = true;
					break;
				}
			}
			if($hasItem == false)
			{
				if($sql != "")
				{
					$sql = $sql.";";
				}
				
				$sql = $sql."INSERT INTO customer_register_line(";
				$sql = $sql."id";
				$sql = $sql.", create_date";
				$sql = $sql.", write_date";
				$sql = $sql.", company_id";
				$sql = $sql.", status";
				$sql = $sql.", register_id";
				$sql = $sql.", rel_id";
				$sql = $sql." )VALUES(";
				$sql = $sql."'".gen_uuid()."'";
				$sql = $sql.", NOW()";
				$sql = $sql.", NOW()";
				$sql = $sql.", '".COMPANY_ID."'";
				$sql = $sql.", 0";
				$sql = $sql.", '".$register_id."'";
				$sql = $sql.", '".$private_id."'";
				$sql = $sql.")";
			}
			$code = $row["code"];
			$name = $row["name"];
			$version = $row["version"];
			$create_date = $row["create_date"];
			
			if($create_date != "")
			{
				$firstIndex = stripos($create_date, " ");
				if($firstIndex != -1)
				{
					$create_date = substr($create_date, 0, $firstIndex);
					$arr = explode("-", $create_date);
					if(count($arr)>2)
					{
						$create_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
					}
				}
			}
		?>
		<tr>
				<td><?php echo $j + 1; ?></td>
				<td><input <?php if($can_edit == "0"){ echo " disabled=\"disabled\" "; }?> onclick="stateChange(this, '<?php echo $private_id;?>')" type="checkbox" class="form-control" name="[]" id="<?php echo $private_id;?>" <?php if($status == 0){ echo " checked ";}?>/></td>
				<td><a href="<?php echo SERVER_URL;?>report/index?report_id=819bf5cc-85fc-488b-87d2-f2eca4054796&type=view&id=<?php echo $private_id;?>" target="_blank"><?php echo $code; ?></a></td>
				<td><?php echo $name; ?></td>
				<td><?php echo $version; ?></td>
				<td><?php echo $create_date; ?></td>
		</tr>
		<?php
		}
		if($sql != "")
		{
			pg_exec($db, $sql);
		}
		
		?>
		<script>
			function stateChange(ck, id)
			{
				var status =0;
				if(!ck.checked)
				{
					status = 1;
				}
				var _url = '<?php echo URL;?>includes/action.php?ac=stateProductRegister';
				_url = _url + '&rel_id=' + encodeURIComponent(id);
				_url = _url + '&register_id=<?php echo $register_id;?>';
				_url = _url + '&status=' + encodeURIComponent(status);
				loadPage('pnQuantityCriteria', _url, function(status, message)
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
		</script>
	</tbody>
	</table>
</div>