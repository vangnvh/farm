<?php

require_once('../config.php' );


$ac = "view";
if(isset($_REQUEST['ac']))
{
	$ac = $_REQUEST['ac'];
}
if($ac == "view")
{
$user_id = "";
if(isset($_REQUEST['user_id']))
{
	$user_id = $_REQUEST['user_id'];
}
$fdate = "";
if(isset($_REQUEST['fdate']))
{
	$fdate = $_REQUEST['fdate'];
}
$tdate = "";
if(isset($_REQUEST['tdate']))
{
	$tdate = $_REQUEST['tdate'];
}

?>
Tiền lương
