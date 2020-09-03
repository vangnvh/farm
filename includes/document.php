<?php
require_once('../config.php' );

function httpPost($url, $data)
{
    $ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
	curl_setopt($ch,CURLOPT_TIMEOUT, 20);
	$response = curl_exec($ch);
	curl_close ($ch);
	return $response;
}

$ac = "";
if(isset($_REQUEST['ac']))
{
	$ac = $_REQUEST['ac'];
}
	
?>
<?php
if($ac == "view")
{
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

	$for_id = '';
	if(isset($_REQUEST['for_id']))
	{
		$for_id = $_REQUEST['for_id'];
	}

	$cview = '';
	if(isset($_REQUEST['cview']))
	{
		$cview = $_REQUEST['cview'];
	}

	$pageIndex = '';
	if(isset($_REQUEST['id']))
	{
		$pageIndex = $_REQUEST['id'];
	}
	$document_id = '';
	if(isset($_REQUEST['document_id']))
	{
		$document_id = $_REQUEST['document_id'];
	}
	$can_edit = '';
	if(isset($_REQUEST['can_edit']))
	{
		$can_edit = $_REQUEST['can_edit'];
	}
?>
<?php if($can_edit == "1"){?>
<input id="files<?php echo $pageIndex;?>" type="file" multiple  style="display:none" onchange="readFile<?php echo $pageIndex;?>('', this);"><a href="javascript:document.getElementById('files<?php echo $pageIndex;?>').click();"> &nbsp;&nbsp; <img src="<?php echo URL;?>assets/images/publish.png"> <?php echo __('Attachment');?>&nbsp;&nbsp;</a> <span id="progress_upload<?php echo $pageIndex;?>"></span>
<?php } ?>
<div id="list<?php echo $pageIndex;?>"></div>
<!-- Modal -->
<div class="modal fade" id="exampleModal<?php echo $pageIndex; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo __('Attachment');?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="documentTitleBody<?php echo $pageIndex; ?>">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo __('Close');?></button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="uploadDoc<?php echo $pageIndex; ?>()"><?php echo __('Upload');?></button>
      </div>
    </div>
  </div>
</div>

<script>


var reader<?php echo $pageIndex; ?>;
var files<?php echo $pageIndex; ?>;
var names<?php echo $pageIndex; ?> = new Array();
var file_index<?php echo $pageIndex; ?> = 0;

function viewDoc<?php echo $pageIndex; ?>()
{
	var _url = '<?php echo URL;?>includes/document.php?ac=list_doc&for_id=<?php echo $for_id;?>&pageIndex=<?php echo $pageIndex; ?>&can_edit=<?php echo $can_edit;?>' ;
	loadPage('list<?php echo $pageIndex;?>', _url, function(status, message)
	{
		if(status== 0)
		{
			
		}
		
	}, false);
			
}
function updateName<?php echo $pageIndex; ?>(id, name)
{
	var name = prompt("Please enter your name", name);
	if (name != null) {
		var _url = '<?php echo URL;?>includes/document.php?ac=updateName&id=' + id;
		_url = _url + "&name=" + encodeURIComponent(name);
		loadPage('pnFileManager<?php echo $pageIndex;?>', _url, function(status, message)
		{
			if(status  == 0)
			{
				if(message == "OK")
				{
					viewDoc<?php echo $pageIndex;?>();
				}else
				{
					alert(message);
				}
			}
		}, true);
	}
}

function delDoc<?php echo $pageIndex; ?>(id)
{
	var result = confirm("<?php echo __('Are you sure to clone');?>");
	if (!result) {
		return;
	}	
	var _url = '<?php echo URL;?>includes/document.php?ac=delDoc&id=' + id;
	loadPage('pnFileManager<?php echo $pageIndex;?>', _url, function(status, message)
	{
		if(status  == 0)
		{
			if(message == "OK")
			{
				viewDoc<?php echo $pageIndex;?>();
			}else
			{
				alert(message);
			}
		}
	}, true);
}
function readFile<?php echo $pageIndex; ?>(parent_id, theFiles) 
{
	
	files<?php echo $pageIndex; ?> = theFiles.files;
	if (!files<?php echo $pageIndex; ?>.length) {
	  alert('Please select a file!');
	  return;
	}
	names<?php echo $pageIndex; ?> = new Array();
	for(var i =0; i<files<?php echo $pageIndex; ?>.length; i++)
	{
		var name = files<?php echo $pageIndex; ?>[i].name;
		var index = name.lastIndexOf(".");
		if(index != -1)
		{
			name = name.substr(0, index);
		}
		names<?php echo $pageIndex; ?>.push(name);
		
	}
	var s = "";
	for(var i=0; i<names<?php echo $pageIndex; ?>.length; i++)
	{
		if(s += "")
		{
			s += "<br>";
		}
		s += (i + 1) + '. <input type="text" maxLength="250" class="form-control" id="<?php echo $pageIndex; ?>' + i + '" value="' + names<?php echo $pageIndex; ?>[i] + '"/>';
	}
	document.getElementById('documentTitleBody<?php echo $pageIndex; ?>').innerHTML = s;
	$('#exampleModal<?php echo $pageIndex; ?>').modal({
	  keyboard: true
	})
	
	
	
}
function uploadDoc<?php echo $pageIndex; ?>()
{
	for(var i=0; i<names<?php echo $pageIndex; ?>.length; i++)
	{
		names<?php echo $pageIndex; ?>[i] = document.getElementById('<?php echo $pageIndex; ?>' + i).value;
	
	}
	
	file_index<?php echo $pageIndex; ?> = 0;
	upByFile<?php echo $pageIndex; ?>();
}

function postDataPage<?php echo $pageIndex; ?>(_url, params, complete)
{
	  
	var xmlHttp = GetXmlHttpObject();
	if (xmlHttp == null) {
			alert("Browser does not support HTTP Request");
			return;
	}
	xmlHttp.open("POST", _url, true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=UTF-8");
	xmlHttp.onreadystatechange = function() 
	{
		if ((xmlHttp.readyState == 4) || (xmlHttp.readyState == "complete")) 
		{
			var responseMsg =  xmlHttp.responseText;
			if (xmlHttp.status == 200) 
			{
				complete(0, responseMsg);	
			}
		}
	};
	
	xmlHttp.send(params);
	
}

function upByFile<?php echo $pageIndex; ?>()
{
	var file = files<?php echo $pageIndex; ?>[file_index<?php echo $pageIndex; ?>];
	file.name = "";
	var document_id = '<?php echo $document_id; ?>';
	if(file_index<?php echo $pageIndex; ?> >0)
	{
		document_id = "";
	}
	var _url = '<?php echo URL;?>includes/document.php?ac=createFile&document_id=' + document_id;
	
	loadPage('pnFileManager<?php echo $pageIndex; ?>', _url, function(status, message)
	{
		if(status  == 0)
		{
			if(message.length == 36)
			{
				var file_id = message;
				reader<?php echo $pageIndex; ?> = new FileReader();
				
				var byteRead = 1024 * 100;
				
				
				var start = 0;
				var stop = byteRead -1;
				var sizes = file.size;
				
				if(sizes<=byteRead)
				{
					stop = sizes - 1;
				}
				
				var baseUrl = '<?php echo URL;?>includes/document.php?ac=writeFile&file_id=' + file_id ;
				var proccess = document.getElementById('progress_upload<?php echo $pageIndex; ?>');
				reader<?php echo $pageIndex; ?>.onloadend = function(evt) {
				
				  if (evt.target.readyState == FileReader.DONE) { 
					
	
					var _url = baseUrl;
					if(start == 0)
					{
						_url = _url + '&n=1' 
					}
					
					var base64String = btoa(String.fromCharCode.apply(null, new Uint8Array(evt.target.result)));
				
					base64String = 'sData=' + encodeURIComponent(base64String);
					
					if(evt.target.result.byteLength>0)
					{
						postDataPage<?php echo $pageIndex; ?>(_url, base64String, function(status, message)
						{
							if(status  == 0)
							{
								if(message == "OK")
								{
									var p = (start/sizes) * 100;
									p = parseInt(p);
									proccess.innerHTML  = p + "%";
									stop = stop + 1;
									start = stop ;
									if((stop + 1)<sizes)
									{
						
										if((stop + byteRead)>=sizes)
										{
											byteRead = (sizes - stop);
										}
										stop = stop + byteRead;
										stop = stop -1;
										readBytes<?php echo $pageIndex; ?>(file, start, stop);
									}else
									{
										var extension = files<?php echo $pageIndex; ?>[file_index<?php echo $pageIndex; ?>].name;
										var index = extension.lastIndexOf(".");
										if(index != -1)
										{
											extension = extension.substr(index);
										}else{
											extension = "";
										}
										_url = '<?php echo URL;?>includes/document.php?ac=commitDocument&file_id=' + file_id ;
										_url = _url + '&file_name=' + encodeURIComponent(names<?php echo $pageIndex; ?>[file_index<?php echo $pageIndex; ?>] + extension);
										_url = _url + '&document_id=' +document_id;
										_url = _url + '&for_id=<?php echo $for_id; ?>';
										
										loadPage('pnFileManager<?php echo $pageIndex; ?>', _url, function(status, message)
										{
											if(status  == 0)
											{
												if(message == "OK")
												{
													file_index<?php echo $pageIndex; ?> += 1;
													if(files<?php echo $pageIndex; ?>.length>file_index<?php echo $pageIndex; ?>)
													{
														if(files<?php echo $pageIndex; ?>.length>1)
														{
															viewDoc<?php echo $pageIndex; ?>();
														}
														
														upByFile<?php echo $pageIndex; ?>();
													}
													else{
													
														proccess.setAttribute("class", "");
														proccess.innerHTML = "";
														<?php
														$func = "";
														if(isset($_REQUEST['func']))
														{
															$func = $_REQUEST['func'];
														}
														
														if($func != "")
														{
															echo $func."();";;
														}else{
														?>
														var ctr= document.getElementById('document_name');
														if(ctr != null && ctr.value == '')
														{
															var s = names<?php echo $pageIndex; ?>[file_index<?php echo $pageIndex; ?>];
															ctr.value = s;
														}
														viewDoc<?php echo $pageIndex; ?>();
														<?php }?>
													}
													
												}else
												{
													alert(message);
												}
											}
										}, true);
										
									}
								}else
								{
									alert(message);
								}
							}
							
						});
					}
					
				  }
				};
				
				readBytes<?php echo $pageIndex; ?>(file, start, stop);
				
			}else
			{
				alert(message);
			}
		}
	}, true);
}
function readBytes<?php echo $pageIndex; ?>(file, start, stop)
{
 
	var blob = file.slice(start, stop + 1);
	reader<?php echo $pageIndex; ?>.readAsArrayBuffer(blob);
}
viewDoc<?php echo $pageIndex; ?>();

</script>
<?php
}else if($ac == "createFile")
{
	
	$document_id = "";
	if(isset($_REQUEST['document_id']))
	{
		$document_id = $_REQUEST['document_id'];
	}
	$data = "ac=createFile&document_id=".$document_id;
	
	echo httpPost(SERVER_URL."document/action", $data);
	
	
}else if($ac == "writeFile")
{
	$file_id = "";
	if(isset($_REQUEST['file_id']))
	{
		$file_id = $_REQUEST['file_id'];
	}
	$sData = "";
	if(isset($_REQUEST['sData']))
	{
		$sData = $_REQUEST['sData'];
	}
	
	$data = "ac=writeFile&file_id=".$file_id."&sData=".urlencode($sData);
	echo httpPost(SERVER_URL."document/action", $data);
	
}else if($ac == "commitDocument")
{
	$document_id = "";
	if(isset($_REQUEST['document_id']))
	{
		$document_id = $_REQUEST['document_id'];
	}
	$file_id = "";
	if(isset($_REQUEST['file_id']))
	{
		$file_id = $_REQUEST['file_id'];
	}
	$for_id = "";
	if(isset($_REQUEST['for_id']))
	{
		$for_id = $_REQUEST['for_id'];
	}
	$file_name = "";
	if(isset($_REQUEST['file_name']))
	{
		$file_name = $_REQUEST['file_name'];
	}
	$data = "ac=commitDocument&document_id=".$document_id."&file_id=".$file_id."&for_id=".$for_id."&file_name=".urlencode($file_name);
	echo httpPost(SERVER_URL."document/action", $data);
	
}
else if($ac == "list_doc")
{
	$for_id = "";
	if(isset($_REQUEST['for_id']))
	{
		$for_id = $_REQUEST['for_id'];
	}
	$can_edit = "";
	if(isset($_REQUEST['can_edit']))
	{
		$can_edit = $_REQUEST['can_edit'];
	}
	$pageIndex = "";
	if(isset($_REQUEST['pageIndex']))
	{
		$pageIndex = $_REQUEST['pageIndex'];
	}
	$sql = "SELECT d1.id, d1.document_name, d1.extension, d1.content_length FROM document d1 WHERE d1.for_id='".$for_id."' AND d1.status =0 ORDER BY d1.create_date ASC";
	
	
	
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);
	for($i =0; $i<$numrows; $i++)
	{
		$row = pg_fetch_array($result, $i);
		$file_id = $row["id"];
		$document_name = $row["document_name"];
		if($i>0)
		{
			echo "<br>";
		}
		?>
		<?php echo ($i+1);?>. <?php echo $document_name; ?> <a href="<?php echo SERVER_URL;?>document/action?ac=download&id=<?php echo $file_id; ?>" target="_blank"><img src="<?php echo URL;?>assets/images/get_app_black.png"></a> <?php if($can_edit == "1"){?><a href="javascript:updateName<?php echo $pageIndex;?>('<?php echo $file_id; ?>', '<?php echo $document_name;?>');"><img src="<?php echo URL;?>assets/images/edit.png"></a> <a href="javascript:delDoc<?php echo $pageIndex;?>('<?php echo $file_id; ?>')"><img src="<?php echo URL;?>assets/images/delete.png"></a><?php } ?>
		<?php
	}
	
}else if($ac == "updateName")
{
	$name = "";
	if(isset($_REQUEST['name']))
	{
		$name = $_REQUEST['name'];
	}
	$id = "";
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}
	
	
	$sql = "UPDATE document SET document_name ='".str_replace("'", "''", $name)."', write_date=NOW() WHERE id ='".$id."'";
	pg_exec($db, $sql);
	echo "OK";
}else if($ac == "delDoc")
{
	$id = "";
	if(isset($_REQUEST['id']))
	{
		$id = $_REQUEST['id'];
	}
	$sql = "UPDATE document SET status =1, write_date=NOW() WHERE id ='".$id."'";
	pg_exec($db, $sql);
	echo "OK";
}
?>
