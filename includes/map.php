<?php
require_once('../config.php' );
require_once('../tool.php' );
$lat = 0;
if(isset($_REQUEST['lat']))
{
	$lat = $_REQUEST['lat'];
}
$lng = 0;
if(isset($_REQUEST['lng']))
{
	$lng = $_REQUEST['lng'];
}
$area = 0;
if(isset($_REQUEST['area']))
{
	$area = $_REQUEST['area'];
}
?>
Diện tích: <?php echo integer_format($area);?> (m<sup>2</sup>)
<iframe width="100%" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/view?key=<?php echo GOOGLE_API_KEY;?>&center=<?php echo $lat;?>,<?php echo $lng;?>&zoom=18"></iframe>
