<?php

require_once('../config.php' );

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

?>

<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<?php if($func != ""){?>
		<th nowrap="nowrap" width="30"></th>
		<?php } ?>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Customer Code');?></th>
		<th  nowrap="nowrap" align="center" width="250"><?php echo __('Company Name');?></th>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Phone');?></th>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Email');?></th>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('VAT');?></th>
		<th nowrap="nowrap" align="center" width="300"><?php echo __('Address');?></th>
		
		
	  </tr>
	</thead>
	<?php
	
	$sql = "SELECT d1.id, d2.partner_code, d2.partner_name, d2.phone, d2.email, d2.vat, d2.address FROM customer d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND d2.partner_code != ''";
	if($customer_id != "")
	{
		$sql1 = "SELECT parent_id FROM customer WHERE id='".$customer_id."'";
		$result = pg_exec($db, $sql1);
		$numrows = pg_numrows($result);	
		$parent_id = "";
		
		if($numrows>0)
		{
			$row = pg_fetch_array($result, 0);
			$parent_id = $row["parent_id"];
		}
	
		$sql = $sql." AND (d1.id='".$customer_id."' OR d1.parent_id ='".$customer_id."'";
		if($parent_id  != "")
		{
			$sql = $sql." OR d1.id='".$parent_id."' OR d1.parent_id ='".$parent_id."'";
		}
		$sql = $sql.")";
		
	}
	if($search != "")
	{
		$search = str_replace("'", "''", $search);
		$sql = $sql." AND (d2.partner_code ILIKE '%".$search."%' OR d2.partner_name ILIKE '%".$search."%' OR d2.vat ILIKE '%".$search."%' OR d2.phone ILIKE '%".$search."%' OR d2.email ILIKE '%".$search."%' OR d2.address ILIKE '%".$search."%')";
	}
	$sql = $sql." ORDER BY d2.partner_code ASC LIMIT 20";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	?>
	<tbody>
		<?php
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$id = $row["id"];
			$partner_code = $row["partner_code"];
			$partner_name = $row["partner_name"];
			$phone = $row["phone"];
			$email = $row["email"];
			$vat = $row["vat"];
			$address = $row["address"];
		?>
		<tr>
			<td><?php echo $j + 1; ?></td>
			<?php if($func != ""){?>
			<td width="30"><a href="javascript:<?php echo $func; ?>('<?php echo $id; ?>')"><?php echo __('Apply');?></a></td>
			<?php } ?>
			<td><?php echo $partner_code;?></td>
			<td><?php echo $partner_name;?></td>
			<td><?php echo $phone;?></td>
			<td><?php echo $email;?></td>
			<td><?php echo $vat;?></td>
			<td><?php echo $address;?></td>
			
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>