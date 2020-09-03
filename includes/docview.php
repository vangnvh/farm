<?php
require_once('../config.php' );
$name = '';
if(isset($_REQUEST['name']))
{
	$name = $_REQUEST['name'];
}
$extension = '';
if(isset($_REQUEST['extension']))
{
	$extension = $_REQUEST['extension'];
}
?>
<img src="<?php echo URL;?>api/api.php?ac=document&name=<?php echo $name;?>&extension=<?php echo $extension;?>">