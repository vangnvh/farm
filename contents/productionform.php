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
		<h1><?php echo __('SOP');?></h1>
		
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item ><a href="<?php echo URL;?><?php echo $lang; ?>/production"><?php echo __('SOP');?></a></li>
			
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
		$id = "";
		if($uri !='/' && $uri != '')
		{
			$items = explode("/", substr($uri, 1));
			if(count($items)> 2)
			{
				$ac = $items[2];
				if($ac == "edit")
				{
					$id = $items[3];
				}else if($ac == "clone")
				{
					$id = $items[3];
					
				}
			}
		}
		
		
		$name = '';
		
		if($id != "")
		{
			$sql = "SELECT d1.id, d1.name FROM mrp_production d1 WHERE d1.status =0 AND d1.id='".$id."'";
			$result = pg_exec($db, $sql);
			$numrows = pg_numrows($result);
			if($numrows>0)
			{
				$row = pg_fetch_array($result, 0);
				$name = $row["name"];
				$id = $row["id"];

			}
		}else
		{
			$id= gen_uuid();
		}
			
		
		?>
		<form id="frmRegister" name="frmRegister" class="nobottommargin" >

					<div class="col_full">
						<label for="editname"><?php echo __('Name');?> <span style="color:red"><small >*</small></span></label>
						<input type="text" autocomplete="off" id="editname" name="editname" value="<?php echo $name; ?>" class="form-control" maxlength = "250" />
					</div>
					<div class="col-sm-12 form-group">
							<div class="row">
								<div class="col-sm-12 ">
									<label for="editvalue_1_2"><?php echo __('Routing');?> </label>
									<br>
									<a href="javascript:addRouting()">+ Thêm</a>
						
									<br>
									<div id="pnProducts"></div>
									<br>
									<a href="javascript:addRouting()">+ Thêm</a>
								</div>
							</div>
					</div>
			
					<div class="clear"></div>

					<div class="col_half nobottommargin">
						<a class="button button-3d nomargin" href="javascript:saveProduction()"><?php echo __('Save');?></a> 
						<a class="button button-3d button-black nomargin" href="<?php echo URL;?><?php echo $lang; ?>/production"><?php echo __('Back');?></a> 
						
					</div>
					<div class="col_half col_last" style="text-align:right">
						<span style="color:red"><small >*</small></span> <?php echo __('Require input');?>
					</div>

				</form>
		</div>

	</div>
	<script>
		
		
		function saveProduction()
		{
			
			var ctr = document.frmRegister.editname;
			if(ctr.value == '')
			{
				ctr.focus();
				alert("<?php echo __('Please, enter name');?>");
				return false;
			}
			var name = ctr.value;
			
			var _url = '<?php echo URL;?>includes/action.php?ac=saveProduction';
			_url = _url + '&name=' + encodeURIComponent(name);
			_url = _url + '&id=<?php echo $id;?>';
			_url = _url + '&company_id=<?php echo $LOGIN_COMPANY_ID;?>';
	
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						document.location.href ='<?php echo URL;?><?php echo $lang; ?>/production';
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		
		}
		function loadRouting()
		{
			var _url = '<?php echo URL;?>includes/routinglist.php?production_id=<?php echo $id;?>';
			loadPage('pnProducts', _url, function(status, message)
			{
				if(status== 0)
				{
					
				}
				
			}, false);
		}
		function addRouting()
		{
			var _url = '<?php echo URL;?>includes/routinglist.php?ac=addRouting';
			_url = _url + '&production_id=<?php echo $id;?>';
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadRouting();
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		function saveRouting(id, theInput, name)
		{
			var _url = '<?php echo URL;?>includes/routinglist.php?ac=saveRouting';
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
		function delRouting(id)
		{
			var result = confirm("<?php echo __('Want to delete?');?>");
			if (!result) {
				return;
			}
			var _url = '<?php echo URL;?>includes/routinglist.php?ac=delRouting';
			_url = _url + '&id=' + id;
			
			
			loadPage('gotoTop', _url, function(status, message)
			{
				if(status== 0)
				{
					if(message == "OK")
					{
						
						loadRouting();
					}
					else{
						alert(message);
					}
				}
				
			}, true);
		}
		loadRouting();
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
				if (arr[i].toUpperCase().indexOf(val.toUpperCase())){
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
		
	</script>


</section><!-- #content end -->
