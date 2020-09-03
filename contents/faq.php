<?php
$page_name = "";

$sql = "select d1.id, d1.url_id, d1.name, dl.name AS name_lang FROM faq_category d1 LEFT OUTER JOIN res_lang_rel dl ON(d1.id = dl.rel_id AND dl.lang_id='".$lang_id."' AND dl.status =0 AND dl.column_name='name') WHERE d1.company_id='".COMPANY_ID."' AND d1.status =0 ORDER BY d1.sequence ASC";
$result = pg_exec($db, $sql);
$numrows = pg_numrows($result);
for($ri = 0; $ri < $numrows; $ri++) 
{
	$row = pg_fetch_array($result, $ri);
	if($row["id"] == $rel_id)
	{
		$page_name = $row["name_lang"];
		if($page_name == '' )
		{
			$page_name =  $row["name"];
		}	
	}
	
}


if($rel_id != "")
{
	
	$sql = "select d1.id, d1.name, dl1.name AS name_lang, d1.answer, dl2.name AS answer_lang FROM faq_category_rel d LEFT OUTER JOIN faq d1 ON(d.rel_id = d1.id) LEFT OUTER JOIN res_lang_rel dl1 ON(d1.id = dl1.rel_id AND dl1.lang_id='".$lang_id."' AND dl1.status =0 AND dl1.column_name='name') LEFT OUTER JOIN res_lang_rel dl2 ON(d1.id = dl2.rel_id AND dl2.lang_id='".$lang_id."' AND dl2.status =0 AND dl2.column_name='answer') WHERE d.status =0 AND d1.status =0 AND d.category_id='".$rel_id."'";
	
}else
{
	
	
	$sql = "select d1.id, d1.name, dl1.name AS name_lang, d1.answer, dl2.name AS answer_lang FROM faq d1 LEFT OUTER JOIN res_lang_rel dl1 ON(d1.id = dl1.rel_id AND dl1.lang_id='".$lang_id."' AND dl1.status =0 AND dl1.column_name='name') LEFT OUTER JOIN res_lang_rel dl2 ON(d1.id = dl2.rel_id AND dl2.lang_id='".$lang_id."' AND dl2.status =0 AND dl2.column_name='answer') WHERE d1.status =0 AND d1.company_id='".COMPANY_ID."'";
	
}
$sql = $sql." ORDER BY d1.sequence ASC";

$result_items = pg_exec($db, $sql);
$numrows_items = pg_numrows($result_items);	
?>
<!-- Page Title
============================================= -->
<section id="page-title">

	<div class="container clearfix">
		<h1><?php echo __('faqs');?></h1>
		<span><?php echo __('All your Questions answered in one place');?></span>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo URL;?>"><?php echo __('home');?></a></li>
			<li class="breadcrumb-item <?php if($rel_id == ''){ ?> active <?php } ?>" ><a href="<?php echo URL;?><?php echo $lang; ?>/faq"><?php echo __('faqs');?></a></li>
			<?php if($rel_id != ''){ ?>
			<li class="breadcrumb-item  active " aria-current="page"><?php echo $page_name; ?></li>
			<?php } ?>
		</ol>
	</div>

</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		<div class="container clearfix">

			<!-- Post Content
			============================================= -->
			<div class="postcontent nobottommargin clearfix">

				<ul id="portfolio-filter" class="portfolio-filter customjs clearfix">

					<li <?php if($rel_id == ""){ ?>class="activeFilter"<?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/faq" data-filter="all">All</a></li>
					<?php
				
					for($ri = 0; $ri < $numrows; $ri++) 
					{
						$row = pg_fetch_array($result, $ri);
						$id =$row["id"];
						$url_id =$row["url_id"];
						$name = $row["name_lang"];
						if($name == '')
						{
							$name =  $row["name"];
						}
					?>
					<li <?php if($rel_id == $id){ ?>class="activeFilter" <?php } ?>><a href="<?php echo URL;?><?php echo $lang; ?>/<?php echo $url_id; ?>-<?php echo $name; ?>/<?php echo $url_id; ?>" data-filter=".faq-marketplace"><?php echo $name; ?></a></li>
					<?php } ?>

				</ul>

				<div class="clear"></div>

				<div id="faqs" class="faqs">
					<?php
					for($j =0; $j<$numrows_items; $j++)
					{
						$row = pg_fetch_array($result_items, $j);
						$name = $row["name_lang"];
						if($name == '')
						{
							$name =  $row["name"];
						}
						$answer = $row["answer_lang"];
						if($answer == '')
						{
							$answer =  $row["answer"];
						}
					?>
					<div class="toggle faq faq-marketplace faq-authors">
						<div class="togglet"><i class="toggle-closed icon-question-sign"></i><i class="toggle-open icon-question-sign"></i><?php echo $name; ?></div>
						<div class="togglec"><?php echo $answer; ?></div>
					</div>
					<?php
					}
					?>
					

				</div>


			</div><!-- .postcontent end -->


		</div>

	</div>

</section><!-- #content end -->
