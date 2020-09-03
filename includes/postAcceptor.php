<?php
	
	$data = '';
	if(isset($_REQUEST['data']))
	{
		$data = $_REQUEST['data'];
	}
	$name = '';
	if(isset($_REQUEST['name']))
	{
		$name = $_REQUEST['name'];
	}
	$customer_id = '';
	if(isset($_REQUEST['customer_id']))
	{
		$customer_id = $_REQUEST['customer_id'];
	}
	$dir = dirname( __FILE__ );
	$dir = str_replace("includes", "", $dir);
	$dir = $dir."disk\\".$customer_id;
	$dir = str_replace("\\", "/", $dir);
	
	if (!is_dir($dir)) {
		 mkdir($dir, 0755, true);
		 echo "create dir";
	}
	$dir = $dir."/".$name;
	
	$file = fopen($dir, "wb");

   
    fwrite($file, base64_decode($data));
    fclose($file);
	echo $dir;
?>