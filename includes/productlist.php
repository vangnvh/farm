<?php
session_start();
require_once('../config.php' );
require_once('../tool.php' );

$LOGIN_COMPANY_ID = "";
if(isset($_SESSION["company_id"]))
{
	$LOGIN_COMPANY_ID = $_SESSION["company_id"];
}
$LOGIN_PARENT_COMPANY_ID = "";
if(isset($_SESSION["parent_company_id"]))
{
	$LOGIN_PARENT_COMPANY_ID = $_SESSION["parent_company_id"];
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
<style type="text/css">
.container .content {
    display: none;
    padding : 5px;
}
</style>
	<?php
	$order_id = '';
	if(isset($_REQUEST['order_id']))
	{
		$order_id = $_REQUEST['order_id'];
	}
		
	$sql = "SELECT d1.id, d2.name, d3.name AS unit_name, d1.quantity, d1.factor, d1.unit_price FROM sale_order_product d1 LEFT OUTER JOIN product d2 ON(d1.product_id = d2.id) LEFT OUTER JOIN product_unit d3 ON(d1.unit_id = d3.id) WHERE d1.status =0 AND d1.order_id='".$order_id."' ORDER BY d1.create_date ASC";

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	$sql = "SELECT d.id, d.document_name, d.extension, d.for_id FROM document d LEFT OUTER JOIN sale_order_product d1 ON(d.for_id = d1.id) LEFT OUTER JOIN product d2 ON(d1.product_id = d2.id) WHERE d1.status =0 AND d1.order_id='".$order_id."' ORDER BY d.create_date ASC";
	

	$result_doc = pg_exec($db, $sql);
	$numrows_doc = pg_numrows($result_doc);	
	$docs = array();
	for($i = 0; $i < $numrows_doc; $i++) 
	{
		$row = pg_fetch_array($result_doc, $i);
		$arr = array();
		$arr[0] = $row["id"];
		$arr[1] = $row["document_name"];
		$arr[2] = $row["extension"];
		$arr[3] = $row["for_id"];
		$docs[$i] = $arr;
	}

	?>

		<?php
		
		$sql = "SELECT d1.id, d1.rel_id, d3.partner_code, d3.partner_name, d1.employee_id FROM hr_employee_rel d1 LEFT OUTER JOIN hr_employee d2 ON(d1.employee_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) LEFT OUTER JOIN sale_order_product d4 ON(d1.rel_id = d4.id) WHERE d4.order_id = '".$order_id."' AND d1.status =0";
		$sql = $sql." ORDER BY d3.partner_code ASC";
		
		
		$result_emp = pg_exec($db, $sql);
		$numrows_emp = pg_numrows($result_emp);	
		$emloyees = array();
		for($i = 0; $i < $numrows_emp; $i++) 
		{
			$row = pg_fetch_array($result_emp, $i);
			$arr = array();
			$arr[0] = $row["id"];
			$arr[1] = $row["partner_code"];
			$arr[2] = $row["partner_name"];
			$arr[3] = $row["rel_id"];
			$arr[4] = $row["employee_id"];
			$emloyees[$i] = $arr;
		}
		
		
		$sql = "SELECT d1.id, d2.partner_code, d2.partner_name FROM hr_employee d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."' ORDER BY d2.partner_code ASC";
						
		$result_emp = pg_exec($db, $sql);
		$numrows_emp = pg_numrows($result_emp);	
		$emloyeesList = array();
		for($i = 0; $i < $numrows_emp; $i++) 
		{
			$row = pg_fetch_array($result_emp, $i);
			$arr = array();
			$arr[0] = $row["id"];
			$arr[1] = $row["partner_code"];
			$arr[2] = $row["partner_name"];
			$emloyeesList[$i] = $arr;
		}
		
		
						
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$id = $row["id"];
			$name = $row["name"];
			$unit_name = $row["unit_name"];
			$quantity = $row["quantity"];
			$unit_price = $row["unit_price"];
			$factor = $row["factor"];
		?>
		<div class="row">
				<div class="col-sm-2 col-form-label">
					Tên sản phẩm:
				</div>
				<div class="col-sm-6 ">
					 <h2><?php echo $name; ?> </h2>
				</div>
				<div class="col-sm-2 col-form-label">
					
				</div>
				<div class="col-sm-2 ">
				<a href="javascript:delOrderProduct('<?php echo $id; ?>')">Xóa</a>
				</div>
		</div>
		<br>
		<div class="row">
				
				<div class="col-sm-2 col-form-label">
					Đơ vị tính:
				</div>
				<div class="col-sm-4 ">
				<input type="text" onblur="saveOrderProduct('<?php echo $id; ?>', this, 'unit_name');" value="<?php echo $unit_name; ?>" class="form-control" maxlength = "50" id="editunit<?php echo $i;?>" />
				</div>
				<div class="col-sm-2 col-form-label">
					Điện tích(hecta):
				</div>
				<div class="col-sm-4 ">
					<input type="text" onblur="saveOrderProduct('<?php echo $id; ?>', this, 'factor');" value="<?php echo $factor; ?>" class="form-control" maxlength = "100" />
				</div>
		</div>
		<br>
		<div class="row">
				
				<div class="col-sm-2 col-form-label">
					Số lượng:
				</div>
				<div class="col-sm-4 ">
					<input type="text" onblur="saveOrderProduct('<?php echo $id; ?>', this, 'quantity');" value="<?php echo $quantity; ?>" class="form-control" maxlength = "100" />
				</div>
				<div class="col-sm-2 col-form-label">
					Số tiền:
				</div>
				<div class="col-sm-4 ">
					<input type="text" onblur="saveOrderProduct('<?php echo $id; ?>', this, 'unit_price');" value="<?php echo $unit_price; ?>" class="form-control" maxlength = "100" />
				</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Quy trình sản xuất:
			</div>
			<div class="col-sm-10 ">
				
					<div id="pnRouting<?php echo $j;?>"></div>
			
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Kế hoạch:
			</div>
			<div class="col-sm-10 ">
				
					<div id="pnPlanning<?php echo $j;?>">
				
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Người thực hiện:
			</div>
			<div class="col-sm-9 ">
				<select class="form-control" id="emp<?php echo $id;?>">
						
						<?php
						for($k =0; $k<count($emloyeesList); $k++)
						{
							$arr = $emloyeesList[$k];
							$employee_id = $arr[0];
							$employee_code = $arr[1];
							$employee_name = $arr[2];
							$hasItem = false;
							for($n =0; $n<count($emloyees); $n++)
							{
								if( $emloyees[$n][4] == $employee_id && $emloyees[$n][3] == $id)
								{
									$hasItem = true;
									break;
								}
							}
							if($hasItem == false)
							{
						?>
							<option value="<?php echo $employee_id;?>"><?php echo $employee_code;?>. <?php echo $employee_name;?></option>
						<?php
							}
						}
						?>
						</select>
						
						<br>
						<?php
						for($k =0; $k<count($emloyees); $k++)
						{
							$arr = $emloyees[$k];
							if($arr[3] == $id)
							{
								$department_employee_id = $arr[0];
								$employee_code = $arr[1];
								$employee_name = $arr[2];
								
						?>
						<a href="javascript:delProductEmployee('<?php echo $department_employee_id; ?>')">  - </a> <?php echo $employee_code;?>. <?php echo $employee_name;?><br>
						<?php 
							}
						} 
						?>
			</div>
			<div class="col-sm-1 ">
			<a href="javascript:addProductEmployee('<?php echo $id; ?>')">+ Thêm</a>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Sản lượng dự kiến:
			</div>
			<div class="col-sm-10 ">
				
					<div id="pnProductType<?php echo $j;?>">
						
				</div>
			</div>
		</div>
		
		
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Chi phí: 
			</div>
			<div class="col-sm-10 ">
				<div id="pnPayment<?php echo $j;?>">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Thực tế nhập: 
			</div>
			<div class="col-sm-10 ">
				
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Vị trí sản xuất:
			</div>
			<div class="col-sm-10 ">
				<div id="pnLocation<?php echo $j;?>">
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-2 col-form-label">
				Tập tin đính kèm:
			</div>
			<div class="col-sm-10 ">
				<?php
				for($k =0; $k<count($docs); $k++)
				{
					$arr = $docs[$k];
					if($arr[3] == $id)
					{
						$doc_id = $arr[0];
						$document_name = $arr[1];
						$extension = $arr[2];
						
				?>
				<a href="<?php echo URL;?>includes/docview.php?name=<?php echo $doc_id;?>&extension=<?php echo $extension;?>" target="_blank"><img src="<?php echo URL;?>api/api.php?ac=document&name=<?php echo $doc_id;?>&extension=<?php echo $extension;?>" width="122" height="92"></a>
				<?php 
					}
				} 
				?>	
			</div>
		</div>
	
		<?php
		}
		?>
		<script>
		<?php
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			$id = $row["id"];
		?>
		loadRouting('<?php echo $id;?>', <?php echo $j; ?>); 
		loadPlanning('<?php echo $id;?>', <?php echo $j; ?>); 
		loadProductType('<?php echo $id;?>', <?php echo $j; ?>); 
		loadPayment('<?php echo $id;?>', <?php echo $j; ?>); 
		loadLocation('<?php echo $id;?>', <?php echo $j; ?>); 
		<?php
		}
		?>
		</script>
	
<script>
	function addProductEmployee(rel_id)
	{
		var ctr = document.getElementById('emp' + rel_id);
		if(ctr.value == '')
		{
			alert("Select employee");
			return;
			
		}
		var _url = '<?php echo URL;?>includes/action.php?ac=addProductEmployee';
		_url = _url + '&employee_id=' + ctr.value;
		_url = _url + '&rel_id=' + rel_id;
		
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					
					loadProduct();
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	var order_product_id = "";
	function addProduction(rel_id)
	{
		order_product_id = rel_id;
		var _url = '<?php echo URL;?>includes/productlist.php?ac=productionList';
		openPopup(_url);
	}
	
	function doAddProduction()
	{
		var ctr = document.getElementById("production_id");
		if(ctr.value == "")
		{
			alert("Select SOP");
			ctr.focus();
			return;
		}
		var production_id = ctr.value;
		var ctr = document.getElementById("workorder_date");
		var start_date = ctr.value;
		var _url = '<?php echo URL;?>includes/productlist.php?ac=addProduction&order_product_id=' + order_product_id;
		_url = _url + '&production_id=' + production_id;
		_url = _url + '&start_date=' + encodeURIComponent(start_date);
		closePopup();
		
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					
					loadProduct();
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	function delProduction(id)
	{
		var result = confirm("Want to delete?");
		if (!result) {
			return;
		}
		var _url = '<?php echo URL;?>includes/productlist.php?ac=delProduction&order_product_id=' + id;
		loadPage('gotoTop', _url, function(status, message)
		{
			if(status== 0)
			{
				if(message == "OK")
				{
					
					loadProduct();
				}
				else{
					alert(message);
				}
			}
			
		}, true);
	}
	
	$(".header").click(function () {
		
		$header = $(this);
		//getting the next element
		$content = $header.next();
		//open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
		$content.slideToggle(500, function () {
			//execute this after slideToggle is done
			//change text of header based on visibility of content div
			$header.text(function () {
				//change text based on condition
				return $content.is(":visible") ? "  -  " : "  +  ";
			});
		});

	});
	<?php
for($j =0; $j<$numrows; $j++)
{
?>
autocomplete(document.getElementById("editunit<?php echo $i;?>"), units);
<?php
}
?>
</script>
<?php
}else if($ac == "routing")
{
	$order_id = '';
	if(isset($_REQUEST['order_id']))
	{
		$order_id = $_REQUEST['order_id'];
	}
	$order_product_id = '';
	if(isset($_REQUEST['order_product_id']))
	{
		$order_product_id = $_REQUEST['order_product_id'];
	}
	$index = '';
	if(isset($_REQUEST['index']))
	{
		$index = $_REQUEST['index'];
	}
		
	
	$sql = "SELECT d1.id, d5.category_name, d1.start_date, d1.end_date, d1.proccess, (SELECT SUM(m.quantity) FROM mrp_workorder_routing_quantity m WHERE m.status =0 AND m.workorder_routing_id = d1.id) AS done_quantity FROM mrp_workorder_routing d1 LEFT OUTER JOIN mrp_workorder d3 ON(d1.workorder_id = d3.id) LEFT OUTER JOIN mrp_routing d4 ON(d1.routing_id = d4.id) LEFT OUTER JOIN mrp_routing_category d5 ON(d4.category_id = d5.id) WHERE d1.status =0 AND d3.order_product_id='".$order_product_id."' ORDER BY d1.start_date ASC";
	
	
	$result_routing = pg_exec($db, $sql);
	$numrows_routing = pg_numrows($result_routing);	
	$routingList = array();
	
	$sql = "SELECT d4.id, d4.code, d4.name, d4.unit_id, SUM(d1.quantity * d5.factor) AS quantity FROM mrp_bom_line d1 LEFT OUTER JOIN mrp_workorder_routing d2 ON(d1.bom_id = d2.routing_id) LEFT OUTER JOIN mrp_workorder d3 ON(d2.workorder_id = d3.id) LEFT OUTER JOIN product d4 ON(d1.product_id = d4.id) LEFT OUTER JOIN sale_order_product d5 ON(d3.order_product_id = d5.id)
WHERE d1.status =0 AND d3.order_product_id='".$order_product_id."' GROUP BY d4.id, d4.name, d4.code ORDER BY d4.name ASC ";
	$result_bom = pg_exec($db, $sql);
	$numrows_bom = pg_numrows($result_bom);	
	
?>

	<a href="javascript:addProduction('<?php echo $order_product_id; ?>')">+ Thêm</a>
	| <a href="javascript:delProduction('<?php echo $order_product_id; ?>')">- Xóa quy trình</a>
		<br>
		<table>
			<tr>
			<td align="center"><b>Bước thực hiện</b>
			</td>
			<td align="center"><b>Định mức</b></td>
			</tr>
			<tr>
			<td>
		<?php
		for($i = 0; $i < $numrows_routing; $i++) 
		{
			$row = pg_fetch_array($result_routing, $i);
			$arr = array();
			$work_routing_id = $row["id"];
			$routing_name = $row["category_name"];
			$done_quantity = $row["done_quantity"];
			$start_date = $row["start_date"];
			if($start_date != "")
			{
				$firstIndex = stripos($start_date, " ");
				if($firstIndex != -1)
				{
					$start_date = substr($start_date, 0, $firstIndex);
					$arr = explode("-", $start_date);
					if(count($arr)>2)
					{
						$start_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
					}
				}
			}
			
				
				
		?>
		<?php echo ($i + 1);?>. <a href="javascript:delRouting('<?php echo $work_routing_id; ?>', '<?php echo $order_product_id; ?>', <?php echo $index;?>)">  - </a> <a href="javascript:showRouting('<?php echo $work_routing_id; ?>', '<?php echo $index; ?>')"><?php echo $routing_name;?> (<?php echo $start_date;?>)</a> <br>
		<?php 
			
		} 
		?>
		</td>
		<td valign="top" align="center" style="padding-left: 30px">
		<table class="table table-bordered nobottommargin">
			<tr>
				<td>Stt</td>
				<td>Mã</td>
				<td>Tên</td>
				<td>ĐVT</td>
				<td>Số lượng</td>
			</tr>
			<?php
			for($i = 0; $i < $numrows_bom; $i++) 
			{
				$row = pg_fetch_array($result_bom, $i);
				$code = $row["code"];
				$name = $row["name"];
				$unit_id = $row["unit_id"];
				$quantity = $row["quantity"];
				$color = EVEN_COLOR;
				if($i %2 == 0)
				{
					$color = ODD_COLOR;
				}
			?>
			<tr style="background-color:<?php echo $color;?>">
				<td><?php echo ($i + 1);?></td>
				<td><?php echo $code;?></td>
				<td><?php echo $name;?></td>
				<td><?php echo $unit_id;?></td>
				<td align="right"><?php echo $quantity;?></td>
			</tr>
			<?php
			}
			?>
		</table>
		</td>
		</tr>
		</table>
<?php
}else if($ac == "planning")
{
	$order_id = '';
	if(isset($_REQUEST['order_id']))
	{
		$order_id = $_REQUEST['order_id'];
	}
	$order_product_id = '';
	if(isset($_REQUEST['order_product_id']))
	{
		$order_product_id = $_REQUEST['order_product_id'];
	}
	$index = '';
	if(isset($_REQUEST['index']))
	{
		$index = $_REQUEST['index'];
	}
	
	$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d1.start_date, d1.end_date, d1.quantity, d3.order_product_id FROM mrp_workorder_planning d1 LEFT OUTER JOIN mrp_workorder d3 ON(d1.workorder_id = d3.id) LEFT OUTER JOIN sale_order_product d5 ON(d3.order_product_id = d5.id) WHERE d3.order_product_id='".$order_product_id."' AND d1.status =0";
	$sql = $sql." ORDER BY d1.receipt_date DESC";
	
	
	$result_routing = pg_exec($db, $sql);
	$numrows_routing = pg_numrows($result_routing);	
	$planningList = array();
	for($i = 0; $i < $numrows_routing; $i++) 
	{
		$row = pg_fetch_array($result_routing, $i);
		$arr = array();
		$arr[0] = $row["id"];
		$arr[1] = $row["receipt_no"];
		$arr[2] = $row["receipt_date"];
		$arr[3] = $row["start_date"];
		$arr[4] = $row["end_date"];
		$arr[5] = $row["quantity"];
		$arr[6] = $row["order_product_id"];
		$planningList[$i] = $arr;
	}
?>
<a href="javascript:addPlanning('<?php echo $order_product_id; ?>', <?php echo $index;?>)">+ Thêm</a>
<br>
<?php
for($k =0; $k<count($planningList); $k++)
{
	$arr = $planningList[$k];
	
		$planning_id = $arr[0];
		$receipt_no = $arr[1];
		$receipt_date = $arr[2];
		$start_date = $arr[3];
		$end_date = $arr[4];
		
		if($start_date != "")
		{
			$firstIndex = stripos($start_date, " ");
			if($firstIndex != -1)
			{
				$start_date = substr($start_date, 0, $firstIndex);
				$arr = explode("-", $start_date);
				if(count($arr)>2)
				{
					$start_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
				}
			}
		}
		if($end_date != "")
		{
			$firstIndex = stripos($end_date, " ");
			if($firstIndex != -1)
			{
				$end_date = substr($end_date, 0, $firstIndex);
				$arr = explode("-", $end_date);
				if(count($arr)>2)
				{
					$end_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
				}
			}
		}
	
		
		
?>
<a href="javascript:delPlanning('<?php echo $planning_id; ?>', '<?php echo $order_product_id; ?>', <?php echo $index;?>)">  - </a> <a href="javascript:planning('<?php echo $planning_id;?>')"><?php echo $receipt_no;?> - (<?php echo $start_date;?> - <?php echo $end_date;?>)</a><br>
<?php 
	
} 
?>
<?php
}else if($ac == "productionList")
{
	$sql = "SELECT d1.id, d1.name FROM mrp_production d1 WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d1.company_id='".$LOGIN_PARENT_COMPANY_ID."') ORDER BY d1.name ASC";
						
	$result_emp = pg_exec($db, $sql);
	$numrows_emp = pg_numrows($result_emp);	
	$productions = array();
	for($i = 0; $i < $numrows_emp; $i++) 
	{
		$row = pg_fetch_array($result_emp, $i);
		$arr = array();
		$arr[0] = $row["id"];
		$arr[1] = $row["name"];
		
		$productions[$i] = $arr;
	}
	?>
	Chọn quy trình:
	<select class="form-control" id="production_id">
	<?php
			for($k =0; $k<count($productions); $k++)
			{
				$arr = $productions[$k];
				$production_id = $arr[0];
				$production_name = $arr[1];
				
				
			?>
				<option value="<?php echo $production_id;?>"><?php echo $production_name;?></option>
			<?php
				
			}
			?>
	</select>
	<br>
	Ngày thực hiện:
	<input type="date" id="workorder_date" class="form-control" value="<?php echo date('Y-m-d'); ?>"/>
	<br>
	<a class="button button-3d nomargin" href="javascript:doAddProduction()">Áp dụng</a> 
	<?php
}else if($ac == "addProduction")
{	
	$order_product_id = '';
	if(isset($_REQUEST['order_product_id']))
	{
		$order_product_id = $_REQUEST['order_product_id'];
	}
	$production_id = '';
	if(isset($_REQUEST['production_id']))
	{
		$production_id = $_REQUEST['production_id'];
	}
	$start_date = "";
	if(isset($_REQUEST['start_date']))
	{
		$start_date = $_REQUEST['start_date'];
	}
	$d= strtotime($start_date." 00:00:00");
	
	$workorder_id = gen_uuid();
	$sql = "INSERT INTO mrp_workorder(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", production_id";
	$sql = $sql.", order_product_id";
	$sql = $sql.", rel_id";
	$sql = $sql.", start_date";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$workorder_id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
	$sql = $sql.", 0";
	$sql = $sql.", '".$production_id."'";
	$sql = $sql.", '".$order_product_id."'";
	$sql = $sql.", '".$order_product_id."'";
	$sql = $sql.", '".$start_date."'";
	$sql = $sql.")";
	$result = pg_exec($db, $sql);
	

	
	$sql = "SELECT d1.id, d1.days, d1.description FROM mrp_routing d1 WHERE d1.status =0 AND d1.production_id='".$production_id."' ORDER BY d1.days ASC";

	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	for($i =0; $i<$numrows; $i++)
	{
		$row = pg_fetch_array($result, $i);
		$routing_id = $row["id"];
		$description = $row["description"];
		$days = $row["days"];
		
		$dDate = strtotime($start_date . ' +'.$days.' day');
		$workorder_routing_id= gen_uuid();
		$sql = "INSERT INTO mrp_workorder_routing(";
		$sql = $sql."id";
		$sql = $sql.", create_date";
		$sql = $sql.", company_id";
		$sql = $sql.", status";
		$sql = $sql.", routing_id";
		$sql = $sql.", workorder_id";
		$sql = $sql.", proccess";
		$sql = $sql.", start_date";
		$sql = $sql.", end_date";
		$sql = $sql.", description";
		$sql = $sql." )VALUES(";
		$sql = $sql."'".$workorder_routing_id."'";
		$sql = $sql.", NOW()";
		$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".$routing_id."'";
		$sql = $sql.", '".$workorder_id."'";
		$sql = $sql.", 0";
		$sql = $sql.", '".date("Y-m-d", $dDate)." 00:00:00'";
		$sql = $sql.", '".date("Y-m-d", $dDate)." 23:59:59'";
		$sql = $sql.", '".str_replace("'", "''", $description)."'";
		$sql = $sql.")";
	
		$rs = pg_exec($db, $sql);
		
	}
	
	echo "OK";
}else if($ac == "delProduction")
{
	$sql = "SELECT id FROM mrp_workorder WHERE rel_id ='".$order_product_id."' AND status =0";
	$result = pg_exec($db, $sql);
	$numrows= pg_numrows($result);	
	if($numrows>0)
	{
		$row = pg_fetch_array($result, 0);
		$workorder_id = $row["id"];
		$sql = "UPDATE mrp_workorder_routing SET status =1, write_date =NOW() WHERE workorder_id='".$workorder_id."'";
		$result = pg_exec($db, $sql);
		$sql = "UPDATE mrp_workorder SET status =1, write_date =NOW() WHERE id='".$workorder_id."'";
		$result = pg_exec($db, $sql);
	}
	
}
else if($ac == "workorder_routing")
{
	$id = '';
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}
	$index = '';
	if(isset($_REQUEST['index']))
	{
		$index = $_REQUEST['index'];
	}
	$sql = "SELECT d1.id, d1.start_date, d1.end_date, d1.description, d2.order_product_id FROM mrp_workorder_routing d1 LEFT OUTER JOIN mrp_workorder d2 ON(d1.workorder_id = d2.id) WHERE d1.id='".$id."'";
	
	$result = pg_exec($db, $sql);
	$numrows= pg_numrows($result);	
	if($numrows>0)
	{
		$row = pg_fetch_array($result, 0);
		$start_date = $row["start_date"];
		$end_date = $row["end_date"];
		$description = $row["description"];
		$order_product_id = $row["order_product_id"];
		
		$sql = "SELECT d.id, d.document_name, d.extension FROM document d WHERE d.status =0 AND d.for_id='".$id."' ORDER BY d.create_date ASC";
	

		$result_doc = pg_exec($db, $sql);
		$numrows_doc = pg_numrows($result_doc);	
		
		
	
	?>
	Bắt đầu:
	<input type="date" id="editrouting_start_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($start_date)); ?>"/>
	<br>
	Bắt kết thúc:
	<input type="date" id="editrouting_end_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($end_date)); ?>"/>
	<br>
	<textarea id="editrouting_description" class="form-control" maxlength="250"><?php echo $description;?></textarea>
	<br>
	<a class="button button-3d nomargin" href="javascript:saveWorkOrderRouting('<?php echo $id;?>', '<?php echo $order_product_id;?>', '<?php echo $index;?>')">Áp dụng</a> 
	<hr>
	<?php
	for($i = 0; $i < $numrows_doc; $i++) 
		{
			$row = pg_fetch_array($result_doc, $i);
			$arr = array();
			$doc_id = $row["id"];
			$document_name = $row["document_name"];
			$extension = $row["extension"];
			
		?>
		<br><a href="<?php echo URL;?>includes/docview.php?name=<?php echo $doc_id;?>&extension=<?php echo $extension;?>" target="_blank"><img src="<?php echo URL;?>api/api.php?ac=document&name=<?php echo $doc_id;?>&extension=<?php echo $extension;?>" width="122" height="92"> <?php echo $document_name;?></a>
		<?php 
		}
		?>
	<?php
	}
}else if($ac == "productlist")
{
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

	$sql = "SELECT d1.id, d1.code, d1.name FROM product d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id= d2.id) WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d1.company_id ='".$LOGIN_PARENT_COMPANY_ID."') AND d1.type='PRODUCT'";
	if($search != "")
	{
		$search = str_replace("'", "''", $search);
		$sql = $sql." AND (d1.name ILIKE '%".$search."%' OR d1.code ILIKE '%".$search."%')";
	}
	$sql = $sql." ORDER BY d1.name ASC";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
?>
<div class="row">
	
	
	<div class="col-sm-6">
	</div>
	<div class="col-sm-6">
		<div class="input-group">
			<input type="text" id="editsearchProduct" class="form-control" value="<?php echo $search; ?>" placeholder="<?php echo __('Search');?>" onKeyDown="if(event.keyCode == 13){doSearchProduct();}">
			<div class="input-group-prepend">
				<a href="javascript:doSearchProduct()" class="input-group-text"><?php echo __('Search');?></a>
			</div>
		</div>
	</div>
</div>
<br>
<div class="table-responsive">
  <table class="table table-bordered nobottommargin">
	<thead>
	  <tr>
		<th width="30">#</th>
		<?php if($func != ""){?>
		<th nowrap="nowrap"></th>
		<?php } ?>
		<th nowrap="nowrap" align="center" width="80"><?php echo __('Code');?></th>
		<th  nowrap="nowrap" align="center" ><?php echo __('Name');?></th>
	  </tr>
	</thead>
	<tbody>
		<?php
		for($j =0; $j<$numrows; $j++)
		{
			$row = pg_fetch_array($result, $j);
			
			$id = $row["id"];
			$code = $row["code"];
			$name = $row["name"];

		?>
		<tr>
			<td><?php echo $j + 1; ?></td>
			<?php if($func != ""){?>
			<td  style="text-align:center; width:20px"><a class="button" href="javascript:<?php echo $func; ?>('<?php echo $id; ?>')">+</a></td>
			<?php } ?>
			<td><?php echo $code;?></td>
			<td><?php echo $name;?></td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>
<script>
	function doSearchProduct()
	{
		var _url = '<?php echo URL;?>includes/productlist.php?ac=productlist&func=<?php echo $func;?>';
		var search = document.getElementById('editsearchProduct').value;
		_url = _url + "&search=" + encodeURIComponent(search);
		loadPage('pnFullDialogContent', _url, function(status, message)
		{
			
			
		}, false);
	}
</script>
<?php
		
}else if($ac == "product_type")
{
	$order_product_id = '';
	if(isset($_REQUEST['order_product_id']))
	{
		$order_product_id = $_REQUEST['order_product_id'];
	}
	$index = '';
	if(isset($_REQUEST['index']))
	{
		$index = $_REQUEST['index'];
	}
	
	$sql = "SELECT d1.id, d1.name FROM product_type d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id= d2.id) WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d1.company_id ='".$LOGIN_PARENT_COMPANY_ID."') ";
	
	$sql = $sql." ORDER BY d1.name ASC";
	$result_type = pg_exec($db, $sql);
	$numrows_type = pg_numrows($result_type);	
	
	$sql = "SELECT d1.id, d2.name, d2.factor, d3.quantity, d4.name AS unit_name, d1.type_id,d1.unit_price, d3.unit_price AS order_unit_price, d3.factor AS order_factor FROM sale_order_product_delivery d1 LEFT OUTER JOIN product_type d2 ON(d1.type_id = d2.id) LEFT OUTER JOIN sale_order_product d3 ON(d1.order_product_id = d3.id) LEFT OUTER JOIN product_unit d4 ON(d3.unit_id = d4.id) WHERE d1.status =0 AND d1.order_product_id='".$order_product_id."'";
	
	$sql = $sql." ORDER BY d2.name ASC, d1.create_date ASC";
	
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
				
?>
<div class="row">
	<div class="col-sm-10 ">
		<select class="form-control" id="editproductType<?php echo $index;?>">
				
				<?php
				for($k =0; $k<$numrows_type; $k++)
				{
					$hasItem = false;
					$row = pg_fetch_array($result_type, $k);
					$id = $row["id"];
					for($n =0; $n<$numrows; $n++)
					{
						$row1 = pg_fetch_array($result, $n);
						if($row1["type_id"] == $id)
						{
							$hasItem = true;
							break;
						}
						
					}
					
					
					if($hasItem == false)
					{
						$name = $row["name"];
				?>
					<option value="<?php echo $id;?>"><?php echo $name;?></option>
				<?php
					}
				}
				?>
				</select>
				
				<br>
				<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30">#</th>
						<th nowrap="nowrap" align="center">Loại</th>
						<th width="80" nowrap="nowrap" align="center">ĐVT</th>
						<th width="80" nowrap="nowrap" align="center">%</th>
						<th width="100" nowrap="nowrap" align="center">Số lượng</th>
						<th width="100" nowrap="nowrap" align="center">Đơn giá</th>
						<th width="100" nowrap="nowrap" align="center">Thành tiền</th>
						<th nowrap="nowrap" width="30"></th>
					  </tr>
					</thead>
				<?php
				
				$total = 0;
				for($k =0; $k<$numrows; $k++)
				{
					$row = pg_fetch_array($result, $k);
					$id = $row["id"];
					$name = $row["name"];
					$unit_name = $row["unit_name"];
					$factor = $row["factor"];
					$quantity = $row["quantity"];
					$order_factor = $row["order_factor"];
					$unit_price = $row["unit_price"];
					$order_unit_price = $row["order_unit_price"];
					$unit_price = $order_unit_price/$quantity;
					if($order_factor<=0)
					{
						$order_factor = 1;
					}
					$quantity = $quantity * $order_factor;
					if($factor != "")
					{
						$quantity = ($quantity *$factor)/100;
					}
					
					$amount = $quantity * $unit_price;
					$total = $total + ($quantity* $unit_price);
				?>
				<tr>
			<td><?php echo $k + 1; ?></td>
			<td ><?php echo $name; ?></td>
			
			<td ><?php echo $unit_name; ?></td>
			<td style="text-align:right"><?php echo $factor; ?></td>
			<td style="text-align:right"><?php echo double_format($quantity); ?></td>
			<td style="text-align:right; width:120px" ><?php echo double_format($unit_price); ?></td>
			<td  style="text-align:right" ><?php echo double_format($amount); ?></td>
			<td width="30"><a href="javascript:delToDelivery('<?php echo $id; ?>', '<?php echo $order_product_id; ?>', '<?php echo $index; ?>')">  - </a></td>
		</tr>
		
				
				<?php 

				} 
				?>
					<tr>
			<td colspan="6"><b>Tổng tiền</b></td>
			
			<td style="text-align:right" ><b><?php echo double_format($total); ?></b></td>
			<td width="30"></td>
		</tr>
				</table>
			</div>
	</div>
	<div class="col-sm-2 ">
	<a href="javascript:addToDelivery('<?php echo $order_product_id; ?>', '<?php echo $index; ?>')">+ Thêm</a>
	</div>

</div>

<?php
}else if($ac == "addToDelivery")
{
	$order_product_id = '';
	if(isset($_REQUEST['order_product_id']))
	{
		$order_product_id = $_REQUEST['order_product_id'];
	}
	$type_id = '';
	if(isset($_REQUEST['type_id']))
	{
		$type_id = $_REQUEST['type_id'];
	}
	$id = gen_uuid();
	$sql = "INSERT INTO sale_order_product_delivery(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", quantity";
	$sql = $sql.", order_product_id";
	$sql = $sql.", type_id";
	$sql = $sql.", unit_price";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
	$sql = $sql.", 0";
	$sql = $sql.", 1";
	$sql = $sql.", '".$order_product_id."'";
	$sql = $sql.", '".$type_id."'";
	$sql = $sql.", 0";
	$sql = $sql.")";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "delToDelivery")
{
	$id = '';
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}	
	$sql = "UPDATE sale_order_product_delivery SET status =1, write_date=NOW() WHERE id ='".$id."'";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "saveOrderProductTypePrice")
{
	$id = '';
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}
	$unit_price = 0;
	if(isset($_REQUEST['unit_price']))
	{
		$unit_price = $_REQUEST['unit_price'];
	}	
	$sql = "UPDATE sale_order_product_delivery SET unit_price =".$unit_price.", write_date=NOW() WHERE id ='".$id."'";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "payment")
{
	$order_product_id = '';
	if(isset($_REQUEST['order_product_id']))
	{
		$order_product_id = $_REQUEST['order_product_id'];
	}
	$index = '';
	if(isset($_REQUEST['index']))
	{
		$index = $_REQUEST['index'];
	}
	
	$sql = "SELECT d1.id, d1.category_name FROM res_payment_category d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id= d2.id) WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d1.company_id ='".$LOGIN_PARENT_COMPANY_ID."') ";
	
	$sql = $sql." ORDER BY d1.category_name ASC";
	$result_type = pg_exec($db, $sql);
	$numrows_type = pg_numrows($result_type);

	$sql = "SELECT d1.id, d1.payment_name FROM res_payment d1 LEFT OUTER JOIN res_company d2 ON(d1.company_id= d2.id) WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d1.company_id ='".$LOGIN_PARENT_COMPANY_ID."') ";
	
	$sql = $sql." ORDER BY d1.payment_name ASC";
	$result_payment = pg_exec($db, $sql);
	$numrows_payment = pg_numrows($result_payment);	
	
	$sql = "SELECT d1.id, d1.receipt_no, d1.receipt_date, d2.payment_name, d3.category_name, d1.amount, d1.description FROM line_payment d1 LEFT OUTER JOIN res_payment d2 ON(d1.payment_id = d2.id) LEFT OUTER JOIN res_payment_category d3 ON(d1.category_id = d3.id) WHERE d1.status =0 AND d1.rel_id='".$order_product_id."'";
	
	$sql = $sql." ORDER BY d1.receipt_date ASC";
	
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
?>
<div class="row">
	<div class="col-sm-5 ">
	Loại thanh toán:
		<select class="form-control" id="editPaymentCategory<?php echo $index;?>">
				
		<?php
		for($k =0; $k<$numrows_type; $k++)
		{
			
			$row = pg_fetch_array($result_type, $k);
			$id = $row["id"];
			$name = $row["category_name"];
		?>
			<option value="<?php echo $id;?>"><?php echo $name;?></option>
		<?php
			
		}
		?>
		</select>
	</div>
	<div class="col-sm-5 ">
	Hình thức thanh toán:
		<select class="form-control" id="editPayment<?php echo $index;?>">
				
		<?php
		for($k =0; $k<$numrows_payment; $k++)
		{
			
			$row = pg_fetch_array($result_payment, $k);
			$id = $row["id"];
			$name = $row["payment_name"];
		?>
			<option value="<?php echo $id;?>"><?php echo $name;?></option>
		<?php
			
		}
		?>
		</select>
	</div>
	<div class="col-sm-2 ">
		<br>
		<a href="javascript:addPayment('<?php echo $order_product_id; ?>', '<?php echo $index; ?>')">+ Thêm</a>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		
				
				<br>
				<div class="table-responsive">
				  <table class="table table-bordered nobottommargin">
					<thead>
					  <tr>
						<th width="30">#</th>
						<th width="80" nowrap="nowrap" align="center">Số</th>
						<th width="80" nowrap="nowrap" align="center">Ngày</th>
						<th width="100" nowrap="nowrap" align="center">Loại</th>
						<th width="100" nowrap="nowrap" align="center">Hình thức</th>
						<th width="100" nowrap="nowrap" align="center">Số tiến</th>
						<th nowrap="nowrap" align="center">Ghi chú</th>
						<th nowrap="nowrap" width="30"></th>
					  </tr>
					</thead>
				<?php
				
				$total = 0;
				for($k =0; $k<$numrows; $k++)
				{
					$row = pg_fetch_array($result, $k);
					$id = $row["id"];
					$category_name = $row["category_name"];
					$payment_name = $row["payment_name"];
					$receipt_no = $row["receipt_no"];
					$receipt_date = $row["receipt_date"];
					if($receipt_date != "")
					{
						$firstIndex = stripos($receipt_date, " ");
						if($firstIndex != -1)
						{
							$receipt_date = substr($receipt_date, 0, $firstIndex);
							$arr = explode("-", $receipt_date);
							if(count($arr)>2)
							{
								$receipt_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
							}
						}
					}
					
					$amount = $row["amount"];
					$description = $row["description"];
					
					$total = $total + $amount;
				?>
				<tr>
			<td><?php echo $k + 1; ?></td>
			<td ><?php echo $receipt_no; ?></td>
			
			<td ><?php echo $receipt_date; ?></td>
			<td style="text-align:right"><?php echo $category_name; ?></td>
			<td style="text-align:right"><?php echo $payment_name; ?></td>
			<td style="text-align:right; width:160px" ><input type="text" style="text-align:right" class="form-control" onblur="savePaymentAmount(this, '<?php echo $id; ?>', '<?php echo $order_product_id; ?>', '<?php echo $index; ?>');" value="<?php echo $amount;?>"></td>
			<td  style="text-align:right" ><?php echo $description; ?></td>
			<td width="30"><a href="javascript:delPayment('<?php echo $id; ?>', '<?php echo $order_product_id; ?>', '<?php echo $index; ?>')">  - </a></td>
		</tr>
		
				
				<?php 

				} 
				?>
					<tr>
			<td colspan="5"><b>Tổng thanh toán</b></td>
			
			<td style="text-align:right" ><b><?php echo double_format($total); ?></b></td>
			<td ></td>
			<td width="30"></td>
			</tr>
				</table>
			</div>
	</div>
	

</div>
<?php
}else if($ac == "addPayment")
{
	$category_id = '';
	if(isset($_REQUEST['category_id']))
	{
		$category_id = $_REQUEST['category_id'];
	}
	$payment_id = '';
	if(isset($_REQUEST['payment_id']))
	{
		$payment_id = $_REQUEST['payment_id'];
	}
	$rel_id = '';
	if(isset($_REQUEST['rel_id']))
	{
		$rel_id = $_REQUEST['rel_id'];
	}
	$receipt_no =findReceiptNo($db, $LOGIN_COMPANY_ID."line_payment");
	$id = gen_uuid();
	$sql = "INSERT INTO line_payment(";
	$sql = $sql."id";
	$sql = $sql.", create_date";
	$sql = $sql.", write_date";
	$sql = $sql.", company_id";
	$sql = $sql.", status";
	$sql = $sql.", payment_id";
	$sql = $sql.", category_id";
	$sql = $sql.", amount";
	$sql = $sql.", receipt_no";
	$sql = $sql.", receipt_date";
	$sql = $sql.", rel_id";
	$sql = $sql." )VALUES(";
	$sql = $sql."'".$id."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$LOGIN_COMPANY_ID."'";
	$sql = $sql.", 0";
	$sql = $sql.", '".$payment_id."'";
	$sql = $sql.", '".$category_id."'";
	$sql = $sql.", 0";
	$sql = $sql.", '".$receipt_no."'";
	$sql = $sql.", NOW()";
	$sql = $sql.", '".$rel_id."'";
	$sql = $sql.")";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "delPayement")
{
	$id = '';
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}	
	$sql = "UPDATE line_payment SET status =1, write_date=NOW() WHERE id ='".$id."'";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "savePaymentAmount")
{
	$id = '';
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}
	$amount = 0;
	if(isset($_REQUEST['amount']))
	{
		$amount = $_REQUEST['amount'];
	}	
	$sql = "UPDATE line_payment SET amount =".$amount.", write_date=NOW() WHERE id ='".$id."'";
	$result = pg_exec($db, $sql);
	echo "OK";
}else if($ac == "mrp_workorder_location")
{
	$rel_id = '';
	if(isset($_REQUEST['rel_id']))
	{
		$rel_id = $_REQUEST['rel_id'];
	}
	$index = '';
	if(isset($_REQUEST['index']))
	{
		$index = $_REQUEST['index'];
	}
		
	$sql = "SELECT d1.id, d2.name, d1.polygon FROM mrp_workorder_location d1 LEFT OUTER JOIN mrp_location d2 ON(d1.location_id = d2.id) WHERE d1.status =0 AND d1.rel_id='".$rel_id."' ORDER BY d1.create_date ASC";
	
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	
	?>
	
	<div class="table-responsive">
	  <table class="table table-bordered nobottommargin">
		<thead>
		  <tr>
			<th width="30">#</th>
			<th nowrap="nowrap" align="center" width="120">Tên</th>
		
			<th style="width:400px" nowrap="nowrap" align="center">Vị trí</th>
			<th nowrap="nowrap" width="30"></th>
		  </tr>
		</thead>
		<tbody>
		<?php
		
		
		for($i =0; $i<$numrows; $i++)
		{
			$row = pg_fetch_array($result, $i);
			
			$id = $row["id"];
			$name = $row["name"];
			$polygon = $row["polygon"];
			$arr = explode(';', $polygon);
			$polygons = array();
			for($i =0; $i<count($arr); $i++)
			{
				$arr1 = explode(',', $arr[$i]);
				if(count($arr1)>1)
				{
					$polygons[$i]= array($arr1[0], $arr1[1]);
				}
				
			}
			if(count($polygons)>0)
			{
				$polygons[count($polygons)] = $polygons[0];
			}
			
			
			
		?>
		<tr>
			<td><?php echo $i + 1; ?></td>
			<td><?php echo $name; ?></td>
			<td width="30"><a href="javascript:delLocation('<?php echo $id; ?>', '<?php echo $rel_id; ?>', '<?php echo $index; ?>')">Xóa</a></td>
		</tr>
		<tr>
			<td colspan="3">
			<div id="area<?php echo $i; ?>"></div>
			<div id="map<?php echo $i; ?>" style="100%; height:300px"></div>
			</td>
		</tr>
		<script>
			
			var bounds<?php echo $i; ?> = new google.maps.LatLngBounds();
			var polygonCoords<?php echo $i; ?> = [
			  <?php 
			  for($j=0; $j<count($polygons); $j++)
			  {
				  if($j>0)
				  {
						echo ', ';
				  }
				?>
				new google.maps.LatLng(<?php echo $polygons[$j][0];?>, <?php echo $polygons[$j][1];?>)
				<?php
			  }
			  ?>
			 
			];

			for (var i = 0; i < polygonCoords<?php echo $i; ?>.length; i++) {
			  bounds<?php echo $i; ?>.extend(polygonCoords<?php echo $i; ?>[i]);
			}
			var centerPoint = bounds<?php echo $i; ?>.getCenter();

			if(polygonCoords<?php echo $i; ?>.length == 0)
			{
				centerPoint = new google.maps.LatLng(21.028511, 105.804817);
			}
			var area = google.maps.geometry.spherical.computeArea(polygonCoords<?php echo $i; ?>);
			area = area/10000;
			document.getElementById('area<?php echo $i; ?>').innerHTML = parseFloat(area).toFixed(2) + " (ha)";
			var map<?php echo $i; ?> = new google.maps.Map(document.getElementById('map<?php echo $i; ?>'), {
				zoom: 18,
				center: centerPoint
			  }
			  
			  );
			  var marker<?php echo $i; ?> = new google.maps.Marker({
				  position: centerPoint,
				  map: map<?php echo $i; ?>
				});
			  var cascadiaFault<?php echo $i; ?> = new google.maps.Polyline({
			  
				strokeColor: '#e91e63',
				strokeOpacity: 1.0,
				strokeWeight: 3,
		
				path: [
				  <?php 
				  for($j=0; $j<count($polygons); $j++)
				  {
					  if($j>0)
					  {
							echo ', ';
					  }
					?>
					new google.maps.LatLng(<?php echo $polygons[$j][0];?>, <?php echo $polygons[$j][1];?>)
					<?php
				  }
				  ?>
				]
			  });
			  cascadiaFault<?php echo $i; ?>.setMap(map<?php echo $i; ?>);
			
			
			
		</script>
		
		<?php
		}
		?>
		
		
	</tbody>
</table>
</div>
	<?php
}else if($ac == "delLocation")
{
	$id = '';
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}	
	$sql = "UPDATE mrp_workorder_location SET status =1, write_date=NOW() WHERE id ='".$id."'";
	$result = pg_exec($db, $sql);
	echo "OK";
}
?>