<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $title_for_layout; ?>
    </title>
    <link rel="stylesheet" type="text/css" media="all" href="/cgi-bin/filechucker.cgi?css" />

    <?php
        echo $this->Javascript->link('jquery.js');
        echo $this->Javascript->link('jquery-ui.js');
        echo $this->Javascript->link('application.js');
        echo $this->Javascript->link('/cgi-bin/filechucker.cgi?js');
        echo $this->Javascript->link('jquery.cookie.js');

        echo $this->Html->meta('icon');
        echo $this->Html->meta('rss', '/podcasts/rss.rss');
        echo $this->Html->css('cake.generic');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('podcast-server');
        echo $this->Html->css('ou-header');
		echo $this->Html->css('type');
		echo $this->Html->css('interface');
        echo $scripts_for_layout;
		flush();
    ?>
    
    <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="/css/all-ie-only.css" />
	<![endif]-->
    
</head>
<body>

    <!--display OU Header-->
	<?php echo $this->element('header'); ?>
    <!--/display OU Header-->
    
    <div id="container">
    
        <div id="header">
            <h1 class="sitename">OU Podcast Server</h1>
            <p class="strapline">For the management of podcast collections</p><div class="clear"></div>

            <?php echo $this->element('breadcrumb', array('breadcrumbs' => $breadcrumbs ) ); ?>
            
        </div>
        

        
        <div id="content">
            <?php echo $this->Session->flash(); ?>
            <?php echo $this->Session->flash('email'); ?>
            <?php echo $this->element('error'); ?>
            <div class="collection_wrapper">
	            <?php echo $content_for_layout; ?>
               </div>
        </div>
         
        <div id="footer">
            &nbsp;
        </div>
    </div>
    
    <!--display OU Footer-->
	<?php echo $this->element('footer'); ?>
    <!--/display OU Footer-->
    
	<?php echo $this->element('sql_dump'); ?>
    <div id="modal"></div>
</body>
</html>