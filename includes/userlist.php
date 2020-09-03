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
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Id');?></th>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Code');?></th>
		<th  nowrap="nowrap" align="center" width="250"><?php echo __('Name');?></th>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Phone');?></th>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Email');?></th>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('VAT');?></th>
		<th nowrap="nowrap" align="center" width="300"><?php echo __('Address');?></th>
		
		
	  </tr>
	</thead>
	<?php
	
	$sql = "SELECT d1.id, d1.user_name, d3.partner_code, d3.partner_name, d3.phone, d3.email, d3.vat, d3.address FROM res_user d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.status =0";
	
	if($search != "")
	{
		$search = str_replace("'", "''", $search);
		$sql = $sql." AND (d3.partner_code ILIKE '%".$search."%' OR d3.partner_name ILIKE '%".$search."%' OR d3.vat ILIKE '%".$search."%' OR d3.phone ILIKE '%".$search."%' OR d3.email ILIKE '%".$search."%' OR d3.address ILIKE '%".$search."%' OR d1.user_name ILIKE '%".$search."%')";
	}else{
		$sql = $sql." AND 1=0";
	}
	$sql = $sql." ORDER BY d3.partner_name ASC LIMIT 20";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	?>
	<tbody>
		<?php
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$id = $row["id"];
			$user_name = $row["user_name"];
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
			<td><?php echo $user_name;?></td>
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