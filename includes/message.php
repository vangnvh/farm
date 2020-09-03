<?php
require_once('../config.php' );
$ac = '';
if(isset($_REQUEST['ac']))
{
	$ac = $_REQUEST['ac'];
}
if($ac == '')
{
	$ac = 'viewMessage';
}
if($ac == "viewMessage")
{
	$sql = "SELECT id, from_user_id, to_user_id, message, status, create_date, write_date FROM chat_message";
	$sql = $sql." WHERE location='TEXT' AND (to_user_id='".$_REQUEST['user_id']."' OR from_user_id='".$_REQUEST['user_id']."'";
	$users = $_REQUEST['users'];
	$items = explode(",", $users);
	$users = '';
	for($i =0; $i<count($items); $i++)
	{
		if($users != "")
		{
			$users = $users." OR ";
		}
		$users = $users." from_user_id='".$items[$i]."' OR to_user_id='".$items[$i]."'";
	}
	if($users != "")
	{
		$sql = $sql." OR ".$users; 
	}
	$sql = $sql.")"; 
	$write_date = $_REQUEST['write_date'];
	if($write_date != "")
	{
		$sql = $sql." AND write_date>='".$write_date ."'";
	}
	
	$sql = $sql." ORDER BY create_date ASC";

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);
	for($i = 0; $i<$numrows; $i++)
	{
		$row = pg_fetch_array($result, $i);
		echo $row["id"]."\t".$row["from_user_id"]."\t".$row["to_user_id"]."\t".$row["message"]."\t".$row["status"]."\t".$row["create_date"]."\t".$row["write_date"]."\n";
		
	}
		
}
?>