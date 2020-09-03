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
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('Orders');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item" ><a href="<?php echo URL;?><?php echo $lang; ?>/order"><?php echo __('Orders');?></a></li>
			
		</ol>
	</div>

</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		<div class="container clearfix">
		
		
	<?php
		$ac = "new";
		$order_id = "";
		$planning_id = "";
		$back = "";
		
		if($uri !='/' && $uri != '')
		{
			$items = explode("/", substr($uri, 1));
			if(count($items)> 2)
			{
				$ac = $items[2];
				if($ac == "edit")
				{
					$order_id = $items[3];
				}else if($ac == "planning")
				{
					$order_id = $items[3];
					$planning_id = $items[4];
					$back = $items[5];
				}
			}
		}
		if($ac == "planning")
		{
			$receipt_no = "";
			$start_date = "";
			$end_date = "";
			$quantity = 1;
			$sql = "SELECT d1.id, d1.receipt_no, d1.start_date, d1.end_date, d1.quantity FROM mrp_workorder_planning d1 WHERE d1.id = '".$planning_id."' AND d1.status =0";
			
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$receipt_no = $row["receipt_no"];
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
				$end_date = $row["end_date"];
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
				$quantity = $row["quantity"];
				
			}
			
		?>
		<a class="button button-3d  nomargin" href="javascript:doSavePlanning()" value="register"><?php echo __('Save');?></a> 
		<a class="button button-3d button-black nomargin" target="_blank" href="<?php echo URL;?>includes/export_planning.php?ac=export&id=<?php echo $planning_id;?>"><?php echo __('Export');?></a> 
		<a class="button button-3d button-black nomargin"  target="_blank" href="<?php echo URL;?>includes/export_planning.php?ac=import&id=<?php echo $planning_id;?>"><?php echo __('Import');?></a> 
		<?php if($back == "order"){?>
		<a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/orderform/edit/<?php echo $order_id;?>"><?php echo __('Back');?></a> 
		<?php
		}else
		{
		?>
		<a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>"><?php echo __('Back');?></a> 
		<?php
		}
		?>
		<br>
		<div class="col-sm-12 form-group">
			<div class="row">
				<div class="col-sm-3 col-form-label">
					<label for="editcode"><?php echo __('Receipt No');?></label>
					<input type="text" autocomplete="off" id="editreceipt_no" name="editreceipt_no" value="<?php echo $receipt_no; ?>" class="form-control" maxlength = "64" />
				</div>
				<div class="col-sm-3">
					<label for="editstart_date"><?php echo __('Start Date');?></label>
					<input type="text" id="editstart_date" name="editstart_date" value="<?php echo $start_date; ?>" class="form-control" />
				</div>
				<div class="col-sm-3">
					<label for="editend_date"><?php echo __('End Date');?></label>
					<input type="text" autocomplete="off" id="editend_date" name="editend_date" value="<?php echo $end_date; ?>" class="form-control" />
				</div>
				<div class="col-sm-3">
					<label for="editquantity"><?php echo __('Quantity');?></label>
					<input type="text" autocomplete="off" id="editquantity" name="editquantity" value="<?php echo $quantity; ?>" class="form-control" />
				</div>
			</div>
		</div>
		<div class="col-sm-12 form-group">
			<div class="row">
				<div class="col-sm-12" id="pnPlanning">
				</div>
			</div>
		</div>
		<div id="pnFullDialog" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-body">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">Nhân viên</h4>
						<button type="button" id="btnClose" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body" id="pnFullDialogContent">
						
					</div>
				</div>
			</div>
		</div>
	</div>
			<script src="<?php echo URL;?>assets/datepicker/foopicker.js"></script>			
		<script>
			var sdate = new FooPicker({
			
				id: 'editstart_date',
			
				dateFormat: 'dd/MM/yyyy'
			
			});
			var edate = new FooPicker({
			
				id: 'editend_date',
			
				dateFormat: 'dd/MM/yyyy'
			
			});


			function loadPlanning()
			{
				var _url = '<?php echo URL;?>includes/planninglist.php?planning_id=<?php echo $planning_id;?>';
				loadPage('pnPlanning', _url, function(status, message)
				{
					if(status== 0)
					{
						
					}
					
				}, false);
			}
			loadPlanning();
			function doSavePlanning()
			{
				var _url = '<?php echo URL;?>includes/action.php?ac=doSavePlanning';
				_url = _url + '&planning_id=<?php echo $planning_id;?>';
				
				var ctr = document.getElementById('editreceipt_no');
				var receipt_no = ctr.value;
				ctr = document.getElementById('editstart_date');
				var start_date = ctr.value;
				
				if(start_date != "")
				{
					
					var arr = start_date.split("/")
					start_date = "";
					if(arr.length>2)
					{
						start_date = arr[2] + "-" + arr[1] + "-" + arr[0] + " 00:00:00";
					}
				}
				ctr = document.getElementById('editend_date');
				var end_date = ctr.value;
				if(end_date != "")
				{
					
					var arr = end_date.split("/")
					end_date = "";
					if(arr.length>2)
					{
						end_date = arr[2] + "-" + arr[1] + "-" + arr[0] + " 23:59:59";
					}
				}
				
				ctr = document.getElementById('editquantity');
				var quantity = ctr.value;
				
				_url = _url + '&receipt_no=' + encodeURIComponent(receipt_no);
				_url = _url + '&start_date=' + encodeURIComponent(start_date);
				_url = _url + '&end_date=' + encodeURIComponent(end_date);
				_url = _url + '&quantity=' + encodeURIComponent(quantity);
				
				loadPage('gotoTop', _url, function(status, message)
				{
					if(status== 0)
					{
						if(message == "OK")
						{
							
							document.location.href ='<?php echo URL;?><?php echo $lang; ?>/orderform/edit/<?php echo $order_id;?>';
						}
						else{
							alert(message);
						}
					}
					
				}, true);
			}
			var routing_id = "";
			function addPlanningEmployee(id)
			{
				
				routing_id = id;
				var _url = '<?php echo URL;?>includes/employeelist.php?func=selEmployee';
				loadPage('pnFullDialogContent', _url, function(status, message)
				{
					if(status== 0)
					{
						$("#pnFullDialog").modal();
					}
					
				}, false);
				
				
			}
			function selEmployee(employee_id)
			{
				document.getElementById("btnClose").click();
				
				var _url = '<?php echo URL;?>includes/action.php?ac=addPlanningEmployee&planning_id=<?php echo $planning_id;?>';
				_url = _url + "&employee_id=" + employee_id;
				_url = _url + "&routing_id=" + routing_id;
				var quantity = document.getElementById("editquantity").value;
				_url = _url + "&quantity=" + encodeURIComponent(quantity);
				
				loadPage('pnPlanning', _url, function(status, message)
				{
					if(status== 0)
					{
						if(message == "OK")
						{
							loadPlanning();
						}else{
							alert(message);
						}
						
					}
					
				}, true);
			}
			function delRoutingEmployee(id)
			{
				var result = confirm("<?php echo __('Want to delete?');?>");
				if (!result) {
					return;
				}
				var _url = '<?php echo URL;?>includes/action.php?ac=delRoutingEmployee';
				_url = _url + '&id=' + id;
				
				
				loadPage('gotoTop', _url, function(status, message)
				{
					if(status== 0)
					{
						if(message == "OK")
						{
							
							loadPlanning();
						}
						else{
							alert(message);
						}
					}
					
				}, true);
			}
			function savePlanningEmployee(id, theInput, name)
			{
				var _url = '<?php echo URL;?>includes/action.php?ac=saveChanged';
				_url = _url + '&id=' + id;
				_url = _url + '&t=mrp_workorder_planning_employee';
				_url = _url + '&c=' + name;
				_url = _url + '&v=' + encodeURIComponent(theInput.value);
				
				loadPage('gotoTop', _url, function(status, message)
				{
					if(status== 0)
					{
						if(message == "OK")
						{
						
						}
						else{
							alert(message);
						}
					}
					
				}, true);
			}
			
		</script>
		
		<?php
		}
		else
		{
			
		
		$receipt_no = "";
		$receipt_date = "";
		$delivery_date = "";
		$company_id = "";
		if($order_id != "")
		{
			$sql = "SELECT d1.receipt_no, d1.receipt_date, d1.company_id, d1.delivery_date FROM sale_order d1 LEFT OUTER JOIN customer d2 ON(d1.customer_id = d2.id) LEFT OUTER JOIN res_partner d3 ON(d2.partner_id = d3.id) WHERE d1.status =0 AND d1.id='".$order_id."'";
			
			
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
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
				$delivery_date = $row["delivery_date"];
				if($delivery_date != "")
				{
					$firstIndex = stripos($delivery_date, " ");
					if($firstIndex != -1)
					{
						$delivery_date = substr($delivery_date, 0, $firstIndex);
						$arr = explode("-", $delivery_date);
						if(count($arr)>2)
						{
							$delivery_date = $arr[2]."/". + $arr[1]."/". + $arr[0];
						}
					}
				}
				
				$company_id = $row["company_id"];
				
			}
		}
		
		
		?>
		
		<a class="button button-3d  nomargin" href="javascript:doSaveOrder()" value="register"><?php echo __('Save');?></a> 
						
		<a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/order"><?php echo __('Back');?></a> 
		
		<br><br>
		<form id="frmPrivateProduct" name="frmPrivateProduct" class="nobottommargin" >
			<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-4 col-form-label">
							<label for="editcode"><?php echo __('Receipt No');?></label>
							<input type="text" autocomplete="off" id="editreceipt_no" name="editreceipt_no" value="<?php echo $receipt_no; ?>" class="form-control" maxlength = "64" />
						</div>
						<div class="col-sm-4">
							<label for="editreceipt_date"><?php echo __('Receipt Date');?></label>
							<input type="text" autocomplete="off" id="editreceipt_date" name="editreceipt_date" value="<?php echo $receipt_date; ?>" class="form-control" />
						</div>
						<div class="col-sm-4">
							<label for="editreceipt_date"><?php echo __('Delivery Date');?></label>
							<input type="text" autocomplete="off" id="editdelivery_date" name="editdelivery_date" value="<?php echo $delivery_date; ?>" class="form-control" />
						</div>
					</div>
				</div>
				<div class="col-sm-12 form-group">
					<div class="row">
						
						<div class="col-sm-12">
							<label for="editreceipt_date"><?php echo __('Customer Name');?></label>
							<div  class="autocomplete" style="width:100%">
							<?php
							$sql = "SELECT d1.id, d2.partner_code, d2.partner_name FROM res_company d1 LEFT OUTER JOIN res_partner d2 ON(d1.partner_id = d2.id) WHERE d1.status =0 AND (d1.company_id='".$LOGIN_COMPANY_ID."' OR d1.parent_id ='".$LOGIN_COMPANY_ID."') ORDER BY d2.partner_code ASC";
						
								$result_cust = pg_exec($db, $sql);
								$numrows_cust = pg_numrows($result_cust);	
								$customerList = array();
								for($i = 0; $i < $numrows_cust; $i++) 
								{
									$row = pg_fetch_array($result_cust, $i);
									$arr = array();
									$arr[0] = $row["id"];
									$arr[1] = $row["partner_code"];
									$arr[2] = $row["partner_name"];
									$customerList[$i] = $arr;
								}
								
							?>
							<select type="text" id="editcompany_id"  class="form-control" style="width:100%">
							<?php
							for($k =0; $k<count($customerList); $k++)
							{
								$arr = $customerList[$k];
								$cust_id = $arr[0];
								$cust_code = $arr[1];
								$cust_name = $arr[2];
								
							?>
								<option value="<?php echo $cust_id;?>" <?php if($cust_id == $company_id){?> selected <?php }?>><?php echo $cust_code;?>. <?php echo $cust_name;?></option>
							<?php
								
							}
							?>
						
							</select>
							</div>
						</div>
					</div>
				</div>
				
		
			<?php if($order_id != "")
			{
			?>
			<div class="col-sm-12 form-group">
					<div class="row">
						<div class="col-sm-12 ">
							<label for="editvalue_1_2"><?php echo __('Products');?>:  <a href="javascript:showProductList()">+ Thêm</a></label>
							<br>
							<div id="pnProducts"></div>
						</div>
					</div>
			</div>
			<?php
			}
			?>
			
			<div class="col_full">
				<a class="button button-3d  nomargin" href="javascript:doSaveOrder()" value="register"><?php echo __('Save');?></a> 
				<a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/order"><?php echo __('Back');?></a> 
						 					
			</div>
			<div class="col_full" style="text-align:right">
				<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
			</div>
					
		</form>
					
		</div>

	</div>

	<script src="<?php echo URL;?>assets/datepicker/foopicker.js"></script>
	<script>
	
	
		var sdate = new FooPicker({
			
				id: 'editreceipt_date',
			
				dateFormat: 'dd/MM/yyyy'
			
			});
			var edate = new FooPicker({
			
				id: 'editdelivery_date',
			
				dateFormat: 'dd/MM/yyyy'
			
			});


		function doSaveOrder()
		{
			var _url = '<?php echo URL;?>includes/action.php?ac=doSaveOrder';
			_url = _url + '&order_id=<?php echo $order_id;?>';
			
			var ctr = document.getElementById('editreceipt_no');
			var receipt_no = ctr.value;
			ctr = document.getElementById('editreceipt_date');
			var receipt_date = ctr.value;
			if(receipt_date != "")
			{
				
				var arr = receipt_date.split("/")
				receipt_date = "";
				if(arr.length>2)
				{
					receipt_date = arr[2] + "-" + arr[1] + "-" + arr[0];
				}
			}
			ctr = document.getElementById('editdelivery_date');
			var delivery_date = ctr.value;
			if(delivery_date != "")
			{
				
				var arr = delivery_date.split("/")
				delivery_date = "";
				if(arr.length>2)
				{
					delivery_date = arr[2] + "-" + arr[1] + "-" + arr[0];
				}
			}
			
			ctr = document.getElementById('editcompany_id');
			var company_id = ctr.value;
			_url = _url + '&receipt_no=' + encodeURIComponent(receipt_no);
			_url = _url + '&receipt_date=' + encodeURIComponent(receipt_date);
			_url = _url + '&delivery_date=' + encodeURIComponent(delivery_date);
			_url = _url + '&company_id=' + encodeURIComponent(company_id);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message.length == 36)
					{
						
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/orderform/edit/' + message;
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function loadProduct()
		{
			var _url = '<?php echo URL;?>includes/productlist.php?order_id=<?php echo $order_id;?>';
			loadPage('pnProducts', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		function showProductList()
		{
			var _url = '<?php echo URL;?>includes/productlist.php?ac=productlist&func=addProduct';
			openPopup(_url);
		}
		function addProduct(product_id)
		{
				closePopup();
			var _url = '<?php echo URL;?>includes/action.php?ac=addOrderProduct';
			_url = _url + '&order_id=<?php echo $order_id;?>&product_id=' + product_id;
			
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
		function saveOrderProduct(id, theInput, name)
		{
			var _url = '<?php echo URL;?>includes/action.php?ac=saveOrderProduct';
			_url = _url + '&id=' + id;
			_url = _url + '&name=' + name;
			_url = _url + '&value=' + encodeURIComponent(theInput.value);
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function delOrderProduct(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delOrderProduct';
			_url = _url + '&id=' + id;
			
			
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
		
		function loadRouting(order_product_id, index)
		{
			var _url = '<?php echo URL;?>includes/productlist.php?ac=routing&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&index=' + index;
	
			loadPage('pnRouting' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		function showRouting(work_routing_id, index)
		{
			var _url = '<?php echo URL;?>includes/productlist.php?ac=workorder_routing&id=' + work_routing_id + '&index=' + index;
			openPopup(_url);
		}
		function saveWorkOrderRouting(id, order_product_id, index)
		{
			var ctr = document.getElementById('editrouting_start_date');
			var start_date = ctr.value;
			ctr = document.getElementById('editrouting_end_date');
			var end_date = ctr.value;
			ctr = document.getElementById('editrouting_description');
			var description = ctr.value;
			var _url = '<?php echo URL;?>includes/productlist.php?ac=save_workorder_routing&id=' + id;
			_url = _url + '&start_date=' + encodeURIComponent(start_date);
			_url = _url + '&end_date=' + encodeURIComponent(end_date);
			_url = _url + '&description=' + encodeURIComponent(description);
			loadPage('pnRouting' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						closePopup();
						loadRouting(order_product_id, index);
						
					}else{
						alert(message);
					}
				}
				
			}, true);
			
		}
		
		function addRouting(order_product_id, index)
		{
			var name = prompt("Please enter your name", "");
			if (name == null || name == "") {
			  return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=addRouting';
			_url = _url + '&name=' + encodeURIComponent(name);
			_url = _url + '&order_product_id=' + order_product_id;
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadRouting(order_product_id, index);
					}
					else{
						alert(message);
					}
				}
				
			}, true);
			
		}
		
		function delRouting(routing_id, order_product_id, index)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delRouting';
			_url = _url + '&id=' + routing_id;
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadRouting(order_product_id, index);
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function addPlanning(order_product_id, index)
		{
			var name = prompt("Please enter your name", "");
			if (name == null || name == "") {
			  return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=addPlanning';
			_url = _url + '&receipt_no=' + encodeURIComponent(name);
			_url = _url + '&order_product_id=' + order_product_id;
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message.length == 36)
					{
						
						planning(message);
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function loadPlanning(order_product_id, index)
		{
			var _url = '<?php echo URL;?>includes/productlist.php?ac=planning&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&index=' + index;
			
			loadPage('pnPlanning' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		function planning(planning_id)
		{
			document.location.href ='<?php echo URL;?><?php echo $lang; ?>/orderform/planning/<?php echo $order_id;?>/' + planning_id +'/order';
		}
		
		function delPlanning(id, order_product_id, index)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/action.php?ac=delPlanning';
			_url = _url + '&id=' + id;
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadPlanning(order_product_id, index);
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function loadProductType(order_product_id, index)
		{
			var _url = '<?php echo URL;?>includes/productlist.php?ac=product_type&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&index=' + index;
			
			loadPage('pnProductType' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		
		function addToDelivery(order_product_id, index)
		{
			
			var type_id = document.getElementById('editproductType' + index).value;
			if(type_id == '')
			{
				alert("Select type to add");
				return;
			}
			var _url = '<?php echo URL;?>includes/productlist.php?ac=addToDelivery&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&type_id=' + type_id;
			
			loadPage('pnProductType' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						loadProductType(order_product_id, index);
					}else
					{
						alert(message);
					}
					
				}
				
			}, true);
		}
		function delToDelivery(id, order_product_id, index)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/productlist.php?ac=delToDelivery&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&id=' + id;
			
			loadPage('pnProductType' + index, _url, function(status, message)
			{
				if(message == "OK")
				{
					loadProductType(order_product_id, index);
				}else
				{
					alert(message);
				}
				
			}, true);
		}
		
		function loadPayment(order_product_id, index)
		{
			var _url = '<?php echo URL;?>includes/productlist.php?ac=payment&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&index=' + index;
			
			loadPage('pnPayment' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		
		function loadLocation(order_product_id, index)
		{
			var _url = '<?php echo URL;?>includes/productlist.php?ac=mrp_workorder_location&order_id=<?php echo $order_id;?>&rel_id='+ order_product_id + '&index=' + index;
			
			loadPage('pnLocation' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		function delLocation(id, order_product_id, index)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/productlist.php?ac=delLocation&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&id=' + id;
			
			loadPage('pnProductType' + index, _url, function(status, message)
			{
				if(message == "OK")
				{
					loadLocation(order_product_id, index);
				}else
				{
					alert(message);
				}
				
			}, true);
		}
		
		function addPayment(order_product_id, index)
		{
			
			var category_id = document.getElementById('editPaymentCategory' + index).value;
			if(category_id == '')
			{
				alert("Select type to add");
				return;
			}
			var payment_id = document.getElementById('editPayment' + index).value;
			if(payment_id == '')
			{
				alert("Select type to payment");
				return;
			}
			var _url = '<?php echo URL;?>includes/productlist.php?ac=addPayment&order_id=<?php echo $order_id;?>&rel_id='+ order_product_id + '&category_id=' + category_id;
			_url = _url + '&payment_id=' + payment_id;
			loadPage('pnProductType' + index, _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						loadPayment(order_product_id, index);
					}else
					{
						alert(message);
					}
					
				}
				
			}, true);
		}
		function delPayement(id, order_product_id, index)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/productlist.php?ac=delPayement&order_id=<?php echo $order_id;?>&order_product_id='+ order_product_id + '&id=' + id;
			
			loadPage('pnProductType' + index, _url, function(status, message)
			{
				if(message == "OK")
				{
					loadPayment(order_product_id, index);
				}else
				{
					alert(message);
				}
				
			}, true);
		}
		
		function savePaymentAmount(theInput, id, order_product_id, index)
		{
			var amount = theInput.value;
			if(amount == '')
			{
				amount = 0;
			}
			var _url = '<?php echo URL;?>includes/productlist.php?ac=savePaymentAmount&id=' + id;
			_url = _url + '&amount=' + encodeURIComponent(amount);
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadPayment(order_product_id, index);
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		
		loadProduct();
		
		function autocomplete(inp, arr) {
		  /*the autocomplete function takes two arguments,
		  the text field element and an array of possible autocompleted values:*/
		  var currentFocus;
		  /*execute a function when someone writes in the text field:*/
		  inp.addEventListener("input", function(e) {
			  var a, b, i, val = this.value;
			  /*close any already open lists of autocompleted values*/
			  closeAllLists();
			  if (!val) { return false;}
			  currentFocus = -1;
			  /*create a DIV element that will contain the items (values):*/
			  a = document.createElement("DIV");
			  a.setAttribute("id", this.id + "autocomplete-list");
			  a.setAttribute("class", "autocomplete-items");
			  /*append the DIV element as a child of the autocomplete container:*/
			  this.parentNode.appendChild(a);
			  /*for each item in the array...*/
			  for (i = 0; i < arr.length; i++) {
				/*check if the item starts with the same letters as the text field value:*/
				if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
				  /*create a DIV element for each matching element:*/
				  b = document.createElement("DIV");
				  /*make the matching letters bold:*/
				  b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
				  b.innerHTML += arr[i].substr(val.length);
				  /*insert a input field that will hold the current array item's value:*/
				  b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
				  /*execute a function when someone clicks on the item value (DIV element):*/
					  b.addEventListener("click", function(e) {
					  /*insert the value for the autocomplete text field:*/
					  inp.value = this.getElementsByTagName("input")[0].value;
					  
					  /*close the list of autocompleted values,
					  (or any other open lists of autocompleted values:*/
					  closeAllLists();
					  var id = inp.id;
					  var production_id = '';
					  var index = id.indexOf("_");
					  if(index != -1)
					  {
						  production_id = id.substring(index + 1);
					  }
					  id = id.replace('expression', 'test_method');
					  savePrivateProduct(inp.name, inp, "expression", production_id);
					  
				  });
				  a.appendChild(b);
				}
			  }
		  });
		  /*execute a function presses a key on the keyboard:*/
		  inp.addEventListener("keydown", function(e) {
			  var x = document.getElementById(this.id + "autocomplete-list");
			  if (x) x = x.getElementsByTagName("div");
			  if (e.keyCode == 40) {
				/*If the arrow DOWN key is pressed,
				increase the currentFocus variable:*/
				currentFocus++;
				/*and and make the current item more visible:*/
				addActive(x);
			  } else if (e.keyCode == 38) { //up
				/*If the arrow UP key is pressed,
				decrease the currentFocus variable:*/
				currentFocus--;
				/*and and make the current item more visible:*/
				addActive(x);
			  } else if (e.keyCode == 13) {
				/*If the ENTER key is pressed, prevent the form from being submitted,*/
				e.preventDefault();
				if (currentFocus > -1) {
				  /*and simulate a click on the "active" item:*/
				  if (x) x[currentFocus].click();
				}
			  }
		  });
		  function addActive(x) {
			/*a function to classify an item as "active":*/
			if (!x) return false;
			/*start by removing the "active" class on all items:*/
			removeActive(x);
			if (currentFocus >= x.length) currentFocus = 0;
			if (currentFocus < 0) currentFocus = (x.length - 1);
			/*add class "autocomplete-active":*/
			x[currentFocus].classList.add("autocomplete-active");
		  }
		  function removeActive(x) {
			/*a function to remove the "active" class from all autocomplete items:*/
			for (var i = 0; i < x.length; i++) {
			  x[i].classList.remove("autocomplete-active");
			}
		  }
		  function closeAllLists(elmnt) {
			/*close all autocomplete lists in the document,
			except the one passed as an argument:*/
			var x = document.getElementsByClassName("autocomplete-items");
			for (var i = 0; i < x.length; i++) {
			  if (elmnt != x[i] && elmnt != inp) {
			  x[i].parentNode.removeChild(x[i]);
			}
		  }
		}
		/*execute a function when someone clicks in the document:*/
		document.addEventListener("click", function (e) {
			closeAllLists(e.target);
		});
		}
		
		
		<?php
		$sql = "SELECT d1.id, d1.name FROM product_unit d1 WHERE d1.status =0 AND d1.company_id='".$LOGIN_COMPANY_ID."' ORDER BY d1.name ASC";
		
		$result1 = pg_exec($db, $sql);
		$numrows1 = pg_numrows($result1);	
			
		?>
		var units = [
		<?php
		for($j =0; $j<$numrows1; $j++)
		{
			$row = pg_fetch_array($result1, $j);
			$name = $row["name"];
			if($j>0)
			{
				echo ",";
			}
			echo "'".$name."'";
		} 
		?>
		];
		
		
	</script>
	<?php } ?>
