<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-Type" content="text/html;"/>
  <link rel="stylesheet" href="/style.css" type="text/css"/>
  <title><?php echo $site_config['site_name']." - ".$page['page_title'];?></title>
 </head>
 <body>
  <table id="layout" cellspacing="0" cellpadding="0" width="100%">
   <tr>
    <td rowspan="2" id="td_sidebar">
     <a href="/"><img src="/images/logo.png" alt="The Cell" id="logo"/></a><br/>
     <div class="linkbox">
       <div class="links">
        <?php 
          $mylinks = mysql_do_query("SELECT * FROM `cms_menu` ORDER BY `item_order` ASC");
          while ($item = mysql_fetch_assoc($mylinks))
          {
            echo "<a href=\"{$item['item_url']}\">{$item['item_text']}</a>\n"; 
          }
        ?>
      </div>
     </div>
     <?php
       if ($user["editcontent"] == 1)
       {
         ?>
         <br/>
         <br/>
         <div class="linkbox">
          <center><b>Admin</b></center>
          <div class="links">
					 <a href="<?php echo $page['parent_path'].$page['page_key'];?>.config">Site Config</a>
			    </div>
					<br/>
					<center><b>Page editing</b></center>
					<div class="links">
					 <?php if ($page['found'])
				   { ?>
             <a href="<?php echo $page['parent_path'].$page['page_key'];?>.createsection">New Section</a>
             <a href="<?php echo $page['parent_path'].$page['page_key'];?>.edittitle">Edit Title</a>
             <a href="<?php echo $page['parent_path'].$page['page_key'];?>.move">Move</a>
             <a href="<?php echo $page['parent_path'].$page['page_key'];?>.delpage">Nuke Page</a>
					 <?php } ?>
          </div>
         </div>
         <?php
       }
     ?>
    </td>
    <td id="td_content">
     <?php
       $c = "";
       foreach ($path as $v)
       {
         $c .= $v["cat_key"]."/";
         echo " &raquo; <a href=\"$c\">{$v["cat_title"]}</a>";
       }
			 if ($page["page_key"] != "index")
			 	echo " &raquo; <a href=\"$c{$page["page_key"]}\">{$page["page_title"]}</a>";
     ?>
     <br/>
     <br/>

